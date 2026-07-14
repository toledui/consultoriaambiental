<?php

namespace App\Controllers;

use App\Models\BlogPost;
use App\Models\ServiceCatalog;

class SeoController
{
    public function robots(): void
    {
        header('Content-Type: text/plain; charset=UTF-8');
        header('Cache-Control: public, max-age=3600');

        $baseUrl = rtrim(BASE_URL, '/');

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

        $baseUrl = rtrim(BASE_URL, '/');
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
}
