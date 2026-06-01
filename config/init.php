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

    // Fallbacks for case-sensitive filesystems (Linux hosting).
    // The top-level app folders are lowercase (controllers, models, core),
    // while nested namespaces like Admin may keep their original case.
    $parts = explode('/', str_replace('\\', '/', $relativeClass));
    $filename = array_pop($parts) . '.php';
    $firstDirLower = $parts;
    if (!empty($firstDirLower)) {
        $firstDirLower[0] = strtolower($firstDirLower[0]);
        $firstDirLowerFile = $baseDir . implode('/', $firstDirLower) . '/' . $filename;
        if (file_exists($firstDirLowerFile)) {
            require $firstDirLowerFile;
            return;
        }
    }

    $lowerDir = strtolower(implode('/', $parts));
    $lowerFile = $baseDir . ($lowerDir ? $lowerDir . '/' : '') . $filename;
    if (file_exists($lowerFile)) {
        require $lowerFile;
    }
});

if (!function_exists('asset_url')) {
    function asset_url(string $relativePath): string
    {
        $relativePath = ltrim(str_replace('\\', '/', $relativePath), '/');
        $encoded = implode('/', array_map('rawurlencode', explode('/', $relativePath)));
        return BASE_URL . '/' . $encoded;
    }
}

if (!function_exists('asset_prefer_webp')) {
    function asset_prefer_webp(?string $asset): string
    {
        $asset = trim((string) $asset);
        if ($asset === '') {
            return '';
        }

        $parts = parse_url($asset);
        if (!empty($parts['host'])) {
            $baseParts = parse_url(BASE_URL);
            if (($parts['host'] ?? '') !== ($baseParts['host'] ?? '')) {
                return $asset;
            }
            $relativePath = ltrim(rawurldecode($parts['path'] ?? ''), '/');
        } else {
            $relativePath = ltrim(rawurldecode($asset), '/');
        }

        if (str_starts_with($relativePath, 'public/')) {
            $relativePath = substr($relativePath, 7);
        }

        $ext = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'avif'], true)) {
            return $asset;
        }

        $webpRelative = substr($relativePath, 0, -strlen($ext)) . 'webp';
        $webpPath = PUBLIC_DIR . '/' . $webpRelative;

        if (!file_exists($webpPath)) {
            return $asset;
        }

        return asset_url($webpRelative) . '?v=' . filemtime($webpPath);
    }
}

if (!function_exists('convert_image_file_to_webp')) {
    function convert_image_file_to_webp(string $sourcePath, string $mimeType, string $destPath): bool
    {
        $loader = match ($mimeType) {
            'image/jpeg' => 'imagecreatefromjpeg',
            'image/png' => 'imagecreatefrompng',
            'image/avif' => 'imagecreatefromavif',
            default => null,
        };

        if (!$loader || !function_exists($loader) || !function_exists('imagewebp')) {
            return false;
        }

        $image = @$loader($sourcePath);
        if (!$image) {
            return false;
        }

        if (!imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $quality = $mimeType === 'image/png' ? 90 : 82;
        $ok = imagewebp($image, $destPath, $quality);
        imagedestroy($image);

        return $ok;
    }
}
