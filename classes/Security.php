<?php
/**
 * Security Class - Input Validation and Sanitization
 * 
 * This class provides security utilities for sanitizing inputs,
 * CSRF protection, and other security measures.
 */

defined('APP_ACCESS') or die('Direct access not permitted');

class Security {
    
    /**
     * Sanitize string input
     */
    public static function sanitizeString($input) {
        if ($input === null) {
            return null;
        }
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitize email
     */
    public static function sanitizeEmail($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }
    
    /**
     * Validate email
     */
    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Sanitize phone number
     */
    public static function sanitizePhone($phone) {
        // Remove all non-numeric characters except + at the beginning
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        return self::sanitizeString($phone);
    }
    
    /**
     * Validate phone number (Azerbaijan format)
     */
    public static function validatePhone($phone) {
        // Accepts formats: +994XXXXXXXXX, 994XXXXXXXXX, 0XXXXXXXXX
        $pattern = '/^(\+994|994|0)?[1-9][0-9]{8}$/';
        return preg_match($pattern, $phone) === 1;
    }
    
    /**
     * Sanitize integer
     */
    public static function sanitizeInt($input) {
        return filter_var($input, FILTER_SANITIZE_NUMBER_INT);
    }
    
    /**
     * Validate integer
     */
    public static function validateInt($input) {
        return filter_var($input, FILTER_VALIDATE_INT) !== false;
    }
    
    /**
     * Generate CSRF Token
     */
    public static function generateCSRFToken() {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
    
    /**
     * Verify CSRF Token
     */
    public static function verifyCSRFToken($token) {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            return false;
        }
        return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
    
    /**
     * Hash password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_DEFAULT);
    }
    
    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }
    
    /**
     * Escape output (for displaying user content)
     */
    public static function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitize JSON input
     */
    public static function sanitizeJSON($json) {
        $decoded = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return null;
        }
        return $decoded;
    }
    
    /**
     * Generate random token
     */
    public static function generateToken($length = 32) {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Check if user is logged in as admin
     */
    public static function isAdminLoggedIn() {
        return isset($_SESSION[ADMIN_SESSION_NAME]) && !empty($_SESSION[ADMIN_SESSION_NAME]);
    }
    
    /**
     * Require admin login (redirect if not logged in)
     */
    public static function requireAdmin() {
        if (!self::isAdminLoggedIn()) {
            header('Location: /admin/login.php');
            exit;
        }
    }
    
    /**
     * Prevent clickjacking
     */
    public static function setSecurityHeaders() {
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
    }
}
