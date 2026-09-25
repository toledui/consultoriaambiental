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
            $this->testMappedCsvAndSourceOwnership();
            $this->testMappedXlsx();
            $this->testCategoryOverridesInPreview();
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

    private function testMappedCsvAndSourceOwnership(): void
    {
        $path = $this->writeRawCsv(
            ['Body', 'Heading', 'Group', 'Extra', 'Image'],
            [['Texto principal', 'Artículo flexible', 'Noticias', 'detalle', 'https://example.com/imagen.webp']]
        );
        $source = $this->importer->prepareUploadedFile([
            'error' => UPLOAD_ERR_OK,
            'name' => 'columnas.csv',
            'size' => filesize($path),
            'tmp_name' => $path,
        ], 10);
        $storedSource = $this->importer->prepareStoredSource($this->importer->getPreparedSourceData($source['token'], 10), 10, 4);
        $this->assertTrue($storedSource['reused'], 'Debe poder preparar una nueva corrida desde las filas guardadas.');
        $this->assertSame($source['headers'], $storedSource['headers'], 'La nueva corrida debe conservar las columnas de la plantilla.');
        $this->assertSame(['Body', 'Heading', 'Group', 'Extra', 'Image'], $source['headers'], 'Debe aceptar encabezados libres.');
        $this->assertSame(['{Body}', '{Heading}', '{Group}', '{Extra}', '{Image}'], $source['column_tokens'], 'Debe mostrar los encabezados en los marcadores.');
        $this->assertSame(
            ['{Título}', '{Título (2)}', '{Meta Descripción}'],
            BlogCsvImporter::columnTokens(['Título', 'Título', 'Meta Descripción']),
            'Los encabezados duplicados deben tener marcadores únicos.'
        );
        $denied = false;
        try {
            $this->importer->getPreparedSource($source['token'], 11);
        } catch (RuntimeException) {
            $denied = true;
        }
        $this->assertTrue($denied, 'Otro administrador no debe leer el origen temporal.');

        $preview = $this->importer->previewMappedSource($source['token'], 10, [
            'Título' => '{Heading}',
            'Contenido' => '<h2>{Heading}</h2>{Body} y {Extra}',
            'Extracto' => 'Resumen: {Extra}',
            'Meta Título' => 'SEO: {Heading}',
            'Categoría' => '{Group}',
            'Imagen destacada' => '{Image}',
        ]);
        $this->assertTrue($preview['can_import'], 'El mapeo debe producir un post válido.');
        $this->assertSame('Artículo flexible', $preview['rows'][0]['title'], 'Debe tomar la columna elegida.');
        $this->assertContains('<p>Texto principal y detalle</p>', $preview['rows'][0]['content']);
        $this->assertSame('Resumen: detalle', $preview['rows'][0]['data']['excerpt'], 'Debe combinar texto fijo en el extracto.');
        $this->assertSame('SEO: Artículo flexible', $preview['rows'][0]['data']['meta_title'], 'Debe mapear el meta título.');
        $this->assertSame('Noticias', $preview['rows'][0]['category_name'], 'Debe mapear categoría.');
        $this->assertSame('https://example.com/imagen.webp', $preview['rows'][0]['data']['featured_image'], 'Debe mapear la imagen destacada.');
        $savedMapping = BlogCsvImporter::validateMapping($source['headers'], [
            'Título' => '{Heading}',
            'Contenido' => '<h2>{Heading}</h2>{Body}',
        ]);
        $this->assertSame('<h2>{Heading}</h2>{Body}', $savedMapping['Contenido'], 'Debe validar una plantilla sin volver a subir el archivo.');
        $invalidSavedMapping = false;
        try {
            BlogCsvImporter::validateMapping($source['headers'], ['Título' => '{Columna inexistente}']);
        } catch (RuntimeException) {
            $invalidSavedMapping = true;
        }
        $this->assertTrue($invalidSavedMapping, 'No debe guardarse una plantilla con una columna desconocida.');

        $invalidImage = $this->importer->previewMappedSource($source['token'], 10, [
            'Título' => '{Heading}',
            'Contenido' => '{Body}',
            'Imagen destacada' => 'javascript:alert(1)',
        ]);
        $this->assertFalse($invalidImage['can_import'], 'Debe rechazar una imagen con esquema no permitido.');

        $invalid = false;
        try {
            $this->importer->previewMappedSource($source['token'], 10, ['Título' => '{No existe}']);
        } catch (RuntimeException) {
            $invalid = true;
        }
        $this->assertTrue($invalid, 'Debe rechazar encabezados inexistentes.');
    }

    private function testMappedXlsx(): void
    {
        $path = tempnam(sys_get_temp_dir(), 'blog_xlsx_test_');
        if ($path === false) {
            throw new RuntimeException('No se pudo crear el Excel de prueba.');
        }
        $this->temporaryFiles[] = $path;
        $zip = new ZipArchive();
        if ($zip->open($path, ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('No se pudo abrir el Excel de prueba.');
        }
        $zip->addFromString('xl/workbook.xml', '<?xml version="1.0"?><workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main" xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"><sheets><sheet name="Artículos" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', '<?xml version="1.0"?><Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Target="worksheets/sheet1.xml" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/worksheet"/></Relationships>');
        $zip->addFromString('xl/sharedStrings.xml', '<?xml version="1.0"?><sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><si><t>Nombre</t></si><si><t>Texto</t></si></sst>');
        $zip->addFromString('xl/worksheets/sheet1.xml', '<?xml version="1.0"?><worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData><row r="1"><c r="A1" t="s"><v>0</v></c><c r="B1" t="s"><v>1</v></c></row><row r="2"><c r="A2" t="inlineStr"><is><t>Artículo Excel</t></is></c><c r="B2" t="inlineStr"><is><t>Contenido de hoja</t></is></c></row></sheetData></worksheet>');
        $zip->close();

        $source = $this->importer->prepareUploadedFile([
            'error' => UPLOAD_ERR_OK,
            'name' => 'articulos.xlsx',
            'size' => filesize($path),
            'tmp_name' => $path,
        ], 10);
        $this->assertSame(['Nombre', 'Texto'], $source['headers'], 'Debe leer los encabezados del Excel.');
        $preview = $this->importer->previewMappedSource($source['token'], 10, [
            'Título' => '{Nombre}',
            'Introducción' => '{Texto}',
        ]);
        $this->assertTrue($preview['can_import'], 'Debe importar la primera hoja de Excel.');
        $this->assertSame('Artículo Excel', $preview['rows'][0]['title'], 'Debe leer celdas inline del Excel.');
    }

    private function testCategoryOverridesInPreview(): void
    {
        $badCategory = 'No puedo determinar la categoría correcta porque la lista de categorías posibles que proporcionaste está vacía.';
        $path = $this->writeRawCsv(
            ['Nombre', 'Texto', 'Grupo'],
            [
                ['Artículo uno', 'Texto uno', $badCategory],
                ['Artículo dos', 'Texto dos', 'Otra propuesta'],
            ]
        );
        $source = $this->importer->prepareUploadedFile([
            'error' => UPLOAD_ERR_OK,
            'name' => 'categorias.csv',
            'size' => filesize($path),
            'tmp_name' => $path,
        ], 10);
        $mapping = ['Título' => '{Nombre}', 'Contenido' => '{Texto}', 'Categoría' => '{Grupo}'];
        $existing = [['id' => 7, 'name' => 'Noticias', 'slug' => 'noticias']];

        $original = $this->importer->previewMappedSource($source['token'], 10, $mapping, [], $existing);
        $this->assertFalse($original['can_import'], 'Una categoría larga del archivo debe bloquear la fila.');

        $preserved = [
            hash('sha256', 'articulo-uno') => 'Noticias',
            hash('sha256', 'articulo-dos') => 'Noticias',
        ];
        $reused = $this->importer->previewMappedSource($source['token'], 10, $mapping, [], $existing, [], null, $preserved);
        $this->assertTrue($reused['can_import'], 'Repetir el archivo debe conservar las categorías corregidas anteriormente.');
        $this->assertSame('Noticias', $reused['rows'][0]['category_name'], 'La categoría previa debe sustituir el texto inválido del archivo guardado.');
        $reusedOverride = $this->importer->previewMappedSource($source['token'], 10, $mapping, [], $existing, [2 => 'Nueva sección'], null, $preserved);
        $this->assertSame('Nueva sección', $reusedOverride['rows'][0]['category_name'], 'Una corrección manual debe prevalecer sobre la categoría conservada.');

        $fixed = $this->importer->previewMappedSource($source['token'], 10, $mapping, [], $existing, [
            2 => 'noticias',
            3 => 'Nueva sección',
        ]);
        $this->assertTrue($fixed['can_import'], 'Debe validar con categorías corregidas por post.');
        $this->assertSame('Noticias', $fixed['rows'][0]['category_name'], 'Debe usar el nombre canónico de una categoría existente.');
        $this->assertFalse($fixed['rows'][0]['category_is_new'], 'La categoría existente no debe crearse otra vez.');
        $this->assertTrue($fixed['rows'][1]['category_is_new'], 'Una categoría escrita debe marcarse como nueva.');
        $this->assertSame('Nueva sección', $fixed['rows'][1]['data']['category_name'], 'El lote debe conservar la elección individual.');

        $bulk = $this->importer->previewMappedSource($source['token'], 10, $mapping, [], $existing, [2 => 'Otra'], 'Noticias');
        $this->assertTrue($bulk['can_import'], 'La asignación masiva debe corregir todo el lote.');
        $this->assertSame('Noticias', $bulk['rows'][0]['category_name'], 'La asignación masiva debe prevalecer.');
        $this->assertSame('Noticias', $bulk['rows'][1]['category_name'], 'La asignación masiva debe alcanzar cada post.');

        $none = $this->importer->previewMappedSource($source['token'], 10, $mapping, [], $existing, [], '');
        $this->assertTrue($none['can_import'], 'Debe poder importar todos los posts sin categoría.');
        $this->assertSame('', $none['rows'][0]['category_name'], 'La categoría vacía debe quitar la asignación.');
    }

    private function testBatchOwnershipAndSingleUse(): void
    {
        $preview = $this->importer->parseFile($this->writeCsv([$this->baseRow()]));
        $_SESSION['blog_import_batches'] = [];
        unset($_SESSION['blog_import_csrf']);

        $csrf = $this->importer->csrfToken();
        $this->assertTrue($this->importer->verifyCsrfToken($csrf), 'El token CSRF debe validarse.');
        $this->assertFalse($this->importer->verifyCsrfToken('incorrecto'), 'Un CSRF distinto debe fallar.');

        $token = $this->importer->createBatch($preview, 10, ['profile_id' => 4]);
        $wrongOwnerRejected = false;
        try {
            $this->importer->consumeBatch($token, 11);
        } catch (RuntimeException) {
            $wrongOwnerRejected = true;
        }
        $this->assertTrue($wrongOwnerRejected, 'Otro administrador no debe consumir el lote.');

        $batch = $this->importer->consumeBatchPayload($token, 10);
        $this->assertSame(4, $batch['context']['profile_id'], 'El lote debe conservar la importación guardada.');
        $rows = $batch['rows'];
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
