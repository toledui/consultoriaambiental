<?php

require dirname(__DIR__) . '/config/init.php';

use App\Services\BlogCsvImporter;

final class BlogCsvImporterTest
{
    private BlogCsvImporter $importer;
    private int $assertions = 0;
    private array $temporaryFiles = [];

    public function __construct()
    {
        $this->importer = new BlogCsvImporter();
    }

    public function run(?string $realCsvPath): void
    {
        try {
            $this->testValidCsvAndHtmlNormalization();
            $this->testHeaderValidation();
            $this->testDangerousHtmlAndUnsupportedColumns();
            $this->testBatchOwnershipAndSingleUse();

            if ($realCsvPath !== null) {
                $this->testProvidedCsv($realCsvPath);
            }

            echo "OK ({$this->assertions} assertions)\n";
        } finally {
            foreach ($this->temporaryFiles as $path) {
                if (is_file($path)) {
                    unlink($path);
                }
            }
        }
    }

    private function testValidCsvAndHtmlNormalization(): void
    {
        $row = $this->baseRow();
        $row['Meta Descripción'] = str_repeat('Descripción extensa. ', 10);
        $row['Principal H2'] = "<h2>Sección principal</h2>\n\nTexto con <strong>énfasis</strong> y acentos.\n\n"
            . '<table><thead><tr><th>Campo</th></tr></thead><tbody><tr><td>Valor</td></tr></tbody></table>';
        $row['Shortcode'] = "\n";

        $path = $this->writeCsv([$row]);
        $preview = $this->importer->parseFile(
            $path,
            ['guia-de-prueba'],
            []
        );
        $parsed = $preview['rows'][0];

        $this->assertSame(1, $preview['total_rows'], 'Debe leer una fila.');
        $this->assertSame(1, $preview['valid_rows'], 'La fila debe ser válida.');
        $this->assertTrue($preview['can_import'], 'El lote debe poder importarse.');
        $this->assertSame('guia-de-prueba-2', $parsed['slug'], 'Debe agregar sufijo determinista.');
        $this->assertTrue($parsed['category_is_new'], 'La categoría debe marcarse como nueva.');
        $this->assertContains('<p>Introducción con acentos y comas, correctamente.</p>', $parsed['content']);
        $this->assertContains('<div class="video-embed"><iframe', $parsed['content']);
        $this->assertContains('<p>Texto con <strong>énfasis</strong> y acentos.</p>', $parsed['content']);
        $this->assertContains('<table>', $parsed['content']);
        $this->assertTrue(count($parsed['warnings']) >= 2, 'Debe advertir por meta extensa y slug ajustado.');
    }

    private function testHeaderValidation(): void
    {
        $headers = BlogCsvImporter::expectedHeaders();
        [$headers[0], $headers[1]] = [$headers[1], $headers[0]];
        $path = $this->writeRawCsv($headers, [array_values($this->baseRow())]);

        $thrown = false;
        try {
            $this->importer->parseFile($path);
        } catch (RuntimeException $e) {
            $thrown = str_contains($e->getMessage(), 'encabezados');
        }

        $this->assertTrue($thrown, 'Debe rechazar encabezados reordenados.');
    }

    private function testDangerousHtmlAndUnsupportedColumns(): void
    {
        $dangerous = $this->baseRow();
        $dangerous['Principal H2'] = '<h2>Contenido</h2><script>alert(1)</script>';
        $unsupported = $this->baseRow();
        $unsupported['Título'] = 'Otra guía';
        $unsupported['Shortcode'] = '[galeria]';
        $unsupported['Youtube'] = 'http://www.youtube.com/embed/0syjh6AIYgE';

        $path = $this->writeCsv([$dangerous, $unsupported]);
        $preview = $this->importer->parseFile($path);

        $this->assertFalse($preview['can_import'], 'Un error debe bloquear todo el lote.');
        $this->assertTrue($preview['error_count'] >= 3, 'Debe reportar HTML, shortcode y video inválidos.');
        $this->assertContains('etiqueta HTML no permitida', implode(' ', $preview['rows'][0]['errors']));
        $this->assertContains('Shortcode debe estar vacío', implode(' ', $preview['rows'][1]['errors']));
        $this->assertContains('URL HTTPS de embed válida', implode(' ', $preview['rows'][1]['errors']));
    }

