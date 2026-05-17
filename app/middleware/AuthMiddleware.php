<?php
/**
 * CampusWire — Auth Middleware
 * Call these static methods at the top of any protected controller action.
 */

class AuthMiddleware {

    /**
     * Require any logged-in user. Redirect to login otherwise.
     */
    public static function requireLogin(): void {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Please log in first.');
            header('Location: ' . APP_URL . '/auth/login');
            exit;
        }
    }

    /**
     * Require one of the given roles (e.g. ['admin', 'faculty']).
     */
    public static function requireRole(array $roles): void {
        self::requireLogin();
        $userRole = Session::get('user_role');
        if (!in_array($userRole, $roles, true)) {
            Session::flash('error', 'You do not have permission to access that page.');
            header('Location: ' . APP_URL . '/feed');
            exit;
        }
    }

    /**
     * Retrieve the currently logged-in user's data from the session.
     * Returns an associative array or null.
     * FIX: Added profile_pic to the returned array so views can display the avatar image.
     */
    public static function user(): ?array {
        if (!Session::has('user_id')) return null;
        return [
            'id'          => Session::get('user_id'),
            'name'        => Session::get('user_name'),
            'email'       => Session::get('user_email'),
            'role'        => Session::get('user_role'),
            'profile_pic' => Session::get('user_profile_pic', null),
        ];
    }
}
