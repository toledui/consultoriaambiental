<?php

require dirname(__DIR__) . '/config/init.php';

use App\Core\Model;
use App\Models\BlogImportProfile;
use App\Models\BlogPost;
use App\Services\BlogCsvImporter;

if (getenv('RUN_DB_INTEGRATION') !== '1') {
    echo "SKIP (set RUN_DB_INTEGRATION=1 to run against a temporary MySQL database)\n";
    exit(0);
}

$config = require CONFIG_DIR . '/database.php';
$dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $config['host'], $config['port'], $config['dbname']);
$adminPdo = new PDO($dsn, $config['username'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$testDatabase = 'blog_import_test_' . bin2hex(random_bytes(5));
$quotedSource = '`' . str_replace('`', '``', $config['dbname']) . '`';
$quotedTest = '`' . $testDatabase . '`';
$csvFiles = [];

$assert = static function (bool $condition, string $message): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
};
$writeCsv = static function (array $rows) use (&$csvFiles): string {
    $path = tempnam(sys_get_temp_dir(), 'blog_history_');
    if ($path === false) {
        throw new RuntimeException('No se pudo crear el CSV temporal.');
    }
    $csvFiles[] = $path;
    $handle = fopen($path, 'wb');
    fputcsv($handle, ['ID', 'Título', 'Contenido', 'Imagen'], ',', '"', '');
    foreach ($rows as $row) {
        fputcsv($handle, $row, ',', '"', '');
    }
    fclose($handle);
    return $path;
};

try {
    $adminPdo->exec('CREATE DATABASE ' . $quotedTest . ' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    foreach (['users', 'blog_categories', 'blog_posts'] as $table) {
        $quotedTable = '`' . $table . '`';
        $adminPdo->exec('CREATE TABLE ' . $quotedTest . '.' . $quotedTable . ' LIKE ' . $quotedSource . '.' . $quotedTable);
    }
    $testPdo = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4', $config['host'], $config['port'], $testDatabase),
        $config['username'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
    $testPdo->exec("INSERT INTO users (username, email, password) VALUES ('import_test', 'import@test.invalid', 'test')");
    $adminId = (int)$testPdo->lastInsertId();
    $migration = file_get_contents(dirname(__DIR__) . '/migrations/017_blog_import_history.sql');
    foreach (explode(';', $migration) as $statement) {
        if (trim($statement) !== '') {
            $testPdo->exec($statement);
        }
    }
    $testPdo->exec(file_get_contents(dirname(__DIR__) . '/migrations/018_blog_import_saved_source.sql'));
    $connectionProperty = new ReflectionProperty(Model::class, 'pdo');
    $connectionProperty->setValue(null, $testPdo);
    $importer = new BlogCsvImporter();
    $mapping = ['Identificador' => '{ID}', 'Título' => '{Título}', 'Contenido' => '{Contenido}', 'Imagen destacada' => '{Imagen}'];

    $firstCsv = $writeCsv([['1001', 'Artículo original', 'Contenido original', 'https://example.com/original.webp']]);
    $firstSource = $importer->prepareUploadedFile([
        'error' => UPLOAD_ERR_OK, 'name' => 'primero.csv', 'size' => filesize($firstCsv), 'tmp_name' => $firstCsv,
    ], $adminId);
    $firstPreview = $importer->previewMappedSource($firstSource['token'], $adminId, $mapping);
    $assert($firstPreview['can_import'], 'La primera previsualización debe ser válida.');
    $profileId = BlogImportProfile::saveProfile(
        null, 'Prueba de historial', $mapping, $firstSource['headers'], $adminId,
        $importer->getPreparedSourceData($firstSource['token'], $adminId)
    );
    $firstRun = BlogPost::importBatch(array_column($firstPreview['rows'], 'data'), $adminId, $profileId, 'primero.csv');
    $assert($firstRun['post_count'] === 1 && $firstRun['updated_count'] === 0, 'La primera ejecución debe crear un post.');
    $firstSlug = (string)$firstRun['posts'][0]['slug'];
    $assert(BlogPost::findPublishedBySlug($firstSlug) !== null, 'Por defecto el post importado debe publicarse en la URL pública.');
    $assert($testPdo->query('SELECT featured_image FROM blog_posts LIMIT 1')->fetchColumn() === 'https://example.com/original.webp', 'La primera ejecución debe guardar la imagen destacada.');
    $firstMatch = BlogImportProfile::matchedRecords($profileId)[hash('sha256', '1001')] ?? null;
    $assert($firstMatch !== null && (int)$firstMatch['id'] === (int)$firstRun['posts'][0]['id'], 'La plantilla debe reconocer el post existente por identificador.');
    $assert(!BlogPost::importRowHasChanges($firstPreview['rows'][0]['data'], $firstMatch), 'La comparación previa debe detectar un post sin cambios.');
    $storedFirstSource = $importer->prepareStoredSource(BlogImportProfile::savedSource(BlogImportProfile::findProfile($profileId)), $adminId, $profileId);
    $assert($storedFirstSource['reused'] === true, 'Debe poder reutilizar el archivo guardado sin subirlo otra vez.');
    $storedFirstPreview = $importer->previewMappedSource($storedFirstSource['token'], $adminId, $mapping);
    $unchangedRun = BlogPost::importBatch(array_column($storedFirstPreview['rows'], 'data'), $adminId, $profileId, 'primero.csv');
    $assert($unchangedRun['post_count'] === 0 && $unchangedRun['updated_count'] === 0 && $unchangedRun['unchanged_count'] === 1, 'Una corrida idéntica no debe actualizar el post.');
    $assert((int)$testPdo->query('SELECT COUNT(*) FROM blog_import_run_items WHERE run_id = ' . (int)$unchangedRun['run_id'])->fetchColumn() === 0, 'Una corrida sin cambios no debe registrar modificaciones que deshacer.');

    $editedMapping = $mapping;
    $editedMapping['Contenido'] = 'Editado: {Contenido}';
    BlogImportProfile::saveProfile(
        $profileId,
        'Prueba de historial editada',
        BlogCsvImporter::validateMapping($firstSource['headers'], $editedMapping),
        $firstSource['headers'],
        $adminId
    );
    $savedProfile = BlogImportProfile::findProfile($profileId);
    $assert($savedProfile['mapping']['Contenido'] === 'Editado: {Contenido}', 'La plantilla editada debe persistir sin subir otro archivo.');
    $editedSource = $importer->prepareStoredSource(BlogImportProfile::savedSource($savedProfile), $adminId, $profileId);
    $editedPreview = $importer->previewMappedSource($editedSource['token'], $adminId, $savedProfile['mapping']);
    $editedRun = BlogPost::importBatch(array_column($editedPreview['rows'], 'data'), $adminId, $profileId, 'primero.csv');
    $assert($editedRun['post_count'] === 0 && $editedRun['updated_count'] === 1, 'Editar la plantilla y repetir el archivo guardado debe actualizar el post.');
    $assert(str_contains((string)$testPdo->query('SELECT content FROM blog_posts LIMIT 1')->fetchColumn(), 'Editado: Contenido original'), 'El post debe reflejar el nuevo mapeo.');

    $secondCsv = $writeCsv([
        ['1001', 'Artículo revisado', 'Contenido revisado', 'https://example.com/revisada.webp'],
        ['1002', 'Artículo nuevo', 'Contenido nuevo', 'https://example.com/nueva.webp'],
    ]);
    $secondSource = $importer->prepareUploadedFile([
        'error' => UPLOAD_ERR_OK, 'name' => 'segundo.csv', 'size' => filesize($secondCsv), 'tmp_name' => $secondCsv,
    ], $adminId, $profileId);
    $secondPreview = $importer->previewMappedSource($secondSource['token'], $adminId, $savedProfile['mapping']);
    $assert($secondPreview['can_import'], 'La segunda previsualización debe ser válida.');
    $secondRun = BlogPost::importBatch(array_column($secondPreview['rows'], 'data'), $adminId, $profileId, 'segundo.csv', 'draft');
    $assert($secondRun['post_count'] === 1 && $secondRun['updated_count'] === 1, 'El nuevo CSV debe crear uno y actualizar uno.');
    $assert((int)$testPdo->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn() === 2, 'Deben existir dos posts.');
    $assert(BlogPost::findPublishedBySlug((string)$secondRun['posts'][0]['slug']) === null, 'Al elegir borrador, el post nuevo no debe verse en la URL pública.');
    $assert(BlogPost::findPublishedBySlug($firstSlug) === null, 'El estado borrador debe aplicarse también al post existente.');
    $assert($testPdo->query("SELECT featured_image FROM blog_posts WHERE title = 'Artículo revisado'")->fetchColumn() === 'https://example.com/revisada.webp', 'La actualización debe cambiar la imagen destacada.');
    $assert(str_contains((string)$testPdo->query("SELECT content FROM blog_posts WHERE title = 'Artículo revisado'")->fetchColumn(), 'Editado: Contenido revisado'), 'La nueva corrida debe aplicar el mapeo editado al post existente.');
    $assert(count(BlogImportProfile::runs($profileId)) === 4, 'El historial debe registrar todas las corridas, incluso la realizada con el archivo guardado.');
    $olderUndoRejected = false;
    try {
        BlogImportProfile::undoRun((int)$firstRun['run_id'], $adminId);
    } catch (RuntimeException) {
        $olderUndoRejected = true;
    }
    $assert($olderUndoRejected, 'No debe deshacerse una ejecución anterior a la más reciente.');

    $undoSecond = BlogImportProfile::undoRun((int)$secondRun['run_id'], $adminId);
    $assert($undoSecond['deleted_count'] === 1 && $undoSecond['restored_count'] === 1, 'El segundo deshacer debe eliminar y restaurar.');
    $assert((int)$testPdo->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn() === 1, 'Debe quedar el post original.');
    $assert($testPdo->query('SELECT title FROM blog_posts LIMIT 1')->fetchColumn() === 'Artículo original', 'Debe restaurarse el título original.');
    $assert($testPdo->query('SELECT featured_image FROM blog_posts LIMIT 1')->fetchColumn() === 'https://example.com/original.webp', 'Debe restaurarse la imagen original.');
    $assert(BlogPost::findPublishedBySlug($firstSlug) !== null, 'Deshacer debe restaurar el estado publicado previo.');
    $assert(str_contains((string)$testPdo->query('SELECT content FROM blog_posts LIMIT 1')->fetchColumn(), 'Editado: Contenido original'), 'Al deshacer la última corrida debe conservarse el mapeo aplicado en la anterior.');

    $undoEdited = BlogImportProfile::undoRun((int)$editedRun['run_id'], $adminId);
    $assert($undoEdited['restored_count'] === 1, 'Debe deshacerse la corrida realizada con el archivo guardado.');
    $assert($testPdo->query('SELECT content FROM blog_posts LIMIT 1')->fetchColumn() === '<p>Contenido original</p>', 'Debe restaurarse el contenido previo a la edición de la plantilla.');

    $undoUnchanged = BlogImportProfile::undoRun((int)$unchangedRun['run_id'], $adminId);
    $assert($undoUnchanged['deleted_count'] === 0 && $undoUnchanged['restored_count'] === 0, 'Una corrida sin cambios debe poder deshacerse sin modificar posts.');

    $undoFirst = BlogImportProfile::undoRun((int)$firstRun['run_id'], $adminId);
    $assert($undoFirst['deleted_count'] === 1, 'El primer deshacer debe eliminar el post original.');
    $assert((int)$testPdo->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn() === 0, 'No deben quedar posts de prueba.');

    $statusProfileId = BlogImportProfile::saveProfile(null, 'Prueba de publicación', $mapping, $firstSource['headers'], $adminId);
    $draftRun = BlogPost::importBatch(array_column($firstPreview['rows'], 'data'), $adminId, $statusProfileId, 'primero.csv', 'draft');
    $statusSlug = (string)$draftRun['posts'][0]['slug'];
    $assert(BlogPost::findPublishedBySlug($statusSlug) === null, 'Un post importado como borrador no debe ser público.');
    $matchedDraft = BlogImportProfile::matchedRecords($statusProfileId)[hash('sha256', '1001')] ?? null;
    $assert($matchedDraft !== null && BlogPost::importRowHasChanges($firstPreview['rows'][0]['data'], $matchedDraft, 'published'), 'La vista previa debe detectar el cambio de borrador a publicado.');
    $publishRun = BlogPost::importBatch(array_column($firstPreview['rows'], 'data'), $adminId, $statusProfileId, 'primero.csv', 'published');
    $assert($publishRun['post_count'] === 0 && $publishRun['updated_count'] === 1, 'La corrida debe publicar el post existente sin duplicarlo.');
    $assert(BlogPost::findPublishedBySlug($statusSlug) !== null, 'El post actualizado debe verse en la URL pública.');
    BlogImportProfile::undoRun((int)$publishRun['run_id'], $adminId);
    $assert(BlogPost::findPublishedBySlug($statusSlug) === null, 'Deshacer debe restaurar el estado de borrador.');
    BlogImportProfile::undoRun((int)$draftRun['run_id'], $adminId);
    $assert((int)$testPdo->query('SELECT COUNT(*) FROM blog_posts')->fetchColumn() === 0, 'Deshacer debe eliminar el post de prueba.');
    echo "OK (importar, actualizar y deshacer en MySQL temporal)\n";
} finally {
    $connectionProperty = new ReflectionProperty(Model::class, 'pdo');
    $connectionProperty->setValue(null, null);
    $testPdo = null;
    $adminPdo->exec('DROP DATABASE IF EXISTS ' . $quotedTest);
    foreach ($csvFiles as $path) {
        if (is_file($path)) {
            unlink($path);
        }
    }
}
