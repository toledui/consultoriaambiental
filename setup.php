<?php

/**
 * Setup script — runs all database migrations in order.
 * Usage: php setup.php
 */

require_once __DIR__ . '/config/init.php';

echo "=== Gestoría Ambiental — Setup ===\n\n";

$config = require CONFIG_DIR . '/database.php';

try {
    // Connect without database first to create it
    $dsn = sprintf('mysql:host=%s;port=%s;charset=%s', $config['host'], $config['port'], $config['charset']);
    $pdo = new PDO($dsn, $config['username'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);

    // Find all migration files in order
    $migrationDir = __DIR__ . '/migrations';
    $files = glob($migrationDir . '/*.sql');
    sort($files);

    if (empty($files)) {
        die("No migration files found in {$migrationDir}\n");
    }

    foreach ($files as $file) {
        $basename = basename($file);
        echo "─── Running: {$basename} ───\n";

        $sql = file_get_contents($file);

        // Split by semicolons to execute statements one by one
        $statements = explode(';', $sql);

        foreach ($statements as $stmt) {
            $stmt = trim($stmt);
            if (!empty($stmt)) {
                // Skip comment-only lines
                if (preg_match('/^--/', $stmt)) {
                    continue;
                }
                $pdo->exec($stmt);
                echo "  ✓ " . substr($stmt, 0, 60) . "...\n";
            }
        }
        echo "\n";
    }

    echo "✓ All migrations completed successfully!\n";
    echo "  Database: {$config['dbname']}\n";
    echo "  Default admin user: admin / admin123\n\n";

} catch (PDOException $e) {
    echo "✗ Error: " . $e->getMessage() . "\n\n";
    echo "Make sure MySQL is running and credentials in config/database.php are correct.\n";
    exit(1);
}
