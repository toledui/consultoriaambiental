<?php

namespace App\Helpers;

use App\Models\Setting;

class Turnstile
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    public static function isEnabled(?array $settings = null): bool
    {
        $settings ??= Setting::getAll();

        return ($settings['turnstile_enabled'] ?? '0') === '1';
    }

    public static function canRender(?array $settings = null): bool
    {
        $settings ??= Setting::getAll();

        return self::isEnabled($settings)
            && trim((string)($settings['turnstile_site_key'] ?? '')) !== '';
    }

    /**
     * Validate a Turnstile token and bind it to the form action and hostname.
     * Turnstile is bypassed only when the feature is disabled in settings.
     */
    public static function verify(string $token, string $expectedAction, ?array $settings = null): array
    {
        $settings ??= Setting::getAll();
        if (!self::isEnabled($settings)) {
            return ['success' => true, 'enabled' => false];
        }

        if (trim((string)($settings['turnstile_site_key'] ?? '')) === ''
            || trim((string)($settings['turnstile_secret_key'] ?? '')) === '') {
            error_log('Turnstile is enabled but its credentials are incomplete.');
            return self::failure('La verificación de seguridad no está configurada correctamente.');
        }

        $token = trim($token);
        if ($token === '' || strlen($token) > 2048) {
            return self::failure('Completa la verificación de seguridad antes de continuar.');
        }

        $payload = [
            'secret'   => (string)$settings['turnstile_secret_key'],
            'response' => $token,
        ];

        $remoteIp = self::clientIp();
        if ($remoteIp !== null) {
            $payload['remoteip'] = $remoteIp;
        }

        $result = self::requestVerification($payload);
        if ($result === null) {
            error_log('Turnstile validation request failed.');
            return self::failure('No fue posible completar la verificación de seguridad. Intenta nuevamente.');
        }

        if (empty($result['success'])) {
            $codes = is_array($result['error-codes'] ?? null) ? $result['error-codes'] : ['unknown-error'];
            error_log('Turnstile rejected a token: ' . implode(', ', $codes));
            return self::failure('La verificación de seguridad no fue válida. Intenta nuevamente.');
        }

        if (!self::usesOfficialTestSecret((string)$settings['turnstile_secret_key'])) {
            if (($result['action'] ?? '') !== $expectedAction) {
                error_log('Turnstile action mismatch.');
                return self::failure('La verificación de seguridad no corresponde a este formulario.');
            }

            $expectedHostname = self::requestHostname();
            $verifiedHostname = strtolower(trim((string)($result['hostname'] ?? '')));
            if ($expectedHostname !== null && $verifiedHostname !== $expectedHostname) {
                error_log('Turnstile hostname mismatch.');
                return self::failure('La verificación de seguridad no corresponde a este sitio.');
            }
        }

        return ['success' => true, 'enabled' => true];
    }

    private static function requestVerification(array $payload): ?array
    {
        $handle = curl_init(self::VERIFY_URL);
        if ($handle !== false) {
            curl_setopt_array($handle, [
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => http_build_query($payload),
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CONNECTTIMEOUT => 4,
                CURLOPT_TIMEOUT        => 10,
                CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
            ]);

            $response = curl_exec($handle);
            $httpCode = (int)curl_getinfo($handle, CURLINFO_RESPONSE_CODE);
            curl_close($handle);

            if (is_string($response) && $response !== '' && $httpCode === 200) {
                $decoded = json_decode($response, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
        }

        // Fallback for PHP installations where cURL cannot use the system CA store.
        $context = stream_context_create([
            'http' => [
                'method'        => 'POST',
                'header'        => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content'       => http_build_query($payload),
                'timeout'       => 10,
                'ignore_errors' => true,
            ],
        ]);
        $response = @file_get_contents(self::VERIFY_URL, false, $context);
        $statusLine = $http_response_header[0] ?? '';
        if (!is_string($response) || $response === '' || !str_contains($statusLine, ' 200 ')) {
            return null;
        }

        $decoded = json_decode($response, true);
        return is_array($decoded) ? $decoded : null;
    }

    private static function clientIp(): ?string
    {
        foreach (['HTTP_CF_CONNECTING_IP', 'REMOTE_ADDR'] as $key) {
            $value = trim((string)($_SERVER[$key] ?? ''));
            if ($value !== '' && filter_var($value, FILTER_VALIDATE_IP)) {
                return $value;
            }
        }

        return null;
    }

    private static function requestHostname(): ?string
    {
        $httpHost = trim((string)($_SERVER['HTTP_HOST'] ?? ''));
        if ($httpHost === '') {
            return null;
        }

        $hostname = parse_url('http://' . $httpHost, PHP_URL_HOST);
        return is_string($hostname) && $hostname !== '' ? strtolower($hostname) : null;
    }

    private static function usesOfficialTestSecret(string $secret): bool
    {
        return in_array($secret, [
            '1x0000000000000000000000000000000AA',
            '2x0000000000000000000000000000000AA',
            '3x0000000000000000000000000000000AA',
        ], true);
    }

    private static function failure(string $message): array
    {
        return [
            'success' => false,
            'enabled' => true,
            'message' => $message,
        ];
    }
}
