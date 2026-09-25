<?php

require dirname(__DIR__) . '/scripts/MigrationRunner.php';

if (getenv('RUN_DB_INTEGRATION') !== '1') {
    echo "SKIP (set RUN_DB_INTEGRATION=1 to test migrations in a temporary MySQL database)\n";
    exit(0);
}

$config = require dirname(__DIR__) . '/config/database.php';
$serverDsn = sprintf('mysql:host=%s;port=%s;charset=%s', $config['host'], $config['port'], $config['charset']);
$options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC];
$admin = new PDO($serverDsn, $config['username'], $config['password'], $options);
$databaseName = 'migration_test_' . bin2hex(random_bytes(6));
$quotedName = '`' . $databaseName . '`';
$assert = static function (bool $condition, string $message): void {
    if (!$condition) {
        throw new RuntimeException($message);
    }
};

try {
    $admin->exec("CREATE DATABASE {$quotedName} CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $config['host'], $config['port'], $databaseName, $config['charset']),
        $config['username'], $config['password'], $options
    );
    $runner = new MigrationRunner($pdo, dirname(__DIR__) . '/migrations');
    $messages = [];
    $log = static function (string $message) use (&$messages): void { $messages[] = $message; };

    $environment = getenv();
    $environment['DB_HOST'] = $config['host'];
    $environment['DB_PORT'] = $config['port'];
    $environment['DB_NAME'] = $databaseName;
    $environment['DB_USER'] = $config['username'];
    $environment['DB_PASS'] = $config['password'];
    $process = proc_open([PHP_BINARY, dirname(__DIR__) . '/migrate'], [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes, dirname(__DIR__), $environment);
    $assert(is_resource($process), 'No se pudo ejecutar php migrate.');
    $output = stream_get_contents($pipes[1]);
    $errors = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);
    $assert(proc_close($process) === 0, "Falló php migrate: {$output} {$errors}");

    $runner->run($log);
    $migrationCount = count(glob(dirname(__DIR__) . '/migrations/*.sql'));
    $assert((int) $pdo->query('SELECT COUNT(*) FROM schema_migrations')->fetchColumn() === $migrationCount, 'No se registraron todas las migraciones.');
    $assert((int) $pdo->query("SELECT COUNT(*) FROM users WHERE username = 'admin'")->fetchColumn() === 1, 'No se creó el usuario inicial.');
    $assert((int) $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'blog_import_profiles' AND COLUMN_NAME = 'source_records_json'")->fetchColumn() === 1, 'Falta la columna de la migración 018.');

    $pdo->exec("UPDATE users SET password = 'custom-password-hash' WHERE username = 'admin'");
    $pdo->exec("UPDATE settings SET `value` = 'Mi teléfono' WHERE `key` = 'footer_phone_label'");
    $runner->run($log);
    $assert(end($messages) === 'Listo: 0 migración(es) aplicada(s).', 'La segunda ejecución no debe aplicar migraciones.');

    // Simulate a site installed before the migration ledger, with a partially applied migration.
    $pdo->exec('DROP TABLE schema_migrations');
    $pdo->exec('ALTER TABLE blog_import_profiles DROP COLUMN source_records_json');
    $runner->run($log);
    $assert((int) $pdo->query('SELECT COUNT(*) FROM schema_migrations')->fetchColumn() === $migrationCount, 'No se adoptó el esquema existente.');
    $assert((int) $pdo->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'blog_import_profiles' AND COLUMN_NAME = 'source_records_json'")->fetchColumn() === 1, 'No se completó la migración parcial.');
    $assert($pdo->query("SELECT password FROM users WHERE username = 'admin'")->fetchColumn() === 'custom-password-hash', 'La migración alteró la contraseña existente.');
    $assert($pdo->query("SELECT `value` FROM settings WHERE `key` = 'footer_phone_label'")->fetchColumn() === 'Mi teléfono', 'La migración alteró un ajuste existente.');

    echo "OK ({$migrationCount} migraciones, repetición y adopción de esquema existente)\n";
} finally {
    $admin->exec("DROP DATABASE IF EXISTS {$quotedName}");
}
