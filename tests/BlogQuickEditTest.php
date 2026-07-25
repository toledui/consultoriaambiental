<?php

require dirname(__DIR__) . '/config/init.php';

use App\Controllers\Admin\BlogController;

$controller = new BlogController();
$assertions = 0;

$assertSame = static function (mixed $expected, mixed $actual, string $message) use (&$assertions): void {
    $assertions++;
    if ($expected !== $actual) {
        throw new RuntimeException(
            $message . ' Esperado: ' . var_export($expected, true)
            . '; recibido: ' . var_export($actual, true)
        );
    }
};

$assertTrue = static function (bool $condition, string $message) use (&$assertions): void {
    $assertions++;
    if (!$condition) {
        throw new RuntimeException($message);
    }
};

$normalizeDate = new ReflectionMethod(BlogController::class, 'normalizePublishedAt');
$csrfToken = new ReflectionMethod(BlogController::class, 'quickEditCsrfToken');
$verifyCsrf = new ReflectionMethod(BlogController::class, 'verifyQuickEditCsrfToken');

$assertSame(
    '2026-08-15 09:30:00',
    $normalizeDate->invoke($controller, '2026-08-15T09:30'),
    'Debe normalizar datetime-local.'
);
$assertSame(
    '2026-08-15 09:30:00',
    $normalizeDate->invoke($controller, '2026-08-15 09:30:00'),
    'Debe aceptar el formato almacenado.'
);
$assertSame(
    null,
    $normalizeDate->invoke($controller, '2026-02-30T09:30'),
    'Debe rechazar fechas de calendario imposibles.'
);
$assertSame(
    null,
    $normalizeDate->invoke($controller, ''),
    'Una fecha vacía debe convertirse en null.'
);

unset($_SESSION['blog_quick_edit_csrf']);
$token = $csrfToken->invoke($controller);
$assertTrue(
    is_string($token) && strlen($token) === 64,
    'Debe generar un token CSRF aleatorio.'
);
$assertTrue(
    $verifyCsrf->invoke($controller, $token) === true,
    'Debe aceptar el token CSRF de la sesión.'
);
$assertTrue(
    $verifyCsrf->invoke($controller, 'incorrecto') === false,
    'Debe rechazar un token CSRF distinto.'
);

unset($_SESSION['blog_quick_edit_csrf']);
echo "OK ({$assertions} assertions)\n";
