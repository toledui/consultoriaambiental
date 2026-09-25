<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogImportProfile;
use App\Models\MediaFile;
use App\Models\User;
use App\Services\BlogCsvImporter;

class BlogController extends Controller
{
    // ─── Posts CRUD ──────────────────────────────────────────────────

    public function index(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $posts = BlogPost::getAll();
        $users = User::getAll();

        $this->view('admin/blog/index', [
            'title' => 'Administrar Blog',
            'posts' => $posts,
            'users' => $users,
            'quickEditCsrfToken' => $this->quickEditCsrfToken(),
        ], 'admin');
    }

    public function create(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $categories = BlogCategory::getAll();

        $this->view('admin/blog/create', [
            'title'      => 'Nuevo Artículo',
            'categories' => $categories,
        ], 'admin');
    }

    public function store(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $title   = $_POST['title'] ?? '';
        $slug    = $this->normalizePostSlug($_POST['slug'] ?? '', $title);
        $excerpt = $_POST['excerpt'] ?? '';
        $content = $_POST['content'] ?? '';
        $published = isset($_POST['published']) ? 1 : 0;
        $publishedAt = $this->normalizePublishedAt($_POST['published_at'] ?? '');
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;

        try {
            $image = $this->resolveFeaturedImage();
        } catch (\RuntimeException $e) {
            $_SESSION['flash_message'] = $e->getMessage();
            $_SESSION['flash_type'] = 'error';
            $this->redirectBack();
        }

        // SEO fields
        $metaTitle       = $_POST['meta_title'] ?? '';
        $metaDescription = $_POST['meta_description'] ?? '';
        $jsonLd          = $_POST['json_ld'] ?? '';

        // Ensure unique slug
        $existing = BlogPost::findBySlug($slug);
        if ($existing) {
            $slug = $slug . '-' . time();
        }

        BlogPost::createPost([
            'title'            => $title,
            'slug'             => $slug,
            'excerpt'          => $excerpt,
            'content'          => $content,
            'featured_image'   => $image,
            'published'        => $published,
            'published_at'     => $publishedAt,
            'category_id'      => $categoryId,
            'author_id'        => (int)$_SESSION['admin_id'],
            'meta_title'       => $metaTitle,
            'meta_description' => $metaDescription,
            'json_ld'          => $jsonLd,
        ]);

        $this->redirect(BASE_URL . '/admin/blog');
    }

    public function edit(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $post = BlogPost::findById($id);

        if (!$post) {
            $this->redirect(BASE_URL . '/admin/blog');
        }

        $categories = BlogCategory::getAll();
        $users = User::getAll();

        $this->view('admin/blog/edit', [
            'title'      => 'Editar Artículo',
            'post'       => $post,
            'categories' => $categories,
            'users'      => $users,
        ], 'admin');
    }

    public function update(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $post = BlogPost::findById($id);
        if (!$post) {
            $this->redirect(BASE_URL . '/admin/blog');
        }

        $title   = $_POST['title'] ?? '';
        $slug    = $this->normalizePostSlug($_POST['slug'] ?? '', $title);
        $excerpt = $_POST['excerpt'] ?? '';
        $content = $_POST['content'] ?? '';
        $published = isset($_POST['published']) ? 1 : 0;
        $publishedAt = $this->normalizePublishedAt($_POST['published_at'] ?? '');
        $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
        $authorId = $this->normalizeAuthorId($_POST['author_id'] ?? null);

        try {
            $image = $this->resolveFeaturedImage($post['featured_image'] ?? '');
        } catch (\RuntimeException $e) {
            $_SESSION['flash_message'] = $e->getMessage();
            $_SESSION['flash_type'] = 'error';
            $this->redirectBack();
        }

        // SEO fields
        $metaTitle       = $_POST['meta_title'] ?? '';
        $metaDescription = $_POST['meta_description'] ?? '';
        $jsonLd          = $_POST['json_ld'] ?? '';

        // Check slug uniqueness (exclude current)
        $existing = BlogPost::findBySlug($slug);
        if ($existing && (int)$existing['id'] !== $id) {
            $slug = $slug . '-' . time();
        }

        BlogPost::updatePost($id, [
            'title'            => $title,
            'slug'             => $slug,
            'excerpt'          => $excerpt,
            'content'          => $content,
            'featured_image'   => $image,
            'published'        => $published,
            'published_at'     => $publishedAt,
            'category_id'      => $categoryId,
            'author_id'        => $authorId,
            'meta_title'       => $metaTitle,
            'meta_description' => $metaDescription,
            'json_ld'          => $jsonLd,
        ]);

        $this->redirect(BASE_URL . '/admin/blog');
    }

