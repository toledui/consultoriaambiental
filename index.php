<?php

/**
 * Front Controller — all requests enter here.
 */

// ─── Static File Serving ────────────────────────────────────────────
// For PHP built-in server: serve files from /public/ directory
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = urldecode($uri);
$publicPath = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($publicPath) && is_file($publicPath)) {
    $mimeTypes = [
        'ico'  => 'image/x-icon',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png'  => 'image/png',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'webp' => 'image/webp',
        'mp4'  => 'video/mp4',
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'pdf'  => 'application/pdf',
        'doc'  => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
    $ext = strtolower(pathinfo($publicPath, PATHINFO_EXTENSION));
    if (isset($mimeTypes[$ext])) {
        $mtime = filemtime($publicPath);
        $etag = '"' . md5($publicPath . '|' . $mtime . '|' . filesize($publicPath)) . '"';

        header('Cache-Control: public, max-age=31536000, immutable');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $mtime) . ' GMT');
        header('ETag: ' . $etag);

        $clientEtag = $_SERVER['HTTP_IF_NONE_MATCH'] ?? '';
        $clientModified = $_SERVER['HTTP_IF_MODIFIED_SINCE'] ?? '';
        if ($clientEtag === $etag || strtotime($clientModified) === $mtime) {
            http_response_code(304);
            return;
        }

        header('Content-Type: ' . $mimeTypes[$ext]);
        readfile($publicPath);
        return;
    }
}

// Load bootstrap
require_once __DIR__ . '/config/init.php';

$requestPath = (string) (parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/');

// Keep private and transactional endpoints out of search results even when a
// crawler discovers their URLs elsewhere.
if (str_starts_with($requestPath, '/admin')
    || str_starts_with($requestPath, '/checklist/')
    || $requestPath === '/contacto/gracias') {
    header('X-Robots-Tag: noindex, nofollow');
}

// Redirect trailing-slash duplicates so links, sitemap and HTTP signals agree.
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $requestPath !== '/' && str_ends_with($requestPath, '/')) {
    $normalizedPath = rtrim($requestPath, '/');
    $query = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_QUERY);
    $location = canonical_url($normalizedPath) . ($query ? '?' . $query : '');
    header('Location: ' . $location, true, 301);
    exit;
}

use App\Core\Router;

// Load routes
$router = new Router();

// ─── Public Routes ────────────────────────────────────────────────
$router->get('/',                  'HomeController@index');
$router->get('/nosotros',          'HomeController@nosotros');
$router->get('/contacto',          'HomeController@contacto');
$router->post('/contacto',         'HomeController@contacto');
$router->get('/contacto/gracias',  'HomeController@gracias');
$router->get('/aviso-de-privacidad', 'HomeController@avisoPrivacidad');
$router->get('/robots.txt',          'SeoController@robots');
$router->get('/sitemap.xml',         'SeoController@sitemap');

// Checklist download lead capture (AJAX)
$router->post('/checklist/descargar', 'HomeController@checklistDownload');

$router->get('/blog',              'BlogController@index');
$router->post('/blog/newsletter',  'HomeController@newsletter');
$router->get('/blog/{slug}',       'BlogController@show');

$router->get('/servicios',         'ServiceController@index');
$router->get('/servicios/{slug}',  'ServiceController@show');

// ─── Admin Routes ─────────────────────────────────────────────────
$router->get('/admin/login',              'Admin\AuthController@loginForm');
$router->post('/admin/login',             'Admin\AuthController@login');
$router->get('/admin/logout',             'Admin\AuthController@logout');

$router->get('/admin',                    'Admin\DashboardController@index');
$router->get('/admin/dashboard',          'Admin\DashboardController@index');

// Admin — Blog CRUD
$router->get('/admin/blog',               'Admin\BlogController@index');
$router->get('/admin/blog/crear',         'Admin\BlogController@create');
$router->post('/admin/blog/crear',        'Admin\BlogController@store');
$router->get('/admin/blog/editar/{id}',   'Admin\BlogController@edit');
$router->post('/admin/blog/editar/{id}',  'Admin\BlogController@update');
$router->post('/admin/blog/eliminar/{id}','Admin\BlogController@destroy');

// Admin — Blog Categories CRUD
$router->get('/admin/blog/categorias',                 'Admin\BlogController@categories');
$router->get('/admin/blog/categorias/crear',           'Admin\BlogController@createCategory');
$router->post('/admin/blog/categorias/crear',          'Admin\BlogController@storeCategory');
$router->get('/admin/blog/categorias/editar/{id}',     'Admin\BlogController@editCategory');
$router->post('/admin/blog/categorias/editar/{id}',    'Admin\BlogController@updateCategory');
$router->post('/admin/blog/categorias/eliminar/{id}',  'Admin\BlogController@destroyCategory');

// Admin - Users CRUD
$router->get('/admin/usuarios',                'Admin\UserController@index');
$router->get('/admin/usuarios/crear',          'Admin\UserController@create');
$router->post('/admin/usuarios/crear',         'Admin\UserController@store');
$router->get('/admin/usuarios/editar/{id}',    'Admin\UserController@edit');
$router->post('/admin/usuarios/editar/{id}',   'Admin\UserController@update');
$router->post('/admin/usuarios/eliminar/{id}', 'Admin\UserController@destroy');

// Admin — Settings
$router->get('/admin/settings',                    'Admin\SettingController@index');
$router->get('/admin/settings/smtp',               'Admin\SettingController@smtp');
$router->post('/admin/settings/smtp/guardar',      'Admin\SettingController@saveSmtp');
$router->post('/admin/settings/smtp/probar',       'Admin\SettingController@testSmtp');
$router->get('/admin/settings/brand',              'Admin\SettingController@brand');
$router->post('/admin/settings/brand/guardar',     'Admin\SettingController@saveBrand');
$router->post('/admin/settings/social/guardar',    'Admin\SettingController@saveSocial');
$router->post('/admin/settings/footer/guardar',    'Admin\SettingController@saveFooter');
$router->post('/admin/settings/contacto/guardar',  'Admin\SettingController@saveContacto');
$router->post('/admin/settings/privacidad/guardar','Admin\SettingController@savePrivacidad');
$router->post('/admin/settings/codigo/guardar',    'Admin\SettingController@saveCodigo');

// Admin — Contacts
$router->get('/admin/contactos',                   'Admin\ContactController@index');
$router->get('/admin/contactos/csv',               'Admin\ContactController@csv');
$router->get('/admin/contactos/{id}',              'Admin\ContactController@show');
$router->post('/admin/contactos/eliminar/{id}',    'Admin\ContactController@destroy');

// Admin — Media Manager
$router->get('/admin/media',                       'Admin\MediaController@index');
$router->post('/admin/media/subir',                'Admin\MediaController@upload');
$router->post('/admin/media/eliminar/{id}',        'Admin\MediaController@destroy');
$router->get('/admin/media/browse',                'Admin\MediaController@browse');
$router->post('/admin/media/alt/actualizar',       'Admin\MediaController@updateAlt');

// Admin — Checklist Downloads
$router->get('/admin/checklist',                   'Admin\ChecklistController@index');
$router->get('/admin/checklist/csv',               'Admin\ChecklistController@csv');
$router->post('/admin/checklist/eliminar/{id}',    'Admin\ChecklistController@destroy');

// Dispatch
$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
