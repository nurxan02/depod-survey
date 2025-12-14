<?php
/**
 * Settings Model - Handles system settings
 */

defined('APP_ACCESS') or die('Direct access not permitted');

require_once BASE_PATH . '/classes/Database.php';

class Settings {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get setting by key
     */
    public function getSetting($key) {
        $sql = "SELECT setting_value FROM settings WHERE setting_key = :key";
        $result = $this->db->query($sql)->bind(':key', $key)->fetch();
        return $result ? $result['setting_value'] : null;
    }
    
    /**
     * Get all settings
     */
    public function getAllSettings() {
        $sql = "SELECT * FROM settings ORDER BY setting_key ASC";
        $results = $this->db->query($sql)->fetchAll();
        
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }
    
    /**
     * Update setting
     */
    public function updateSetting($key, $value) {
        $sql = "INSERT INTO settings (setting_key, setting_value) 
                VALUES (:key, :value) 
                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)";
        return $this->db->query($sql)
            ->bind(':key', $key)
            ->bind(':value', $value)
            ->execute();
    }
    
    /**
     * Update multiple settings at once
     */
    public function updateSettings($settings) {
        foreach ($settings as $key => $value) {
            $this->updateSetting($key, $value);
        }
        return true;
    }
}
