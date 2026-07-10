<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\MediaFile;

class BlogController extends Controller
{
    // ─── Posts CRUD ──────────────────────────────────────────────────

    public function index(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $posts = BlogPost::getAll();

        $this->view('admin/blog/index', [
            'title' => 'Administrar Blog',
            'posts' => $posts,
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

        $this->view('admin/blog/edit', [
            'title'      => 'Editar Artículo',
            'post'       => $post,
            'categories' => $categories,
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

    // ─── Categories CRUD ─────────────────────────────────────────────

    private function normalizePostSlug(string $slug, string $title): string
    {
        $source = trim($slug) !== '' ? $slug : $title;
        return BlogPost::generateSlug($source);
    }

    private function normalizePublishedAt(?string $value): ?string
    {
        $value = trim((string)$value);
        if ($value === '') {
            return null;
        }

        $timezone = new \DateTimeZone('America/Mexico_City');
        foreach (['Y-m-d\TH:i', 'Y-m-d H:i:s', 'Y-m-d H:i'] as $format) {
            $date = \DateTimeImmutable::createFromFormat($format, $value, $timezone);
            if ($date instanceof \DateTimeImmutable) {
                return $date->format('Y-m-d H:i:s');
            }
        }

        return null;
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
