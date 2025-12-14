<?php
/**
 * Depod Survey Application - Configuration File
 * 
 * This file contains all configuration settings including database credentials,
 * application settings, and security configurations.
 */

// Prevent direct access
defined('APP_ACCESS') or die('Direct access not permitted');

// Database Configuration
// Check if running in Docker
$dbHost = getenv('DB_HOST') ?: '127.0.0.1';
$dbName = getenv('DB_NAME') ?: 'depod_survey';
$dbUser = getenv('DB_USER') ?: 'depod_user';
$dbPass = getenv('DB_PASS') ?: 'depod_pass_2025';

define('DB_HOST', $dbHost);
define('DB_NAME', $dbName);
define('DB_USER', $dbUser);
define('DB_PASS', $dbPass);
define('DB_CHARSET', 'utf8mb4');

// Application Configuration
define('APP_NAME', 'Depod Survey');
define('APP_URL', 'http://localhost/depod-survey');
define('BASE_PATH', dirname(__DIR__));

// Currency Configuration
if (!defined('CURRENCY_SYMBOL')) {
    define('CURRENCY_SYMBOL', '₼');
}

// Security Configuration
define('SESSION_LIFETIME', 7200); // 2 hours
define('CSRF_TOKEN_NAME', 'csrf_token');

// Admin Configuration
define('ADMIN_SESSION_NAME', 'depod_admin_user');
define('ADMIN_PATH', '/admin');

// Application Settings
if (!defined('CURRENCY_SYMBOL')) {
    define('CURRENCY_SYMBOL', '₼');
}
if (!defined('DEFAULT_LANGUAGE')) {
    define('DEFAULT_LANGUAGE', 'az');
}

// Error Reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Session Configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 if using HTTPS

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Timezone
date_default_timezone_set('Asia/Baku');
