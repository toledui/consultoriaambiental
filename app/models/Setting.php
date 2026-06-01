<?php

namespace App\Models;

use App\Core\Model;

class Setting extends Model
{
    protected static string $table = 'settings';

    /**
     * Get a setting value by key.
     */
    public static function get(string $key, mixed $default = null): mixed
    {
        $row = self::fetch(
            "SELECT `value` FROM " . self::$table . " WHERE `key` = :key",
            ['key' => $key]
        );
        return $row ? $row['value'] : $default;
    }

    /**
     * Set a setting value by key.
     */
    public static function set(string $key, mixed $value): void
    {
        self::query(
            "INSERT INTO " . self::$table . " (`key`, `value`) VALUES (:key, :value)
             ON DUPLICATE KEY UPDATE `value` = :value2",
            ['key' => $key, 'value' => $value, 'value2' => $value]
        );
    }

    /**
     * Get all settings as key => value array.
     */
    public static function getAll(): array
    {
        $rows = self::fetchAll("SELECT `key`, `value` FROM " . self::$table);
        $result = [];
        foreach ($rows as $row) {
            $result[$row['key']] = $row['value'];
        }
        return $result;
    }

    /**
     * Update multiple settings at once.
     */
    public static function setMultiple(array $data): void
    {
        foreach ($data as $key => $value) {
            self::set($key, $value);
        }
    }
}
