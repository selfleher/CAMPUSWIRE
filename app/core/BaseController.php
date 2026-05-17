<?php
/**
 * CampusWire — Base Controller
 * Common helpers inherited by all controllers.
 */

class BaseController {

    /**
     * Render a view file, passing $data into scope.
     *
     * @param string $view  Path relative to /views/, e.g. "news/index"
     * @param array  $data  Variables available inside the view
     */
    protected function view(string $view, array $data = []): void {
        // Make variables available inside the view
        extract($data);

        // Common variables always available in every view
        $currentUser    = AuthMiddleware::user();
        $flashMessages  = Session::getFlash();
        $csrfToken      = Session::generateCSRF();
        $appUrl         = APP_URL;

        $viewFile = APP_ROOT . '/views/' . $view . '.php';
        if (!file_exists($viewFile)) {
            http_response_code(404);
            echo '<div style="font-family:Inter,sans-serif;padding:40px;text-align:center;">';
            echo '<h1 style="color:#EF4444;">404 — View Not Found</h1>';
            echo '<p style="color:#6B7280;">View file missing: <code>' . htmlspecialchars($view) . '.php</code></p>';
            echo '<a href="' . APP_URL . '" style="color:#4F46E5;">Go Home</a>';
            echo '</div>';
            return;
        }
        require $viewFile;
    }

    /**
     * Redirect helper.
     */
    protected function redirect(string $path): void {
        header('Location: ' . APP_URL . '/' . ltrim($path, '/'));
        exit;
    }

    /**
     * Return a JSON response (for AJAX endpoints).
     */
    protected function json(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Validate CSRF on POST requests.
     * FIX: Added null coalescing for HTTP_REFERER which may not be set.
     */
    protected function validateCSRF(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return; // Only validate on POST
        }
        $token = $_POST[CSRF_TOKEN_NAME] ?? '';
        if (!Session::validateCSRF($token)) {
            Session::flash('error', 'Security token expired or invalid. Please try again.');
            $referer = $_SERVER['HTTP_REFERER'] ?? APP_URL;
            header('Location: ' . $referer);
            exit;
        }
    }
}
