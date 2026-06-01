<?php

namespace App\Core;

class Router
{
    private array $routes = [];

    public function get(string $pattern, string $handler): void
    {
        $this->routes[] = ['GET', $pattern, $handler];
    }

    public function post(string $pattern, string $handler): void
    {
        $this->routes[] = ['POST', $pattern, $handler];
    }

    /**
     * Convert route pattern like /blog/{slug} into regex.
     */
    private function patternToRegex(string $pattern): string
    {
        // Escape slashes and dots, then replace {param} with named groups
        $regex = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
        return '#^' . $regex . '$#';
    }

    /**
     * Dispatch the current request to the matching controller method.
     */
    public function dispatch(string $method, string $uri): void
    {
        // Strip query string
        $uri = parse_url($uri, PHP_URL_PATH);
        $uri = rtrim($uri, '/') ?: '/';

        foreach ($this->routes as [$routeMethod, $pattern, $handler]) {
            if ($routeMethod !== strtoupper($method)) {
                continue;
            }

            $regex = $this->patternToRegex($pattern);

            if (preg_match($regex, $uri, $matches)) {
                [$controllerName, $action] = explode('@', $handler);

                // Build full namespace
                $controllerClass = 'App\\Controllers\\' . $controllerName;

                if (!class_exists($controllerClass)) {
                    http_response_code(500);
                    echo "Controller not found: {$controllerClass}";
                    return;
                }

                $controller = new $controllerClass();

                if (!method_exists($controller, $action)) {
                    http_response_code(500);
                    echo "Method not found: {$controllerClass}::{$action}";
                    return;
                }

                // Extract named params only (skip numeric keys)
                $params = array_filter($matches, fn($key) => !is_int($key), ARRAY_FILTER_USE_KEY);

                $controller->$action(...$params);
                return;
            }
        }

        // 404
        http_response_code(404);
        $this->load404View();
    }

    private function load404View(): void
    {
        $title = 'Página no encontrada';
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="utf-8"/>
            <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
            <title>404 — <?= APP_NAME ?></title>
            <script src="https://cdn.tailwindcss.com"></script>
            <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
            <style>body { font-family: 'Inter', sans-serif; }</style>
        </head>
        <body class="bg-ca-bg flex items-center justify-center min-h-screen">
            <div class="text-center px-6">
                <h1 class="text-9xl font-extrabold text-ca-navy">404</h1>
                <p class="text-2xl text-ca-dark-gray mt-4">Página no encontrada</p>
                <p class="text-ca-light-gray mt-2 mb-8">La página que buscas no existe o ha sido movida.</p>
                <a href="<?= BASE_URL ?>" class="inline-block bg-ca-green hover:bg-green-700 text-white font-bold py-3 px-8 rounded-full transition-all">
                    Volver al inicio
                </a>
            </div>
        </body>
        </html>
        <?php
        echo ob_get_clean();
    }
}
