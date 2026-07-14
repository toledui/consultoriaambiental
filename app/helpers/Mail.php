<?php

namespace App\Helpers;

use App\Models\Setting;

/**
 * Simple SMTP mailer using PHP sockets.
 * Sends emails via the configured SMTP server from settings.
 */
class Mail
{
    private string $host;
    private int $port;
    private string $encryption;
    private string $username;
    private string $password;
    private string $fromEmail;
    private string $fromName;

    public function __construct()
    {
        $settings = Setting::getAll();

        $this->host       = $settings['smtp_host'] ?? '';
        $this->port       = (int)($settings['smtp_port'] ?? 587);
        $this->encryption = $settings['smtp_encryption'] ?? 'tls';
        $this->username   = $settings['smtp_username'] ?? '';
        $this->password   = $settings['smtp_password'] ?? '';
        $this->fromEmail  = $settings['smtp_from_email'] ?? '';
        $this->fromName   = $settings['smtp_from_name'] ?? 'Gestoría Ambiental';
    }

    /**
     * Check if SMTP is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->host)
            && !empty($this->username)
            && !empty($this->password)
            && filter_var($this->fromEmail, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Send an email using the configured SMTP server.
     *
     * @param string $toEmail  Recipient email
     * @param string $toName   Recipient name
     * @param string $subject  Email subject
     * @param string $htmlBody HTML body content
     * @param string $textBody Plain text fallback
     * @return array{success: bool, message: string}
     */
    public function send(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody,
        string $textBody = '',
        string $replyToEmail = '',
        string $replyToName = ''
    ): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'SMTP no está configurado. Complete los datos del servidor SMTP primero.',
            ];
        }

        if (!filter_var($toEmail, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => 'El correo destino no es válido.',
            ];
        }

        if (!filter_var($this->fromEmail, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'El correo remitente SMTP no es válido.'];
        }

        if ($replyToEmail !== '' && !filter_var($replyToEmail, FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'El correo de respuesta no es válido.'];
        }

        if (empty($textBody)) {
            $textBody = strip_tags($htmlBody);
        }

        try {
            return $this->sendWithSocket(
                $toEmail,
                $toName,
                $subject,
                $htmlBody,
                $textBody,
                $replyToEmail,
                $replyToName
            );
        } catch (\Throwable $e) {
            $errorMsg = 'Error al enviar correo: ' . $e->getMessage();

            // Log the error
            $logDir = dirname(__DIR__, 2) . '/storage/logs';
            if (!is_dir($logDir)) {
                mkdir($logDir, 0755, true);
            }
            file_put_contents(
                $logDir . '/mail.log',
                '[' . date('Y-m-d H:i:s') . '] ' . $errorMsg . PHP_EOL,
                FILE_APPEND
            );

            return [
                'success' => false,
                'message' => $errorMsg,
            ];
        }
    }

    /**
     * Send email via raw SMTP socket connection.
     */
    private function sendWithSocket(
        string $toEmail,
        string $toName,
        string $subject,
        string $htmlBody,
        string $textBody,
        string $replyToEmail,
        string $replyToName
    ): array
    {
        // Determine connection prefix
        $prefix = '';
        $port = $this->port;

        if ($this->encryption === 'ssl') {
            $prefix = 'ssl://';
        }

        // Connect to SMTP server
        $errno = 0;
        $errstr = '';
        $socket = @fsockopen($prefix . $this->host, $port, $errno, $errstr, 30);

        if (!$socket) {
            return [
                'success' => false,
                'message' => "No se pudo conectar a {$this->host}:{$port} — $errstr ($errno)",
            ];
        }

        $this->smtpExpect($socket, [220], 'saludo del servidor');

        // Say hello
        $helloHost = preg_replace('/[^A-Za-z0-9.-]/', '', $_SERVER['SERVER_NAME'] ?? '') ?: 'localhost';
        $this->smtpCommand($socket, "EHLO {$helloHost}", [250]);

        // STARTTLS if using TLS
        if ($this->encryption === 'tls') {
            $this->smtpCommand($socket, "STARTTLS", [220]);
            // Upgrade to TLS
            if (!stream_socket_enable_crypto($socket, true, STREAM_CRYPTO_METHOD_TLS_CLIENT)) {
                throw new \RuntimeException('No fue posible establecer la conexión TLS.');
            }
            // Re-say hello after TLS
            $this->smtpCommand($socket, "EHLO {$helloHost}", [250]);
        }

        // Authenticate
        $this->smtpCommand($socket, "AUTH LOGIN", [334]);
        $this->smtpCommand($socket, base64_encode($this->username), [334]);
        $this->smtpCommand($socket, base64_encode($this->password), [235]);

        // Mail from
        $this->smtpCommand($socket, "MAIL FROM:<{$this->fromEmail}>", [250]);

        // Rcpt to
        $this->smtpCommand($socket, "RCPT TO:<{$toEmail}>", [250, 251]);

        // Data
        $this->smtpCommand($socket, "DATA", [354]);

        // Build headers
        $boundary = '=_Part_' . bin2hex(random_bytes(16));
        $messageIdHost = preg_replace('/[^A-Za-z0-9.-]/', '', parse_url('https://' . $helloHost, PHP_URL_HOST) ?: '') ?: 'localhost';
        $headers = [
            'From: ' . $this->formatAddress($this->fromEmail, $this->fromName),
            'To: ' . $this->formatAddress($toEmail, $toName),
            'Subject: ' . $this->encodeHeader($subject),
            "MIME-Version: 1.0",
            "Content-Type: multipart/alternative; boundary=\"$boundary\"",
            "Date: " . date('r'),
            'Message-ID: <' . bin2hex(random_bytes(12)) . '@' . $messageIdHost . '>',
            "X-Mailer: GestoriaAmbiental-MVC/1.0",
        ];

        if ($replyToEmail !== '') {
            $headers[] = 'Reply-To: ' . $this->formatAddress($replyToEmail, $replyToName);
        }

        // Build body
        $message = implode("\r\n", $headers) . "\r\n\r\n";
        $message .= "--$boundary\r\n";
        $message .= "Content-Type: text/plain; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= chunk_split(base64_encode($this->normalizeBody($textBody)), 76, "\r\n") . "\r\n";
        $message .= "--$boundary\r\n";
        $message .= "Content-Type: text/html; charset=UTF-8\r\n";
        $message .= "Content-Transfer-Encoding: base64\r\n\r\n";
        $message .= chunk_split(base64_encode($this->normalizeBody($htmlBody)), 76, "\r\n") . "\r\n";
        $message .= "--$boundary--\r\n.\r\n";

        fwrite($socket, $message);
        $this->smtpExpect($socket, [250], 'entrega del mensaje');

        // Quit
        $this->smtpCommand($socket, "QUIT", [221]);

        fclose($socket);

        return [
            'success' => true,
            'message' => "Correo de prueba enviado exitosamente a <strong>" . htmlspecialchars($toEmail) . "</strong>.",
        ];
    }

    /**
     * Send an SMTP command and read response.
     */
    private function smtpCommand($socket, string $command, array $expectedCodes): string
    {
        fwrite($socket, $command . "\r\n");
        return $this->smtpExpect($socket, $expectedCodes, strtok($command, ' '));
    }

    private function smtpExpect($socket, array $expectedCodes, string $stage): string
    {
        $response = $this->smtpRead($socket);
        $code = (int) substr($response, 0, 3);
        if (!in_array($code, $expectedCodes, true)) {
            $safeResponse = trim(preg_replace('/[\r\n]+/', ' ', $response));
            throw new \RuntimeException("SMTP rechazó {$stage}: {$safeResponse}");
        }
        return $response;
    }

    private function formatAddress(string $email, string $name = ''): string
    {
        $name = $this->sanitizeHeader($name);
        if ($name === '' || strcasecmp($name, $email) === 0) {
            return $email;
        }
        if (preg_match('/[^\x20-\x7E]/', $name)) {
            $displayName = $this->encodeHeader($name);
        } else {
            $displayName = '"' . addcslashes($name, '"\\') . '"';
        }
        return $displayName . " <{$email}>";
    }

    private function encodeHeader(string $value): string
    {
        $value = $this->sanitizeHeader($value);
        return preg_match('/[^\x20-\x7E]/', $value)
            ? '=?UTF-8?B?' . base64_encode($value) . '?='
            : $value;
    }

    private function sanitizeHeader(string $value): string
    {
        return trim((string) preg_replace('/[\r\n]+/', ' ', $value));
    }

    private function normalizeBody(string $body): string
    {
        return preg_replace("/\r\n|\r|\n/", "\r\n", $body);
    }

    /**
     * Read SMTP response.
     */
    private function smtpRead($socket): string
    {
        $response = '';
        while ($line = fgets($socket, 512)) {
            $response .= $line;
            // If the 4th character is a space, it's the last line of the response
            if (isset($line[3]) && $line[3] === ' ') {
                break;
            }
        }
        return $response;
    }

    /**
     * Build a nice HTML test email.
     *
     * @return array{0: string, 1: string, 2: string} [subject, htmlBody, textBody]
     */
    public static function buildTestEmail(string $companyName = 'Gestoría Ambiental'): array
    {
        $serverName = $_SERVER['SERVER_NAME'] ?? 'localhost';
        $date = date('d/m/Y H:i:s');
        $year = date('Y');

        $subject = "✅ Prueba de configuración SMTP — {$companyName}";

        $htmlBody = <<<HTML
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prueba SMTP</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f4f4; font-family:'Inter',Arial,Helvetica,sans-serif;">
  <table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f4f4; padding:40px 20px;">
    <tr>
      <td align="center">
        <table width="600" cellpadding="0" cellspacing="0" style="max-width:600px; width:100%; background-color:#ffffff; border-radius:16px; overflow:hidden; box-shadow:0 4px 24px rgba(0,0,0,0.08);">
          <!-- Header -->
          <tr>
            <td style="background: linear-gradient(135deg, #1B3A4B 0%, #2E7D32 100%); padding:40px 30px; text-align:center;">
              <h1 style="color:#ffffff; font-size:24px; font-weight:700; margin:0 0 8px 0;">✅ Configuración SMTP</h1>
              <p style="color:#66BB6A; font-size:16px; margin:0; opacity:0.9;">Correo de prueba</p>
            </td>
          </tr>
          <!-- Body -->
          <tr>
            <td style="padding:40px 30px;">
              <h2 style="color:#1B3A4B; font-size:20px; font-weight:600; margin:0 0 16px 0;">
                ¡Hola! 👋
              </h2>
              <p style="color:#555555; font-size:15px; line-height:1.7; margin:0 0 20px 0;">
                Este es un correo de prueba enviado desde el panel de administración de 
                <strong style="color:#2E7D32;">{$companyName}</strong> para verificar que la 
                configuración SMTP funciona correctamente.
              </p>
              <div style="background-color:#f0fdf4; border-left:4px solid #2E7D32; border-radius:8px; padding:20px; margin:0 0 24px 0;">
                <table width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                    <td style="padding:4px 0;">
                      <span style="color:#666; font-size:13px;">Estado:</span>
                      <span style="color:#2E7D32; font-weight:600; font-size:14px; margin-left:8px;">✅ Envío exitoso</span>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0;">
                      <span style="color:#666; font-size:13px;">Servidor:</span>
                      <span style="color:#1B3A4B; font-size:14px; margin-left:8px;">{$serverName}</span>
                    </td>
                  </tr>
                  <tr>
                    <td style="padding:4px 0;">
                      <span style="color:#666; font-size:13px;">Fecha:</span>
                      <span style="color:#1B3A4B; font-size:14px; margin-left:8px;">{$date}</span>
                    </td>
                  </tr>
                </table>
              </div>
              <p style="color:#555555; font-size:15px; line-height:1.7; margin:0 0 24px 0;">
                Si estás viendo este mensaje, la configuración SMTP es correcta y tu sitio 
                web puede enviar correos electrónicos sin problemas. 🎉
              </p>
              <hr style="border:none; border-top:1px solid #e0e0e0; margin:0 0 24px 0;">
              <p style="color:#999999; font-size:12px; line-height:1.5; margin:0;">
                Este correo fue generado automáticamente desde el panel de administración de 
                <strong>{$companyName}</strong>. No es necesario responder a este mensaje.
              </p>
            </td>
          </tr>
          <!-- Footer -->
          <tr>
            <td style="background-color:#f9f9f9; padding:20px 30px; text-align:center; border-top:1px solid #e0e0e0;">
              <p style="color:#999999; font-size:12px; margin:0;">
                &copy; {$year} {$companyName} — Todos los derechos reservados.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
HTML;

        $textBody = "✅ Prueba de configuración SMTP — {$companyName}\r\n\r\n"
            . "¡Hola!\r\n\r\n"
            . "Este es un correo de prueba enviado desde el panel de administración de {$companyName} "
            . "para verificar que la configuración SMTP funciona correctamente.\r\n\r\n"
            . "Estado: ✅ Envío exitoso\r\n"
            . "Servidor: {$serverName}\r\n"
            . "Fecha: {$date}\r\n\r\n"
            . "Si estás viendo este mensaje, la configuración SMTP es correcta.\r\n\r\n"
            . "— {$companyName}";

        return [$subject, $htmlBody, $textBody];
    }
}
