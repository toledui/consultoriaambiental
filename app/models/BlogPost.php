<?php

namespace App\Models;

use App\Core\Model;

class BlogPost extends Model
{
    protected static string $table = 'blog_posts';

    private static function currentPublishTime(): string
    {
        return (new \DateTimeImmutable('now', \app_timezone()))->format('Y-m-d H:i:s');
    }

    private static function publishedWhere(string $alias = 'p'): string
    {
        $prefix = $alias !== '' ? $alias . '.' : '';
        return $prefix . 'published = 1 AND (' . $prefix . 'published_at IS NULL OR ' . $prefix . 'published_at <= :now)';
    }

    private static function publishOrder(string $alias = 'p'): string
    {
        $prefix = $alias !== '' ? $alias . '.' : '';
        return 'COALESCE(' . $prefix . 'published_at, ' . $prefix . 'created_at) DESC';
    }

    /**
     * Get all published posts, ordered by creation date.
     */
    public static function getPublished(): array
    {
        return self::fetchAll(
            "SELECT p.*, c.name as category_name, c.slug as category_slug
             FROM " . self::$table . " p
             LEFT JOIN blog_categories c ON c.id = p.category_id
             WHERE " . self::publishedWhere('p') . "
             ORDER BY " . self::publishOrder('p'),
            ['now' => self::currentPublishTime()]
        );
    }

    /**
     * Get the latest published posts with a limit.
     *
     * @param int $limit Maximum number of posts to return
     * @return array
     */
    public static function getLatest(int $limit = 6): array
    {
        return self::fetchAll(
            "SELECT p.*, c.name as category_name, c.slug as category_slug
             FROM " . self::$table . " p
             LEFT JOIN blog_categories c ON c.id = p.category_id
             WHERE " . self::publishedWhere('p') . "
             ORDER BY " . self::publishOrder('p') . "
             LIMIT :limit",
            ['now' => self::currentPublishTime(), 'limit' => $limit]
        );
    }

    /**
     * Get published posts with pagination.
     *
     * @param int $page Current page (1-based)
     * @param int $perPage Posts per page
     * @return array ['posts' => array, 'total' => int, 'pages' => int]
     */
    public static function getPublishedPaginated(int $page = 1, int $perPage = 6): array
    {
        $total = self::fetch(
            "SELECT COUNT(*) as count
             FROM " . self::$table . " p
             WHERE " . self::publishedWhere('p'),
            ['now' => self::currentPublishTime()]
        );
        $totalCount = (int)($total['count'] ?? 0);
        $pages = max(1, (int)ceil($totalCount / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $posts = self::fetchAll(
            "SELECT p.*, c.name as category_name, c.slug as category_slug
             FROM " . self::$table . " p
             LEFT JOIN blog_categories c ON c.id = p.category_id
             WHERE " . self::publishedWhere('p') . "
             ORDER BY " . self::publishOrder('p') . " LIMIT :limit OFFSET :offset",
            ['now' => self::currentPublishTime(), 'limit' => $perPage, 'offset' => $offset]
        );

        return [
            'posts' => $posts,
            'total' => $totalCount,
            'pages' => $pages,
            'currentPage' => $page,
            'perPage' => $perPage,
        ];
    }

    /**
     * Get published posts by category with pagination.
     */
    public static function getPublishedByCategory(string $categorySlug, int $page = 1, int $perPage = 6): array
    {
        $total = self::fetch(
            "SELECT COUNT(*) as count FROM " . self::$table . " p
             INNER JOIN blog_categories c ON c.id = p.category_id
             WHERE " . self::publishedWhere('p') . " AND c.slug = :slug",
            ['now' => self::currentPublishTime(), 'slug' => $categorySlug]
        );
        $totalCount = (int)($total['count'] ?? 0);
        $pages = max(1, (int)ceil($totalCount / $perPage));
        $page = max(1, min($page, $pages));
        $offset = ($page - 1) * $perPage;

        $posts = self::fetchAll(
            "SELECT p.*, c.name as category_name, c.slug as category_slug
             FROM " . self::$table . " p
             INNER JOIN blog_categories c ON c.id = p.category_id
             WHERE " . self::publishedWhere('p') . " AND c.slug = :slug
             ORDER BY " . self::publishOrder('p') . " LIMIT :limit OFFSET :offset",
            ['now' => self::currentPublishTime(), 'slug' => $categorySlug, 'limit' => $perPage, 'offset' => $offset]
        );

        return [
            'posts' => $posts,
            'total' => $totalCount,
            'pages' => $pages,
            'currentPage' => $page,
            'perPage' => $perPage,
        ];
    }

    /**
     * Get all posts (for admin) with category info.
     */
    public static function getAll(): array
    {
        return self::fetchAll(
            "SELECT p.*, c.name as category_name
             FROM " . self::$table . " p
             LEFT JOIN blog_categories c ON c.id = p.category_id
             ORDER BY p.created_at DESC"
        );
    }

    /**
     * Find a post by slug.
     */
    public static function findBySlug(string $slug): ?array
    {
        return self::fetch(
            "SELECT p.*, c.name as category_name, c.slug as category_slug
             FROM " . self::$table . " p
             LEFT JOIN blog_categories c ON c.id = p.category_id
             WHERE p.slug = :slug",
            ['slug' => $slug]
        );
    }

    /**
     * Find a public post by slug.
     */
    public static function findPublishedBySlug(string $slug): ?array
    {
        return self::fetch(
            "SELECT p.*, c.name as category_name, c.slug as category_slug
             FROM " . self::$table . " p
             LEFT JOIN blog_categories c ON c.id = p.category_id
             WHERE p.slug = :slug AND " . self::publishedWhere('p'),
            ['slug' => $slug, 'now' => self::currentPublishTime()]
        );
    }

    /**
     * Find a post by ID.
     */
    public static function findById(int $id): ?array
    {
        return self::fetch(
            "SELECT p.*, c.name as category_name, c.slug as category_slug
             FROM " . self::$table . " p
             LEFT JOIN blog_categories c ON c.id = p.category_id
             WHERE p.id = :id",
            ['id' => $id]
        );
    }

    /**
     * Create a new blog post.
     */
    public static function createPost(array $data): int
    {
        // Ensure SEO fields are included
        $defaults = [
            'meta_title'       => '',
            'meta_description' => '',
            'json_ld'          => '',
            'category_id'      => null,
            'published_at'     => null,
        ];
        $data = array_merge($defaults, $data);
        return self::insert(self::$table, $data);
    }

    /**
     * Update an existing blog post.
     */
    public static function updatePost(int $id, array $data): int
    {
        return self::update(self::$table, $data, 'id = :id', ['id' => $id]);
    }

    /**
     * Delete a blog post.
     */
    public static function deletePost(int $id): int
    {
        return self::delete(self::$table, 'id = :id', ['id' => $id]);
    }

    /**
     * Generate a URL-friendly slug from a title.
     */
    public static function generateSlug(string $title): string
    {
        $slug = \url_slug($title);
        return $slug !== '' ? $slug : 'post';
    }
}
