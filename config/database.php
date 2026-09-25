<?php

$config = [
    'host'     => 'localhost',
    'port'     => '3306',
    'dbname'   => 'gestoriaambiental',
    'username' => 'root',
    'password' => '',
    'charset'  => 'utf8mb4',
];

// Server-specific credentials live outside Git and are shared by CLI and PHP-FPM.
$localFile = __DIR__ . '/database.local.php';
if (is_file($localFile)) {
    $local = require $localFile;
    if (!is_array($local)) {
        throw new RuntimeException('config/database.local.php debe devolver un arreglo.');
    }
    $config = array_replace($config, $local);
}

// Explicit environment variables take precedence over the local file.
foreach ([
    'DB_HOST' => 'host',
    'DB_PORT' => 'port',
    'DB_NAME' => 'dbname',
    'DB_USER' => 'username',
    'DB_PASS' => 'password',
] as $variable => $key) {
    $value = getenv($variable);
    if ($value !== false && $value !== '') {
        $config[$key] = $value;
    }
}

return $config;
