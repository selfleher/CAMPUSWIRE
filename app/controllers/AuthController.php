<?php
/**
 * Auth Controller — Login, Register, Logout, Forgot Password.
 */

class AuthController extends BaseController {

    /**
     * GET /auth/login — Show login form.
     */
    public function login(): void {
        // If already logged in, redirect to feed
        if (Session::has('user_id')) {
            $this->redirect('feed');
        }
        $this->view('auth/login');
    }

    /**
     * POST /auth/login (or /auth/loginPost) — Process login.
     */
    public function loginPost(): void {
        $this->validateCSRF();

        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!$email || !$password) {
            Session::flash('error', 'Email and password are required.');
            $this->redirect('auth/login');
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid email address.');
            $this->redirect('auth/login');
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if ($user && $userModel->verifyPassword($password, $user['password_hash'])) {
            // Check if active
            if (isset($user['is_active']) && !$user['is_active']) {
                Session::flash('error', 'Your account has been deactivated. Please contact admin.');
                $this->redirect('auth/login');
                return;
            }

            Session::regenerate();
            Session::set('user_id',          $user['id']);
            Session::set('user_name',        $user['name']);
            Session::set('user_email',       $user['email']);
            Session::set('user_role',        $user['role']);
            // FIX: Store profile_pic in session so sidebar avatar works
            Session::set('user_profile_pic', $user['profile_pic'] ?? null);

            Session::flash('success', 'Welcome back, ' . htmlspecialchars($user['name']) . '!');
            $this->redirect('feed');
        } else {
            Session::flash('error', 'Invalid email or password.');
            $this->redirect('auth/login');
        }
    }

    /**
     * GET /auth/register — Show register form.
     */
    public function register(): void {
        if (Session::has('user_id')) {
            $this->redirect('feed');
        }
        $this->view('auth/register');
    }

    /**
     * POST /auth/register (or /auth/registerPost) — Process registration.
     */
    public function registerPost(): void {
        $this->validateCSRF();

        $name     = trim($_POST['name'] ?? '');
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $role     = $_POST['role'] ?? 'student';

        // Validation
        if (!$name || !$email || !$password) {
            Session::flash('error', 'All fields are required.');
            $this->redirect('auth/register');
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid email address.');
            $this->redirect('auth/register');
            return;
        }
        if (strlen($password) < 6) {
            Session::flash('error', 'Password must be at least 6 characters.');
            $this->redirect('auth/register');
            return;
        }
        if (strlen($name) < 2 || strlen($name) > 100) {
            Session::flash('error', 'Name must be between 2 and 100 characters.');
            $this->redirect('auth/register');
            return;
        }
        if (!in_array($role, ['student', 'faculty'])) {
            $role = 'student';
        }

        $userModel = new UserModel();

        // Check existing
        if ($userModel->findByEmail($email)) {
            Session::flash('error', 'An account with this email already exists.');
            $this->redirect('auth/register');
            return;
        }

        $userId = $userModel->create($name, $email, $password, $role);

        // Auto-login after register
        Session::regenerate();
        Session::set('user_id',          $userId);
        Session::set('user_name',        $name);
        Session::set('user_email',       $email);
        Session::set('user_role',        $role);
        Session::set('user_profile_pic', null);

        Session::flash('success', 'Registration successful! Welcome to CampusWire.');
        $this->redirect('feed');
    }

    /**
     * GET /auth/logout — Destroy session and redirect.
     */
    public function logout(): void {
        Session::destroy();
        // Re-start session for flash
        Session::start();
        Session::flash('success', 'You have been logged out successfully.');
        $this->redirect('auth/login');
    }

    /**
     * GET /auth/forgot — Show forgot password form.
     */
    public function forgot(): void {
        $this->view('auth/forgot');
    }

    /**
     * POST /auth/forgot (or /auth/forgotPost) — Process password reset.
     */
    public function forgotPost(): void {
        $this->validateCSRF();

        $email       = trim($_POST['email'] ?? '');
        $newPassword = $_POST['new_password'] ?? '';

        if (!$email || !$newPassword) {
            Session::flash('error', 'All fields are required.');
            $this->redirect('auth/forgot');
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Session::flash('error', 'Please enter a valid email address.');
            $this->redirect('auth/forgot');
            return;
        }
        if (strlen($newPassword) < 6) {
            Session::flash('error', 'New password must be at least 6 characters.');
            $this->redirect('auth/forgot');
            return;
        }

        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        if ($user) {
            $userModel->updatePassword($user['id'], $newPassword);
            Session::flash('success', 'Password updated successfully! Please sign in with your new password.');
        } else {
            Session::flash('error', 'No account found with that email address.');
        }
        $this->redirect('auth/login');
    }
}
