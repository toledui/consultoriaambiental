<?php

$source = dirname(__DIR__) . '/config/database.php';
$directory = sys_get_temp_dir() . '/database_config_test_' . bin2hex(random_bytes(6));
if (!mkdir($directory) || !copy($source, $directory . '/database.php')) {
    throw new RuntimeException('No se pudo preparar la prueba de configuración.');
}

$previousUser = getenv('DB_USER');
$previousName = getenv('DB_NAME');

try {
    putenv('DB_USER');
    putenv('DB_NAME');
    file_put_contents($directory . '/database.local.php', "<?php return ['username' => 'local_user', 'dbname' => 'local_database'];\n");

    $config = require $directory . '/database.php';
    if ($config['username'] !== 'local_user' || $config['dbname'] !== 'local_database') {
        throw new RuntimeException('No se aplicó la configuración local.');
    }

    putenv('DB_USER=environment_user');
    $config = require $directory . '/database.php';
    if ($config['username'] !== 'environment_user' || $config['dbname'] !== 'local_database') {
        throw new RuntimeException('Las variables de entorno deben tener prioridad.');
    }

    echo "OK (configuración local y variables de entorno)\n";
} finally {
    $previousUser === false ? putenv('DB_USER') : putenv('DB_USER=' . $previousUser);
    $previousName === false ? putenv('DB_NAME') : putenv('DB_NAME=' . $previousName);
    unlink($directory . '/database.local.php');
    unlink($directory . '/database.php');
    rmdir($directory);
}