    public function destroy(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        BlogPost::deletePost($id);
        $this->redirect(BASE_URL . '/admin/blog');
    }

    public function quickUpdate(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        if (!$this->verifyQuickEditCsrfToken((string)($_POST['csrf_token'] ?? ''))) {
            $_SESSION['flash_message'] = 'La sesión de edición rápida expiró. Recarga la página.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/blog');
        }

        $post = BlogPost::findById($id);
        if (!$post) {
            $_SESSION['flash_message'] = 'El artículo ya no existe.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/blog');
        }

        $status = (string)($_POST['status'] ?? '');
        if (!in_array($status, ['draft', 'published', 'scheduled'], true)) {
            $_SESSION['flash_message'] = 'Selecciona un estado válido.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/blog');
        }

        $publishedAtInput = trim((string)($_POST['published_at'] ?? ''));
        $publishedAt = $this->normalizePublishedAt($publishedAtInput);
        if ($publishedAtInput !== '' && $publishedAt === null) {
            $_SESSION['flash_message'] = 'La fecha de publicación no es válida.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/blog');
        }

        $now = new \DateTimeImmutable('now', app_timezone());
        $publicationDate = $publishedAt !== null
            ? new \DateTimeImmutable($publishedAt, app_timezone())
            : null;

        if ($status === 'scheduled'
            && (!$publicationDate || $publicationDate <= $now)
        ) {
            $_SESSION['flash_message'] = 'Un post programado necesita una fecha futura.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/blog');
        }

        if ($status === 'published'
            && $publicationDate
            && $publicationDate > $now
        ) {
            $_SESSION['flash_message'] = 'Para usar una fecha futura selecciona el estado Programado.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/blog');
        }

        $authorInput = trim((string)($_POST['author_id'] ?? ''));
        $authorId = $authorInput === '' ? null : $this->normalizeAuthorId($authorInput);
        if ($authorInput !== '' && $authorId === null) {
            $_SESSION['flash_message'] = 'El autor seleccionado no es válido.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/blog');
        }

        BlogPost::updatePost($id, [
            'published' => $status === 'draft' ? 0 : 1,
            'published_at' => $publishedAt,
            'author_id' => $authorId,
        ]);

        $_SESSION['flash_message'] = 'Estado, fecha y autor actualizados correctamente.';
        $_SESSION['flash_type'] = 'success';
        $this->redirect(BASE_URL . '/admin/blog');
    }

    public function importForm(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $importer = new BlogCsvImporter();
        $importer->cleanupExpiredBatches();
        $profileId = isset($_GET['profile']) && ctype_digit((string)$_GET['profile'])
            ? (int)$_GET['profile']
            : null;
        $profile = $profileId !== null ? BlogImportProfile::findProfile($profileId) : null;
        if ($profileId !== null && $profile === null) {
            $_SESSION['flash_message'] = 'La importación guardada no existe.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/blog/importaciones');
        }

        $source = null;
        $mapping = [];
        $globalError = null;
        if ($profile !== null && ($_GET['reuse'] ?? '') === '1') {
            try {
                $snapshot = BlogImportProfile::savedSource($profile);
                if ($snapshot === null) {
                    throw new \RuntimeException('Esta plantilla todavía no tiene un archivo guardado. Sube un CSV o Excel una vez para poder repetirlo.');
                }
                $source = $importer->prepareStoredSource($snapshot, (int)$_SESSION['admin_id'], $profileId);
                $mapping = $profile['mapping'];
            } catch (\Throwable $e) {
                $globalError = $this->importErrorMessage($e);
            }
        }

        $this->view('admin/blog/import', [
            'title' => 'Importar Artículos',
            'csrfToken' => $importer->csrfToken(),
            'preview' => null,
            'source' => $source,
            'mapping' => $mapping,
            'profile' => $profile,
            'profileName' => $profile['name'] ?? '',
            'categoryChoices' => [],
            'batchToken' => null,
            'globalError' => $globalError,
        ], 'admin');
    }

    public function previewImport(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $importer = new BlogCsvImporter();
        $csrfToken = (string)($_POST['csrf_token'] ?? '');
        if (!$importer->verifyCsrfToken($csrfToken)) {
            http_response_code(403);
            $this->renderImportPage(
                $importer,
                null,
                null,
                'La sesión del formulario expiró. Recarga la página e inténtalo nuevamente.'
            );
            return;
        }

        try {
            $sourceToken = (string)($_POST['source_token'] ?? '');
            if ($sourceToken === '') {
                $profileId = isset($_POST['profile_id']) && ctype_digit((string)$_POST['profile_id'])
                    ? (int)$_POST['profile_id']
                    : null;
                $profile = $profileId !== null ? BlogImportProfile::findProfile($profileId) : null;
                if ($profileId !== null && $profile === null) {
                    throw new \RuntimeException('La importación guardada ya no existe.');
                }
                $source = $importer->prepareUploadedFile(
                    $_FILES['source_file'] ?? [],
                    (int)$_SESSION['admin_id'],
                    $profileId
                );
                $mapping = $profile['mapping'] ?? [];
                if ($mapping === []) {
                    foreach (BlogCsvImporter::postMappingFields() as $field) {
                        $index = array_search($field, $source['headers'], true);
                        $mapping[$field] = $index === false ? '' : $source['column_tokens'][$index];
                    }
                }
                $this->renderImportPage($importer, null, null, null, $source, $mapping, [], $profile['name'] ?? '');
                return;
            }
            $source = $importer->getPreparedSource($sourceToken, (int)$_SESSION['admin_id']);
            $mapping = is_array($_POST['mapping'] ?? null) ? $_POST['mapping'] : [];
            $profileName = $this->importProfileName($_POST['profile_name'] ?? '', $source['name']);
            $categories = BlogCategory::getAll();
            $categoryOverrides = is_array($_POST['category_overrides'] ?? null)
                ? $_POST['category_overrides']
                : [];
            $bulkCategory = null;
            if (isset($_POST['use_source_categories'])) {
                $categoryOverrides = [];
            } elseif (isset($_POST['apply_bulk_category'])) {
                if (!is_string($_POST['bulk_category'] ?? null)) {
                    throw new \RuntimeException('Indica una categoría válida para aplicar a todos.');
                }
                $bulkCategory = (string)$_POST['bulk_category'];
            }
            $preservedCategories = [];
            if (!empty($source['reused']) && $source['profile_id'] !== null
                && !isset($_POST['use_source_categories'])) {
                foreach (BlogImportProfile::matchedRecords((int)$source['profile_id']) as $hash => $matchedPost) {
                    $preservedCategories[$hash] = (string)($matchedPost['category_name'] ?? '');
                }
            }
            $preview = $importer->previewMappedSource(
                $sourceToken,
                (int)$_SESSION['admin_id'],
                $mapping,
                BlogPost::getAllSlugs(),
                $categories,
                $categoryOverrides,
                $bulkCategory,
                $preservedCategories
            );
            $matchedRecords = $source['profile_id'] !== null
                ? BlogImportProfile::matchedRecords((int)$source['profile_id'])
                : [];
            $preview['status_counts'] = [
                'published' => ['create_count' => 0, 'update_count' => 0, 'unchanged_count' => 0],
                'draft' => ['create_count' => 0, 'update_count' => 0, 'unchanged_count' => 0],
            ];
            foreach ($preview['rows'] as &$previewRow) {
                $sourceKey = (string)($previewRow['data']['source_key'] ?? '');
                $matched = $sourceKey !== '' ? ($matchedRecords[hash('sha256', $sourceKey)] ?? null) : null;
                $previewRow['import_actions'] = [];
                foreach (['published', 'draft'] as $status) {
                    $action = $matched === null
                        ? 'created'
                        : (BlogPost::importRowHasChanges((array)($previewRow['data'] ?? []), $matched, $status) ? 'updated' : 'unchanged');
                    $previewRow['import_actions'][$status] = $action;
                    if ($previewRow['errors'] === []) {
                        $preview['status_counts'][$status][match ($action) {
                            'updated' => 'update_count',
                            'unchanged' => 'unchanged_count',
                            default => 'create_count',
                        }]++;
                    }
                }
                $previewRow['import_action'] = $previewRow['import_actions']['published'];
                $previewRow['existing_slug'] = $matched['slug'] ?? null;
                if ($matched !== null) {
                    $previewRow['slug'] = $matched['slug'];
                    $previewRow['warnings'] = array_values(array_filter(
                        $previewRow['warnings'],
                        static fn(string $warning): bool => !str_starts_with($warning, 'El slug se ajustó de ')
                    ));
                }
            }
            unset($previewRow);
            foreach ($preview['status_counts']['published'] as $key => $count) {
                $preview[$key] = $count;
            }
            $preview['warning_count'] = array_sum(array_map(
                static fn(array $row): int => count($row['warnings']),
                $preview['rows']
            ));
            $batchToken = null;
            if ($preview['can_import']) {
                $batchToken = $importer->createBatch(
                    $preview,
                    (int)$_SESSION['admin_id'],
                    [
                        'profile_id' => $source['profile_id'],
                        'profile_name' => $profileName,
                        'mapping' => $mapping,
                        'headers' => $source['headers'],
                        'source_name' => $source['name'],
                        'source_snapshot' => $importer->getPreparedSourceData($source['token'], (int)$_SESSION['admin_id']),
                        'status_aware_preview' => true,
                    ]
                );
            } else {
                http_response_code(422);
            }

            $this->renderImportPage($importer, $preview, $batchToken, null, $source, $mapping, $categories, $profileName);
        } catch (\Throwable $e) {
            http_response_code(422);
            $source = null;
            $mapping = is_array($_POST['mapping'] ?? null) ? $_POST['mapping'] : [];
            if (!empty($_POST['source_token'])) {
                try {
                    $source = $importer->getPreparedSource((string)$_POST['source_token'], (int)$_SESSION['admin_id']);
                } catch (\Throwable) {
                    // Expired uploads must be selected again.
                }
            }
            $this->renderImportPage(
                $importer,
                null,
                null,
                $this->importErrorMessage($e),
                $source,
                $mapping,
                [],
                is_string($_POST['profile_name'] ?? null) ? $_POST['profile_name'] : ''
            );
        }
    }

    public function confirmImport(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $importer = new BlogCsvImporter();
        $csrfToken = (string)($_POST['csrf_token'] ?? '');
        if (!$importer->verifyCsrfToken($csrfToken)) {
            http_response_code(403);
            $this->renderImportPage(
                $importer,
                null,
                null,
                'La sesión del formulario expiró. Vuelve a previsualizar el archivo.'
            );
            return;
        }

        try {
            $publicationStatus = (string)($_POST['publication_status'] ?? 'published');
            if (!in_array($publicationStatus, ['published', 'draft'], true)) {
                throw new \RuntimeException('Selecciona un estado de publicación válido.');
            }
            $batch = $importer->consumeBatchPayload(
                (string)($_POST['batch_token'] ?? ''),
                (int)$_SESSION['admin_id']
            );
            $context = is_array($batch['context'] ?? null) ? $batch['context'] : [];
            if (($context['status_aware_preview'] ?? false) !== true) {
                throw new \RuntimeException('Esta vista previa es anterior al cambio de estado. Vuelve a previsualizar la importación guardada antes de ejecutarla.');
            }
            $sourceSnapshot = is_array($context['source_snapshot'] ?? null)
                ? $context['source_snapshot']
                : null;
            if ($sourceSnapshot === null) {
                throw new \RuntimeException('La vista previa no conserva el archivo original. Vuelve a previsualizarlo.');
            }
            $profileId = BlogImportProfile::saveProfile(
                isset($context['profile_id']) ? (int)$context['profile_id'] : null,
                (string)($context['profile_name'] ?? 'Importación del blog'),
                (array)($context['mapping'] ?? []),
                (array)($context['headers'] ?? []),
                (int)$_SESSION['admin_id'],
                $sourceSnapshot
            );
            $result = BlogPost::importBatch(
                $batch['rows'],
                (int)$_SESSION['admin_id'],
                $profileId,
                (string)($context['source_name'] ?? ''),
                $publicationStatus
            );
            $importer->rotateCsrfToken();

            $this->view('admin/blog/import_result', [
                'title' => 'Importación completada',
                'result' => $result,
            ], 'admin');
        } catch (\Throwable $e) {
            http_response_code(422);
            $this->renderImportPage(
                $importer,
                null,
                null,
                $this->importErrorMessage($e)
                    . ' El lote temporal ya no puede reutilizarse; vuelve a previsualizar el archivo.'
            );
        }
    }

    public function saveImportTemplate(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }
        $importer = new BlogCsvImporter();
        if (!$importer->verifyCsrfToken((string)($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            $this->renderImportPage($importer, null, null, 'La sesión expiró. Vuelve a cargar el archivo.');
            return;
        }
        $source = null;
        $mapping = is_array($_POST['mapping'] ?? null) ? $_POST['mapping'] : [];
        try {
            $source = $importer->getPreparedSource((string)($_POST['source_token'] ?? ''), (int)$_SESSION['admin_id']);
            $importer->previewMappedSource($source['token'], (int)$_SESSION['admin_id'], $mapping);
            $name = $this->importProfileName($_POST['profile_name'] ?? '', $source['name']);
            $profileId = BlogImportProfile::saveProfile(
                $source['profile_id'],
                $name,
                $mapping,
                $source['headers'],
                (int)$_SESSION['admin_id'],
                $importer->getPreparedSourceData($source['token'], (int)$_SESSION['admin_id'])
            );
            $_SESSION['flash_message'] = 'Plantilla y archivo guardados. Puedes editarla o repetir la corrida sin subir el archivo otra vez.';
            $_SESSION['flash_type'] = 'success';
            $this->redirect(BASE_URL . '/admin/blog/importar?profile=' . $profileId);
        } catch (\Throwable $e) {
            http_response_code(422);
            $this->renderImportPage(
                $importer,
                null,
                null,
                $this->importErrorMessage($e),
                $source,
                $mapping,
                [],
                is_string($_POST['profile_name'] ?? null) ? $_POST['profile_name'] : ''
            );
        }
    }

    public function importHistory(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }
        $profiles = BlogImportProfile::all();
        foreach ($profiles as &$profile) {
            $profile['mapping'] = json_decode((string)$profile['mapping_json'], true) ?: [];
            $profile['headers'] = json_decode((string)$profile['headers_json'], true) ?: [];
            $profile['runs'] = BlogImportProfile::runs((int)$profile['id']);
            $profile['latest_completed_id'] = null;
            foreach ($profile['runs'] as $run) {
                if ($run['status'] === 'completed') {
                    $profile['latest_completed_id'] = (int)$run['id'];
                    break;
                }
            }
        }
        unset($profile);
        $this->view('admin/blog/import_history', [
            'title' => 'Importaciones guardadas',
            'profiles' => $profiles,
            'csrfToken' => (new BlogCsvImporter())->csrfToken(),
        ], 'admin');
    }

    public function editImportTemplate(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }
        $profile = BlogImportProfile::findProfile($id);
        if ($profile === null) {
            http_response_code(404);
            $_SESSION['flash_message'] = 'La plantilla no existe.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/blog/importaciones');
        }
        $this->view('admin/blog/import_template_edit', [
            'title' => 'Editar plantilla de importación',
            'profile' => $profile,
            'mapping' => $profile['mapping'],
            'profileName' => $profile['name'],
            'csrfToken' => (new BlogCsvImporter())->csrfToken(),
            'globalError' => null,
        ], 'admin');
    }

    public function updateImportTemplate(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }
        $importer = new BlogCsvImporter();
        if (!$importer->verifyCsrfToken((string)($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            $_SESSION['flash_message'] = 'La sesión expiró. Abre la plantilla e inténtalo de nuevo.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/blog/importaciones');
        }
        $profile = BlogImportProfile::findProfile($id);
        if ($profile === null) {
            http_response_code(404);
            $_SESSION['flash_message'] = 'La plantilla no existe.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/blog/importaciones');
        }
        $mapping = is_array($_POST['mapping'] ?? null) ? $_POST['mapping'] : [];
        $profileName = is_string($_POST['profile_name'] ?? null) ? $_POST['profile_name'] : '';
        try {
            $name = $this->importProfileName($profileName, (string)$profile['name']);
            $mapping = BlogCsvImporter::validateMapping($profile['headers'], $mapping);
            BlogImportProfile::saveProfile($id, $name, $mapping, $profile['headers'], (int)$_SESSION['admin_id']);
            $_SESSION['flash_message'] = 'Plantilla actualizada correctamente.';
            $_SESSION['flash_type'] = 'success';
            $nextUrl = '/admin/blog/importaciones/plantilla/' . $id . '/editar';
            if (isset($_POST['save_and_run'])) {
                $nextUrl = '/admin/blog/importar?profile=' . $id;
                if (BlogImportProfile::savedSource($profile) !== null) {
                    $nextUrl .= '&reuse=1';
                }
            }
            $this->redirect(BASE_URL . $nextUrl);
        } catch (\Throwable $e) {
            http_response_code(422);
            $this->view('admin/blog/import_template_edit', [
                'title' => 'Editar plantilla de importación',
                'profile' => $profile,
                'mapping' => $mapping,
                'profileName' => $profileName,
                'csrfToken' => $importer->csrfToken(),
                'globalError' => $this->importErrorMessage($e),
            ], 'admin');
        }
    }

    public function undoImportRun(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }
        $importer = new BlogCsvImporter();
        if (!$importer->verifyCsrfToken((string)($_POST['csrf_token'] ?? ''))) {
            http_response_code(403);
            $_SESSION['flash_message'] = 'La sesión expiró. Vuelve a intentarlo.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/blog/importaciones');
        }
        try {
            $result = BlogImportProfile::undoRun($id, (int)$_SESSION['admin_id']);
            $_SESSION['flash_message'] = sprintf(
                'Ejecución deshecha: %d posts eliminados y %d restaurados.',
                $result['deleted_count'],
                $result['restored_count']
            );
            $_SESSION['flash_type'] = 'success';
        } catch (\Throwable $e) {
            $_SESSION['flash_message'] = $this->importErrorMessage($e);
            $_SESSION['flash_type'] = 'error';
        }
        $this->redirect(BASE_URL . '/admin/blog/importaciones');
    }

    // ─── Categories CRUD ─────────────────────────────────────────────

    private function importProfileName(mixed $value, string $sourceName): string
    {
        if (!is_string($value)) {
            throw new \RuntimeException('El nombre de la importación es inválido.');
        }
        $name = trim($value);
        if ($name === '') {
            $name = pathinfo($sourceName, PATHINFO_FILENAME) ?: 'Importación del blog';
        }
        if (mb_strlen($name, 'UTF-8') > 150) {
            throw new \RuntimeException('El nombre de la importación excede 150 caracteres.');
        }
        return $name;
    }

    private function normalizePostSlug(string $slug, string $title): string
    {
        $source = trim($slug) !== '' ? $slug : $title;
        return BlogPost::generateSlug($source);
    }

    private function quickEditCsrfToken(): string
    {
        if (empty($_SESSION['blog_quick_edit_csrf'])) {
            $_SESSION['blog_quick_edit_csrf'] = bin2hex(random_bytes(32));
        }
        return (string)$_SESSION['blog_quick_edit_csrf'];
    }

    private function verifyQuickEditCsrfToken(string $token): bool
    {
        $stored = (string)($_SESSION['blog_quick_edit_csrf'] ?? '');
        return $stored !== '' && $token !== '' && hash_equals($stored, $token);
    }

    private function renderImportPage(
        BlogCsvImporter $importer,
        ?array $preview,
        ?string $batchToken,
        ?string $globalError,
        ?array $source = null,
        array $mapping = [],
        array $categoryChoices = [],
        string $profileName = ''
    ): void {
        $profileId = isset($source['profile_id']) ? (int)$source['profile_id'] : null;
        $profile = $profileId !== null ? BlogImportProfile::findProfile($profileId) : null;
        $this->view('admin/blog/import', [
            'title' => 'Importar Artículos',
            'csrfToken' => $importer->csrfToken(),
            'preview' => $preview,
            'source' => $source,
            'mapping' => $mapping,
            'profile' => $profile,
            'profileName' => $profileName !== '' ? $profileName : ($profile['name'] ?? ''),
            'categoryChoices' => $categoryChoices,
            'batchToken' => $batchToken,
            'globalError' => $globalError,
        ], 'admin');
    }

    private function importErrorMessage(\Throwable $e): string
    {
        if ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException) {
            return $e->getMessage();
        }

        error_log('Blog import failed: ' . $e->getMessage());
        return 'Ocurrió un error inesperado durante la importación.';
    }

    private function normalizePublishedAt(?string $value): ?string
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        $timezone = app_timezone();
        foreach (['Y-m-d\TH:i', 'Y-m-d H:i:s', 'Y-m-d H:i'] as $format) {
            $date = \DateTimeImmutable::createFromFormat($format, $value, $timezone);
            $errors = \DateTimeImmutable::getLastErrors();
            $isStrictlyValid = $errors === false
                || ((int)$errors['warning_count'] === 0 && (int)$errors['error_count'] === 0);
            if ($date instanceof \DateTimeImmutable && $isStrictlyValid) {
                return $date->format('Y-m-d H:i:s');
            }
        }

        return null;
    }

    private function normalizeAuthorId(mixed $value): ?int
    {
        $authorId = filter_var($value, FILTER_VALIDATE_INT, [
            'options' => ['min_range' => 1],
        ]);

        if ($authorId === false) {
            return null;
        }

        return User::findById((int)$authorId) ? (int)$authorId : null;
    }

    private function resolveFeaturedImage(string $currentImage = ''): string
    {
        if ($this->hasFeaturedImageUpload()) {
            return $this->storeFeaturedImageUpload();
        }

        return trim((string)($_POST['featured_media_url'] ?? $currentImage));
    }

    private function hasFeaturedImageUpload(): bool
    {
        return isset($_FILES['featured_image'])
            && ($_FILES['featured_image']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
    }

    private function storeFeaturedImageUpload(): string
    {
        $file = $_FILES['featured_image'];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException($this->uploadErrorMsg((int)$file['error']));
        }

        $maxSize = 10 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            throw new \RuntimeException('La imagen excede el tamano maximo de 10 MB.');
        }

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = $finfo ? finfo_file($finfo, $file['tmp_name']) : '';
        if ($finfo) {
            finfo_close($finfo);
        }

        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif'];
        if (!in_array($mimeType, $allowedMimes, true)) {
            throw new \RuntimeException('Tipo de imagen no permitido. Usa JPG, PNG, GIF, WebP o AVIF.');
        }

        $yearMonth = date('Y/m');
        $uploadDir = PUBLIC_DIR . '/uploads/' . $yearMonth;
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0755, true)) {
            throw new \RuntimeException('No se pudo crear el directorio de uploads.');
        }

        $shouldConvertToWebp = in_array($mimeType, ['image/jpeg', 'image/png', 'image/avif'], true);
        $storedExt = $shouldConvertToWebp ? 'webp' : ($mimeType === 'image/gif' ? 'gif' : 'webp');
        $uniqueName = uniqid('media_') . '.' . $storedExt;
        $destPath = $uploadDir . '/' . $uniqueName;

        $uploaded = $shouldConvertToWebp
            ? convert_image_file_to_webp($file['tmp_name'], $mimeType, $destPath)
            : move_uploaded_file($file['tmp_name'], $destPath);

        if (!$uploaded) {
            throw new \RuntimeException('Error al guardar la imagen destacada.');
        }

        $relativePath = 'uploads/' . $yearMonth . '/' . $uniqueName;

        MediaFile::create([
            'filename'      => $uniqueName,
            'original_name' => $file['name'],
            'path'          => $relativePath,
            'type'          => 'image',
            'mime_type'     => $shouldConvertToWebp ? 'image/webp' : $mimeType,
            'size'          => filesize($destPath) ?: $file['size'],
            'alt_text'      => null,
        ]);

        return asset_url($relativePath);
    }

    private function uploadErrorMsg(int $code): string
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE   => 'La imagen excede el tamano maximo permitido por el servidor.',
            UPLOAD_ERR_FORM_SIZE  => 'La imagen excede el tamano maximo del formulario.',
            UPLOAD_ERR_PARTIAL    => 'La imagen se subio parcialmente.',
            UPLOAD_ERR_NO_FILE    => 'No se selecciono ninguna imagen.',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal del servidor.',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir la imagen en el disco.',
            UPLOAD_ERR_EXTENSION  => 'Una extension de PHP detuvo la subida.',
        ];

        return $errors[$code] ?? 'Error desconocido al subir la imagen.';
    }

    public function categories(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $categories = BlogCategory::getAllWithCount();

        $this->view('admin/blog/categories/index', [
            'title'      => 'Categorías del Blog',
            'categories' => $categories,
        ], 'admin');
    }

    public function createCategory(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $this->view('admin/blog/categories/create', [
            'title' => 'Nueva Categoría',
        ], 'admin');
    }

    public function storeCategory(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $name        = $_POST['name'] ?? '';
        $slug        = BlogCategory::generateSlug(!empty($_POST['slug']) ? $_POST['slug'] : $name);
        $description = $_POST['description'] ?? '';

        // Ensure unique slug
        $existing = BlogCategory::findBySlug($slug);
        if ($existing) {
            $slug = $slug . '-' . time();
        }

        BlogCategory::createCategory([
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
        ]);

        $this->redirect(BASE_URL . '/admin/blog/categorias');
    }

    public function editCategory(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $category = BlogCategory::findById($id);

        if (!$category) {
            $this->redirect(BASE_URL . '/admin/blog/categorias');
        }

        $this->view('admin/blog/categories/edit', [
            'title'    => 'Editar Categoría',
            'category' => $category,
        ], 'admin');
    }

    public function updateCategory(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $name        = $_POST['name'] ?? '';
        $slug        = BlogCategory::generateSlug(!empty($_POST['slug']) ? $_POST['slug'] : $name);
        $description = $_POST['description'] ?? '';

        // Check slug uniqueness (exclude current)
        $existing = BlogCategory::findBySlug($slug);
        if ($existing && $existing['id'] !== $id) {
            $slug = $slug . '-' . time();
        }

        BlogCategory::updateCategory($id, [
            'name'        => $name,
            'slug'        => $slug,
            'description' => $description,
        ]);

        $this->redirect(BASE_URL . '/admin/blog/categorias');
    }

    public function destroyCategory(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        BlogCategory::deleteCategory($id);
        $this->redirect(BASE_URL . '/admin/blog/categorias');
    }
}
