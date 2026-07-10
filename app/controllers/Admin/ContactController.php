<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Contact;

class ContactController extends Controller
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
     * List all contacts.
     */
    public function index(): void
    {
        $this->checkAuth();

        $contacts = Contact::getAll();
        $unreadCount = Contact::getUnreadCount();

        $this->view('admin/contactos/index', [
            'title'       => 'Contactos',
            'contacts'    => $contacts,
            'unreadCount' => $unreadCount,
        ], 'admin');
    }

    /**
     * View a single contact.
     */
    public function show(int $id): void
    {
        $this->checkAuth();

        $contact = Contact::getById($id);

        if (!$contact) {
            $_SESSION['flash_message'] = 'El contacto no existe.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/contactos');
            return;
        }

        // Mark as read
        if ($contact['read_at'] === null) {
            Contact::markAsRead($id);
            $contact['read_at'] = date('Y-m-d H:i:s');
        }

        $this->view('admin/contactos/show', [
            'title'   => 'Contacto: ' . htmlspecialchars($contact['nombre']),
            'contact' => $contact,
        ], 'admin');
    }

    /**
     * Delete a contact.
     */
    public function destroy(int $id): void
    {
        $this->checkAuth();

        $contact = Contact::getById($id);

        if (!$contact) {
            $_SESSION['flash_message'] = 'El contacto no existe.';
            $_SESSION['flash_type'] = 'error';
        } else {
            Contact::deleteById($id);
            $_SESSION['flash_message'] = 'Contacto eliminado correctamente.';
            $_SESSION['flash_type'] = 'success';
        }

        $this->redirect(BASE_URL . '/admin/contactos');
    }

    /**
     * Export all contacts as CSV.
     */
    public function csv(): void
    {
        $this->checkAuth();

        $contacts = Contact::getAll();

        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="contactos-' . date('Y-m-d') . '.csv"');

        // Open output stream
        $output = fopen('php://output', 'w');

        // UTF-8 BOM for Excel compatibility
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

        // CSV headers
        fputcsv($output, [
            'ID',
            'Nombre',
            'Correo',
            'Teléfono',
            'Sector',
            'Mensaje',
            'Newsletter',
            'Leído',
            'Fecha de contacto (CDMX)',
        ], ',', '"', '');

        // Data rows
        foreach ($contacts as $row) {
            fputcsv($output, [
                $row['id'],
                $row['nombre'],
                $row['correo'],
                $row['telefono'] ?? '',
                $row['sector'] ?? '',
                $row['mensaje'] ?? '',
                $row['newsletter'] ? 'Sí' : 'No',
                $row['read_at'] !== null ? 'Sí' : 'No',
                format_cdmx_datetime($row['created_at']),
            ], ',', '"', '');
        }

        fclose($output);
        exit;
    }
}
