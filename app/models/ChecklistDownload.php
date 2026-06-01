<?php

namespace App\Models;

use App\Core\Model;

class ChecklistDownload extends Model
{
    protected static string $table = 'checklist_downloads';

    /**
     * Get all downloads, newest first.
     */
    public static function getAll(): array
    {
        return self::fetchAll(
            "SELECT * FROM " . self::$table . " ORDER BY created_at DESC"
        );
    }

    /**
     * Create a new checklist download lead record.
     */
    public static function create(array $data): int
    {
        return self::insert(self::$table, [
            'nombre'     => $data['nombre'],
            'apellidos'  => $data['apellidos'],
            'correo'     => $data['correo'],
            'empresa'    => $data['empresa'] ?? null,
            'giro'       => $data['giro'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
        ]);
    }

    /**
     * Delete a download record by ID.
     */
    public static function deleteById(int $id): void
    {
        self::query(
            "DELETE FROM " . self::$table . " WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Get total count.
     */
    public static function getCount(): int
    {
        $row = self::fetch(
            "SELECT COUNT(*) AS count FROM " . self::$table
        );
        return (int)($row['count'] ?? 0);
    }
}
