<?php

namespace App\Models;

use App\Core\Model;

class Service extends Model
{
    protected static string $table = 'services';

    /**
     * Get all published services, ordered by sort_order.
     */
    public static function getPublished(): array
    {
        return self::fetchAll(
            "SELECT * FROM " . self::$table . " WHERE published = 1 ORDER BY sort_order ASC"
        );
    }

    /**
     * Get all services (for admin).
     */
    public static function getAll(): array
    {
        return self::fetchAll(
            "SELECT * FROM " . self::$table . " ORDER BY sort_order ASC"
        );
    }

    /**
     * Find a service by slug.
     */
    public static function findBySlug(string $slug): ?array
    {
        return self::fetch(
            "SELECT * FROM " . self::$table . " WHERE slug = :slug",
            ['slug' => $slug]
        );
    }

    /**
     * Find a service by ID.
     */
    public static function findById(int $id): ?array
    {
        return self::fetch(
            "SELECT * FROM " . self::$table . " WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Create a new service.
     */
    public static function createService(array $data): int
    {
        return self::insert(self::$table, $data);
    }

    /**
     * Update an existing service.
     */
    public static function updateService(int $id, array $data): int
    {
        return self::update(self::$table, $data, 'id = :id', ['id' => $id]);
    }

    /**
     * Delete a service.
     */
    public static function deleteService(int $id): int
    {
        return self::delete(self::$table, 'id = :id', ['id' => $id]);
    }

    /**
     * Generate a URL-friendly slug from a title.
     */
    public static function generateSlug(string $title): string
    {
        $slug = \url_slug($title);
        return $slug !== '' ? $slug : 'servicio';
    }
}
