<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\ChecklistDownload;

class ChecklistController extends Controller
{
    /**
     * Ensure user is authenticated.
     */
    private function checkAuth(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            $this->redirect(BASE_URL . '/admin/login');
        }
    }

    /**
     * List all checklist downloads.
     */
    public function index(): void
    {
        $this->checkAuth();

        $downloads = ChecklistDownload::getAll();
        $count = ChecklistDownload::getCount();

        $this->view('admin/checklist/index', [
            'title'     => 'Descargas de Checklist',
            'downloads' => $downloads,
            'count'     => $count,
        ], 'admin');
    }

    /**
     * Export all downloads as CSV.
     */
    public function csv(): void
    {
        $this->checkAuth();

        $downloads = ChecklistDownload::getAll();

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="checklist-descargas-' . date('Y-m-d') . '.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // CSV headers
        fputcsv($output, [
            'ID',
            'Nombre',
            'Apellidos',
            'Correo',
            'Empresa',
            'Giro / Sector',
            'Dirección IP',
            'Fecha de descarga (CDMX)',
        ], ',', '"', '');

        // Data rows
        foreach ($downloads as $row) {
            fputcsv($output, [
                $row['id'],
                $row['nombre'],
                $row['apellidos'],
                $row['correo'],
                $row['empresa'] ?? '',
                $row['giro'] ?? '',
                $row['ip_address'] ?? '',
                format_cdmx_datetime($row['created_at']),
            ], ',', '"', '');
        }

        fclose($output);
        exit;
    }

    /**
     * Delete a download record.
     */
    public function destroy(int $id): void
    {
        $this->checkAuth();

        ChecklistDownload::deleteById($id);

        $_SESSION['flash_message'] = 'Registro eliminado correctamente.';
        $_SESSION['flash_type'] = 'success';

        $this->redirect(BASE_URL . '/admin/checklist');
    }
}
