<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Service;

class ServiceController extends Controller
{
    public function index(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $services = Service::getAll();

        $this->view('admin/servicios/index', [
            'title'    => 'Administrar Servicios',
            'services' => $services,
        ], 'admin');
    }

    public function create(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $this->view('admin/servicios/create', [
            'title' => 'Nuevo Servicio',
        ], 'admin');
    }

    public function store(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $title       = $_POST['title'] ?? '';
        $slug        = !empty($_POST['slug']) ? $_POST['slug'] : Service::generateSlug($title);
        $description = $_POST['description'] ?? '';
        $icon        = $_POST['icon'] ?? 'fas fa-leaf';
        $content     = $_POST['content'] ?? '';
        $published   = isset($_POST['published']) ? 1 : 0;
        $sortOrder   = (int) ($_POST['sort_order'] ?? 0);

        // SEO fields
        $metaTitle       = $_POST['meta_title'] ?? '';
        $metaDescription = $_POST['meta_description'] ?? '';
        $jsonLd          = $_POST['json_ld'] ?? '';

        // Handle featured image upload or media selection
        $featuredImage = $this->handleFeaturedImageUpload();
        if ($featuredImage === null && !empty($_POST['featured_media_url'])) {
            // Image selected from media library
            $mediaUrl = $_POST['featured_media_url'];
            // Strip BASE_URL prefix if present (e.g., "http://localhost:8000/uploads/..." -> "uploads/...")
            $baseUrl = rtrim(BASE_URL, '/');
            if (strpos($mediaUrl, $baseUrl . '/') === 0) {
                $mediaUrl = substr($mediaUrl, strlen($baseUrl) + 1);
            }
            $featuredImage = $mediaUrl;
        }

        // Ensure unique slug
        $existing = Service::findBySlug($slug);
        if ($existing) {
            $slug = $slug . '-' . time();
        }

        Service::createService([
            'title'           => $title,
            'slug'            => $slug,
            'description'     => $description,
            'icon'            => $icon,
            'featured_image'  => $featuredImage,
            'content'         => $content,
            'meta_title'      => $metaTitle,
            'meta_description'=> $metaDescription,
            'json_ld'         => $jsonLd,
            'published'       => $published,
            'sort_order'      => $sortOrder,
        ]);

        $this->redirect(BASE_URL . '/admin/servicios');
    }

    public function edit(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $service = Service::findById($id);

        if (!$service) {
            $this->redirect(BASE_URL . '/admin/servicios');
        }

        $this->view('admin/servicios/edit', [
            'title'   => 'Editar Servicio',
            'service' => $service,
        ], 'admin');
    }

    public function update(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        $title       = $_POST['title'] ?? '';
        $slug        = !empty($_POST['slug']) ? $_POST['slug'] : Service::generateSlug($title);
        $description = $_POST['description'] ?? '';
        $icon        = $_POST['icon'] ?? 'fas fa-leaf';
        $content     = $_POST['content'] ?? '';
        $published   = isset($_POST['published']) ? 1 : 0;
        $sortOrder   = (int) ($_POST['sort_order'] ?? 0);

        // SEO fields
        $metaTitle       = $_POST['meta_title'] ?? '';
        $metaDescription = $_POST['meta_description'] ?? '';
        $jsonLd          = $_POST['json_ld'] ?? '';

        // Get existing service to check current featured image
        $existingService = Service::findById($id);
        $featuredImage = $existingService['featured_image'] ?? null;

        // Handle featured image upload, removal, or media selection
        $removeImage = isset($_POST['remove_image']);
        if ($removeImage) {
            // Delete old image file
            if ($featuredImage && file_exists(PUBLIC_DIR . '/' . $featuredImage)) {
                unlink(PUBLIC_DIR . '/' . $featuredImage);
            }
            $featuredImage = null;
        } else {
            $newImage = $this->handleFeaturedImageUpload();
            if ($newImage !== null) {
                // Delete old image if new one uploaded
                if ($featuredImage && file_exists(PUBLIC_DIR . '/' . $featuredImage)) {
                    unlink(PUBLIC_DIR . '/' . $featuredImage);
                }
                $featuredImage = $newImage;
            } elseif (!empty($_POST['featured_media_url'])) {
                // Image selected from media library
                $mediaUrl = $_POST['featured_media_url'];
                $baseUrl = rtrim(BASE_URL, '/');
                if (strpos($mediaUrl, $baseUrl . '/') === 0) {
                    $mediaUrl = substr($mediaUrl, strlen($baseUrl) + 1);
                }
                $featuredImage = $mediaUrl;
            }
        }

        Service::updateService($id, [
            'title'           => $title,
            'slug'            => $slug,
            'description'     => $description,
            'icon'            => $icon,
            'featured_image'  => $featuredImage,
            'content'         => $content,
            'meta_title'      => $metaTitle,
            'meta_description'=> $metaDescription,
            'json_ld'         => $jsonLd,
            'published'       => $published,
            'sort_order'      => $sortOrder,
        ]);

        $this->redirect(BASE_URL . '/admin/servicios');
    }

    public function destroy(int $id): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }

        // Delete featured image file if exists
        $service = Service::findById($id);
        if ($service && !empty($service['featured_image'])) {
            $imgPath = PUBLIC_DIR . '/' . $service['featured_image'];
            if (file_exists($imgPath)) {
                unlink($imgPath);
            }
        }

        Service::deleteService($id);
        $this->redirect(BASE_URL . '/admin/servicios');
    }

    /**
     * Handle featured image upload from the form.
     * Returns the relative path or null if no file uploaded.
     */
    private function handleFeaturedImageUpload(): ?string
    {
        if (empty($_FILES['featured_image']) || $_FILES['featured_image']['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $file = $_FILES['featured_image'];

        // Validate mime type
        $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/avif'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimes)) {
            return null;
        }

        // Create directory if needed
        $uploadDir = PUBLIC_DIR . '/images/servicios';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $shouldConvertToWebp = in_array($mimeType, ['image/jpeg', 'image/png', 'image/avif'], true);
        $ext = $shouldConvertToWebp ? 'webp' : strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $uniqueName = 'service_' . uniqid() . '.' . $ext;
        $destPath = $uploadDir . '/' . $uniqueName;

        if ($shouldConvertToWebp) {
            if (!convert_image_file_to_webp($file['tmp_name'], $mimeType, $destPath)) {
                return null;
            }
        } elseif (!move_uploaded_file($file['tmp_name'], $destPath)) {
            return null;
        }

        return 'images/servicios/' . $uniqueName;
    }
}
