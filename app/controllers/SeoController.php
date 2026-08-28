<?php

namespace App\Controllers;

use App\Models\BlogPost;
use App\Models\ServiceCatalog;
use App\Models\Setting;

class SeoController
{
    /**
     * Serve the configured favicon through a permanent public URL.
     *
     * The uploaded filename may change when an administrator replaces the
     * image, but search engines must always discover it at /favicon.png.
     */
    public function favicon(): void
    {
        $configuredPath = ltrim((string) Setting::get('brand_favicon', ''), '/');
        $faviconPath = $this->publicFilePath($configuredPath);

        if ($faviconPath === null) {
            $faviconPath = $this->publicFilePath('favicon.svg');
        }

        if ($faviconPath === null) {
            http_response_code(404);
            return;
        }

        $mimeType = strtolower(pathinfo($faviconPath, PATHINFO_EXTENSION)) === 'svg'
            ? 'image/svg+xml'
            : 'image/png';
        $modifiedAt = (int) filemtime($faviconPath);
        $etag = '"' . hash('sha256', $faviconPath . '|' . $modifiedAt . '|' . filesize($faviconPath)) . '"';

        header('Content-Type: ' . $mimeType);
        header('Cache-Control: public, max-age=86400, must-revalidate');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $modifiedAt) . ' GMT');
        header('ETag: ' . $etag);

        if (trim((string) ($_SERVER['HTTP_IF_NONE_MATCH'] ?? '')) === $etag) {
            http_response_code(304);
            return;
        }

        readfile($faviconPath);
    }

    public function robots(): void
    {
        header('Content-Type: text/plain; charset=UTF-8');
        header('Cache-Control: public, max-age=3600');

        $baseUrl = public_base_url();

        echo "User-agent: *\n";
        echo "Allow: /\n";
        echo "Disallow: /admin\n";
        echo "Disallow: /checklist/\n";
        echo "Disallow: /contacto/gracias\n\n";
        echo "Sitemap: {$baseUrl}/sitemap.xml\n";
    }

    public function sitemap(): void
    {
        header('Content-Type: application/xml; charset=UTF-8');
        header('Cache-Control: public, max-age=3600');

        $baseUrl = public_base_url();
        $urls = [
            ['loc' => $baseUrl . '/'],
            ['loc' => $baseUrl . '/nosotros'],
            ['loc' => $baseUrl . '/servicios'],
            ['loc' => $baseUrl . '/blog'],
            ['loc' => $baseUrl . '/contacto'],
            ['loc' => $baseUrl . '/aviso-de-privacidad'],
        ];

        foreach (ServiceCatalog::navigation() as $service) {
            $urls[] = ['loc' => $baseUrl . '/servicios/' . rawurlencode($service['slug'])];
        }

        try {
            $publishedPosts = BlogPost::getPublished();
        } catch (\Throwable $exception) {
            error_log('No fue posible agregar los posts al sitemap: ' . $exception->getMessage());
            $publishedPosts = [];
        }

        foreach ($publishedPosts as $post) {
            $entry = ['loc' => $baseUrl . '/blog/' . rawurlencode($post['slug'])];
            $lastModified = $post['updated_at'] ?? $post['published_at'] ?? $post['created_at'] ?? null;
            if (is_string($lastModified) && preg_match('/^\d{4}-\d{2}-\d{2}/', $lastModified)) {
                $entry['lastmod'] = substr($lastModified, 0, 10);
            }
            $urls[] = $entry;
        }

        echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";

        foreach ($urls as $entry) {
            echo "  <url>\n";
            echo '    <loc>' . $this->escapeXml($entry['loc']) . "</loc>\n";
            if (!empty($entry['lastmod'])) {
                echo '    <lastmod>' . $entry['lastmod'] . "</lastmod>\n";
            }
            echo "  </url>\n";
        }

        echo "</urlset>\n";
    }

    private function escapeXml(string $value): string
    {
        return htmlspecialchars($value, ENT_XML1 | ENT_QUOTES, 'UTF-8');
    }

    private function publicFilePath(string $relativePath): ?string
    {
        if ($relativePath === '') {
            return null;
        }

        $publicDirectory = realpath(PUBLIC_DIR);
        $candidate = realpath(PUBLIC_DIR . '/' . $relativePath);
        if ($publicDirectory === false
            || $candidate === false
            || !is_file($candidate)
            || !str_starts_with($candidate, $publicDirectory . DIRECTORY_SEPARATOR)) {
            return null;
        }

        return $candidate;
    }
}
