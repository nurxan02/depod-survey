<?php
/**
 * Option Model - Handles all database operations for options
 */

defined('APP_ACCESS') or die('Direct access not permitted');

require_once BASE_PATH . '/classes/Database.php';

class Option {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get option by ID
     */
    public function getOptionById($id) {
        $sql = "SELECT * FROM options WHERE id = :id";
        return $this->db->query($sql)->bind(':id', $id)->fetch();
    }
    
    /**
     * Get all options for a question
     */
    public function getOptionsByQuestionId($questionId) {
        $sql = "SELECT * FROM options WHERE question_id = :question_id ORDER BY id ASC";
        return $this->db->query($sql)->bind(':question_id', $questionId)->fetchAll();
    }
    
    /**
     * Create new option
     */
    public function createOption($questionId, $optionText, $priceValue, $isPremium = false) {
        $sql = "INSERT INTO options (question_id, option_text, price_value, is_premium) 
                VALUES (:question_id, :option_text, :price_value, :is_premium)";
        $this->db->query($sql)
            ->bind(':question_id', $questionId)
            ->bind(':option_text', $optionText)
            ->bind(':price_value', $priceValue)
            ->bind(':is_premium', $isPremium)
            ->execute();
        return $this->db->lastInsertId();
    }
    
    /**
     * Update option
     */
    public function updateOption($id, $optionText, $priceValue, $isPremium = false) {
        $sql = "UPDATE options 
                SET option_text = :option_text, 
                    price_value = :price_value, 
                    is_premium = :is_premium 
                WHERE id = :id";
        return $this->db->query($sql)
            ->bind(':id', $id)
            ->bind(':option_text', $optionText)
            ->bind(':price_value', $priceValue)
            ->bind(':is_premium', $isPremium)
            ->execute();
    }
    
    /**
     * Delete option
     */
    public function deleteOption($id) {
        $sql = "DELETE FROM options WHERE id = :id";
        return $this->db->query($sql)->bind(':id', $id)->execute();
    }
    
    /**
     * Get multiple options by IDs
     */
    public function getOptionsByIds($ids) {
        if (empty($ids)) {
            return [];
        }
        
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $sql = "SELECT * FROM options WHERE id IN ($placeholders)";
        
        $stmt = $this->db->query($sql);
        foreach ($ids as $index => $id) {
            $stmt->bind($index + 1, $id);
        }
        
        return $stmt->fetchAll();
    }
}
