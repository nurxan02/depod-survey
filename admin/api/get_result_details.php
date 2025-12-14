<?php
/**
 * API Endpoint - Get Result Details
 */

define('APP_ACCESS', true);
require_once dirname(dirname(__DIR__)) . '/config/config.php';
require_once BASE_PATH . '/classes/Security.php';
require_once BASE_PATH . '/classes/Result.php';
require_once BASE_PATH . '/classes/Question.php';
require_once BASE_PATH . '/classes/Option.php';

// Check admin authentication
Security::requireAdmin();

header('Content-Type: application/json');

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing ID']);
    exit;
}

try {
    $resultId = (int)$_GET['id'];
    
    $resultModel = new Result();
    $result = $resultModel->getResultById($resultId);
    
    if (!$result) {
        echo json_encode(['success' => false, 'message' => 'Result not found']);
        exit;
    }
    
    // Parse selection details and get full information
    $questionModel = new Question();
    $optionModel = new Option();
    
    $selections = [];
    if (!empty($result['selection_details'])) {
        $selectionData = is_array($result['selection_details']) ? $result['selection_details'] : [];
        
        foreach ($selectionData as $questionId => $optionId) {
            $question = $questionModel->getQuestionById($questionId);
            $option = $optionModel->getOptionById($optionId);
            
            if ($question && $option) {
                $selections[] = [
                    'question' => $question['question_text'],
                    'option' => $option['option_text'],
                    'price' => $option['price_value']
                ];
            }
        }
    }
    
    $result['selections'] = $selections;
    
    echo json_encode([
        'success' => true,
        'result' => $result
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
