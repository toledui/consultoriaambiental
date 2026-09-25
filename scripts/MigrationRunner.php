<?php

final class MigrationRunner
{
    private PDO $pdo;
    private string $directory;

    public function __construct(PDO $pdo, string $directory)
    {
        $this->pdo = $pdo;
        $this->directory = $directory;
    }

    /** @param callable(string): void $log */
    public function run(callable $log): void
    {
        $files = glob($this->directory . '/*.sql');
        if ($files === false || $files === []) {
            throw new RuntimeException('No se encontraron migraciones SQL.');
        }
        sort($files, SORT_STRING);

        // MySQL 5.7 limits advisory lock names to 64 characters.
        $lockName = 'migrate:' . substr(hash('sha256', (string) $this->pdo->query('SELECT DATABASE()')->fetchColumn()), 0, 56);
        $lock = $this->pdo->prepare('SELECT GET_LOCK(?, 10)');
        $lock->execute([$lockName]);
        if ((int) $lock->fetchColumn() !== 1) {
            throw new RuntimeException('Otra ejecución de migraciones está en curso.');
        }

        try {
            $this->pdo->exec(
                'CREATE TABLE IF NOT EXISTS schema_migrations (' .
                'migration VARCHAR(255) NOT NULL PRIMARY KEY, ' .
                'checksum CHAR(64) NOT NULL, ' .
                'applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP' .
                ') ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci'
            );

            $completed = 0;
            foreach ($files as $file) {
                $name = basename($file);
                $sql = file_get_contents($file);
                if ($sql === false) {
                    throw new RuntimeException("No se pudo leer {$name}.");
                }
                $checksum = hash('sha256', $sql);
                $record = $this->pdo->prepare('SELECT checksum FROM schema_migrations WHERE migration = ?');
                $record->execute([$name]);
                $previous = $record->fetchColumn();
                if ($previous !== false) {
                    if (!hash_equals((string) $previous, $checksum)) {
                        throw new RuntimeException("{$name} cambió después de haberse aplicado. Crea una migración nueva.");
                    }
                    $log("Omitida: {$name}");
                    continue;
                }

                $log("Aplicando: {$name}");
                foreach (self::statements($sql) as $statement) {
                    $this->applyStatement($statement);
                }
                // MySQL DDL commits implicitly. Record success only after every statement succeeds.
                $insert = $this->pdo->prepare('INSERT INTO schema_migrations (migration, checksum) VALUES (?, ?)');
                $insert->execute([$name, $checksum]);
                $completed++;
            }
            $log("Listo: {$completed} migración(es) aplicada(s).");
        } finally {
            $release = $this->pdo->prepare('SELECT RELEASE_LOCK(?)');
            $release->execute([$lockName]);
        }
    }

    private function applyStatement(string $sql): void
    {
        if (preg_match('/^ALTER\s+TABLE\s+`?([a-zA-Z_][a-zA-Z0-9_]*)`?\s+(.+)$/is', $sql, $matches)) {
            $table = $matches[1];
            $pending = [];
            foreach (self::splitTopLevel($matches[2], ',') as $clause) {
                if (preg_match('/^ADD\s+(?:INDEX|KEY)\s+`?([a-zA-Z_][a-zA-Z0-9_]*)`?\s*\(/i', $clause, $part)) {
                    if (!$this->indexExists($table, $part[1])) {
                        $pending[] = $clause;
                    }
                } elseif (preg_match('/^ADD\s+CONSTRAINT\s+`?([a-zA-Z_][a-zA-Z0-9_]*)`?\s+/i', $clause, $part)) {
                    if (!$this->constraintExists($table, $part[1])) {
                        $pending[] = $clause;
                    }
                } elseif (preg_match('/^ADD\s+(?:COLUMN\s+)?`?([a-zA-Z_][a-zA-Z0-9_]*)`?\s+/i', $clause, $part)) {
                    if (!$this->columnExists($table, $part[1])) {
                        $pending[] = $clause;
                    }
                } else {
                    throw new RuntimeException("ALTER TABLE {$table} contiene una operación no reconocida: {$clause}");
                }
            }
            if ($pending !== []) {
                $this->pdo->exec('ALTER TABLE `' . $table . '` ' . implode(', ', $pending));
            }
            return;
        }

        if (preg_match('/^CREATE\s+INDEX\s+`?([a-zA-Z_][a-zA-Z0-9_]*)`?\s+ON\s+`?([a-zA-Z_][a-zA-Z0-9_]*)`?\s*\(/i', $sql, $matches)) {
            if ($this->indexExists($matches[2], $matches[1])) {
                return;
            }
        }

        $this->pdo->exec($sql);
    }

