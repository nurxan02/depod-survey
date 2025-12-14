<?php
/**
 * Admin Model - Handles admin user authentication and management
 */

defined('APP_ACCESS') or die('Direct access not permitted');

require_once BASE_PATH . '/classes/Database.php';
require_once BASE_PATH . '/classes/Security.php';

class Admin {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Authenticate admin user
     */
    public function authenticate($username, $password) {
        $sql = "SELECT * FROM admin_users WHERE username = :username AND is_active = 1";
        $user = $this->db->query($sql)->bind(':username', $username)->fetch();
        
        if ($user && Security::verifyPassword($password, $user['password_hash'])) {
            // Update last login
            $this->updateLastLogin($user['id']);
            
            // Set session
            $_SESSION[ADMIN_SESSION_NAME] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ];
            
            return true;
        }
        
        return false;
    }
    
    /**
     * Logout admin user
     */
    public function logout() {
        unset($_SESSION[ADMIN_SESSION_NAME]);
        session_destroy();
    }
    
    /**
     * Update last login timestamp
     */
    private function updateLastLogin($userId) {
        $sql = "UPDATE admin_users SET last_login = NOW() WHERE id = :id";
        $this->db->query($sql)->bind(':id', $userId)->execute();
    }
    
    /**
     * Get admin user by ID
     */
    public function getAdminById($id) {
        $sql = "SELECT id, username, email, is_active, last_login, created_at FROM admin_users WHERE id = :id";
        return $this->db->query($sql)->bind(':id', $id)->fetch();
    }
    
    /**
     * Get current logged in admin
     */
    public function getCurrentAdmin() {
        if (isset($_SESSION[ADMIN_SESSION_NAME]['id'])) {
            return $this->getAdminById($_SESSION[ADMIN_SESSION_NAME]['id']);
        }
        return null;
    }
    
    /**
     * Update admin password
     */
    public function updatePassword($userId, $newPassword) {
        $passwordHash = Security::hashPassword($newPassword);
        $sql = "UPDATE admin_users SET password_hash = :password_hash WHERE id = :id";
        return $this->db->query($sql)
            ->bind(':id', $userId)
            ->bind(':password_hash', $passwordHash)
            ->execute();
    }
    
    /**
     * Create new admin user
     */
    public function createAdmin($username, $password, $email) {
        $passwordHash = Security::hashPassword($password);
        $sql = "INSERT INTO admin_users (username, password_hash, email) VALUES (:username, :password_hash, :email)";
        $this->db->query($sql)
            ->bind(':username', $username)
            ->bind(':password_hash', $passwordHash)
            ->bind(':email', $email)
            ->execute();
        return $this->db->lastInsertId();
    }
}
