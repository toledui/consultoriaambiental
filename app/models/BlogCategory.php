<?php

namespace App\Models;

use App\Core\Model;

class BlogCategory extends Model
{
    protected static string $table = 'blog_categories';

    /**
     * Get all categories ordered by name.
     */
    public static function getAll(): array
    {
        return self::fetchAll(
            "SELECT * FROM " . self::$table . " ORDER BY name ASC"
        );
    }

    /**
     * Get all categories with post count.
     */
    public static function getAllWithCount(bool $publicOnly = false): array
    {
        $joinCondition = 'p.category_id = c.id';
        $params = [];

        if ($publicOnly) {
            $joinCondition .= ' AND p.published = 1 AND (p.published_at IS NULL OR p.published_at <= :now)';
            $params['now'] = (new \DateTimeImmutable('now', new \DateTimeZone('America/Mexico_City')))->format('Y-m-d H:i:s');
        }

        return self::fetchAll(
            "SELECT c.*, COUNT(p.id) as post_count
             FROM " . self::$table . " c
             LEFT JOIN blog_posts p ON " . $joinCondition . "
             GROUP BY c.id
             ORDER BY c.name ASC",
            $params
        );
    }

    /**
     * Find a category by ID.
     */
    public static function findById(int $id): ?array
    {
        return self::fetch(
            "SELECT * FROM " . self::$table . " WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Find a category by slug.
     */
    public static function findBySlug(string $slug): ?array
    {
        return self::fetch(
            "SELECT * FROM " . self::$table . " WHERE slug = :slug",
            ['slug' => $slug]
        );
    }

    /**
     * Create a new category.
     */
    public static function createCategory(array $data): int
    {
        return self::insert(self::$table, $data);
    }

    /**
     * Update an existing category.
     */
    public static function updateCategory(int $id, array $data): int
    {
        return self::update(self::$table, $data, 'id = :id', ['id' => $id]);
    }

    /**
     * Delete a category.
     */
    public static function deleteCategory(int $id): int
    {
        // Set blog posts in this category to null first
        self::query(
            "UPDATE blog_posts SET category_id = NULL WHERE category_id = :id",
            ['id' => $id]
        );
        return self::delete(self::$table, 'id = :id', ['id' => $id]);
    }

    /**
     * Generate a URL-friendly slug from a name.
     */
    public static function generateSlug(string $name): string
    {
        $slug = \url_slug($name);
        return $slug !== '' ? $slug : 'categoria';
    }
}
