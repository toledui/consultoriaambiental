<?php

namespace App\Models;

use App\Core\Model;

class BlogImportProfile extends Model
{
    public static function all(): array
    {
        return self::fetchAll(
            "SELECT p.id, p.name, p.mapping_json, p.headers_json, p.created_at, p.updated_at,
                    p.source_filename, (p.source_records_json IS NOT NULL) AS has_source,
                    COALESCE(r.run_count, 0) AS run_count, r.last_run_at
             FROM blog_import_profiles p
             LEFT JOIN (
                 SELECT profile_id, COUNT(*) AS run_count, MAX(created_at) AS last_run_at
                 FROM blog_import_runs GROUP BY profile_id
             ) r ON r.profile_id = p.id
             ORDER BY p.updated_at DESC, p.id DESC"
        );
    }

    public static function findProfile(int $id): ?array
    {
        $profile = self::fetch('SELECT * FROM blog_import_profiles WHERE id = :id', ['id' => $id]);
        if ($profile === null) {
            return null;
        }
        $profile['mapping'] = json_decode((string)$profile['mapping_json'], true) ?: [];
        $profile['headers'] = json_decode((string)$profile['headers_json'], true) ?: [];
        return $profile;
    }

    public static function saveProfile(
        ?int $id,
        string $name,
        array $mapping,
        array $headers,
        int $adminId,
        ?array $sourceSnapshot = null
    ): int {
        $name = trim($name);
        if ($name === '' || mb_strlen($name, 'UTF-8') > 150) {
            throw new \RuntimeException('El nombre de la importación debe tener entre 1 y 150 caracteres.');
        }
        if ($mapping === [] || $headers === []) {
            throw new \RuntimeException('La plantilla necesita un archivo y un mapeo válidos.');
        }
        $mappingJson = json_encode($mapping, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $headersJson = json_encode($headers, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        $sourceJson = null;
        $sourceFilename = null;
        if ($sourceSnapshot !== null) {
            if ($headers !== ($sourceSnapshot['headers'] ?? null)
                || !is_array($sourceSnapshot['records'] ?? null)
                || $sourceSnapshot['records'] === []) {
                throw new \RuntimeException('El archivo guardado no corresponde a la plantilla.');
            }
            $sourceFilename = mb_strimwidth((string)($sourceSnapshot['name'] ?? ''), 0, 255, '', 'UTF-8');
            $sourceJson = json_encode($sourceSnapshot['records'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
        }
        if ($id !== null) {
            if (self::findProfile($id) === null) {
                throw new \RuntimeException('La importación guardada ya no existe.');
            }
            $updates = ['name' => $name, 'mapping_json' => $mappingJson, 'headers_json' => $headersJson, 'id' => $id];
            $sql = 'UPDATE blog_import_profiles SET name = :name, mapping_json = :mapping_json, headers_json = :headers_json';
            if ($sourceSnapshot !== null) {
                $sql .= ', source_filename = :source_filename, source_records_json = :source_records_json';
                $updates['source_filename'] = $sourceFilename;
                $updates['source_records_json'] = $sourceJson;
            }
            self::query($sql . ' WHERE id = :id', $updates);
            return $id;
        }
        return self::insert('blog_import_profiles', [
            'name' => $name,
            'mapping_json' => $mappingJson,
            'headers_json' => $headersJson,
            'source_filename' => $sourceFilename,
            'source_records_json' => $sourceJson,
            'created_by' => $adminId,
        ]);
    }

    public static function savedSource(array $profile): ?array
    {
        if (empty($profile['source_records_json'])) {
            return null;
        }
        $records = json_decode((string)$profile['source_records_json'], true);
        if (!is_array($records) || $records === []) {
            throw new \RuntimeException('El archivo guardado de esta plantilla no se puede leer.');
        }
        return [
            'name' => (string)($profile['source_filename'] ?: 'archivo-guardado.csv'),
            'headers' => $profile['headers'],
            'records' => $records,
        ];
    }

    public static function runs(int $profileId): array
    {
        return self::fetchAll(
            'SELECT * FROM blog_import_runs WHERE profile_id = :profile_id ORDER BY id DESC',
            ['profile_id' => $profileId]
        );
    }

    public static function matchedRecords(int $profileId): array
    {
        $rows = self::fetchAll(
            'SELECT r.source_key_hash, p.id, p.slug, p.title, p.excerpt, p.content,
                    p.featured_image, p.meta_title, p.meta_description, p.published, p.published_at,
                    c.slug AS category_slug, c.name AS category_name
             FROM blog_import_records r
             JOIN blog_posts p ON p.id = r.post_id
             LEFT JOIN blog_categories c ON c.id = p.category_id
             WHERE r.profile_id = :profile_id',
            ['profile_id' => $profileId]
        );
        $matched = [];
        foreach ($rows as $row) {
            $matched[(string)$row['source_key_hash']] = $row;
        }
        return $matched;
    }

    public static function findRun(int $runId): ?array
    {
        return self::fetch(
            'SELECT r.*, p.name AS profile_name FROM blog_import_runs r
             JOIN blog_import_profiles p ON p.id = r.profile_id WHERE r.id = :id',
            ['id' => $runId]
        );
    }

    /** Undo the latest active run atomically: delete created posts and restore updated posts. */
    public static function undoRun(int $runId, int $adminId): array
    {
        $pdo = self::getDB();
        $pdo->beginTransaction();
        try {
            $runStatement = $pdo->prepare('SELECT * FROM blog_import_runs WHERE id = :id FOR UPDATE');
            $runStatement->execute(['id' => $runId]);
            $run = $runStatement->fetch();
            if (!$run || $run['status'] !== 'completed') {
                throw new \RuntimeException('La ejecución no existe o ya se deshizo.');
            }
            $latestStatement = $pdo->prepare(
                "SELECT id FROM blog_import_runs WHERE profile_id = :profile_id AND status = 'completed' ORDER BY id DESC LIMIT 1 FOR UPDATE"
            );
            $latestStatement->execute(['profile_id' => $run['profile_id']]);
            if ((int)$latestStatement->fetchColumn() !== $runId) {
                throw new \RuntimeException('Primero debes deshacer la ejecución más reciente de esta importación.');
            }

            $itemsStatement = $pdo->prepare('SELECT * FROM blog_import_run_items WHERE run_id = :run_id ORDER BY id DESC');
            $itemsStatement->execute(['run_id' => $runId]);
            $items = $itemsStatement->fetchAll();
            $deletePost = $pdo->prepare('DELETE FROM blog_posts WHERE id = :id');
            $existingPost = $pdo->prepare('SELECT id, published, published_at FROM blog_posts WHERE id = :id FOR UPDATE');
            $restorePost = $pdo->prepare(
                'UPDATE blog_posts SET title = :title, excerpt = :excerpt, content = :content,
                 featured_image = :featured_image,
                 category_id = :category_id, meta_title = :meta_title, meta_description = :meta_description,
                 published = :published, published_at = :published_at
                 WHERE id = :id'
            );
            $restoreRecord = $pdo->prepare(
                'UPDATE blog_import_records SET last_run_id = :prior_run_id
                 WHERE profile_id = :profile_id AND source_key_hash = :source_key_hash AND post_id = :post_id'
            );
            $deleted = 0;
            $restored = 0;
            foreach ($items as $item) {
                $postId = (int)$item['post_id'];
                if ($item['action'] === 'created') {
                    $deletePost->execute(['id' => $postId]);
                    $deleted += $deletePost->rowCount();
                    continue;
                }
                $before = json_decode((string)$item['before_json'], true, 512, JSON_THROW_ON_ERROR);
                if (!is_array($before)) {
                    throw new \RuntimeException('No se pudo restaurar el post ' . $postId . '.');
                }
                $existingPost->execute(['id' => $postId]);
                $currentPost = $existingPost->fetch();
                if ($currentPost === false) {
                    throw new \RuntimeException('No se puede deshacer porque el post ' . $postId . ' ya no existe.');
                }
                $restorePost->execute([
                    'title' => $before['title'],
                    'excerpt' => $before['excerpt'],
                    'content' => $before['content'],
                    'featured_image' => $before['featured_image'] ?? '',
                    'category_id' => $before['category_id'],
                    'meta_title' => $before['meta_title'],
                    'meta_description' => $before['meta_description'],
                    'published' => $before['published'] ?? $currentPost['published'],
                    'published_at' => array_key_exists('published_at', $before)
                        ? $before['published_at'] : $currentPost['published_at'],
                    'id' => $postId,
                ]);
                $restored += $restorePost->rowCount();
                $restoreRecord->execute([
                    'prior_run_id' => $item['prior_run_id'],
                    'profile_id' => $run['profile_id'],
                    'source_key_hash' => $item['source_key_hash'],
                    'post_id' => $postId,
                ]);
            }
            $pdo->prepare(
                "UPDATE blog_import_runs SET status = 'undone', undone_at = NOW(), undone_by = :admin_id WHERE id = :id"
            )->execute(['admin_id' => $adminId, 'id' => $runId]);
            $pdo->commit();
            return [
                'profile_id' => (int)$run['profile_id'],
                'deleted_count' => $deleted,
                'restored_count' => $restored,
            ];
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
}