    private function testBatchOwnershipAndSingleUse(): void
    {
        $preview = $this->importer->parseFile($this->writeCsv([$this->baseRow()]));
        $_SESSION['blog_import_batches'] = [];
        unset($_SESSION['blog_import_csrf']);

        $csrf = $this->importer->csrfToken();
        $this->assertTrue($this->importer->verifyCsrfToken($csrf), 'El token CSRF debe validarse.');
        $this->assertFalse($this->importer->verifyCsrfToken('incorrecto'), 'Un CSRF distinto debe fallar.');

        $token = $this->importer->createBatch($preview, 10);
        $wrongOwnerRejected = false;
        try {
            $this->importer->consumeBatch($token, 11);
        } catch (RuntimeException) {
            $wrongOwnerRejected = true;
        }
        $this->assertTrue($wrongOwnerRejected, 'Otro administrador no debe consumir el lote.');

        $rows = $this->importer->consumeBatch($token, 10);
        $this->assertSame(1, count($rows), 'El dueño debe consumir el lote.');

        $replayRejected = false;
        try {
            $this->importer->consumeBatch($token, 10);
        } catch (RuntimeException) {
            $replayRejected = true;
        }
        $this->assertTrue($replayRejected, 'El token debe ser de un solo uso.');
    }

    private function testProvidedCsv(string $path): void
    {
        $this->assertTrue(is_file($path), 'El CSV adjunto debe existir.');
        $preview = $this->importer->parseFile($path);

        $videos = 0;
        $categories = [];
        $longMetaDescriptions = 0;
        foreach ($preview['rows'] as $row) {
            $videos += substr_count($row['content'], '<iframe');
            if ($row['category_name'] !== '') {
                $categories[$row['category_name']] = true;
            }
            if (mb_strlen($row['data']['meta_description'], 'UTF-8') > 160) {
                $longMetaDescriptions++;
            }
        }

        $this->assertSame(20, $preview['total_rows'], 'Guías.csv debe contener 20 filas.');
        $this->assertSame(20, $preview['valid_rows'], 'Las 20 filas adjuntas deben ser válidas.');
        $this->assertSame(0, $preview['error_count'], 'Guías.csv no debe producir errores.');
        $this->assertSame(19, $videos, 'Deben generarse 19 embeds de YouTube.');
        $this->assertSame(11, count($categories), 'Deben detectarse 11 categorías distintas.');
        $this->assertSame(20, $longMetaDescriptions, 'Las metas extensas deben conservarse.');
        $this->assertTrue(
            str_starts_with($preview['rows'][0]['content'], '<p>'),
            'La introducción debe abrir el contenido como párrafo.'
        );
    }

    private function baseRow(): array
    {
        return [
            'Título' => 'Guía de prueba',
            'Meta Descripción' => 'Resumen para buscadores.',
            'Introducción' => 'Introducción con acentos y comas, correctamente.',
            'Youtube' => 'https://www.youtube.com/embed/0syjh6AIYgE',
            'Principal H2' => '<h2>Contenido principal</h2>Texto principal.',
            'PAA H2' => '',
            'Respaldo H2' => '<h2>Contenido de respaldo</h2><p>Respaldo.</p>',
            'FAQ' => '<h2>Preguntas frecuentes</h2>',
            'Respuestas' => '<h3>¿Pregunta?</h3><p>Respuesta.</p>',
            'Categoría' => 'Categoría de prueba',
            'Shortcode' => '',
            'Directorio' => '',
            'Slug-Seo' => '',
            'Amazon-Resenas' => '',
        ];
    }

    private function writeCsv(array $rows): string
    {
        return $this->writeRawCsv(
            BlogCsvImporter::expectedHeaders(),
            array_map('array_values', $rows)
        );
    }

    private function writeRawCsv(array $headers, array $rows): string
    {
        $path = tempnam(sys_get_temp_dir(), 'blog_csv_test_');
        if ($path === false) {
            throw new RuntimeException('No se pudo crear el CSV de prueba.');
        }
        $this->temporaryFiles[] = $path;

        $handle = fopen($path, 'wb');
        if ($handle === false) {
            throw new RuntimeException('No se pudo abrir el CSV de prueba.');
        }
        fputcsv($handle, $headers, ',', '"', '');
        foreach ($rows as $row) {
            fputcsv($handle, $row, ',', '"', '');
        }
        fclose($handle);

        return $path;
    }

    private function assertTrue(bool $condition, string $message): void
    {
        $this->assertions++;
        if (!$condition) {
            throw new RuntimeException($message);
        }
    }

    private function assertFalse(bool $condition, string $message): void
    {
        $this->assertTrue(!$condition, $message);
    }

    private function assertSame(mixed $expected, mixed $actual, string $message): void
    {
        $this->assertions++;
        if ($expected !== $actual) {
            throw new RuntimeException(
                $message . ' Esperado: ' . var_export($expected, true)
                . '; recibido: ' . var_export($actual, true)
            );
        }
    }

    private function assertContains(string $needle, string $haystack, string $message = ''): void
    {
        $this->assertions++;
        if (!str_contains($haystack, $needle)) {
            throw new RuntimeException(
                ($message !== '' ? $message . ' ' : '')
                . 'No se encontró: ' . $needle
            );
        }
    }
}

$realCsvPath = $argv[1] ?? null;
(new BlogCsvImporterTest())->run($realCsvPath);
