<?php

namespace App\Models;

use App\Core\Model;

class Contact extends Model
{
    protected static string $table = 'contacts';

    /**
     * Get all contacts, newest first.
     */
    public static function getAll(): array
    {
        return self::fetchAll(
            "SELECT * FROM " . self::$table . " ORDER BY created_at DESC"
        );
    }

    /**
     * Get a single contact by ID.
     */
    public static function getById(int $id): ?array
    {
        return self::fetch(
            "SELECT * FROM " . self::$table . " WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Create a new contact submission.
     */
    public static function create(array $data): int
    {
        return self::insert(self::$table, [
            'nombre'     => $data['nombre'],
            'correo'     => $data['correo'],
            'telefono'   => $data['telefono'],
            'sector'     => $data['sector'] ?? null,
            'mensaje'    => $data['mensaje'],
            'newsletter' => !empty($data['newsletter']) ? 1 : 0,
        ]);
    }

    /**
     * Mark a contact as read.
     */
    public static function markAsRead(int $id): void
    {
        self::query(
            "UPDATE " . self::$table . " SET read_at = NOW() WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Delete a contact by ID.
     */
    public static function deleteById(int $id): void
    {
        self::query(
            "DELETE FROM " . self::$table . " WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Get unread count.
     */
    public static function getUnreadCount(): int
    {
        $row = self::fetch(
            "SELECT COUNT(*) AS count FROM " . self::$table . " WHERE read_at IS NULL"
        );
        return (int)($row['count'] ?? 0);
    }
}
