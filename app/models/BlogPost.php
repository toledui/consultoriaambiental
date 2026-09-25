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
     * new posts use the selected publication status and the confirming admin.
     */
    public static function importBatch(
        array $rows,
        int $authorId,
        ?int $profileId = null,
        string $sourceFilename = '',
        string $publicationStatus = 'published'
    ): array
    {
        if ($rows === []) {
            throw new \InvalidArgumentException('El lote de importación está vacío.');
        }
        if (!in_array($publicationStatus, ['published', 'draft'], true)) {
            throw new \InvalidArgumentException('El estado de publicación no es válido.');
        }

        $pdo = self::getDB();
        $createdPosts = [];
        $updatedPosts = [];
        $unchangedPosts = [];
        $createdCategories = [];
        $slugAdjustments = [];

        try {
            $pdo->beginTransaction();
            $runId = null;
            $importRecords = [];
            if ($profileId !== null) {
                $createRun = $pdo->prepare(
                    "INSERT INTO blog_import_runs (profile_id, source_filename, created_by)
                     VALUES (:profile_id, :source_filename, :created_by)"
                );
                $createRun->execute([
                    'profile_id' => $profileId,
                    'source_filename' => mb_strimwidth($sourceFilename, 0, 255, '', 'UTF-8'),
                    'created_by' => $authorId,
                ]);
                $runId = (int)$pdo->lastInsertId();
                $recordsQuery = $pdo->prepare(
                    'SELECT r.source_key_hash, r.post_id, r.last_run_id, p.title, p.slug, p.excerpt,
                     p.content, p.featured_image, p.category_id, p.meta_title, p.meta_description,
                     p.published, p.published_at,
                     c.slug AS category_slug
                     FROM blog_import_records r
                     LEFT JOIN blog_posts p ON p.id = r.post_id
                     LEFT JOIN blog_categories c ON c.id = p.category_id
                     WHERE r.profile_id = :profile_id FOR UPDATE'
                );
                $recordsQuery->execute(['profile_id' => $profileId]);
                foreach ($recordsQuery->fetchAll() as $record) {
                    $importRecords[$record['source_key_hash']] = $record;
                }
            }

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
                    (:title, :slug, :excerpt, :content, :featured_image, :published,
                     NULL, :category_id, :author_id, :meta_title,
                     :meta_description, '')"
            );
            $postUpdate = $pdo->prepare(
                "UPDATE blog_posts SET title = :title, excerpt = :excerpt, content = :content,
                 featured_image = COALESCE(NULLIF(:featured_image, ''), featured_image),
                 category_id = :category_id, meta_title = :meta_title, meta_description = :meta_description,
                 published = :published, published_at = :published_at
                 WHERE id = :id"
            );
            $recordInsert = $pdo->prepare(
                'INSERT INTO blog_import_records (profile_id, source_key_hash, post_id, last_run_id)
                 VALUES (:profile_id, :source_key_hash, :post_id, :last_run_id)'
            );
            $recordUpdate = $pdo->prepare(
                'UPDATE blog_import_records SET last_run_id = :last_run_id
                 WHERE profile_id = :profile_id AND source_key_hash = :source_key_hash'
            );
            $runItemInsert = $pdo->prepare(
                'INSERT INTO blog_import_run_items
                 (run_id, post_id, source_key_hash, action, before_json, prior_run_id)
                 VALUES (:run_id, :post_id, :source_key_hash, :action, :before_json, :prior_run_id)'
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

                $sourceKey = (string)($row['source_key'] ?? $row['base_slug']);
                $sourceKeyHash = hash('sha256', $sourceKey);
                $matched = $profileId !== null ? ($importRecords[$sourceKeyHash] ?? null) : null;
                if (is_array($matched) && $matched['title'] !== null) {
                    $postId = (int)$matched['post_id'];
                    if (!self::importRowHasChanges($row, $matched, $publicationStatus)) {
                        $unchangedPosts[] = [
                            'id' => $postId,
                            'title' => (string)$matched['title'],
                            'slug' => (string)$matched['slug'],
                            'category_name' => $categoryName,
                            'action' => 'unchanged',
                        ];
                        continue;
                    }
                    $before = [
                        'title' => $matched['title'],
                        'excerpt' => $matched['excerpt'],
                        'content' => $matched['content'],
                        'featured_image' => $matched['featured_image'],
                        'category_id' => $matched['category_id'],
                        'meta_title' => $matched['meta_title'],
                        'meta_description' => $matched['meta_description'],
                        'published' => $matched['published'],
                        'published_at' => $matched['published_at'],
                    ];
                    $postUpdate->execute([
                        'title' => (string)$row['title'],
                        'excerpt' => (string)$row['excerpt'],
                        'content' => (string)$row['content'],
                        'featured_image' => (string)($row['featured_image'] ?? ''),
                        'category_id' => $categoryId,
                        'meta_title' => (string)($row['meta_title'] ?? ''),
                        'meta_description' => (string)$row['meta_description'],
                        'published' => $publicationStatus === 'published' ? 1 : 0,
                        'published_at' => self::importPublicationHasChanges($matched, $publicationStatus)
                            ? null : $matched['published_at'],
                        'id' => $postId,
                    ]);
                    $recordUpdate->execute([
                        'last_run_id' => $runId,
                        'profile_id' => $profileId,
                        'source_key_hash' => $sourceKeyHash,
                    ]);
                    $runItemInsert->execute([
                        'run_id' => $runId,
                        'post_id' => $postId,
                        'source_key_hash' => $sourceKeyHash,
                        'action' => 'updated',
                        'before_json' => json_encode($before, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR),
                        'prior_run_id' => $matched['last_run_id'],
                    ]);
                    $updatedPosts[] = [
                        'id' => $postId,
                        'title' => (string)$row['title'],
                        'slug' => (string)$matched['slug'],
                        'category_name' => $categoryName,
                        'action' => 'updated',
                    ];
                    continue;
                }
                if (is_array($matched)) {
                    $pdo->prepare(
                        'DELETE FROM blog_import_records WHERE profile_id = :profile_id AND source_key_hash = :source_key_hash'
                    )->execute(['profile_id' => $profileId, 'source_key_hash' => $sourceKeyHash]);
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
                            'featured_image' => (string)($row['featured_image'] ?? ''),
                            'published' => $publicationStatus === 'published' ? 1 : 0,
                            'category_id' => $categoryId,
                            'author_id' => $authorId,
                            'meta_title' => (string)($row['meta_title'] ?? ''),
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
                    'action' => 'created',
                    'publication_status' => $publicationStatus,
                ];
                if ($profileId !== null) {
                    $recordInsert->execute([
                        'profile_id' => $profileId,
                        'source_key_hash' => $sourceKeyHash,
                        'post_id' => $postId,
                        'last_run_id' => $runId,
                    ]);
                    $runItemInsert->execute([
                        'run_id' => $runId,
                        'post_id' => $postId,
                        'source_key_hash' => $sourceKeyHash,
                        'action' => 'created',
                        'before_json' => null,
                        'prior_run_id' => null,
                    ]);
                }
            }

            if ($runId !== null) {
                $pdo->prepare(
                    'UPDATE blog_import_runs SET created_count = :created_count, updated_count = :updated_count WHERE id = :id'
                )->execute([
                    'created_count' => count($createdPosts),
                    'updated_count' => count($updatedPosts),
                    'id' => $runId,
                ]);
            }

            $pdo->commit();

            return [
                'posts' => array_merge($createdPosts, $updatedPosts, $unchangedPosts),
                'categories' => $createdCategories,
                'slug_adjustments' => $slugAdjustments,
                'post_count' => count($createdPosts),
                'updated_count' => count($updatedPosts),
                'unchanged_count' => count($unchangedPosts),
                'category_count' => count($createdCategories),
                'run_id' => $runId,
                'profile_id' => $profileId,
                'publication_status' => $publicationStatus,
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

    /** Compare fields managed by the import; an empty image keeps the current one. */
    public static function importRowHasChanges(array $row, array $existing, string $publicationStatus = 'published'): bool
    {
        if (self::importPublicationHasChanges($existing, $publicationStatus)) {
            return true;
        }
        foreach (['title', 'excerpt', 'content', 'meta_title', 'meta_description'] as $field) {
            if ((string)($row[$field] ?? '') !== (string)($existing[$field] ?? '')) {
                return true;
            }
        }
        if ((string)($row['category_slug'] ?? '') !== (string)($existing['category_slug'] ?? '')) {
            return true;
        }
        $image = (string)($row['featured_image'] ?? '');
        return $image !== '' && $image !== (string)($existing['featured_image'] ?? '');
    }

    private static function importPublicationHasChanges(array $existing, string $publicationStatus): bool
    {
        if ((int)($existing['published'] ?? 0) !== ($publicationStatus === 'published' ? 1 : 0)) {
            return true;
        }
        if ($publicationStatus !== 'published' || empty($existing['published_at'])) {
            return false;
        }
        return new \DateTimeImmutable((string)$existing['published_at'], app_timezone())
            > new \DateTimeImmutable('now', app_timezone());
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

        if (isset($row['source_key']) && (!is_string($row['source_key'])
            || trim($row['source_key']) === ''
            || mb_strlen($row['source_key'], 'UTF-8') > 255)) {
            throw new \InvalidArgumentException('El lote contiene un identificador de importación inválido.');
        }
        if (isset($row['featured_image']) && (!is_string($row['featured_image'])
            || mb_strlen($row['featured_image'], 'UTF-8') > 255)) {
            throw new \InvalidArgumentException('El lote contiene una imagen destacada inválida.');
        }

        if (trim($row['title']) === ''
            || trim($row['base_slug']) === ''
            || (trim(strip_tags($row['content'])) === '' && !str_contains($row['content'], '<iframe'))
        ) {
            throw new \InvalidArgumentException(
                'El lote contiene un post sin título, slug o contenido.'
            );
        }
        if (mb_strlen($row['title'], 'UTF-8') > 255
            || mb_strlen($row['base_slug'], 'UTF-8') > 255
            || strlen($row['content']) > 65535
            || strlen($row['excerpt']) > 65535
            || mb_strlen((string)($row['meta_title'] ?? ''), 'UTF-8') > 255
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
