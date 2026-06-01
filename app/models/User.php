<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static string $table = 'users';

    /**
     * Find a user by username.
     */
    public static function findByUsername(string $username): ?array
    {
        return self::fetch(
            "SELECT * FROM " . self::$table . " WHERE username = :username",
            ['username' => $username]
        );
    }

    /**
     * Find a user by ID.
     */
    public static function findById(int $id): ?array
    {
        return self::fetch(
            "SELECT * FROM " . self::$table . " WHERE id = :id",
            ['id' => $id]
        );
    }

    /**
     * Verify password against hash.
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
