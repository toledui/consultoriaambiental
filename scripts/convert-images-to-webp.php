<?php

$root = dirname(__DIR__);
$dirs = [
    $root . '/public/images',
    $root . '/public/uploads',
    $root . '/images',
];
$extensions = ['jpg', 'jpeg', 'png', 'avif'];
$created = [];
$skipped = [];
$failed = [];

foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        continue;
    }

    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );

    foreach ($iterator as $file) {
        $source = $file->getPathname();
        $ext = strtolower(pathinfo($source, PATHINFO_EXTENSION));

        if (!in_array($ext, $extensions, true)) {
            continue;
        }

        $target = substr($source, 0, -strlen($ext)) . 'webp';

        if (file_exists($target) && filemtime($target) >= filemtime($source)) {
            $skipped[] = $target;
            continue;
        }

        $loader = match ($ext) {
            'jpg', 'jpeg' => 'imagecreatefromjpeg',
            'png' => 'imagecreatefrompng',
            'avif' => 'imagecreatefromavif',
            default => null,
        };

        if (!$loader || !function_exists($loader)) {
            $failed[] = ['file' => $source, 'reason' => 'Unsupported image loader'];
            continue;
        }

        $image = @$loader($source);
        if (!$image) {
            $failed[] = ['file' => $source, 'reason' => 'Could not read image'];
            continue;
        }

        if (!imageistruecolor($image)) {
            imagepalettetotruecolor($image);
        }
        imagealphablending($image, true);
        imagesavealpha($image, true);

        $quality = $ext === 'png' ? 90 : 82;
        $ok = @imagewebp($image, $target, $quality);
        imagedestroy($image);

        if (!$ok) {
            $failed[] = ['file' => $source, 'reason' => 'imagewebp failed'];
            continue;
        }

        touch($target, filemtime($source));
        $created[] = [
            'source' => str_replace('\\', '/', substr($source, strlen($root) + 1)),
            'webp' => str_replace('\\', '/', substr($target, strlen($root) + 1)),
            'source_bytes' => filesize($source),
            'webp_bytes' => filesize($target),
        ];
    }
}

$sourceBytes = array_sum(array_column($created, 'source_bytes'));
$webpBytes = array_sum(array_column($created, 'webp_bytes'));

echo json_encode([
    'created' => count($created),
    'skipped' => count($skipped),
    'failed' => $failed,
    'source_bytes' => $sourceBytes,
    'webp_bytes' => $webpBytes,
    'saved_bytes' => $sourceBytes - $webpBytes,
    'files' => $created,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
echo PHP_EOL;
