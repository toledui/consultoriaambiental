<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ServiceCatalog;

class ServiceController extends Controller
{
    public function index(): void
    {
        $services = ServiceCatalog::navigation();

        $this->view('servicios/index', [
            'title'       => 'Servicios',
            'currentPage' => 'servicios',
            'services'    => $services,
        ]);
    }

    public function show(string $slug): void
    {
        $service = ServiceCatalog::find($slug);

        if (!$service) {
            http_response_code(404);
            $this->view('servicios/show', [
                'title'       => 'Servicio no encontrado',
                'service'     => null,
                'currentPage' => 'servicios',
                'robotsContent' => 'noindex, nofollow',
            ]);
            return;
        }

        if ($service['slug'] !== $slug) {
            header('Location: ' . canonical_url('/servicios/' . $service['slug']), true, 301);
            return;
        }

        $pageTitle = $service['meta_title'] ?? $service['title'];
        $metaDesc  = $service['meta_description'] ?? $service['description'];
        $ogImage   = !empty($service['hero_image']) ? asset_url($service['hero_image']) : asset_url('images/consultoria-ambiental-logo.webp');
        $schema    = [
            '@context'    => 'https://schema.org',
            '@type'       => 'Service',
            'name'        => $service['title'],
            'description' => $service['description'],
            'provider'    => [
                '@type' => 'ProfessionalService',
                'name'  => APP_NAME,
                'url'   => public_base_url(),
            ],
            'areaServed'  => 'México',
            'url'         => canonical_url('/servicios/' . $service['slug']),
        ];

        $headExtra = '<meta property="og:title" content="' . htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8') . '"/>' . "\n";
        $headExtra .= '<meta property="og:description" content="' . htmlspecialchars($metaDesc, ENT_QUOTES, 'UTF-8') . '"/>' . "\n";
        $headExtra .= '<meta property="og:image" content="' . htmlspecialchars($ogImage, ENT_QUOTES, 'UTF-8') . '"/>' . "\n";
        $headExtra .= '<meta property="og:type" content="website"/>' . "\n";
        $headExtra .= '<script type="application/ld+json">' . "\n";
        $headExtra .= json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
        $headExtra .= '</script>' . "\n";

        if (!empty($service['faqs'])) {
            $faqSchema = [
                '@context'   => 'https://schema.org',
                '@type'      => 'FAQPage',
                'mainEntity' => array_map(static function (array $faq): array {
                    return [
                        '@type'          => 'Question',
                        'name'           => $faq['question'],
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text'  => $faq['answer'],
                        ],
                    ];
                }, $service['faqs']),
            ];

            $headExtra .= '<script type="application/ld+json">' . "\n";
            $headExtra .= json_encode($faqSchema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . "\n";
            $headExtra .= '</script>' . "\n";
        }

        $this->view('servicios/show', [
            'title'        => $pageTitle,
            'metaDesc'     => $metaDesc,
            'headExtra'    => $headExtra,
            'service'      => $service,
            'relatedServices' => ServiceCatalog::related($service['slug']),
            'currentPage'  => 'servicios',
            'currentSlug'  => $service['slug'],
        ]);
    }
}
