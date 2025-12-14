<?php
/**
 * Product Model - Handles all database operations for products
 */

defined('APP_ACCESS') or die('Direct access not permitted');

require_once BASE_PATH . '/classes/Database.php';

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    /**
     * Get all active products
     */
    public function getAllProducts($activeOnly = true) {
        $sql = "SELECT * FROM products";
        if ($activeOnly) {
            $sql .= " WHERE is_active = 1";
        }
        $sql .= " ORDER BY base_price ASC";
        return $this->db->query($sql)->fetchAll();
    }
    
    /**
     * Get product by ID
     */
    public function getProductById($id) {
        $sql = "SELECT * FROM products WHERE id = :id";
        return $this->db->query($sql)->bind(':id', $id)->fetch();
    }
    
    /**
     * Create new product
     */
    public function createProduct($name, $basePrice, $optimalFitScore, $description, $imageUrl = null) {
        $sql = "INSERT INTO products (product_name, base_price, optimal_fit_score, description, image_url) 
                VALUES (:name, :base_price, :optimal_fit_score, :description, :image_url)";
        $this->db->query($sql)
            ->bind(':name', $name)
            ->bind(':base_price', $basePrice)
            ->bind(':optimal_fit_score', $optimalFitScore)
            ->bind(':description', $description)
            ->bind(':image_url', $imageUrl)
            ->execute();
        return $this->db->lastInsertId();
    }
    
    /**
     * Update product
     */
    public function updateProduct($id, $name, $basePrice, $optimalFitScore, $description, $productImage = null, $birmarketLink = null, $features = null, $isActive = true) {
        $sql = "UPDATE products 
                SET product_name = :name, 
                    base_price = :base_price, 
                    optimal_fit_score = :optimal_fit_score, 
                    description = :description, 
                    product_image = :product_image,
                    birmarket_link = :birmarket_link,
                    features = :features,
                    is_active = :is_active
                WHERE id = :id";
        return $this->db->query($sql)
            ->bind(':id', $id)
            ->bind(':name', $name)
            ->bind(':base_price', $basePrice)
            ->bind(':optimal_fit_score', $optimalFitScore)
            ->bind(':description', $description)
            ->bind(':product_image', $productImage)
            ->bind(':birmarket_link', $birmarketLink)
            ->bind(':features', $features)
            ->bind(':is_active', $isActive)
            ->execute();
    }
    
    /**
     * Delete product
     */
    public function deleteProduct($id) {
        $sql = "DELETE FROM products WHERE id = :id";
        return $this->db->query($sql)->bind(':id', $id)->execute();
    }
    
    /**
     * Recommend product based on user selections
     * 
     * @param int $totalPrice Total calculated price from selections
     * @param array $selectedOptions Array of selected option details
     * @return array|null Recommended product
     */
    public function recommendProduct($totalPrice, $selectedOptions) {
        $products = $this->getAllProducts();
        
        // Check if ANC was selected
        $ancRequired = false;
        $isPremiumUser = false;
        
        foreach ($selectedOptions as $option) {
            if (isset($option['is_premium']) && $option['is_premium']) {
                $isPremiumUser = true;
            }
            // Check if option text contains ANC keywords
            if (strpos(strtolower($option['option_text']), 'anc') !== false && 
                strpos(strtolower($option['option_text']), 'vacib deyil') === false) {
                $ancRequired = true;
            }
        }
        
        // Recommendation logic
        $recommendedProduct = null;
        
        foreach ($products as $product) {
            $fitScore = json_decode($product['optimal_fit_score'], true);
            
            // Skip if fit score is invalid
            if (!$fitScore) continue;
            
            // Check price range
            if ($totalPrice >= $fitScore['min_price'] && $totalPrice <= $fitScore['max_price']) {
                // Check ANC requirement
                if (isset($fitScore['anc_required']) && $fitScore['anc_required'] && !$ancRequired) {
                    continue;
                }
                
                // If ANC is required by user, prioritize products with ANC
                if ($ancRequired && isset($fitScore['anc_required']) && $fitScore['anc_required']) {
                    $recommendedProduct = $product;
                    break;
                }
                
                // If no specific ANC requirement, recommend based on price
                if (!$recommendedProduct || 
                    abs($product['base_price'] - $totalPrice) < abs($recommendedProduct['base_price'] - $totalPrice)) {
                    $recommendedProduct = $product;
                }
            }
        }
        
        // If no match found, return product closest to calculated price
        if (!$recommendedProduct && !empty($products)) {
            $recommendedProduct = $products[0];
            foreach ($products as $product) {
                if (abs($product['base_price'] - $totalPrice) < abs($recommendedProduct['base_price'] - $totalPrice)) {
                    $recommendedProduct = $product;
                }
            }
        }
        
        return $recommendedProduct;
    }
}
