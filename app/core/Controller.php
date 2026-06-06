<?php

namespace App\Core;

abstract class Controller
{
    /**
     * Render a view with layout.
     *
     * @param string $view   Relative path from app/views/ (without .php)
     * @param array  $data   Variables to extract into the view
     * @param string $layout Layout file from app/views/layouts/ (without .php)
     */
    protected function view(string $view, array $data = [], string $layout = 'main'): void
    {
        // Auto-inject global settings (brand logo, company name, SMTP, etc.)
        if (!isset($data['settings'])) {
            $data['settings'] = \App\Models\Setting::getAll();
        }

        // Auto-inject static services for the navbar dropdown
        if (!isset($data['navbarServices'])) {
            $data['navbarServices'] = \App\Models\ServiceCatalog::navigation();
        }

        // Extract data for the view
        extract($data);

        // Capture the view content
        ob_start();
        require VIEWS_DIR . '/' . $view . '.php';
        $content = ob_get_clean();

        // Render inside layout
        require VIEWS_DIR . '/layouts/' . $layout . '.php';
    }

    /**
     * Render a view WITHOUT a layout (for AJAX, partials, etc.)
     */
    protected function viewPartial(string $view, array $data = []): void
    {
        extract($data);
        require VIEWS_DIR . '/' . $view . '.php';
    }

    /**
     * HTTP redirect.
     */
    protected function redirect(string $url): void
    {
        header('Location: ' . $url);
        exit;
    }

    /**
     * Redirect back to the previous page.
     */
    protected function redirectBack(): void
    {
        $referer = $_SERVER['HTTP_REFERER'] ?? BASE_URL;
        $this->redirect($referer);
    }

    /**
     * Return JSON response.
     */
    protected function json(array $data, int $statusCode = 200): void
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