    private function columnExists(string $table, string $column): bool
    {
        return $this->exists(
            'SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ? LIMIT 1',
            [$table, $column]
        );
    }

    private function indexExists(string $table, string $index): bool
    {
        return $this->exists(
            'SELECT 1 FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ? LIMIT 1',
            [$table, $index]
        );
    }

    private function constraintExists(string $table, string $constraint): bool
    {
        return $this->exists(
            'SELECT 1 FROM information_schema.TABLE_CONSTRAINTS WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ? LIMIT 1',
            [$table, $constraint]
        );
    }

    /** @param list<string> $parameters */
    private function exists(string $sql, array $parameters): bool
    {
        $query = $this->pdo->prepare($sql);
        $query->execute($parameters);
        return $query->fetchColumn() !== false;
    }

    /** @return list<string> */
    public static function statements(string $sql): array
    {
        $clean = '';
        $quote = null;
        $length = strlen($sql);
        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            if ($quote !== null) {
                $clean .= $char;
                if ($char === '\\' && $i + 1 < $length) {
                    $clean .= $sql[++$i];
                } elseif ($char === $quote) {
                    if ($i + 1 < $length && $sql[$i + 1] === $quote) {
                        $clean .= $sql[++$i];
                    } else {
                        $quote = null;
                    }
                }
                continue;
            }
            if ($char === "'" || $char === '"' || $char === '`') {
                $quote = $char;
                $clean .= $char;
                continue;
            }
            if ($char === '#' || ($char === '-' && substr($sql, $i, 2) === '--' && ($i + 2 === $length || ctype_space($sql[$i + 2])))) {
                while ($i < $length && $sql[$i] !== "\n") {
                    $i++;
                }
                $clean .= "\n";
                continue;
            }
            if ($char === '/' && substr($sql, $i, 2) === '/*') {
                $end = strpos($sql, '*/', $i + 2);
                if ($end === false) {
                    throw new RuntimeException('Comentario SQL sin cerrar.');
                }
                $clean .= ' ';
                $i = $end + 1;
                continue;
            }
            $clean .= $char;
        }
        if ($quote !== null) {
            throw new RuntimeException('Cadena SQL sin cerrar.');
        }
        return self::splitTopLevel($clean, ';');
    }

    /** @return list<string> */
    private static function splitTopLevel(string $sql, string $separator): array
    {
        $parts = [];
        $buffer = '';
        $quote = null;
        $depth = 0;
        $length = strlen($sql);
        for ($i = 0; $i < $length; $i++) {
            $char = $sql[$i];
            if ($quote !== null) {
                $buffer .= $char;
                if ($char === '\\' && $i + 1 < $length) {
                    $buffer .= $sql[++$i];
                } elseif ($char === $quote) {
                    if ($i + 1 < $length && $sql[$i + 1] === $quote) {
                        $buffer .= $sql[++$i];
                    } else {
                        $quote = null;
                    }
                }
                continue;
            }
            if ($char === "'" || $char === '"' || $char === '`') {
                $quote = $char;
                $buffer .= $char;
            } elseif ($char === '(') {
                $depth++;
                $buffer .= $char;
            } elseif ($char === ')') {
                $depth--;
                $buffer .= $char;
            } elseif ($char === $separator && $depth === 0) {
                if (trim($buffer) !== '') {
                    $parts[] = trim($buffer);
                }
                $buffer = '';
            } else {
                $buffer .= $char;
            }
        }
        if (trim($buffer) !== '') {
            $parts[] = trim($buffer);
        }
        return $parts;
    }
}
