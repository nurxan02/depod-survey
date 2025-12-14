<?php
/**
 * API Endpoint - Get Product Recommendation
 */

define('APP_ACCESS', true);
require_once dirname(__DIR__) . '/config/config.php';
require_once BASE_PATH . '/classes/Product.php';
require_once BASE_PATH . '/classes/Option.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get JSON input
$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!isset($data['selections']) || !isset($data['total_price'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

try {
    $selections = $data['selections'];
    $totalPrice = (int)$data['total_price'];
    
    // Get selected options details
    $optionModel = new Option();
    $selectedOptions = [];
    
    foreach ($selections as $questionId => $optionId) {
        $option = $optionModel->getOptionById($optionId);
        if ($option) {
            $selectedOptions[] = $option;
        }
    }
    
    // Get product recommendation
    $productModel = new Product();
    $recommendedProduct = $productModel->recommendProduct($totalPrice, $selectedOptions);
    
    if ($recommendedProduct) {
        echo json_encode([
            'success' => true,
            'product' => $recommendedProduct,
            'calculated_price' => $totalPrice
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'No suitable product found'
        ], JSON_UNESCAPED_UNICODE);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
