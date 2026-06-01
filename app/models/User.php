<?php

namespace App\Models;

use App\Core\Model;

class User extends Model
{
    protected static string $table = 'users';

    /**
     * Get all admin users.
     */
    public static function getAll(): array
    {
        return self::fetchAll(
            "SELECT id, username, email, created_at FROM " . self::$table . " ORDER BY id ASC"
        );
    }

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
     * Find a user by email.
     */
    public static function findByEmail(string $email): ?array
    {
        return self::fetch(
            "SELECT * FROM " . self::$table . " WHERE email = :email",
            ['email' => $email]
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
     * Create a user and return its ID.
     */
    public static function createUser(array $data): int
    {
        return self::insert(self::$table, [
            'username' => $data['username'],
            'email'    => $data['email'],
            'password' => password_hash($data['password'], PASSWORD_DEFAULT),
        ]);
    }

    /**
     * Update profile fields and optionally password.
     */
    public static function updateUser(int $id, array $data): int
    {
        $updateData = [
            'username' => $data['username'],
            'email'    => $data['email'],
        ];

        if (!empty($data['password'])) {
            $updateData['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        return self::update(self::$table, $updateData, 'id = :id', ['id' => $id]);
    }

    /**
     * Delete a user.
     */
    public static function deleteUser(int $id): int
    {
        return self::delete(self::$table, 'id = :id', ['id' => $id]);
    }

    /**
     * Count admin users.
     */
    public static function countAll(): int
    {
        $row = self::fetch("SELECT COUNT(*) AS total FROM " . self::$table);
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Verify password against hash.
     */
    public static function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }
}
