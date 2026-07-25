<?php

namespace App\Services;

use App\Models\BlogCategory;
use App\Models\BlogPost;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMXPath;

class BlogCsvImporter
{
    public const MAX_FILE_SIZE = 10 * 1024 * 1024;
    public const MAX_ROWS = 500;
    public const BATCH_TTL = 1800;

    private const EXPECTED_HEADERS = [
        'Título',
        'Meta Descripción',
        'Introducción',
        'Youtube',
        'Principal H2',
        'PAA H2',
        'Respaldo H2',
        'FAQ',
        'Respuestas',
        'Categoría',
        'Shortcode',
        'Directorio',
        'Slug-Seo',
        'Amazon-Resenas',
    ];

    private const BLOCK_TAGS = [
        'p', 'h2', 'h3', 'h4', 'ul', 'ol', 'li', 'blockquote',
        'table', 'thead', 'tbody', 'tfoot', 'tr', 'th', 'td', 'hr',
    ];

    private const INLINE_TAGS = [
        'a', 'strong', 'b', 'em', 'i', 'u', 's', 'br', 'code', 'span',
    ];

    private const ALLOWED_ATTRIBUTES = [
        'a' => ['href', 'title', 'target', 'rel'],
        'th' => ['colspan', 'rowspan', 'scope'],
        'td' => ['colspan', 'rowspan'],
    ];

    public static function expectedHeaders(): array
    {
        return self::EXPECTED_HEADERS;
    }

    public function parseUploadedFile(
        array $file,
        array $existingSlugs,
        array $existingCategories
    ): array {
        $error = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException($this->uploadErrorMessage($error));
        }

        $name = trim((string)($file['name'] ?? ''));
        if (strtolower(pathinfo($name, PATHINFO_EXTENSION)) !== 'csv') {
            throw new \RuntimeException('El archivo debe tener extensión .csv.');
        }

        $size = (int)($file['size'] ?? 0);
        if ($size <= 0) {
            throw new \RuntimeException('El archivo CSV está vacío.');
        }
        if ($size > self::MAX_FILE_SIZE) {
            throw new \RuntimeException('El archivo CSV excede el máximo permitido de 10 MB.');
        }

        $path = (string)($file['tmp_name'] ?? '');
        if ($path === '' || !is_file($path) || !is_readable($path)) {
            throw new \RuntimeException('No se pudo leer el archivo CSV recibido.');
        }
        if (PHP_SAPI !== 'cli' && !is_uploaded_file($path)) {
            throw new \RuntimeException('El archivo recibido no es una subida válida.');
        }

