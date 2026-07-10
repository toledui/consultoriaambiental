<?php

/**
 * Bootstrap file — loads config, sets up autoloading, starts session.
 */

// Load config constants
require_once __DIR__ . '/app.php';

date_default_timezone_set('America/Mexico_City');

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

if (!function_exists('url_slug')) {
    function url_slug(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        $value = strtr($value, [
            'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a', 'ã' => 'a', 'å' => 'a',
            'Á' => 'A', 'À' => 'A', 'Ä' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Å' => 'A',
            'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
            'É' => 'E', 'È' => 'E', 'Ë' => 'E', 'Ê' => 'E',
            'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
            'Í' => 'I', 'Ì' => 'I', 'Ï' => 'I', 'Î' => 'I',
            'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o', 'õ' => 'o',
            'Ó' => 'O', 'Ò' => 'O', 'Ö' => 'O', 'Ô' => 'O', 'Õ' => 'O',
            'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
            'Ú' => 'U', 'Ù' => 'U', 'Ü' => 'U', 'Û' => 'U',
            'ñ' => 'n', 'Ñ' => 'N', 'ç' => 'c', 'Ç' => 'C',
        ]);

        if (class_exists('Transliterator')) {
            $transliterator = \Transliterator::create('Any-Latin; Latin-ASCII');
            if ($transliterator) {
                $value = $transliterator->transliterate($value);
            }
        } else {
            $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);
            if ($converted !== false) {
                $value = $converted;
            }
        }

        $slug = mb_strtolower($value, 'UTF-8');
        $slug = preg_replace('/[^a-z0-9\s_-]/', '', $slug);
        $slug = preg_replace('/[\s_]+/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
}

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
