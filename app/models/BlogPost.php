<?php

namespace App\Models;

use App\Core\Model;
use PDO;
use PDOException;

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
            "SELECT p.*, c.name as category_name, u.username as author_name
             FROM " . self::$table . " p
             LEFT JOIN blog_categories c ON c.id = p.category_id
             LEFT JOIN users u ON u.id = p.author_id
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
            'author_id'        => null,
            'published_at'     => null,
        ];
        $data = array_merge($defaults, $data);
        return self::insert(self::$table, $data);
    }

    /**
     * Return every slug currently reserved by a blog post.
     */
    public static function getAllSlugs(): array
    {
        $rows = self::fetchAll("SELECT slug FROM " . self::$table);
        return array_values(array_map(
            static fn(array $row): string => (string)$row['slug'],
            $rows
        ));
    }

    /**
     * Import a validated CSV batch atomically.
     *
     * Categories are resolved/created inside the same transaction and every
     * imported post is saved as a draft assigned to the confirming admin.
     */
    public static function importBatch(array $rows, int $authorId): array
    {
        if ($rows === []) {
            throw new \InvalidArgumentException('El lote de importación está vacío.');
        }

        $pdo = self::getDB();
        $createdPosts = [];
        $createdCategories = [];
        $slugAdjustments = [];

        try {
            $pdo->beginTransaction();

            $categoryRows = $pdo->query(
                "SELECT id, name, slug FROM blog_categories"
            )->fetchAll();
            $categories = [];
            foreach ($categoryRows as $category) {
                $categories[(string)$category['slug']] = $category;
            }

            $slugRows = $pdo->query(
                "SELECT slug FROM " . self::$table
            )->fetchAll();
            $usedSlugs = [];
            foreach ($slugRows as $slugRow) {
                $usedSlugs[(string)$slugRow['slug']] = true;
            }

            $categoryInsert = $pdo->prepare(
                "INSERT INTO blog_categories (name, slug, description)
                 VALUES (:name, :slug, '')"
            );
            $postInsert = $pdo->prepare(
                "INSERT INTO " . self::$table . "
                    (title, slug, excerpt, content, featured_image, published,
                     published_at, category_id, author_id, meta_title,
                     meta_description, json_ld)
                 VALUES
                    (:title, :slug, :excerpt, :content, '', 0,
                     NULL, :category_id, :author_id, '',
                     :meta_description, '')"
            );

            foreach ($rows as $row) {
                self::validateImportRow($row);

                $categoryId = null;
                $categoryName = trim((string)$row['category_name']);
                $categorySlug = trim((string)$row['category_slug']);
                if ($categoryName !== '') {
                    if (!isset($categories[$categorySlug])) {
                        try {
                            $categoryInsert->execute([
                                'name' => $categoryName,
                                'slug' => $categorySlug,
                            ]);
                            $categoryId = (int)$pdo->lastInsertId();
                            $categories[$categorySlug] = [
                                'id' => $categoryId,
                                'name' => $categoryName,
                                'slug' => $categorySlug,
                            ];
                            $createdCategories[] = [
                                'id' => $categoryId,
                                'name' => $categoryName,
                                'slug' => $categorySlug,
                            ];
                        } catch (PDOException $e) {
                            if (!self::isDuplicateKeyException($e)) {
                                throw $e;
                            }
                            $lookup = $pdo->prepare(
                                "SELECT id, name, slug FROM blog_categories WHERE slug = :slug"
                            );
                            $lookup->execute(['slug' => $categorySlug]);
                            $category = $lookup->fetch();
                            if (!$category) {
                                throw $e;
                            }
                            $categories[$categorySlug] = $category;
                            $categoryId = (int)$category['id'];
                        }
                    } else {
                        $categoryId = (int)$categories[$categorySlug]['id'];
                    }
                }

                $baseSlug = (string)$row['base_slug'];
                $slug = self::nextAvailableImportSlug($baseSlug, $usedSlugs);
                if ($slug !== $baseSlug) {
                    $slugAdjustments[] = [
                        'title' => (string)$row['title'],
                        'from' => $baseSlug,
                        'to' => $slug,
                    ];
                }

                while (true) {
                    try {
                        $postInsert->execute([
                            'title' => (string)$row['title'],
                            'slug' => $slug,
                            'excerpt' => (string)$row['excerpt'],
                            'content' => (string)$row['content'],
                            'category_id' => $categoryId,
                            'author_id' => $authorId,
                            'meta_description' => (string)$row['meta_description'],
                        ]);
                        break;
                    } catch (PDOException $e) {
                        if (!self::isDuplicateKeyException($e)) {
                            throw $e;
                        }
                        $usedSlugs[$slug] = true;
                        $nextSlug = self::nextAvailableImportSlug($baseSlug, $usedSlugs);
                        $slugAdjustments[] = [
                            'title' => (string)$row['title'],
                            'from' => $slug,
                            'to' => $nextSlug,
                        ];
                        $slug = $nextSlug;
                    }
                }

                $postId = (int)$pdo->lastInsertId();
                $usedSlugs[$slug] = true;
                $createdPosts[] = [
                    'id' => $postId,
                    'title' => (string)$row['title'],
                    'slug' => $slug,
                    'category_name' => $categoryName,
                ];
            }

            $pdo->commit();

            return [
                'posts' => $createdPosts,
                'categories' => $createdCategories,
                'slug_adjustments' => $slugAdjustments,
                'post_count' => count($createdPosts),
                'category_count' => count($createdCategories),
            ];
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
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

    private static function validateImportRow(array $row): void
    {
        foreach ([
            'title',
            'base_slug',
            'excerpt',
            'content',
            'category_name',
            'category_slug',
            'meta_description',
        ] as $field) {
            if (!array_key_exists($field, $row) || !is_string($row[$field])) {
                throw new \InvalidArgumentException(
                    'El lote contiene datos incompletos o alterados.'
                );
            }
        }

        if (trim($row['title']) === ''
            || trim($row['base_slug']) === ''
            || trim(strip_tags($row['content'])) === ''
        ) {
            throw new \InvalidArgumentException(
                'El lote contiene un post sin título, slug o contenido.'
            );
        }
        if (mb_strlen($row['title'], 'UTF-8') > 255
            || mb_strlen($row['base_slug'], 'UTF-8') > 255
            || strlen($row['content']) > 65535
            || strlen($row['excerpt']) > 65535
            || strlen($row['meta_description']) > 65535
        ) {
            throw new \InvalidArgumentException(
                'El lote contiene un valor que excede la capacidad de la base de datos.'
            );
        }
    }

    private static function nextAvailableImportSlug(string $baseSlug, array $usedSlugs): string
    {
        if (!isset($usedSlugs[$baseSlug])) {
            return $baseSlug;
        }

        $suffix = 2;
        do {
            $suffixText = '-' . $suffix;
            $candidate = rtrim(
                substr($baseSlug, 0, 255 - strlen($suffixText)),
                '-'
            ) . $suffixText;
            $suffix++;
        } while (isset($usedSlugs[$candidate]));

        return $candidate;
    }

    private static function isDuplicateKeyException(PDOException $e): bool
    {
        return (string)$e->getCode() === '23000'
            && (int)($e->errorInfo[1] ?? 0) === 1062;
    }
}
