<?php
/**
 * CampusWire — Global Configuration
 * Adjust these values based on your WAMP/XAMPP environment.
 */

// ── Database ─────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');          // Default XAMPP/WAMP password is empty
define('DB_NAME', 'campuswire_db');
define('DB_CHARSET', 'utf8mb4');

// ── Application ──────────────────────────────────
define('APP_NAME', 'CampusWire');
define('APP_URL', 'http://localhost/campuswire-php/public');
define('APP_ROOT', dirname(dirname(__DIR__)));   // points to /campuswire-php
define('PUBLIC_ROOT', APP_ROOT . '/public');

// ── Session ──────────────────────────────────────
define('SESSION_LIFETIME', 86400); // 24 hours

// ── Uploads ──────────────────────────────────────
define('UPLOAD_DIR',         PUBLIC_ROOT . '/uploads/');
define('UPLOAD_DIR_PROFILE', PUBLIC_ROOT . '/uploads/profile/');
define('UPLOAD_DIR_NEWS',    PUBLIC_ROOT . '/uploads/news/');
define('MAX_FILE_SIZE',      5 * 1024 * 1024); // 5 MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'webp']);

// ── Security ─────────────────────────────────────
define('CSRF_TOKEN_NAME', 'csrf_token');

// ── Error Handling (development) ─────────────────
error_reporting(E_ALL);
ini_set('display_errors', 1);
