<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\ServiceCatalog;
use App\Models\BlogPost;
use App\Models\Contact;
use App\Models\Setting;
use App\Models\ChecklistDownload;
use App\Helpers\Mail;

class HomeController extends Controller
{
    public function index(): void
    {
        $services = ServiceCatalog::navigation();
        $blogPosts = BlogPost::getLatest(6);

        $this->view('home/index', [
            'title'       => 'Inicio',
            'seoTitle'    => 'Consultoría Ambiental para Empresas e Industrias en México | ' . APP_NAME,
            'currentPage' => 'home',
            'headExtra'   => '<link rel="preload" as="image" href="' . asset_url('images/impacto ambiental imagen3.webp') . '" fetchpriority="high">',
            'services'    => $services,
            'blogPosts'   => $blogPosts,
        ]);
    }

    public function nosotros(): void
    {
        $this->view('home/nosotros', [
            'title'       => 'Nosotros',
            'currentPage' => 'nosotros',
        ]);
    }

    public function contacto(): void
    {
        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->handleContactForm();
            return;
        }

        $paquete = $_GET['paquete'] ?? '';

        $this->view('home/contacto', [
            'title'       => 'Contacto',
            'currentPage' => 'contacto',
            'paquete'     => $paquete,
        ]);
    }

    public function gracias(): void
    {
        $this->view('home/gracias', [
            'title'       => 'Gracias por contactarnos',
            'currentPage' => 'contacto',
        ]);
    }

    public function avisoPrivacidad(): void
    {
        $this->view('home/aviso_privacidad', [
            'title'       => 'Aviso de Privacidad',
            'seoTitle'    => 'Aviso de Privacidad | ' . APP_NAME,
            'currentPage' => 'aviso-privacidad',
        ]);
    }

    /**
     * Handle checklist download lead capture via AJAX.
     * Saves the lead and returns the file URL for download.
     */
    public function checklistDownload(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
            return;
        }

        $nombre     = trim($_POST['nombre'] ?? '');
        $apellidos  = trim($_POST['apellidos'] ?? '');
        $correo     = trim($_POST['correo'] ?? '');
        $empresa    = trim($_POST['empresa'] ?? '');
        $giro       = trim($_POST['giro'] ?? '');
        $newsletter = !empty($_POST['newsletter']) ? 1 : 0;

        $errors = [];
        if (empty($nombre))   $errors[] = 'El nombre es obligatorio.';
        if (empty($apellidos)) $errors[] = 'Los apellidos son obligatorios.';
        if (empty($correo))   $errors[] = 'El correo es obligatorio.';
        if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo no es válido.';
        }

        if (!empty($errors)) {
            $this->json(['success' => false, 'message' => implode(' ', $errors)], 400);
            return;
        }

        // Save lead to checklist_downloads table
        ChecklistDownload::create([
            'nombre'     => $nombre,
            'apellidos'  => $apellidos,
            'correo'     => $correo,
            'empresa'    => $empresa,
            'giro'       => $giro,
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
        ]);

        // Also save to contacts table so it appears in /admin/contactos
        Contact::create([
            'nombre'     => $nombre . ' ' . $apellidos,
            'correo'     => $correo,
            'telefono'   => '',
            'sector'     => $giro ?? '',
            'mensaje'    => 'Descarga de Checklist Ambiental' . ($empresa ? ' - Empresa: ' . $empresa : ''),
            'newsletter' => $newsletter,
        ]);

        // Get file URL
        $checklistUrl = Setting::get('footer_checklist_url');
        $fileUrl = $checklistUrl ? BASE_URL . '/' . htmlspecialchars($checklistUrl) : '';

        $this->json([
            'success' => true,
            'message' => '¡Gracias! Tu descarga comenzará en breve.',
            'file_url' => $fileUrl,
        ]);
    }

    /**
     * Handle newsletter subscription from the blog.
     */
    public function newsletter(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo 'Método no permitido';
            return;
        }

        $correo = trim($_POST['correo'] ?? '');

        if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['newsletter_error'] = 'Por favor ingresa un correo electrónico válido.';
            $this->redirect(BASE_URL . '/blog');
            return;
        }

        // Save to contacts table as a newsletter subscription
        Contact::create([
            'nombre'     => 'Suscriptor Newsletter',
            'correo'     => $correo,
            'telefono'   => '',
            'sector'     => 'Newsletter',
            'mensaje'    => 'Suscripción a newsletter desde el blog.',
            'newsletter' => 1,
        ]);

        $_SESSION['newsletter_success'] = '¡Gracias por suscribirte! Revisa tu correo para confirmar.';
        $this->redirect(BASE_URL . '/blog');
    }

    /**
     * Process the contact form submission.
     */
    private function handleContactForm(): void
    {
        // Validate required fields
        $nombre   = trim($_POST['nombre'] ?? '');
        $correo   = trim($_POST['correo'] ?? '');
        $telefono = trim($_POST['telefono'] ?? '');
        $sector   = trim($_POST['sector'] ?? '');
        $mensaje  = trim($_POST['mensaje'] ?? '');
        $newsletter = isset($_POST['newsletter']) ? 1 : 0;

        $errors = [];
        if (empty($nombre))   $errors[] = 'El nombre es obligatorio.';
        if (empty($correo))   $errors[] = 'El correo electrónico es obligatorio.';
        if (empty($telefono)) $errors[] = 'El teléfono es obligatorio.';
        if (empty($mensaje))  $errors[] = 'El mensaje es obligatorio.';

        if (!empty($correo) && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El correo electrónico no es válido.';
        }

        if (!empty($errors)) {
            $_SESSION['contact_error'] = implode(' ', $errors);
            $this->redirect(BASE_URL . '/contacto');
            return;
        }

        // Save to database
        $contactId = Contact::create([
            'nombre'     => $nombre,
            'correo'     => $correo,
            'telefono'   => $telefono,
            'sector'     => $sector,
            'mensaje'    => $mensaje,
            'newsletter' => $newsletter,
        ]);

        // Send email notification to configured recipients
        $this->sendContactNotification($nombre, $correo, $telefono, $sector, $mensaje, $newsletter);

        // Redirect to thank-you page
        $this->redirect(BASE_URL . '/contacto/gracias');
    }

    /**
     * Send email notification about the new contact form submission.
     */
    private function sendContactNotification(
        string $nombre,
        string $correo,
        string $telefono,
        string $sector,
        string $mensaje,
        int $newsletter
    ): void {
        // Get recipient emails from settings
        $recipientsRaw = Setting::get('contact_emails', '');
        $recipients = [];

        if (!empty($recipientsRaw)) {
            // Support both comma-separated and line-break separated
            $parts = preg_split('/[,\r\n]+/', $recipientsRaw);
            foreach ($parts as $part) {
                $email = trim($part);
                if (!empty($email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $recipients[] = $email;
                }
            }
        }

        // Fallback to SMTP from email if no recipients configured
        if (empty($recipients)) {
            $settings = Setting::getAll();
            $fallback = $settings['smtp_from_email'] ?? '';
            if (!empty($fallback)) {
                $recipients[] = $fallback;
            }
        }

        if (empty($recipients)) {
            return; // No one to notify
        }

        $sectorLabel = !empty($sector) ? $sector : 'No especificado';
        $newsletterLabel = $newsletter ? 'Sí, desea suscribirse' : 'No desea suscribirse';

        $hostName = $_SERVER['HTTP_HOST'] ?? APP_NAME;
        $subject = 'Nuevo contacto desde el sitio web - ' . APP_NAME;

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; background: #f4f4f4; padding: 20px;">
  <div style="max-width: 600px; margin: 0 auto; background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
    <div style="background: #1B5E20; padding: 20px; text-align: center;">
      <h1 style="color: #fff; margin: 0; font-size: 22px;">Nuevo contacto recibido</h1>
    </div>
    <div style="padding: 24px;">
      <table style="width: 100%; border-collapse: collapse;">
        <tr><td style="padding: 8px 0; font-weight: bold; color: #333; width: 120px;">Nombre:</td><td style="padding: 8px 0; color: #555;">{$nombre}</td></tr>
        <tr><td style="padding: 8px 0; font-weight: bold; color: #333;">Correo:</td><td style="padding: 8px 0; color: #555;"><a href="mailto:{$correo}">{$correo}</a></td></tr>
        <tr><td style="padding: 8px 0; font-weight: bold; color: #333;">Teléfono:</td><td style="padding: 8px 0; color: #555;">{$telefono}</td></tr>
        <tr><td style="padding: 8px 0; font-weight: bold; color: #333;">Sector:</td><td style="padding: 8px 0; color: #555;">{$sectorLabel}</td></tr>
        <tr><td style="padding: 8px 0; font-weight: bold; color: #333;">Newsletter:</td><td style="padding: 8px 0; color: #555;">{$newsletterLabel}</td></tr>
      </table>
      <hr style="border: none; border-top: 1px solid #eee; margin: 16px 0;">
      <h3 style="color: #333; margin: 0 0 8px;">Mensaje:</h3>
      <p style="color: #555; line-height: 1.6; white-space: pre-wrap;">{$mensaje}</p>
      <hr style="border: none; border-top: 1px solid #eee; margin: 16px 0;">
      <p style="font-size: 12px; color: #999; text-align: center;">
        Este correo fue generado automáticamente desde el formulario de contacto de {$hostName}.
      </p>
    </div>
  </div>
</body>
</html>
HTML;

        $textBody = "Nuevo contacto recibido\n\n"
            . "Nombre: {$nombre}\n"
            . "Correo: {$correo}\n"
            . "Teléfono: {$telefono}\n"
            . "Sector: {$sectorLabel}\n"
            . "Newsletter: {$newsletterLabel}\n\n"
            . "Mensaje:\n{$mensaje}\n\n"
            . "---\n"
            . "Formulario de contacto - " . APP_NAME;

        $mail = new Mail();

        foreach ($recipients as $recipient) {
            $mail->send($recipient, $recipient, $subject, $htmlBody, $textBody);
        }
    }
}
