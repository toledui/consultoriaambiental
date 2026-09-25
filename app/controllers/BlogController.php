<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;

class BlogController extends Controller
{
    public function index(): void
    {
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $categorySlug = $_GET['categoria'] ?? '';
        $canonicalQuery = $page > 1 && $categorySlug === '' ? ['page' => $page] : [];
        $hasFilteredResults = $categorySlug !== '' || isset($_GET['buscar']);

        if ($categorySlug) {
            $result = BlogPost::getPublishedByCategory($categorySlug, $page, 6);
            $category = BlogCategory::findBySlug($categorySlug);
            $pageTitle = $category ? 'Blog - ' . $category['name'] : 'Blog';
        } else {
            $result = BlogPost::getPublishedPaginated($page, 6);
            $pageTitle = 'Blog';
        }

        $categories = BlogCategory::getAllWithCount(true);

        $this->view('blog/index', [
            'title'       => $pageTitle,
            'currentPage' => 'blog',
            'posts'       => $result['posts'],
            'categories'  => $categories,
            'activeCategory' => $categorySlug,
            'pagination'  => [
                'currentPage' => $result['currentPage'],
                'totalPages'  => $result['pages'],
                'total'       => $result['total'],
            ],
            'canonicalUrl' => canonical_url('/blog', $canonicalQuery),
            'robotsContent' => $hasFilteredResults
                ? 'noindex, follow'
                : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
        ]);
    }

    public function show(string $slug): void
    {
        $isPreview = isset($_SESSION['admin_id']) && ($_GET['preview'] ?? '') === '1';
        $post = $isPreview ? BlogPost::findBySlug($slug) : BlogPost::findPublishedBySlug($slug);

        if (!$post) {
            http_response_code(404);
            $this->view('blog/show', [
                'title' => 'Artículo no encontrado',
                'post'  => null,
                'robotsContent' => 'noindex, nofollow',
            ]);
            return;
        }

        // Use meta_title if set, otherwise fall back to post title
        $pageTitle = !empty($post['meta_title']) ? $post['meta_title'] : $post['title'];
        $metaDesc  = $post['meta_description'] ?? '';

        // Keep headExtra for structured data only. Open Graph is rendered once
        // by the main layout from the values passed below.
        $headExtra = '';

        // JSON-LD Schema
        if (!empty($post['json_ld'])) {
            $headExtra .= '<script type="application/ld+json">' . "\n" . $post['json_ld'] . "\n" . '</script>' . "\n";
        }

        // Fix hardcoded localhost URLs in content (from admin editor)
        if (!empty($post['content'])) {
            $content = $post['content'];
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
            $content = preg_replace(
                '/class\s*=\s*"([^"]*?)bg-\[url\(\'([^\']+)\'\)\]([^"]*?)"/',
                'style="background-image: url(\'$2\');background-size:cover;background-position:center;background-repeat:no-repeat;" class="$1$3"',
                $content
            );

            $post['content'] = $content;
        }

        $this->view('blog/show', [
            'title'       => $pageTitle,
            'metaDesc'    => $metaDesc,
            'headExtra'   => $headExtra,
            'post'        => $post,
            'ogDescription' => $metaDesc ?: ($post['excerpt'] ?? ''),
            'ogImage'       => $post['featured_image'] ?? '',
            'ogImageAlt'    => $post['title'],
            'ogType'        => 'article',
            'isPreview'     => $isPreview,
            'robotsContent' => $isPreview ? 'noindex, nofollow' : 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1',
        ]);
    }
}
