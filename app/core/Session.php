<?php
/**
 * CampusWire — Session Helper
 * Start / manage sessions with security measures.
 */

class Session {

    /**
     * Start session with secure settings (call once at entry point).
     */
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_strict_mode', '1');
            ini_set('session.cookie_samesite', 'Lax');
            session_set_cookie_params(SESSION_LIFETIME);
            session_start();
        }
    }

    /**
     * Set a session value.
     */
    public static function set(string $key, $value): void {
        $_SESSION[$key] = $value;
    }

    /**
     * Get a session value.
     */
    public static function get(string $key, $default = null) {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * Check if a key exists.
     */
    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    /**
     * Remove a key.
     */
    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    /**
     * Destroy the entire session (logout).
     */
    public static function destroy(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $p = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $p['path'], $p['domain'], $p['secure'], $p['httponly']
            );
        }
        session_destroy();
    }

    /**
     * Regenerate session ID (call after login to prevent fixation).
     */
    public static function regenerate(): void {
        session_regenerate_id(true);
    }

    // ── Flash Messages ───────────────────────────
    public static function flash(string $type, string $message): void {
        $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
    }

    public static function getFlash(): array {
        $msgs = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);
        return $msgs;
    }

    // ── CSRF Protection ──────────────────────────
    // FIX: Only generate a new token if one doesn't exist yet.
    // This prevents the token from changing between form renders and POST validation.
    public static function generateCSRF(): string {
        if (empty($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }

    public static function validateCSRF(string $token): bool {
        $valid = isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
        // Rotate the token after validation to prevent reuse
        if ($valid) {
            unset($_SESSION[CSRF_TOKEN_NAME]);
        }
        return $valid;
    }
}
