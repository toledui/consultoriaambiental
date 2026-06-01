<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\MediaFile;

class MediaController extends Controller
{
    private function checkAuth(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }
    }

    /**
     * Media library grid view.
     */
    public function index(): void
    {
        $this->checkAuth();

        $files = MediaFile::getAll();

        $this->view('admin/media/index', [
            'title' => 'Mediateca',
            'files' => $files,
        ], 'admin');
    }

    /**
     * Handle file upload via AJAX or regular POST.
     */
    public function upload(): void
    {
        $this->checkAuth();

        if (empty($_FILES['file'])) {
            if ($this->isAjax()) {
                $this->json(['error' => 'No se recibió ningún archivo.'], 400);
            } else {
                $_SESSION['flash_message'] = 'No se recibió ningún archivo.';
                $_SESSION['flash_type'] = 'error';
                $this->redirect(BASE_URL . '/admin/media');
            }
            return;
        }

        $file = $_FILES['file'];

        // Validate upload
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $msg = $this->uploadErrorMsg($file['error']);
            if ($this->isAjax()) {
                $this->json(['error' => $msg], 400);
            } else {
                $_SESSION['flash_message'] = $msg;
                $_SESSION['flash_type'] = 'error';
                $this->redirect(BASE_URL . '/admin/media');
            }
            return;
        }

        // Validate file size (10MB max)
        $maxSize = 10 * 1024 * 1024;
        if ($file['size'] > $maxSize) {
            $msg = 'El archivo excede el tamaño máximo de 10 MB.';
            if ($this->isAjax()) {
                $this->json(['error' => $msg], 400);
            } else {
                $_SESSION['flash_message'] = $msg;
                $_SESSION['flash_type'] = 'error';
                $this->redirect(BASE_URL . '/admin/media');
            }
            return;
        }

        // Validate file type
        $allowedMimes = [
            'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml',
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ];

        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mimeType, $allowedMimes)) {
            $msg = 'Tipo de archivo no permitido. Solo imágenes (JPG, PNG, GIF, WebP, SVG) y documentos (PDF, DOC, DOCX, XLS, XLSX).';
            if ($this->isAjax()) {
                $this->json(['error' => $msg], 400);
            } else {
                $_SESSION['flash_message'] = $msg;
                $_SESSION['flash_type'] = 'error';
                $this->redirect(BASE_URL . '/admin/media');
            }
            return;
        }

        // Determine file type category
        $type = 'other';
        if (strpos($mimeType, 'image') === 0) {
            $type = 'image';
        } elseif (strpos($mimeType, 'pdf') !== false) {
            $type = 'document';
        } elseif (strpos($mimeType, 'word') !== false || strpos($mimeType, 'document') !== false) {
            $type = 'document';
        } elseif (strpos($mimeType, 'excel') !== false || strpos($mimeType, 'spreadsheet') !== false) {
            $type = 'document';
        }

        // Create upload directory: public/uploads/YYYY/MM/
        $yearMonth = date('Y/m');
        $uploadDir = PUBLIC_DIR . '/uploads/' . $yearMonth;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Generate unique filename
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $uniqueName = uniqid('media_') . '.' . $ext;
        $destPath = $uploadDir . '/' . $uniqueName;

        if (!move_uploaded_file($file['tmp_name'], $destPath)) {
            $msg = 'Error al mover el archivo al directorio de uploads.';
            if ($this->isAjax()) {
                $this->json(['error' => $msg], 500);
            } else {
                $_SESSION['flash_message'] = $msg;
                $_SESSION['flash_type'] = 'error';
                $this->redirect(BASE_URL . '/admin/media');
            }
            return;
        }

        // Relative path for DB storage (without 'public/' prefix — web URL path)
        $relativePath = 'uploads/' . $yearMonth . '/' . $uniqueName;

        // Save to database
        $fileId = MediaFile::create([
            'filename'      => $uniqueName,
            'original_name' => $file['name'],
            'path'          => $relativePath,
            'type'          => $type,
            'mime_type'     => $mimeType,
            'size'          => $file['size'],
            'alt_text'      => null,
        ]);

        if ($this->isAjax()) {
            $this->json([
                'success' => true,
                'id'      => $fileId,
                'url'     => BASE_URL . '/' . $relativePath,
                'name'    => $file['name'],
            ]);
        } else {
            $_SESSION['flash_message'] = 'Archivo subido correctamente.';
            $_SESSION['flash_type'] = 'success';
            $this->redirect(BASE_URL . '/admin/media');
        }
    }

    /**
     * Delete a media file.
     */
    public function destroy(int $id): void
    {
        $this->checkAuth();

        $file = MediaFile::getById($id);
        if (!$file) {
            $_SESSION['flash_message'] = 'Archivo no encontrado.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/media');
            return;
        }

        // Delete physical file
        $fullPath = PUBLIC_DIR . '/' . $file['path'];
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }

        // Delete DB record
        MediaFile::deleteById($id);

        $_SESSION['flash_message'] = 'Archivo eliminado correctamente.';
        $_SESSION['flash_type'] = 'success';
        $this->redirect(BASE_URL . '/admin/media');
    }

    /**
     * Browse modal for WYSIWYG integration — returns JSON list or renders modal HTML.
     */
    public function browse(): void
    {
        $this->checkAuth();

        // If AJAX request, return JSON
        if ($this->isAjax()) {
            $type = $_GET['type'] ?? '';
            if ($type) {
                $files = MediaFile::getByType($type);
            } else {
                $files = MediaFile::getAll();
            }

            $result = array_map(function ($f) {
                return [
                    'id'            => $f['id'],
                    'url'           => MediaFile::getUrl($f),
                    'thumbnailUrl'  => MediaFile::getThumbnailUrl($f),
                    'name'          => $f['original_name'],
                    'size'          => MediaFile::formatSize($f['size']),
                    'type'          => $f['type'],
                    'mime_type'     => $f['mime_type'],
                    'icon'          => MediaFile::getFileIcon($f),
                    'alt_text'      => $f['alt_text'] ?? '',
                ];
            }, $files);

            $this->json($result);
            return;
        }

        // Render modal HTML
        $files = MediaFile::getAll();
        $this->viewPartial('admin/media/browse', [
            'files' => $files,
        ]);
    }

    /**
     * Update alt text for a media file (AJAX).
     */
    public function updateAlt(): void
    {
        $this->checkAuth();

        $id = (int) ($_POST['id'] ?? 0);
        $altText = $_POST['alt_text'] ?? '';

        if ($id <= 0) {
            $this->json(['error' => 'ID inválido.'], 400);
            return;
        }

        MediaFile::updateAltText($id, $altText);
        $this->json(['success' => true]);
    }

    /**
     * Check if request is AJAX.
     */
    private function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Convert upload error code to message.
     */
    private function uploadErrorMsg(int $code): string
    {
        $errors = [
            UPLOAD_ERR_INI_SIZE   => 'El archivo excede el tamaño máximo permitido por el servidor.',
            UPLOAD_ERR_FORM_SIZE  => 'El archivo excede el tamaño máximo del formulario.',
            UPLOAD_ERR_PARTIAL    => 'El archivo se subió parcialmente.',
            UPLOAD_ERR_NO_FILE    => 'No se seleccionó ningún archivo.',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal del servidor.',
            UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en el disco.',
            UPLOAD_ERR_EXTENSION  => 'Una extensión de PHP detuvo la subida.',
        ];
        return $errors[$code] ?? 'Error desconocido al subir el archivo.';
    }
}
