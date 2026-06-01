<?php

/**
 * Bootstrap file — loads config, sets up autoloading, starts session.
 */

// Load config constants
require_once __DIR__ . '/app.php';

// Error reporting
if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// PSR-4 style autoloader
spl_autoload_register(function (string $class) {
    // Map namespace prefix 'App\' to 'app/'
    $prefix   = 'App\\';
    $baseDir  = APP_DIR . '/';

    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }

    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';

    // Try exact case first (works on Windows)
    if (file_exists($file)) {
        require $file;
        return;
    }

    // Fallback: lowercase only directories for case-sensitive filesystems (Linux)
    // Our directories are all lowercase (core, controllers, models, etc.)
    // but filenames may have capital letters (e.g. Router.php)
    $parts = explode('/', str_replace('\\', '/', $relativeClass));
    $filename = array_pop($parts) . '.php';
    $lowerDir = strtolower(implode('/', $parts));
    $lowerFile = $baseDir . ($lowerDir ? $lowerDir . '/' : '') . $filename;
    if (file_exists($lowerFile)) {
        require $lowerFile;
    }
});
