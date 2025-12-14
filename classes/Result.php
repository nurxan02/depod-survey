<?php
/**
 * Result Model - Handles all database operations for survey results
 */

defined('APP_ACCESS') or die('Direct access not permitted');

require_once BASE_PATH . '/classes/Database.php';

class Result {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Save survey result
     */
    public function saveResult($userName, $userSurname, $phoneNumber, $calculatedPrice, $selectedProductId, $selectionDetails) {
        $sql = "INSERT INTO results 
                (user_name, user_surname, phone_number, calculated_price, selected_product_id, selection_details) 
                VALUES (:user_name, :user_surname, :phone_number, :calculated_price, :selected_product_id, :selection_details)";
        
        // Convert selection details to JSON
        $selectionJson = json_encode($selectionDetails, JSON_UNESCAPED_UNICODE);
        
        $this->db->query($sql)
            ->bind(':user_name', $userName)
            ->bind(':user_surname', $userSurname)
            ->bind(':phone_number', $phoneNumber)
            ->bind(':calculated_price', $calculatedPrice)
            ->bind(':selected_product_id', $selectedProductId)
            ->bind(':selection_details', $selectionJson)
            ->execute();
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Get all results with product details
     */
    public function getAllResults($limit = 100, $offset = 0) {
        $sql = "SELECT r.*, p.product_name, p.base_price as product_base_price 
                FROM results r 
                LEFT JOIN products p ON r.selected_product_id = p.id 
                ORDER BY r.created_at DESC 
                LIMIT :limit OFFSET :offset";
        
        return $this->db->query($sql)
            ->bind(':limit', $limit, PDO::PARAM_INT)
            ->bind(':offset', $offset, PDO::PARAM_INT)
            ->fetchAll();
    }
    
    /**
     * Get result by ID
     */
    public function getResultById($id) {
        $sql = "SELECT r.*, p.product_name, p.base_price as product_base_price, p.description as product_description 
                FROM results r 
                LEFT JOIN products p ON r.selected_product_id = p.id 
                WHERE r.id = :id";
        
        $result = $this->db->query($sql)->bind(':id', $id)->fetch();
        
        if ($result) {
            $result['selection_details'] = json_decode($result['selection_details'], true);
        }
        
        return $result;
    }
    
    /**
     * Get total results count
     */
    public function getResultsCount() {
        $sql = "SELECT COUNT(*) as count FROM results";
        $result = $this->db->query($sql)->fetch();
        return $result['count'];
    }
    
    /**
     * Delete result
     */
    public function deleteResult($id) {
        $sql = "DELETE FROM results WHERE id = :id";
        return $this->db->query($sql)->bind(':id', $id)->execute();
    }
    
    /**
     * Get statistics
     */
    public function getStatistics() {
        $stats = [];
        
        // Total submissions
        $sql = "SELECT COUNT(*) as total FROM results";
        $stats['total_submissions'] = $this->db->query($sql)->fetch()['total'];
        
        // Average price
        $sql = "SELECT AVG(calculated_price) as avg_price FROM results";
        $result = $this->db->query($sql)->fetch();
        $stats['average_price'] = $result && $result['avg_price'] ? round($result['avg_price'], 2) : 0;
        
        // Most recommended product
        $sql = "SELECT p.product_name, COUNT(*) as count 
                FROM results r 
                JOIN products p ON r.selected_product_id = p.id 
                GROUP BY r.selected_product_id 
                ORDER BY count DESC 
                LIMIT 1";
        $topProduct = $this->db->query($sql)->fetch();
        $stats['top_product'] = $topProduct ? $topProduct['product_name'] : 'N/A';
        
        // Recent submissions (last 7 days)
        $sql = "SELECT COUNT(*) as count FROM results WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $stats['recent_submissions'] = $this->db->query($sql)->fetch()['count'];
        
        return $stats;
    }
    
    /**
     * Search results
     */
    public function searchResults($keyword) {
        $sql = "SELECT r.*, p.product_name 
                FROM results r 
                LEFT JOIN products p ON r.selected_product_id = p.id 
                WHERE r.user_name LIKE :keyword 
                   OR r.user_surname LIKE :keyword 
                   OR r.phone_number LIKE :keyword 
                ORDER BY r.created_at DESC";
        
        return $this->db->query($sql)
            ->bind(':keyword', '%' . $keyword . '%')
            ->fetchAll();
    }
}
