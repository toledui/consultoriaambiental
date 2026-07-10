<?php

namespace App\Core;

use PDO;
use PDOException;

abstract class Model
{
    protected static ?PDO $pdo = null;

    /**
     * Get the PDO connection (singleton).
     */
    protected static function getDB(): PDO
    {
        if (self::$pdo === null) {
            $config = require CONFIG_DIR . '/database.php';

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                $config['host'],
                $config['port'],
                $config['dbname'],
                $config['charset']
            );

            try {
                self::$pdo = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false,
                ]);

                self::$pdo->exec("SET time_zone = " . self::$pdo->quote(\app_timezone_offset()));
            } catch (PDOException $e) {
                if (APP_DEBUG) {
                    die('Database connection failed: ' . $e->getMessage());
                }
                die('Database connection failed.');
            }
        }

        return self::$pdo;
    }

    /**
     * Execute a query with params and return the statement.
     */
    protected static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single row.
     */
    protected static function fetch(string $sql, array $params = []): ?array
    {
        $result = self::query($sql, $params)->fetch();
        return $result ?: null;
    }

    /**
     * Fetch all rows.
     */
    protected static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    /**
     * Insert a row and return the last insert ID.
     */
    protected static function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));

        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        self::query($sql, $data);

        return (int) self::getDB()->lastInsertId();
    }

    /**
     * Update rows and return the number of affected rows.
     */
    protected static function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $sets = implode(', ', array_map(fn($col) => "{$col} = :{$col}", array_keys($data)));

        $sql = "UPDATE {$table} SET {$sets} WHERE {$where}";
        $stmt = self::getDB()->prepare($sql);
        $stmt->execute(array_merge($data, $whereParams));

        return $stmt->rowCount();
    }

    /**
     * Delete rows and return the number of affected rows.
     */
    protected static function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM {$table} WHERE {$where}";
        $stmt = self::query($sql, $params);
        return $stmt->rowCount();
    }
}
