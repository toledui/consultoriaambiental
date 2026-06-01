<?php

namespace App\Models;

use App\Core\Model;

class MediaFile extends Model
{
    protected static string $table = 'media_files';

    /**
     * Get all media files, newest first.
     */
    public static function getAll(): array
    {
        return self::fetchAll(
            "SELECT * FROM " . self::$table . " ORDER BY created_at DESC"
        );
    }

    /**
     * Find a media file by ID.
     */
    public static function getById(int $id): ?array
    {
        return self::fetch(
            "SELECT * FROM " . self::$table . " WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Get media files filtered by type.
     */
    public static function getByType(string $type): array
    {
        return self::fetchAll(
            "SELECT * FROM " . self::$table . " WHERE type = :type ORDER BY created_at DESC",
            ['type' => $type]
        );
    }

    /**
     * Insert a new media file record.
     */
    public static function create(array $data): int
    {
        return self::insert(self::$table, $data);
    }

    /**
     * Delete a media file record by ID.
     */
    public static function deleteById(int $id): void
    {
        self::delete(self::$table, 'id = :id', ['id' => $id]);
    }

    /**
     * Update alt text for a media file.
     */
    public static function updateAltText(int $id, string $altText): int
    {
        return self::update(
            self::$table,
            ['alt_text' => $altText],
            'id = :id',
            ['id' => $id]
        );
    }

    /**
     * Format bytes into human-readable string.
     */
    public static function formatSize(int $bytes): string
    {
        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 1) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }
        return $bytes . ' B';
    }

    /**
     * Get the URL path for a media file.
     */
    public static function getUrl(array $file): string
    {
        return BASE_URL . '/' . $file['path'];
    }

    /**
     * Get thumbnail URL for a media file (for images, show the image itself; for docs, return null).
     */
    public static function getThumbnailUrl(array $file): ?string
    {
        if (strpos($file['mime_type'], 'image') === 0) {
            return self::getUrl($file);
        }
        return null;
    }

    /**
     * Get FontAwesome icon class based on mime type.
     */
    public static function getFileIcon(array $file): string
    {
        $mime = $file['mime_type'] ?? '';
        if (strpos($mime, 'image') === 0) return 'fas fa-file-image text-blue-500';
        if (strpos($mime, 'pdf') !== false) return 'fas fa-file-pdf text-red-500';
        if (strpos($mime, 'word') !== false || strpos($mime, 'document') !== false) return 'fas fa-file-word text-blue-700';
        if (strpos($mime, 'excel') !== false || strpos($mime, 'spreadsheet') !== false) return 'fas fa-file-excel text-green-600';
        return 'fas fa-file text-gray-400';
    }
}