        return $this->parseFile($path, $existingSlugs, $existingCategories);
    }

    public function parseFile(
        string $path,
        array $existingSlugs = [],
        array $existingCategories = []
    ): array {
        if (!is_file($path) || !is_readable($path)) {
            throw new \RuntimeException('No se pudo abrir el archivo CSV.');
        }

        $size = filesize($path);
        if ($size === false || $size <= 0) {
            throw new \RuntimeException('El archivo CSV está vacío.');
        }
        if ($size > self::MAX_FILE_SIZE) {
            throw new \RuntimeException('El archivo CSV excede el máximo permitido de 10 MB.');
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new \RuntimeException('No se pudo leer el archivo CSV.');
        }
        if (!mb_check_encoding($raw, 'UTF-8')) {
            throw new \RuntimeException('El archivo debe estar codificado en UTF-8.');
        }
        unset($raw);

        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('No se pudo abrir el archivo CSV.');
        }

        try {
            $headers = fgetcsv($handle, null, ',', '"', '');
            if (!is_array($headers)) {
                throw new \RuntimeException('El archivo no contiene encabezados.');
            }

            if (isset($headers[0])) {
                $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$headers[0]);
            }
            $headers = array_map(static fn($value): string => (string)$value, $headers);

            if ($headers !== self::EXPECTED_HEADERS) {
                throw new \RuntimeException($this->headerMismatchMessage($headers));
            }

            $usedSlugs = [];
            foreach ($existingSlugs as $slug) {
                $usedSlugs[(string)$slug] = true;
            }

            $categoryMap = [];
            foreach ($existingCategories as $category) {
                $slug = (string)($category['slug'] ?? '');
                if ($slug !== '') {
                    $categoryMap[$slug] = $category;
                }
            }

            $rows = [];
            $recordNumber = 1;
            while (($values = fgetcsv($handle, null, ',', '"', '')) !== false) {
                $recordNumber++;

                if ($this->isBlankRecord($values)) {
                    continue;
                }

                if (count($rows) >= self::MAX_ROWS) {
                    throw new \RuntimeException('El archivo excede el máximo permitido de 500 filas.');
                }

                if (count($values) !== count(self::EXPECTED_HEADERS)) {
                    $rows[] = [
                        'row_number' => $recordNumber,
                        'title' => '',
                        'slug' => '',
                        'category_name' => '',
                        'category_is_new' => false,
                        'warnings' => [],
                        'errors' => [
                            sprintf(
                                'Se esperaban 14 columnas y se encontraron %d.',
                                count($values)
                            ),
                        ],
                        'content' => '',
                        'data' => null,
                    ];
                    continue;
                }

                $source = array_combine(self::EXPECTED_HEADERS, $values);
                if (!is_array($source)) {
                    throw new \RuntimeException('No se pudo asociar una fila con sus encabezados.');
                }

                $row = $this->normalizeRow($source, $recordNumber, $usedSlugs, $categoryMap);
                $rows[] = $row;

                if ($row['errors'] === []) {
                    $usedSlugs[$row['slug']] = true;
                }
            }
        } finally {
            fclose($handle);
        }

        if ($rows === []) {
            throw new \RuntimeException('El archivo no contiene filas de datos.');
        }

        $errorCount = 0;
        $warningCount = 0;
        foreach ($rows as $row) {
            $errorCount += count($row['errors']);
            $warningCount += count($row['warnings']);
        }

        return [
            'headers' => self::EXPECTED_HEADERS,
            'rows' => $rows,
            'total_rows' => count($rows),
            'valid_rows' => count(array_filter($rows, static fn(array $row): bool => $row['errors'] === [])),
            'error_count' => $errorCount,
            'warning_count' => $warningCount,
            'can_import' => $errorCount === 0,
        ];
    }

    public function createBatch(array $preview, int $adminId): string
    {
        if (($preview['can_import'] ?? false) !== true || empty($preview['rows'])) {
            throw new \RuntimeException('El lote contiene errores y no puede prepararse para importar.');
        }

        $rows = [];
        foreach ($preview['rows'] as $row) {
            if (!is_array($row['data'] ?? null)) {
                throw new \RuntimeException('El lote contiene una fila sin datos normalizados.');
            }
            $rows[] = $row['data'];
        }

        $this->cleanupExpiredBatches();
        $token = bin2hex(random_bytes(32));
        $path = $this->batchPath($token);
        $now = time();
        $payload = [
            'admin_id' => $adminId,
            'created_at' => $now,
            'expires_at' => $now + self::BATCH_TTL,
            'rows' => $rows,
        ];

        $encoded = json_encode(
            $payload,
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR
        );
        if (file_put_contents($path, $encoded, LOCK_EX) === false) {
            throw new \RuntimeException('No se pudo guardar temporalmente la previsualización.');
        }
        @chmod($path, 0600);

        $_SESSION['blog_import_batches'][$token] = [
            'admin_id' => $adminId,
            'path' => $path,
            'expires_at' => $payload['expires_at'],
        ];

        return $token;
    }

    public function consumeBatch(string $token, int $adminId): array
    {
        $this->cleanupExpiredBatches();
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
            throw new \RuntimeException('El lote de importación no es válido.');
        }

        $entry = $_SESSION['blog_import_batches'][$token] ?? null;
        if (!is_array($entry)
            || (int)($entry['admin_id'] ?? 0) !== $adminId
            || (int)($entry['expires_at'] ?? 0) < time()
        ) {
            throw new \RuntimeException('La previsualización expiró o pertenece a otra sesión.');
        }

        $expectedPath = $this->batchPath($token);
        $path = (string)($entry['path'] ?? '');
        if ($path !== $expectedPath || !is_file($path) || !is_readable($path)) {
            unset($_SESSION['blog_import_batches'][$token]);
            throw new \RuntimeException('No se encontró el lote temporal. Vuelve a previsualizar el CSV.');
        }

        $raw = file_get_contents($path);
        @unlink($path);
        unset($_SESSION['blog_import_batches'][$token]);

        if ($raw === false) {
            throw new \RuntimeException('No se pudo leer el lote temporal.');
        }

        $payload = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($payload)
            || (int)($payload['admin_id'] ?? 0) !== $adminId
            || (int)($payload['expires_at'] ?? 0) < time()
            || !is_array($payload['rows'] ?? null)
        ) {
            throw new \RuntimeException('El lote temporal ya no es válido.');
        }

        return $payload['rows'];
    }

    public function csrfToken(): string
    {
        if (empty($_SESSION['blog_import_csrf'])) {
            $_SESSION['blog_import_csrf'] = bin2hex(random_bytes(32));
        }
        return (string)$_SESSION['blog_import_csrf'];
    }

    public function verifyCsrfToken(string $token): bool
    {
        $stored = (string)($_SESSION['blog_import_csrf'] ?? '');
        return $stored !== '' && $token !== '' && hash_equals($stored, $token);
    }

    public function rotateCsrfToken(): string
    {
        unset($_SESSION['blog_import_csrf']);
        return $this->csrfToken();
    }

    public function cleanupExpiredBatches(): void
    {
        $now = time();
        foreach ((array)($_SESSION['blog_import_batches'] ?? []) as $token => $entry) {
            if (!is_array($entry) || (int)($entry['expires_at'] ?? 0) < $now) {
                $path = is_array($entry) ? (string)($entry['path'] ?? '') : '';
                if ($path !== '' && is_file($path) && dirname($path) === $this->batchDirectory()) {
                    @unlink($path);
                }
                unset($_SESSION['blog_import_batches'][$token]);
            }
        }

        $threshold = $now - (self::BATCH_TTL * 2);
        foreach (glob($this->batchDirectory() . DIRECTORY_SEPARATOR . '*.json') ?: [] as $path) {
            if (is_file($path) && (int)filemtime($path) < $threshold) {
                @unlink($path);
            }
        }
    }

    private function normalizeRow(
        array $source,
        int $recordNumber,
        array $usedSlugs,
        array $categoryMap
    ): array {
        $errors = [];
        $warnings = [];

        $title = trim((string)$source['Título']);
        $metaDescription = trim((string)$source['Meta Descripción']);
        $categoryName = trim((string)$source['Categoría']);
        $slugSource = trim((string)$source['Slug-Seo']);

        if ($title === '') {
            $errors[] = 'El título es obligatorio.';
        } elseif (mb_strlen($title, 'UTF-8') > 255) {
            $errors[] = 'El título excede 255 caracteres.';
        }

        if (strlen($metaDescription) > 65535) {
            $errors[] = 'La meta descripción excede la capacidad del campo TEXT.';
        }
        if (mb_strlen($metaDescription, 'UTF-8') > 160) {
            $warnings[] = sprintf(
                'La meta descripción tiene %d caracteres; se conservará completa.',
                mb_strlen($metaDescription, 'UTF-8')
            );
        }

        if (trim((string)$source['Shortcode']) !== '') {
            $errors[] = 'Shortcode debe estar vacío porque el sitio no ejecuta shortcodes.';
        }
        if (trim((string)$source['Directorio']) !== '') {
            $errors[] = 'Directorio debe estar vacío porque no tiene un campo equivalente.';
        }

        $categorySlug = '';
        $categoryIsNew = false;
        if ($categoryName !== '') {
            if (mb_strlen($categoryName, 'UTF-8') > 100) {
                $errors[] = 'La categoría excede 100 caracteres.';
            }
            $categorySlug = BlogCategory::generateSlug($categoryName);
            if (mb_strlen($categorySlug, 'UTF-8') > 120) {
                $errors[] = 'El slug de la categoría excede 120 caracteres.';
            }
            $categoryIsNew = !isset($categoryMap[$categorySlug]);
        } else {
            $warnings[] = 'El post se importará sin categoría.';
        }

        $baseSlug = BlogPost::generateSlug($slugSource !== '' ? $slugSource : $title);
        if (mb_strlen($baseSlug, 'UTF-8') > 255) {
            $errors[] = 'El slug del post excede 255 caracteres.';
        }
        $slug = $this->nextAvailableSlug($baseSlug, $usedSlugs, 255);
        if ($slug !== $baseSlug) {
            $warnings[] = sprintf('El slug se ajustó de "%s" a "%s".', $baseSlug, $slug);
        }

        $content = '';
        try {
            $parts = [];
            $introduction = trim((string)$source['Introducción']);
            if ($introduction !== '') {
                $introduction = preg_replace('/\s+/u', ' ', $introduction) ?? $introduction;
                $parts[] = '<p>' . htmlspecialchars(
                    $introduction,
                    ENT_QUOTES | ENT_SUBSTITUTE,
                    'UTF-8'
                ) . '</p>';
            }

            $youtube = trim((string)$source['Youtube']);
            if ($youtube !== '') {
                $parts[] = $this->youtubeEmbed($youtube, $title);
            }

            foreach ([
                'Principal H2',
                'PAA H2',
                'Respaldo H2',
                'FAQ',
                'Respuestas',
                'Amazon-Resenas',
            ] as $column) {
                $fragment = trim((string)$source[$column]);
                if ($fragment !== '') {
                    $parts[] = $this->normalizeHtmlFragment($fragment, $column);
                }
            }

            $parts = array_values(array_filter($parts, static fn(string $part): bool => trim($part) !== ''));
            $content = implode("\n", $parts);
            if (trim(strip_tags($content)) === '' && !str_contains($content, '<iframe')) {
                $errors[] = 'El contenido compuesto está vacío.';
            }
            if (strlen($content) > 65535) {
                $errors[] = 'El contenido compuesto excede la capacidad de 65,535 bytes.';
            }
        } catch (\InvalidArgumentException $e) {
            $errors[] = $e->getMessage();
        }

        $data = null;
        if ($errors === []) {
            $data = [
                'title' => $title,
                'base_slug' => $baseSlug,
                'excerpt' => $metaDescription,
                'content' => $content,
                'category_name' => $categoryName,
                'category_slug' => $categorySlug,
                'meta_description' => $metaDescription,
            ];
        }

        return [
            'row_number' => $recordNumber,
            'title' => $title,
            'slug' => $slug,
            'category_name' => $categoryName,
            'category_is_new' => $categoryIsNew,
            'warnings' => $warnings,
            'errors' => $errors,
            'content' => $content,
            'data' => $data,
        ];
    }

    private function normalizeHtmlFragment(string $html, string $column): string
    {
        if (preg_match(
            '/<\s*\/?\s*(script|style|iframe|object|embed|form|input|button|link|meta|svg|math)\b/i',
            $html
        )) {
            throw new \InvalidArgumentException(
                sprintf('%s contiene una etiqueta HTML no permitida.', $column)
            );
        }
        if (preg_match('/<\?|<!DOCTYPE/i', $html)) {
            throw new \InvalidArgumentException(
                sprintf('%s contiene instrucciones HTML no permitidas.', $column)
            );
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $loaded = $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="csv-import-root">' . $html . '</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        if (!$loaded) {
            throw new \InvalidArgumentException(
                sprintf('%s no contiene HTML válido.', $column)
            );
        }

        $root = $document->getElementById('csv-import-root');
        if (!$root instanceof DOMElement) {
            throw new \InvalidArgumentException(
                sprintf('No se pudo normalizar el contenido de %s.', $column)
            );
        }

        $allowedTags = array_fill_keys(array_merge(self::BLOCK_TAGS, self::INLINE_TAGS), true);
        $xpath = new DOMXPath($document);
        $elements = [];
        foreach ($xpath->query('//*') ?: [] as $element) {
            if ($element instanceof DOMElement && $element !== $root) {
                $elements[] = $element;
            }
        }

        foreach ($elements as $element) {
            $tag = strtolower($element->tagName);
            if (!isset($allowedTags[$tag])) {
                throw new \InvalidArgumentException(
                    sprintf('%s contiene la etiqueta <%s>, que no está permitida.', $column, $tag)
                );
            }
            $this->validateElementAttributes($element, $column);
        }

        $this->wrapRootInlineContent($document, $root);

        $normalized = '';
        foreach ($root->childNodes as $child) {
            $normalized .= $document->saveHTML($child);
        }

        return trim($normalized);
    }

    private function validateElementAttributes(DOMElement $element, string $column): void
    {
        $tag = strtolower($element->tagName);
        $allowed = self::ALLOWED_ATTRIBUTES[$tag] ?? [];
        $attributes = [];
        foreach ($element->attributes as $attribute) {
            $attributes[] = [$attribute->name, $attribute->value];
        }

        foreach ($attributes as [$name, $value]) {
            $lowerName = strtolower($name);
            if (str_starts_with($lowerName, 'on') || !in_array($lowerName, $allowed, true)) {
                throw new \InvalidArgumentException(
                    sprintf(
                        '%s contiene el atributo "%s" no permitido en <%s>.',
                        $column,
                        $name,
                        $tag
                    )
                );
            }

            if ($lowerName === 'href' && !$this->isSafeHref($value)) {
                throw new \InvalidArgumentException(
                    sprintf('%s contiene un enlace no permitido.', $column)
                );
            }
            if ($lowerName === 'target' && !in_array($value, ['_blank', '_self'], true)) {
                throw new \InvalidArgumentException(
                    sprintf('%s contiene un target de enlace no permitido.', $column)
                );
            }
            if (in_array($lowerName, ['colspan', 'rowspan'], true)
                && (!ctype_digit($value) || (int)$value < 1 || (int)$value > 100)
            ) {
                throw new \InvalidArgumentException(
                    sprintf('%s contiene una dimensión de tabla inválida.', $column)
                );
            }
        }

        if ($tag === 'a' && $element->getAttribute('target') === '_blank') {
            $element->setAttribute('rel', 'noopener noreferrer');
        }
    }

    private function wrapRootInlineContent(DOMDocument $document, DOMElement $root): void
    {
        $normalizedRoot = $document->createElement('div');
        $paragraph = null;
        $nodes = [];
        foreach ($root->childNodes as $child) {
            $nodes[] = $child;
        }

        foreach ($nodes as $node) {
            if ($node->nodeType === XML_TEXT_NODE) {
                $this->appendTextNode($document, $normalizedRoot, $paragraph, (string)$node->nodeValue);
                continue;
            }

            if ($node->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $tag = strtolower($node->nodeName);
            if (in_array($tag, self::BLOCK_TAGS, true)) {
                $paragraph = null;
                $normalizedRoot->appendChild($node->cloneNode(true));
                continue;
            }

            if (!$paragraph instanceof DOMElement) {
                $paragraph = $document->createElement('p');
                $normalizedRoot->appendChild($paragraph);
            }
            $paragraph->appendChild($node->cloneNode(true));
        }

        while ($root->firstChild) {
            $root->removeChild($root->firstChild);
        }
        while ($normalizedRoot->firstChild) {
            $root->appendChild($normalizedRoot->firstChild);
        }
    }

    private function appendTextNode(
        DOMDocument $document,
        DOMElement $root,
        ?DOMElement &$paragraph,
        string $value
    ): void {
        $value = str_replace(["\r\n", "\r"], "\n", $value);
        $chunks = preg_split('/\n[ \t]*\n+/u', $value);
        if ($chunks === false) {
            $chunks = [$value];
        }

        foreach ($chunks as $index => $chunk) {
            $hasLeadingSpace = preg_match('/^\s/u', $chunk) === 1;
            $hasTrailingSpace = preg_match('/\s$/u', $chunk) === 1;
            $text = preg_replace('/\s+/u', ' ', trim($chunk));
            if ($text !== null && $text !== '') {
                if (!$paragraph instanceof DOMElement) {
                    $paragraph = $document->createElement('p');
                    $root->appendChild($paragraph);
                }
                if ($hasLeadingSpace && $paragraph->hasChildNodes()) {
                    $paragraph->appendChild($document->createTextNode(' '));
                }
                $paragraph->appendChild($document->createTextNode($text));
                if ($hasTrailingSpace) {
                    $paragraph->appendChild($document->createTextNode(' '));
                }
            }

            if ($index < count($chunks) - 1) {
                $paragraph = null;
            }
        }
    }

    private function youtubeEmbed(string $url, string $title): string
    {
        $parts = parse_url($url);
        $scheme = strtolower((string)($parts['scheme'] ?? ''));
        $host = strtolower((string)($parts['host'] ?? ''));
        $path = (string)($parts['path'] ?? '');
        $allowedHosts = [
            'youtube.com',
            'www.youtube.com',
            'youtube-nocookie.com',
            'www.youtube-nocookie.com',
        ];

        if ($scheme !== 'https'
            || !in_array($host, $allowedHosts, true)
            || !preg_match('#^/embed/([A-Za-z0-9_-]{11})$#', $path, $matches)
            || isset($parts['user'])
            || isset($parts['pass'])
            || isset($parts['port'])
        ) {
            throw new \InvalidArgumentException(
                'Youtube debe ser una URL HTTPS de embed válida.'
            );
        }

        $embedHost = str_contains($host, 'nocookie')
            ? 'www.youtube-nocookie.com'
            : 'www.youtube.com';
        $src = 'https://' . $embedHost . '/embed/' . $matches[1];
        $safeTitle = htmlspecialchars(
            'Video: ' . ($title !== '' ? $title : 'contenido del artículo'),
            ENT_QUOTES | ENT_SUBSTITUTE,
            'UTF-8'
        );

        return '<div class="video-embed"><iframe src="' . $src
            . '" title="' . $safeTitle
            . '" loading="lazy" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"'
            . ' referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe></div>';
    }

    private function isSafeHref(string $href): bool
    {
        $href = trim($href);
        if ($href === '' || str_starts_with($href, '#') || str_starts_with($href, '/')) {
            return true;
        }
        if (preg_match('/[\x00-\x1F\x7F]/', $href)) {
            return false;
        }
        $scheme = strtolower((string)parse_url($href, PHP_URL_SCHEME));
        if ($scheme === '') {
            return !str_starts_with($href, '//');
        }
        return in_array($scheme, ['http', 'https', 'mailto'], true);
    }

    private function nextAvailableSlug(string $baseSlug, array $usedSlugs, int $maxLength): string
    {
        if (!isset($usedSlugs[$baseSlug])) {
            return $baseSlug;
        }

        $suffix = 2;
        do {
            $suffixText = '-' . $suffix;
            $prefixLength = $maxLength - strlen($suffixText);
            $candidate = rtrim(substr($baseSlug, 0, $prefixLength), '-') . $suffixText;
            $suffix++;
        } while (isset($usedSlugs[$candidate]));

        return $candidate;
    }

    private function isBlankRecord(array $values): bool
    {
        foreach ($values as $value) {
            if (trim((string)$value) !== '') {
                return false;
            }
        }
        return true;
    }

    private function headerMismatchMessage(array $headers): string
    {
        $expected = implode(', ', self::EXPECTED_HEADERS);
        $received = implode(', ', $headers);
        return 'Los encabezados no coinciden con la plantilla requerida. '
            . 'Esperados: ' . $expected . '. Recibidos: ' . $received . '.';
    }

    private function uploadErrorMessage(int $error): string
    {
        return match ($error) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE =>
                'El archivo excede el tamaño máximo permitido por el servidor.',
            UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente.',
            UPLOAD_ERR_NO_FILE => 'Selecciona un archivo CSV.',
            UPLOAD_ERR_NO_TMP_DIR => 'El servidor no tiene un directorio temporal disponible.',
            UPLOAD_ERR_CANT_WRITE => 'El servidor no pudo escribir el archivo temporal.',
            UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida.',
            default => 'No se pudo recibir el archivo CSV.',
        };
    }

    private function batchDirectory(): string
    {
        $directory = rtrim(sys_get_temp_dir(), '/\\')
            . DIRECTORY_SEPARATOR
            . 'consultoriaambiental_blog_imports';
        if (!is_dir($directory) && !mkdir($directory, 0700, true) && !is_dir($directory)) {
            throw new \RuntimeException('No se pudo crear el directorio temporal de importaciones.');
        }
        return $directory;
    }

    private function batchPath(string $token): string
    {
        return $this->batchDirectory()
            . DIRECTORY_SEPARATOR
            . hash('sha256', $token)
            . '.json';
    }
}
