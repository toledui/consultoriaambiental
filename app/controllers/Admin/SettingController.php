<?php

namespace App\Controllers\Admin;

use App\Core\Controller;
use App\Models\Setting;
use App\Helpers\Mail;

class SettingController extends Controller
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
     * Settings index — shows the tabbed settings page.
     */
    public function index(): void
    {
        $this->checkAuth();

        $tab = $_GET['tab'] ?? 'smtp';
        $settings = Setting::getAll();

        $this->view('admin/settings/index', [
            'title'    => 'Configuración',
            'tab'      => $tab,
            'settings' => $settings,
        ], 'admin');
    }

    /**
     * SMTP configuration form.
     */
    public function smtp(): void
    {
        $this->checkAuth();

        $settings = Setting::getAll();

        $this->view('admin/settings/index', [
            'title'    => 'Configuración SMTP',
            'tab'      => 'smtp',
            'settings' => $settings,
        ], 'admin');
    }

    /**
     * Save SMTP configuration.
     */
    public function saveSmtp(): void
    {
        $this->checkAuth();

        $data = [
            'smtp_host'       => $_POST['smtp_host'] ?? '',
            'smtp_port'       => $_POST['smtp_port'] ?? '587',
            'smtp_encryption'  => $_POST['smtp_encryption'] ?? 'tls',
            'smtp_username'   => $_POST['smtp_username'] ?? '',
            'smtp_password'   => $_POST['smtp_password'] ?? '',
            'smtp_from_email' => $_POST['smtp_from_email'] ?? '',
            'smtp_from_name'  => $_POST['smtp_from_name'] ?? 'Gestoría Ambiental',
        ];

        Setting::setMultiple($data);

        $_SESSION['flash_message'] = 'Configuración SMTP guardada correctamente.';
        $_SESSION['flash_type'] = 'success';

        $this->redirect(BASE_URL . '/admin/settings?tab=smtp');
    }

    /**
     * Brand configuration form.
     */
    public function brand(): void
    {
        $this->checkAuth();

        $settings = Setting::getAll();

        $this->view('admin/settings/index', [
            'title'    => 'Configuración de Marca',
            'tab'      => 'brand',
            'settings' => $settings,
        ], 'admin');
    }

    /**
     * Save brand configuration (including logo upload).
     */
    public function saveBrand(): void
    {
        $this->checkAuth();

        $data = [
            'brand_company_name' => $_POST['brand_company_name'] ?? 'Gestoría Ambiental',
        ];

        // Handle logo upload
        if (isset($_FILES['brand_logo']) && $_FILES['brand_logo']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = PUBLIC_DIR . '/images/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = strtolower(pathinfo($_FILES['brand_logo']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'webp', 'avif'];

            if (in_array($ext, $allowed)) {
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                $mimeType = finfo_file($finfo, $_FILES['brand_logo']['tmp_name']);
                finfo_close($finfo);

                $shouldConvertToWebp = in_array($mimeType, ['image/jpeg', 'image/png', 'image/avif'], true);
                $storedExt = $shouldConvertToWebp ? 'webp' : $ext;
                $filename = 'brand_logo_' . time() . '.' . $storedExt;
                $destPath = $uploadDir . $filename;

                $uploaded = $shouldConvertToWebp
                    ? convert_image_file_to_webp($_FILES['brand_logo']['tmp_name'], $mimeType, $destPath)
                    : move_uploaded_file($_FILES['brand_logo']['tmp_name'], $destPath);

                if ($uploaded) {
                    // Delete old logo if exists
                    $oldLogo = Setting::get('brand_logo');
                    if ($oldLogo && file_exists($uploadDir . basename($oldLogo))) {
                        unlink($uploadDir . basename($oldLogo));
                    }
                    $data['brand_logo'] = 'images/' . $filename;
                }
            }
        }

        Setting::setMultiple($data);

        $_SESSION['flash_message'] = 'Configuración de marca guardada correctamente.';
        $_SESSION['flash_type'] = 'success';

        $this->redirect(BASE_URL . '/admin/settings?tab=brand');
    }

    /**
     * Send a test email to verify SMTP configuration.
     */
    public function testSmtp(): void
    {
        $this->checkAuth();

        $toEmail = $_POST['test_email'] ?? '';

        if (empty($toEmail)) {
            $_SESSION['flash_message'] = 'Debes ingresar un correo destino para la prueba.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/settings?tab=smtp');
        }

        $settings = Setting::getAll();
        $companyName = $settings['brand_company_name'] ?? 'Gestoría Ambiental';

        $mail = new Mail();

        if (!$mail->isConfigured()) {
            $_SESSION['flash_message'] = 'SMTP no está configurado. Guarda la configuración SMTP primero.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/settings?tab=smtp');
        }

        [$subject, $htmlBody, $textBody] = Mail::buildTestEmail($companyName);

        $result = $mail->send($toEmail, '', $subject, $htmlBody, $textBody);

        if ($result['success']) {
            $_SESSION['flash_message'] = $result['message'];
            $_SESSION['flash_type'] = 'success';
        } else {
            $_SESSION['flash_message'] = 'Error: ' . $result['message'];
            $_SESSION['flash_type'] = 'error';
        }

        $this->redirect(BASE_URL . '/admin/settings?tab=smtp');
    }

    /**
     * Save social media configuration (dynamic JSON-based rows).
     */
    public function saveSocial(): void
    {
        $this->checkAuth();

        $sections = ['social_header', 'social_footer', 'social_contact'];

        foreach ($sections as $section) {
            $icons = $_POST[$section]['icon'] ?? [];
            $urls  = $_POST[$section]['url'] ?? [];

            $items = [];
            foreach ($icons as $i => $icon) {
                $icon = trim($icon);
                $url  = trim($urls[$i] ?? '');
                if ($icon !== '' && $url !== '') {
                    $items[] = [
                        'icon' => $icon,
                        'url'  => $url,
                    ];
                }
            }

            Setting::set($section, json_encode($items, JSON_UNESCAPED_UNICODE));
        }

        $_SESSION['flash_message'] = 'Configuración de redes sociales guardada correctamente.';
        $_SESSION['flash_type'] = 'success';

        $this->redirect(BASE_URL . '/admin/settings?tab=social');
    }

    /**
     * Save footer configuration (contact info, links, copyright, tagline, checklist).
     */
    public function saveFooter(): void
    {
        $this->checkAuth();

        // Contact info
        $data = [
            'footer_phone_label'   => $_POST['footer_phone_label'] ?? 'Teléfono',
            'footer_phone_value'   => $_POST['footer_phone_value'] ?? '',
            'footer_whatsapp_label' => $_POST['footer_whatsapp_label'] ?? 'WhatsApp',
            'footer_whatsapp_value' => $_POST['footer_whatsapp_value'] ?? '',
            'footer_email_label'   => $_POST['footer_email_label'] ?? 'Correo',
            'footer_email_value'   => $_POST['footer_email_value'] ?? '',
            'footer_tagline'       => $_POST['footer_tagline'] ?? '',
            'footer_copyright'     => $_POST['footer_copyright'] ?? 'Consultoría Ambiental CA.',
            'footer_checklist_label' => $_POST['footer_checklist_label'] ?? 'Checklist Ambiental',
            'whatsapp_floating_number'  => $_POST['whatsapp_floating_number'] ?? '523387654321',
            'whatsapp_floating_message' => $_POST['whatsapp_floating_message'] ?? 'Hola, me gustaría recibir información sobre sus servicios de consultoría ambiental.',
        ];

        // Handle checklist file upload
        if (isset($_FILES['footer_checklist_file']) && $_FILES['footer_checklist_file']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = PUBLIC_DIR . '/uploads/checklist/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = strtolower(pathinfo($_FILES['footer_checklist_file']['name'], PATHINFO_EXTENSION));
            $allowed = ['pdf', 'doc', 'docx'];

            if (in_array($ext, $allowed)) {
                $filename = 'checklist_ambiental_' . time() . '.' . $ext;
                $destPath = $uploadDir . $filename;

                if (move_uploaded_file($_FILES['footer_checklist_file']['tmp_name'], $destPath)) {
                    // Delete old file if exists
                    $oldFile = Setting::get('footer_checklist_url');
                    if ($oldFile) {
                        $oldPath = PUBLIC_DIR . '/' . $oldFile;
                        if (file_exists($oldPath)) {
                            unlink($oldPath);
                        }
                    }
                    $data['footer_checklist_url'] = 'uploads/checklist/' . $filename;
                }
            }
        }

        // Footer links (dynamic JSON)
        $linkTexts = $_POST['footer_links']['text'] ?? [];
        $linkUrls  = $_POST['footer_links']['url'] ?? [];
        $links = [];
        foreach ($linkTexts as $i => $text) {
            $text = trim($text);
            $url  = trim($linkUrls[$i] ?? '');
            if ($text !== '' && $url !== '') {
                $links[] = [
                    'text' => $text,
                    'url'  => $url,
                ];
            }
        }
        $data['footer_links'] = json_encode($links, JSON_UNESCAPED_UNICODE);

        Setting::setMultiple($data);

        $_SESSION['flash_message'] = 'Configuración del footer guardada correctamente.';
        $_SESSION['flash_type'] = 'success';

        $this->redirect(BASE_URL . '/admin/settings?tab=footer');
    }

    /**
     * Save contact form email recipients configuration.
     */
    public function saveContacto(): void
    {
        $this->checkAuth();

        $contactEmails = trim($_POST['contact_emails'] ?? '');

        $turnstileEnabled = isset($_POST['turnstile_enabled']) ? '1' : '0';
        $turnstileSiteKey = trim($_POST['turnstile_site_key'] ?? '');
        $turnstileSecretKey = trim($_POST['turnstile_secret_key'] ?? '');
        $storedSecretKey = trim((string)Setting::get('turnstile_secret_key', ''));
        $effectiveSecretKey = $turnstileSecretKey !== '' ? $turnstileSecretKey : $storedSecretKey;

        if ($turnstileEnabled === '1' && ($turnstileSiteKey === '' || $effectiveSecretKey === '')) {
            $_SESSION['flash_message'] = 'Para activar Turnstile debes ingresar la clave del sitio y la clave secreta.';
            $_SESSION['flash_type'] = 'error';
            $this->redirect(BASE_URL . '/admin/settings?tab=contacto');
        }

        $data = [
            'contact_emails'     => $contactEmails,
            'turnstile_enabled'  => $turnstileEnabled,
            'turnstile_site_key' => $turnstileSiteKey,
        ];

        if ($turnstileSecretKey !== '') {
            $data['turnstile_secret_key'] = $turnstileSecretKey;
        }

        Setting::setMultiple($data);

        $_SESSION['flash_message'] = 'Configuración de contacto guardada correctamente.';
        $_SESSION['flash_type'] = 'success';

        $this->redirect(BASE_URL . '/admin/settings?tab=contacto');
    }

    /**
     * Privacy policy configuration form.
     */
    public function privacidad(): void
    {
        $this->checkAuth();

        $settings = Setting::getAll();

        // Default content for the privacy policy (used when no custom content is saved yet)
        $defaultContent = <<<'HTML'
<section>
  <h2 class="text-2xl font-bold text-ca-navy mb-4">1. Identidad y domicilio del responsable</h2>
  <p>
    <strong>{{brand_company_name}}</strong> (en adelante, "la Empresa"), con domicilio en {{footer_address}}, es el responsable del tratamiento de sus datos personales.
  </p>
  <p class="mt-3">
    Para cualquier comunicación relacionada con el presente aviso de privacidad, puede contactarnos a través de:
  </p>
  <ul class="list-disc pl-6 mt-2 space-y-1">
    <li><strong>Correo electrónico:</strong> {{footer_email_value}}</li>
    <li><strong>Teléfono:</strong> {{footer_phone_value}}</li>
  </ul>
</section>

<section>
  <h2 class="text-2xl font-bold text-ca-navy mb-4">2. Datos personales que recabamos</h2>
  <p>Para las finalidades descritas en el presente aviso de privacidad, podemos recabar los siguientes datos personales:</p>
  <ul class="list-disc pl-6 mt-2 space-y-1">
    <li>Nombre completo</li>
    <li>Correo electrónico</li>
    <li>Teléfono (fijo y/o móvil)</li>
    <li>Empresa o institución para la que labora</li>
    <li>Cargo o puesto</li>
    <li>Información de contacto proporcionada a través de formularios en nuestro sitio web</li>
  </ul>
  <p class="mt-3">
    No recabamos datos personales sensibles conforme a la Ley Federal de Protección de Datos Personales en Posesión de los Particulares.
  </p>
</section>

<section>
  <h2 class="text-2xl font-bold text-ca-navy mb-4">3. Finalidades del tratamiento de datos</h2>
  <p class="font-semibold text-ca-navy">Finalidades primarias (necesarias):</p>
  <ul class="list-disc pl-6 mt-2 space-y-1">
    <li>Atender solicitudes de información, cotizaciones y servicios de consultoría ambiental</li>
    <li>Dar seguimiento a comunicaciones iniciadas a través de nuestros formularios de contacto</li>
    <li>Prestar los servicios de consultoría ambiental contratados</li>
    <li>Dar cumplimiento a obligaciones legales y regulatorias aplicables</li>
  </ul>
  <p class="font-semibold text-ca-navy mt-4">Finalidades secundarias (no necesarias):</p>
  <ul class="list-disc pl-6 mt-2 space-y-1">
    <li>Enviar boletines informativos, noticias y actualizaciones sobre normativa ambiental</li>
    <li>Realizar encuestas de satisfacción sobre nuestros servicios</li>
    <li>Invitar a eventos, webinars y capacitaciones relacionadas con materia ambiental</li>
  </ul>
</section>

<section>
  <h2 class="text-2xl font-bold text-ca-navy mb-4">4. Transferencia de datos personales</h2>
  <p>
    No transferimos sus datos personales a terceros sin su consentimiento, salvo las excepciones previstas en el artículo 37 de la Ley Federal de Protección de Datos Personales en Posesión de los Particulares, como autoridades competentes en ejercicio de sus funciones.
  </p>
</section>

<section>
  <h2 class="text-2xl font-bold text-ca-navy mb-4">5. Derechos ARCO (Acceso, Rectificación, Cancelación y Oposición)</h2>
  <p>
    Usted o su representante legal podrán ejercer los derechos de acceso, rectificación, cancelación u oposición al tratamiento de sus datos personales (derechos ARCO) enviando una solicitud a nuestro correo electrónico:
  </p>
  <p class="mt-2">
    {{footer_email_value}}
  </p>
  <p class="mt-3">
    La solicitud deberá contener: nombre completo del titular, correo electrónico para recibir notificaciones, documentos que acrediten la identidad, descripción clara y precisa de los datos personales respecto de los cuales se busca ejercer alguno de los derechos ARCO, y cualquier otro elemento que facilite la localización de los datos.
  </p>
  <p class="mt-3">
    Le responderemos en un plazo máximo de 20 días hábiles contados desde la fecha de recepción de su solicitud.
  </p>
</section>

<section>
  <h2 class="text-2xl font-bold text-ca-navy mb-4">6. Limitación y divulgación de datos</h2>
  <p>
    Usted puede limitar el uso o divulgación de sus datos personales enviando una solicitud a nuestro correo electrónico. Asimismo, podrá solicitar dejar de recibir comunicaciones promocionales o de marketing en cualquier momento, mediante el enlace de baja que incluimos en cada comunicación o contactándonos directamente.
  </p>
</section>

<section>
  <h2 class="text-2xl font-bold text-ca-navy mb-4">7. Uso de cookies y tecnologías de rastreo</h2>
  <p>
    Nuestro sitio web puede utilizar cookies y otras tecnologías de rastreo para mejorar la experiencia del usuario, analizar el tráfico del sitio y personalizar el contenido. Puede configurar su navegador para rechazar todas las cookies o para indicar cuándo se envía una cookie.
  </p>
</section>

<section>
  <h2 class="text-2xl font-bold text-ca-navy mb-4">8. Cambios al aviso de privacidad</h2>
  <p>
    Nos reservamos el derecho de modificar o actualizar este aviso de privacidad en cualquier momento. Las modificaciones entrarán en vigor inmediatamente después de su publicación en nuestro sitio web. Le recomendamos revisar periódicamente esta página para mantenerse informado sobre cualquier cambio.
  </p>
</section>

<section>
  <h2 class="text-2xl font-bold text-ca-navy mb-4">9. Consentimiento</h2>
  <p>
    Al proporcionar sus datos personales a través de nuestros formularios, usted manifiesta su consentimiento para el tratamiento de los mismos conforme a los términos y condiciones del presente aviso de privacidad.
  </p>
</section>
HTML;

        $this->view('admin/settings/index', [
            'title'          => 'Aviso de Privacidad',
            'tab'            => 'privacidad',
            'settings'       => $settings,
            'defaultContent' => $defaultContent,
        ], 'admin');
    }

    /**
     * Save privacy policy content.
     */
    public function savePrivacidad(): void
    {
        $this->checkAuth();

        $content = $_POST['privacidad_content'] ?? '';

        Setting::set('privacidad_content', $content);

        $_SESSION['flash_message'] = 'Aviso de Privacidad guardado correctamente.';
        $_SESSION['flash_type'] = 'success';

        $this->redirect(BASE_URL . '/admin/settings?tab=privacidad');
    }

    /**
     * Save custom header/body code.
     */
    public function saveCodigo(): void
    {
        $this->checkAuth();

        Setting::set('custom_head_code', $_POST['custom_head_code'] ?? '');
        Setting::set('custom_body_code', $_POST['custom_body_code'] ?? '');

        $_SESSION['flash_message'] = 'Código personalizado guardado correctamente.';
        $_SESSION['flash_type'] = 'success';

        $this->redirect(BASE_URL . '/admin/settings?tab=codigo');
    }
}
