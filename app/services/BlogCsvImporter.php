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

    public static function mappingFields(): array
    {
        return array_merge(self::EXPECTED_HEADERS, ['Contenido', 'Extracto', 'Meta Título', 'Identificador', 'Imagen destacada']);
    }

    /** Fields shown in the current post mapper; the legacy columns remain readable in saved templates. */
    public static function postMappingFields(): array
    {
        return ['Título', 'Contenido', 'Imagen destacada', 'Categoría', 'Extracto', 'Slug-Seo', 'Meta Título', 'Meta Descripción'];
    }

    /** Validate a saved mapping against its source column names without needing an uploaded file. */
    public static function validateMapping(array $headers, array $mapping): array
    {
        $tokenIndexes = array_flip(self::columnTokens($headers));
        $templates = [];
        foreach (self::mappingFields() as $field) {
            $template = $mapping[$field] ?? '';
            if (!is_string($template) || strlen($template) > 10000) {
                throw new \RuntimeException('El mapeo de ' . $field . ' es demasiado largo o inválido.');
            }
            preg_match_all('/\{[^{}]+\}/u', $template, $matches);
            foreach ($matches[0] as $reference) {
                if (!array_key_exists($reference, $tokenIndexes)) {
                    throw new \RuntimeException('El mapeo de ' . $field . ' usa una columna desconocida: ' . $reference . '.');
                }
            }
            $templates[$field] = $template;
        }
        if (trim($templates['Título']) === '') {
            throw new \RuntimeException('Asigna al menos una columna al campo Título.');
        }
        return $templates;
    }

    /** Make readable, unique placeholders from the source file's column headings. */
    public static function columnTokens(array $headers): array
    {
        $tokens = [];
        $used = [];
        foreach ($headers as $header) {
            $label = trim(str_replace(['{', '}'], '', (string)$header));
            $label = preg_replace('/\s+/u', ' ', $label) ?? $label;
            if ($label === '') {
                $label = 'Columna ' . (count($tokens) + 1);
            }
            $candidate = $label;
            $suffix = 2;
            while (isset($used[$candidate])) {
                $candidate = $label . ' (' . $suffix++ . ')';
            }
            $used[$candidate] = true;
            $tokens[] = '{' . $candidate . '}';
        }
        return $tokens;
    }

    /** Store a source file for the mapping step without trusting its later form fields. */
    public function prepareUploadedFile(array $file, int $adminId, ?int $profileId = null): array
    {
        $error = (int)($file['error'] ?? UPLOAD_ERR_NO_FILE);
        if ($error !== UPLOAD_ERR_OK) {
            throw new \RuntimeException($this->uploadErrorMessage($error));
        }
        $name = trim((string)($file['name'] ?? ''));
        $extension = strtolower(pathinfo($name, PATHINFO_EXTENSION));
        if (!in_array($extension, ['csv', 'xlsx'], true)) {
            throw new \RuntimeException('Selecciona un archivo .csv o .xlsx.');
        }
        $size = (int)($file['size'] ?? 0);
        if ($size <= 0 || $size > self::MAX_FILE_SIZE) {
            throw new \RuntimeException('El archivo debe contener datos y no superar 10 MB.');
        }
        $path = (string)($file['tmp_name'] ?? '');
        if ($path === '' || !is_file($path) || !is_readable($path)
            || (PHP_SAPI !== 'cli' && !is_uploaded_file($path))) {
            throw new \RuntimeException('No se pudo leer el archivo recibido.');
        }
        $actualSize = filesize($path);
        if ($actualSize === false || $actualSize <= 0 || $actualSize > self::MAX_FILE_SIZE) {
            throw new \RuntimeException('El archivo debe contener datos y no superar 10 MB.');
        }

        [$headers, $records] = $extension === 'xlsx'
            ? $this->readXlsx($path)
            : $this->readMappedCsv($path);
        if ($records === []) {
            throw new \RuntimeException('El archivo no contiene filas de datos.');
        }
        $token = bin2hex(random_bytes(32));
        $payload = [
            'admin_id' => $adminId,
            'expires_at' => time() + self::BATCH_TTL,
            'name' => basename($name),
            'profile_id' => $profileId,
            'headers' => $headers,
            'records' => $records,
        ];
        $this->writePayload($this->sourcePath($token), $payload);
        $_SESSION['blog_import_sources'][$token] = [
            'admin_id' => $adminId,
            'expires_at' => $payload['expires_at'],
        ];
        return $this->sourceSummary($token, $payload);
    }

    public function getPreparedSource(string $token, int $adminId): array
    {
        $payload = $this->readSourcePayload($token, $adminId);
        return $this->sourceSummary($token, $payload);
    }

    public function getPreparedSourceData(string $token, int $adminId): array
    {
        $payload = $this->readSourcePayload($token, $adminId);
        return [
            'name' => $payload['name'],
            'headers' => $payload['headers'],
            'records' => $payload['records'],
        ];
    }

    public function prepareStoredSource(array $snapshot, int $adminId, int $profileId): array
    {
        $headers = $snapshot['headers'] ?? null;
        $records = $snapshot['records'] ?? null;
        if (!is_array($headers) || $headers === []
            || !is_array($records) || $records === [] || count($records) > self::MAX_ROWS) {
            throw new \RuntimeException('El archivo guardado de esta plantilla no es válido.');
        }
        $token = bin2hex(random_bytes(32));
        $payload = [
            'admin_id' => $adminId,
            'expires_at' => time() + self::BATCH_TTL,
            'name' => (string)($snapshot['name'] ?? 'archivo-guardado.csv'),
            'profile_id' => $profileId,
            'headers' => $headers,
            'records' => $records,
            'reused' => true,
        ];
        $this->writePayload($this->sourcePath($token), $payload);
        $_SESSION['blog_import_sources'][$token] = [
            'admin_id' => $adminId,
            'expires_at' => $payload['expires_at'],
        ];
        return $this->sourceSummary($token, $payload);
    }

    public function previewMappedSource(
        string $token,
        int $adminId,
        array $mapping,
        array $existingSlugs = [],
        array $existingCategories = [],
        array $categoryOverrides = [],
        ?string $bulkCategory = null,
        array $preservedCategories = []
    ): array {
        $payload = $this->readSourcePayload($token, $adminId);
        $headers = $payload['headers'];
        $columnTokens = self::columnTokens($headers);
        $templates = self::validateMapping($headers, $mapping);

        $usedSlugs = array_fill_keys(array_map('strval', $existingSlugs), true);
        $categoryMap = [];
        foreach ($existingCategories as $category) {
            $slug = (string)($category['slug'] ?? '');
            if ($slug !== '') {
                $categoryMap[$slug] = $category;
            }
        }
        $rows = [];
        $usedSourceKeys = [];
        foreach ($payload['records'] as $record) {
            $replacements = [];
            foreach ($columnTokens as $index => $reference) {
                $replacements[$reference] = (string)($record['values'][$index] ?? '');
            }
            $source = [];
            foreach ($templates as $field => $template) {
                $source[$field] = strtr($template, $replacements);
            }
            $recordNumber = (int)$record['row_number'];
            if ($bulkCategory === null && !array_key_exists($recordNumber, $categoryOverrides)
                && $preservedCategories !== []) {
                $sourceKey = trim($source['Identificador']);
                if ($sourceKey === '') {
                    $slugSource = trim($source['Slug-Seo']);
                    $sourceKey = BlogPost::generateSlug($slugSource !== '' ? $slugSource : trim($source['Título']));
                }
                $sourceKeyHash = hash('sha256', $sourceKey);
                if (array_key_exists($sourceKeyHash, $preservedCategories)) {
                    $source['Categoría'] = (string)$preservedCategories[$sourceKeyHash];
                }
            }
            if ($bulkCategory !== null || array_key_exists($recordNumber, $categoryOverrides)) {
                $categoryName = $bulkCategory ?? $categoryOverrides[$recordNumber];
                if (!is_string($categoryName) || strlen($categoryName) > 2000) {
                    throw new \RuntimeException('La categoría de la fila ' . $recordNumber . ' es inválida.');
                }
                $categoryName = trim($categoryName);
                $categorySlug = $categoryName !== '' ? BlogCategory::generateSlug($categoryName) : '';
                $source['Categoría'] = isset($categoryMap[$categorySlug])
                    ? (string)$categoryMap[$categorySlug]['name']
                    : $categoryName;
            }
            $row = $this->normalizeRow($source, $recordNumber, $usedSlugs, $categoryMap);
            $sourceKey = (string)($row['data']['source_key'] ?? '');
            if ($sourceKey !== '') {
                if (isset($usedSourceKeys[$sourceKey])) {
                    $row['errors'][] = 'El identificador de importación se repite en otra fila. Asigna una columna única.';
                    $row['data'] = null;
                } else {
                    $usedSourceKeys[$sourceKey] = true;
                }
            }
            $rows[] = $row;
            if ($row['errors'] === []) {
                $usedSlugs[$row['slug']] = true;
            }
        }
        $errorCount = array_sum(array_map(static fn(array $row): int => count($row['errors']), $rows));
        $warningCount = array_sum(array_map(static fn(array $row): int => count($row['warnings']), $rows));
        return [
            'headers' => $headers,
            'rows' => $rows,
            'total_rows' => count($rows),
            'valid_rows' => count($rows) - count(array_filter($rows, static fn(array $row): bool => $row['errors'] !== [])),
            'error_count' => $errorCount,
            'warning_count' => $warningCount,
            'can_import' => $errorCount === 0,
        ];
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

    public function createBatch(array $preview, int $adminId, array $context = []): string
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
            'context' => $context,
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
        return $this->consumeBatchPayload($token, $adminId)['rows'];
    }

    public function consumeBatchPayload(string $token, int $adminId): array
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

        return $payload;
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
        foreach ((array)($_SESSION['blog_import_sources'] ?? []) as $token => $entry) {
            if (!is_array($entry) || (int)($entry['expires_at'] ?? 0) < $now) {
                if (preg_match('/^[a-f0-9]{64}$/', (string)$token)) {
                    @unlink($this->sourcePath((string)$token));
                }
                unset($_SESSION['blog_import_sources'][$token]);
            }
        }
    }

    private function readMappedCsv(string $path): array
    {
        $raw = file_get_contents($path);
        if ($raw === false || !mb_check_encoding($raw, 'UTF-8')) {
            throw new \RuntimeException('El CSV debe estar codificado en UTF-8.');
        }
        $firstLine = strtok($raw, "\r\n") ?: '';
        $delimiter = ',';
        $max = -1;
        foreach ([',', ';', "\t"] as $candidate) {
            $count = count(str_getcsv($firstLine, $candidate, '"', ''));
            if ($count > $max) {
                $max = $count;
                $delimiter = $candidate;
            }
        }
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            throw new \RuntimeException('No se pudo abrir el CSV.');
        }
        try {
            $headers = fgetcsv($handle, null, $delimiter, '"', '');
            if (!is_array($headers)) {
                throw new \RuntimeException('El archivo no contiene encabezados.');
            }
            $headers[0] = preg_replace('/^\xEF\xBB\xBF/', '', (string)$headers[0]);
            $headers = $this->validateSourceHeaders($headers);
            $records = [];
            $rowNumber = 1;
            while (($values = fgetcsv($handle, null, $delimiter, '"', '')) !== false) {
                $rowNumber++;
                if ($this->isBlankRecord($values)) {
                    continue;
                }
                if (count($records) >= self::MAX_ROWS) {
                    throw new \RuntimeException('El archivo excede el máximo de 500 filas.');
                }
                if (count($values) > count($headers)) {
                    throw new \RuntimeException('La fila ' . $rowNumber . ' tiene más columnas que el encabezado.');
                }
                $records[] = [
                    'row_number' => $rowNumber,
                    'values' => array_pad(array_map('strval', $values), count($headers), ''),
                ];
            }
        } finally {
            fclose($handle);
        }
        return [$headers, $records];
    }

    private function readXlsx(string $path): array
    {
        if (!class_exists(\ZipArchive::class)) {
            throw new \RuntimeException('El servidor necesita la extensión ZIP para leer .xlsx.');
        }
        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            throw new \RuntimeException('El archivo .xlsx no es válido.');
        }
        try {
            $totalSize = 0;
            for ($index = 0; $index < $zip->numFiles; $index++) {
                $stat = $zip->statIndex($index);
                $totalSize += (int)($stat['size'] ?? 0);
                if ($totalSize > 50 * 1024 * 1024) {
                    throw new \RuntimeException('El archivo .xlsx descomprimido excede 50 MB.');
                }
            }
            $workbook = $this->xlsxXml($zip, 'xl/workbook.xml');
            $relations = $this->xlsxXml($zip, 'xl/_rels/workbook.xml.rels');
            $workbook->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $sheet = ($workbook->xpath('//x:sheets/x:sheet') ?: [])[0] ?? null;
            if (!$sheet instanceof \SimpleXMLElement) {
                throw new \RuntimeException('El Excel no contiene hojas.');
            }
            $relationId = (string)$sheet->attributes('http://schemas.openxmlformats.org/officeDocument/2006/relationships')['id'];
            $target = '';
            foreach ($relations->children('http://schemas.openxmlformats.org/package/2006/relationships') as $relation) {
                $attributes = $relation->attributes();
                if ((string)$attributes['Id'] === $relationId) {
                    $target = (string)$attributes['Target'];
                    break;
                }
            }
            $sheetPath = str_starts_with($target, '/')
                ? ltrim($target, '/')
                : 'xl/' . ltrim($target, '/');
            if ($target === '' || str_contains($sheetPath, '..')) {
                throw new \RuntimeException('No se pudo ubicar la primera hoja del Excel.');
            }
            $shared = [];
            if ($zip->locateName('xl/sharedStrings.xml') !== false) {
                $strings = $this->xlsxXml($zip, 'xl/sharedStrings.xml');
                $strings->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                foreach ($strings->xpath('//x:si') ?: [] as $item) {
                    $item->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
                    $shared[] = implode('', array_map('strval', $item->xpath('.//x:t') ?: []));
                }
            }
            $worksheet = $this->xlsxXml($zip, $sheetPath);
            $worksheet->registerXPathNamespace('x', 'http://schemas.openxmlformats.org/spreadsheetml/2006/main');
            $grid = [];
            foreach ($worksheet->xpath('//x:sheetData/x:row') ?: [] as $row) {
                $number = (int)$row->attributes()['r'];
                if ($number < 1 || $number > 1000000) {
                    throw new \RuntimeException('La hoja contiene un número de fila inválido.');
                }
                $cells = [];
                foreach ($row->children('http://schemas.openxmlformats.org/spreadsheetml/2006/main') as $cell) {
                    $cellAttributes = $cell->attributes();
                    if (!preg_match('/^([A-Z]+)\d+$/i', (string)$cellAttributes['r'], $match)) {
                        continue;
                    }
                    $column = 0;
                    foreach (str_split(strtoupper($match[1])) as $letter) {
                        $column = $column * 26 + ord($letter) - 64;
                    }
                    $column--;
                    if ($column >= 200) {
                        throw new \RuntimeException('La hoja excede el máximo de 200 columnas.');
                    }
                    $type = (string)$cellAttributes['t'];
                    $value = $type === 'inlineStr'
                        ? implode('', array_map('strval', $cell->xpath('.//*[local-name()="t"]') ?: []))
                        : (string)(($cell->xpath('./*[local-name()="v"]') ?: [])[0] ?? '');
                    $cells[$column] = $type === 's' ? (string)($shared[(int)$value] ?? '') : $value;
                }
                if ($this->isBlankRecord($cells)) {
                    continue;
                }
                $grid[$number] = $cells;
                if (count($grid) > self::MAX_ROWS + 1) {
                    throw new \RuntimeException('El archivo excede el máximo de 500 filas.');
                }
            }
            if ($grid === []) {
                throw new \RuntimeException('La primera hoja está vacía.');
            }
            ksort($grid);
            $headerRow = (int)array_key_first($grid);
            $columnCount = max(array_keys($grid[$headerRow] ?: [0 => ''])) + 1;
            $headers = $this->validateSourceHeaders(array_replace(array_fill(0, $columnCount, ''), $grid[$headerRow]));
            $records = [];
            foreach ($grid as $number => $cells) {
                if ($number === $headerRow || $this->isBlankRecord($cells)) {
                    continue;
                }
                if (count($records) >= self::MAX_ROWS) {
                    throw new \RuntimeException('El archivo excede el máximo de 500 filas.');
                }
                $records[] = [
                    'row_number' => $number,
                    'values' => array_replace(array_fill(0, count($headers), ''), $cells),
                ];
            }
            return [$headers, $records];
        } finally {
            $zip->close();
        }
    }

    private function xlsxXml(\ZipArchive $zip, string $name): \SimpleXMLElement
    {
        $xml = $zip->getFromName($name);
        if ($xml === false || strlen($xml) > 25 * 1024 * 1024) {
            throw new \RuntimeException('No se pudo leer ' . $name . ' del archivo Excel.');
        }
        $previous = libxml_use_internal_errors(true);
        try {
            $parsed = simplexml_load_string($xml, \SimpleXMLElement::class, LIBXML_NONET);
        } finally {
            libxml_clear_errors();
            libxml_use_internal_errors($previous);
        }
        if (!$parsed instanceof \SimpleXMLElement) {
            throw new \RuntimeException('El archivo Excel contiene XML inválido.');
        }
        return $parsed;
    }

    private function validateSourceHeaders(array $headers): array
    {
        if ($headers === [] || count($headers) > 200) {
            throw new \RuntimeException('El archivo debe tener entre 1 y 200 columnas.');
        }
        $headers = array_map(static fn($value): string => trim((string)$value), $headers);
        foreach ($headers as $index => &$header) {
            if ($header === '') {
                $header = 'Columna ' . ($index + 1);
            }
            if (mb_strlen($header, 'UTF-8') > 150) {
                throw new \RuntimeException('Un encabezado excede 150 caracteres.');
            }
        }
        return $headers;
    }

    private function readSourcePayload(string $token, int $adminId): array
    {
        $entry = $_SESSION['blog_import_sources'][$token] ?? null;
        if (!preg_match('/^[a-f0-9]{64}$/', $token) || !is_array($entry)
            || (int)($entry['admin_id'] ?? 0) !== $adminId
            || (int)($entry['expires_at'] ?? 0) < time()) {
            throw new \RuntimeException('El archivo temporal expiró. Vuelve a subirlo.');
        }
        $raw = file_get_contents($this->sourcePath($token));
        $payload = $raw === false ? null : json_decode($raw, true);
        if (!is_array($payload) || (int)($payload['admin_id'] ?? 0) !== $adminId
            || (int)($payload['expires_at'] ?? 0) < time()
            || !is_array($payload['headers'] ?? null)
            || !is_array($payload['records'] ?? null)) {
            throw new \RuntimeException('No se encontró el archivo temporal. Vuelve a subirlo.');
        }
        return $payload;
    }

    private function sourceSummary(string $token, array $payload): array
    {
        return [
            'token' => $token,
            'name' => $payload['name'],
            'profile_id' => isset($payload['profile_id']) ? (int)$payload['profile_id'] : null,
            'reused' => !empty($payload['reused']),
            'headers' => $payload['headers'],
            'column_tokens' => self::columnTokens($payload['headers']),
            'sample' => array_map(
                static fn($value): string => mb_strimwidth((string)$value, 0, 500, '…', 'UTF-8'),
                $payload['records'][0]['values'] ?? []
            ),
            'total_rows' => count($payload['records']),
        ];
    }

    private function sourcePath(string $token): string
    {
        return $this->batchDirectory() . DIRECTORY_SEPARATOR . hash('sha256', 'source-' . $token) . '.json';
    }

    private function writePayload(string $path, array $payload): void
    {
        $encoded = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        if (file_put_contents($path, $encoded, LOCK_EX) === false) {
            throw new \RuntimeException('No se pudo guardar el archivo temporal.');
        }
        @chmod($path, 0600);
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
        $metaTitle = trim((string)($source['Meta Título'] ?? ''));
        $excerpt = trim((string)($source['Extracto'] ?? ''));
        $featuredImage = trim((string)($source['Imagen destacada'] ?? ''));
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
        if (mb_strlen($metaTitle, 'UTF-8') > 255) {
            $errors[] = 'El meta título excede 255 caracteres.';
        }
        if (strlen($excerpt) > 65535) {
            $errors[] = 'El extracto excede la capacidad del campo TEXT.';
        }
        if ($featuredImage !== '') {
            if (preg_match('~^https?://~i', $featuredImage)) {
                if (filter_var($featuredImage, FILTER_VALIDATE_URL) === false) {
                    $errors[] = 'La URL de la imagen destacada no es válida.';
                }
            } elseif (preg_match('~^/?uploads/[A-Za-z0-9_./%+\-]+$~', $featuredImage)
                && !str_contains($featuredImage, '..')) {
                $featuredImage = \asset_url(ltrim($featuredImage, '/'));
            } else {
                $errors[] = 'La imagen destacada debe ser una URL http(s) o una ruta de uploads.';
            }
            if (mb_strlen($featuredImage, 'UTF-8') > 255) {
                $errors[] = 'La URL de la imagen destacada excede 255 caracteres.';
            }
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
        $sourceKey = trim((string)($source['Identificador'] ?? ''));
        if ($sourceKey === '') {
            $sourceKey = $baseSlug;
        }
        if (mb_strlen($sourceKey, 'UTF-8') > 255) {
            $errors[] = 'El identificador de importación excede 255 caracteres.';
        }
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
            $directContent = trim((string)($source['Contenido'] ?? ''));
            if ($directContent !== '') {
                $parts[] = $this->normalizeHtmlFragment($directContent, 'Contenido');
            }
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
                'source_key' => $sourceKey,
                'featured_image' => $featuredImage,
                'excerpt' => $excerpt !== '' ? $excerpt : $metaDescription,
                'content' => $content,
                'meta_title' => $metaTitle,
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
            UPLOAD_ERR_NO_FILE => 'Selecciona un archivo CSV o Excel.',
            UPLOAD_ERR_NO_TMP_DIR => 'El servidor no tiene un directorio temporal disponible.',
            UPLOAD_ERR_CANT_WRITE => 'El servidor no pudo escribir el archivo temporal.',
            UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida.',
            default => 'No se pudo recibir el archivo.',
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
