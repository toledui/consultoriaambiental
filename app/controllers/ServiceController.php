<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Service;
use App\Models\Setting;

class ServiceController extends Controller
{
    public function index(): void
    {
        $services = Service::getPublished();
        $settings = Setting::getAll();

        $this->view('servicios/index', [
            'title'       => 'Servicios',
            'currentPage' => 'servicios',
            'services'    => $services,
            'settings'    => $settings,
        ]);
    }

    public function show(string $slug): void
    {
        $service = Service::findBySlug($slug);

        if (!$service) {
            http_response_code(404);
            $this->view('servicios/show', [
                'title'   => 'Servicio no encontrado',
                'service' => null,
            ]);
            return;
        }

        // Use meta_title if set, otherwise fall back to service title
        $pageTitle = !empty($service['meta_title']) ? $service['meta_title'] : $service['title'];
        $metaDesc  = $service['meta_description'] ?? '';

        // Build head extra content (JSON-LD + Open Graph)
        $headExtra = '';

        // JSON-LD Schema
        if (!empty($service['json_ld'])) {
            $headExtra .= '<script type="application/ld+json">' . "\n" . $service['json_ld'] . "\n" . '</script>' . "\n";
        }

        // Open Graph tags
        $headExtra .= '<meta property="og:title" content="' . htmlspecialchars($pageTitle) . '"/>' . "\n";
        $headExtra .= '<meta property="og:description" content="' . htmlspecialchars($metaDesc ?: ($service['description'] ?? '')) . '"/>' . "\n";
        if (!empty($service['featured_image'])) {
            $headExtra .= '<meta property="og:image" content="' . BASE_URL . '/' . htmlspecialchars($service['featured_image']) . '"/>' . "\n";
        }
        $headExtra .= '<meta property="og:type" content="website"/>' . "\n";

        // Fix hardcoded localhost URLs in content (from admin editor)
        if (!empty($service['content'])) {
            $content = $service['content'];
            $baseUrl = rtrim(BASE_URL, '/');

            // Replace hardcoded localhost URLs with BASE_URL
            // Use regex to avoid double-port issue (e.g., http://localhost:8000:8000)
            // Replace http://localhost (optionally followed by :8000) with BASE_URL
            $content = preg_replace(
                '/http:\/\/localhost(?::8000)?/',
                $baseUrl,
                $content
            );

            // Convert Tailwind bg-[url('...')] classes to inline style for dynamic content
            // Tailwind arbitrary values don't work with runtime content
            $content = preg_replace(
                '/class\s*=\s*"([^"]*?)bg-\[url\(\'([^\']+)\'\)\]([^"]*?)"/',
                'style="background-image: url(\'$2\');background-size:cover;background-position:center;background-repeat:no-repeat;" class="$1$3"',
                $content
            );

            $service['content'] = $content;
        }

        $this->view('servicios/show', [
            'title'        => $pageTitle,
            'metaDesc'     => $metaDesc,
            'headExtra'    => $headExtra,
            'service'      => $service,
            'currentPage'  => 'servicios',
            'currentSlug'  => $slug,
        ]);
    }
}
