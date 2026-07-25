<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
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

        $this->view('admin/blog/import', [
            'title' => 'Importar Artículos',
            'csrfToken' => $importer->csrfToken(),
            'expectedHeaders' => BlogCsvImporter::expectedHeaders(),
            'preview' => null,
            'batchToken' => null,
            'globalError' => null,
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
            $preview = $importer->parseUploadedFile(
                $_FILES['csv_file'] ?? [],
                BlogPost::getAllSlugs(),
                BlogCategory::getAll()
            );
            $batchToken = null;
            if ($preview['can_import']) {
                $batchToken = $importer->createBatch(
                    $preview,
                    (int)$_SESSION['admin_id']
                );
            } else {
                http_response_code(422);
            }

            $this->renderImportPage($importer, $preview, $batchToken, null);
        } catch (\Throwable $e) {
            http_response_code(422);
            $this->renderImportPage(
                $importer,
                null,
                null,
                $this->importErrorMessage($e)
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
                'La sesión del formulario expiró. Vuelve a previsualizar el CSV.'
            );
            return;
        }

        try {
            $rows = $importer->consumeBatch(
                (string)($_POST['batch_token'] ?? ''),
                (int)$_SESSION['admin_id']
            );
            $result = BlogPost::importBatch(
                $rows,
                (int)$_SESSION['admin_id']
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
                    . ' El lote temporal ya no puede reutilizarse; vuelve a previsualizar el CSV.'
            );
        }
    }

    // ─── Categories CRUD ─────────────────────────────────────────────

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
        ?string $globalError
    ): void {
        $this->view('admin/blog/import', [
            'title' => 'Importar Artículos',
            'csrfToken' => $importer->csrfToken(),
            'expectedHeaders' => BlogCsvImporter::expectedHeaders(),
            'preview' => $preview,
            'batchToken' => $batchToken,
            'globalError' => $globalError,
        ], 'admin');
    }

    private function importErrorMessage(\Throwable $e): string
    {
        if ($e instanceof \RuntimeException || $e instanceof \InvalidArgumentException) {
            return $e->getMessage();
        }

        error_log('Blog CSV import failed: ' . $e->getMessage());
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
