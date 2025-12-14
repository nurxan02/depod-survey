<?php
/**
 * Admin Questions Management Page
 */

define('APP_ACCESS', true);
require_once dirname(__DIR__) . '/config/config.php';
require_once BASE_PATH . '/classes/Security.php';
require_once BASE_PATH . '/classes/Admin.php';
require_once BASE_PATH . '/classes/Question.php';
require_once BASE_PATH . '/classes/Option.php';

Security::setSecurityHeaders();
Security::requireAdmin();

$questionModel = new Question();
$optionModel = new Option();

$message = null;
$error = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Təhlükəsizlik xətası.';
    } else {
        $action = $_POST['action'] ?? '';
        
        // Create New Question
        if ($action === 'create_question') {
            $questionText = Security::sanitizeString($_POST['question_text']);
            $order = (int)$_POST['order'];
            
            if ($questionModel->createQuestion($questionText, $order)) {
                $message = 'Yeni sual əlavə edildi.';
            } else {
                $error = 'Xəta baş verdi.';
            }
        }
        
        // Update Question
        if ($action === 'update_question') {
            $questionId = (int)$_POST['question_id'];
            $questionText = Security::sanitizeString($_POST['question_text']);
            $order = (int)$_POST['order'];
            
            if ($questionModel->updateQuestion($questionId, $questionText, $order)) {
                $message = 'Sual uğurla yeniləndi.';
            } else {
                $error = 'Xəta baş verdi.';
            }
        }
        
        // Delete Question
        if ($action === 'delete_question') {
            $questionId = (int)$_POST['question_id'];
            
            if ($questionModel->deleteQuestion($questionId)) {
                $message = 'Sual silindi.';
            } else {
                $error = 'Xəta baş verdi.';
            }
        }
        
        // Create New Option
        if ($action === 'create_option') {
            $questionId = (int)$_POST['question_id'];
            $optionText = Security::sanitizeString($_POST['option_text']);
            $priceValue = (int)$_POST['price_value'];
            $isPremium = isset($_POST['is_premium']) ? 1 : 0;
            
            if ($optionModel->createOption($questionId, $optionText, $priceValue, $isPremium)) {
                $message = 'Yeni cavab əlavə edildi.';
            } else {
                $error = 'Xəta baş verdi.';
            }
        }
        
        // Update Option
        if ($action === 'update_option') {
            $optionId = (int)$_POST['option_id'];
            $optionText = Security::sanitizeString($_POST['option_text']);
            $priceValue = (int)$_POST['price_value'];
            $isPremium = isset($_POST['is_premium']) ? 1 : 0;
            
            if ($optionModel->updateOption($optionId, $optionText, $priceValue, $isPremium)) {
                $message = 'Cavab uğurla yeniləndi.';
            } else {
                $error = 'Xəta baş verdi.';
            }
        }
        
        // Delete Option
        if ($action === 'delete_option') {
            $optionId = (int)$_POST['option_id'];
            
            if ($optionModel->deleteOption($optionId)) {
                $message = 'Cavab silindi.';
            } else {
                $error = 'Xəta baş verdi.';
            }
        }
    }
}

// Get all questions with options
$questions = $questionModel->getAllQuestionsWithOptions();
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suallar - Admin Panel</title>
    <link rel="icon" type="image/x-icon" href="/favicon/favicon.ico">
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'depod-red': '#E53E3E',
                        'depod-orange': '#F56565',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-100">

    <!-- Navigation -->
    <?php include 'includes/nav.php'; ?>

    <!-- Main Content -->
    <div class="container mx-auto px-4 py-8">
        
        <!-- Page Header -->
        <div class="mb-8 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Sualların İdarə Edilməsi</h1>
                <p class="text-gray-600 mt-1">Sorğu sualları və cavablarını redaktə edin</p>
            </div>
            <button onclick="toggleNewQuestion()" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:opacity-90 font-medium">
                + Yeni Sual Əlavə Et
            </button>
        </div>

        <!-- New Question Form -->
        <div id="new-question-form" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6 hidden">
            <div class="bg-green-50 px-6 py-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-800">Yeni Sual</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="create_question">
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Sual Mətni</label>
                        <textarea name="question_text" rows="2" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600"></textarea>
                    </div>

                    <div class="w-32">
                        <label class="block text-gray-700 font-medium mb-2">Sıra</label>
                        <input type="number" name="order" value="<?php echo count($questions) + 1; ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600">
                    </div>

                    <div class="flex gap-3">
                        <button type="submit" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-2 rounded-lg hover:opacity-90">
                            Əlavə Et
                        </button>
                        <button type="button" onclick="toggleNewQuestion()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                            Ləğv Et
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($message): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <p class="text-green-700"><?php echo Security::escape($message); ?></p>
            </div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <p class="text-red-700"><?php echo Security::escape($error); ?></p>
            </div>
        <?php endif; ?>

        <!-- Questions List -->
        <div class="space-y-6">
            <?php foreach ($questions as $question): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Question Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="font-bold text-gray-800">Sual <?php echo $question['order']; ?></h3>
                            <div class="flex items-center gap-3">
                                <button onclick="toggleEdit('question-<?php echo $question['id']; ?>')" class="text-blue-600 hover:text-blue-700 font-medium">
                                    Redaktə Et
                                </button>
                                <form method="POST" action="" class="inline" onsubmit="return confirm('Bu sualı silmək istədiyinizə əminsiniz? Bütün cavablar da silinəcək!');">
                                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="delete_question">
                                    <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                                    <button type="submit" class="text-red-600 hover:text-red-700 font-medium">
                                        Sil
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Question Content -->
                    <div class="p-6">
                        
                        <!-- View Mode -->
                        <div id="view-question-<?php echo $question['id']; ?>">
                            <p class="text-lg font-medium text-gray-800 mb-4"><?php echo Security::escape($question['question_text']); ?></p>
                        </div>

                        <!-- Edit Mode -->
                        <div id="edit-question-<?php echo $question['id']; ?>" class="hidden mb-6">
                            <form method="POST" action="" class="space-y-4">
                                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                <input type="hidden" name="action" value="update_question">
                                <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                                
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Sual Mətni</label>
                                    <textarea name="question_text" rows="2" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-depod-red"><?php echo Security::escape($question['question_text']); ?></textarea>
                                </div>

                                <div class="w-32">
                                    <label class="block text-gray-700 font-medium mb-2">Sıra</label>
                                    <input type="number" name="order" value="<?php echo $question['order']; ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-depod-red">
                                </div>

                                <div class="flex gap-3">
                                    <button type="submit" class="bg-gradient-to-r from-depod-red to-depod-orange text-white px-6 py-2 rounded-lg hover:opacity-90">
                                        Yadda Saxla
                                    </button>
                                    <button type="button" onclick="toggleEdit('question-<?php echo $question['id']; ?>')" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                                        Ləğv Et
                                    </button>
                                </div>
                            </form>
                        </div>

                        <!-- Options -->
                        <div class="space-y-4">
                            <!-- Add New Option Button -->
                            <button onclick="toggleNewOption(<?php echo $question['id']; ?>)" class="text-sm text-green-600 hover:text-green-700 font-medium">
                                + Yeni Cavab Əlavə Et
                            </button>
                            
                            <!-- New Option Form -->
                            <div id="new-option-form-<?php echo $question['id']; ?>" class="hidden border-l-4 border-green-500 pl-4 mb-4">
                                <form method="POST" action="" class="space-y-3 bg-green-50 p-4 rounded">
                                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                    <input type="hidden" name="action" value="create_option">
                                    <input type="hidden" name="question_id" value="<?php echo $question['id']; ?>">
                                    
                                    <div>
                                        <label class="block text-gray-700 text-sm font-medium mb-1">Cavab Mətni</label>
                                        <input type="text" name="option_text" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600">
                                    </div>

                                    <div class="flex gap-4 items-end">
                                        <div class="w-32">
                                            <label class="block text-gray-700 text-sm font-medium mb-1">Qiymət</label>
                                            <input type="number" name="price_value" value="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600">
                                        </div>
                                        
                                        <div class="flex items-center">
                                            <input type="checkbox" name="is_premium" id="new-premium-<?php echo $question['id']; ?>" class="w-4 h-4 text-green-600">
                                            <label for="new-premium-<?php echo $question['id']; ?>" class="ml-2 text-sm text-gray-700">Premium</label>
                                        </div>
                                    </div>

                                    <div class="flex gap-2">
                                        <button type="submit" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-4 py-2 rounded-lg text-sm hover:opacity-90">
                                            Əlavə Et
                                        </button>
                                        <button type="button" onclick="toggleNewOption(<?php echo $question['id']; ?>)" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-400">
                                            Ləğv Et
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <?php foreach ($question['options'] as $option): ?>
                                <div class="border-l-4 border-depod-red pl-4">
                                    
                                    <!-- View Mode -->
                                    <div id="view-option-<?php echo $option['id']; ?>">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <p class="text-gray-800 font-medium"><?php echo Security::escape($option['option_text']); ?></p>
                                                <div class="flex items-center gap-3 mt-1">
                                                    <span class="text-sm text-gray-600">Qiymət: +<?php echo $option['price_value']; ?> ₼</span>
                                                    <?php if ($option['is_premium']): ?>
                                                        <span class="bg-orange-100 text-orange-600 text-xs px-2 py-1 rounded">Premium</span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button onclick="toggleEdit('option-<?php echo $option['id']; ?>')" class="text-sm text-blue-600 hover:text-blue-700">
                                                    Redaktə
                                                </button>
                                                <form method="POST" action="" class="inline" onsubmit="return confirm('Bu cavabı silmək istədiyinizə əminsiniz?');">
                                                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                                    <input type="hidden" name="action" value="delete_option">
                                                    <input type="hidden" name="option_id" value="<?php echo $option['id']; ?>">
                                                    <button type="submit" class="text-sm text-red-600 hover:text-red-700">
                                                        Sil
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Edit Mode -->
                                    <div id="edit-option-<?php echo $option['id']; ?>" class="hidden">
                                        <form method="POST" action="" class="space-y-3">
                                            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                            <input type="hidden" name="action" value="update_option">
                                            <input type="hidden" name="option_id" value="<?php echo $option['id']; ?>">
                                            
                                            <div>
                                                <label class="block text-gray-700 text-sm font-medium mb-1">Cavab Mətni</label>
                                                <input type="text" name="option_text" value="<?php echo Security::escape($option['option_text']); ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-depod-red">
                                            </div>

                                            <div class="flex gap-4 items-end">
                                                <div class="w-32">
                                                    <label class="block text-gray-700 text-sm font-medium mb-1">Qiymət</label>
                                                    <input type="number" name="price_value" value="<?php echo $option['price_value']; ?>" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-depod-red">
                                                </div>
                                                
                                                <div class="flex items-center">
                                                    <input type="checkbox" name="is_premium" id="premium-<?php echo $option['id']; ?>" <?php echo $option['is_premium'] ? 'checked' : ''; ?> class="w-4 h-4 text-depod-red">
                                                    <label for="premium-<?php echo $option['id']; ?>" class="ml-2 text-sm text-gray-700">Premium</label>
                                                </div>
                                            </div>

                                            <div class="flex gap-2">
                                                <button type="submit" class="bg-gradient-to-r from-depod-red to-depod-orange text-white px-4 py-2 rounded-lg text-sm hover:opacity-90">
                                                    Yadda Saxla
                                                </button>
                                                <button type="button" onclick="toggleEdit('option-<?php echo $option['id']; ?>')" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm hover:bg-gray-400">
                                                    Ləğv Et
                                                </button>
                                            </div>
                                        </form>
                                    </div>

                                </div>
                            <?php endforeach; ?>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    </div>

    <script>
        function toggleEdit(id) {
            const viewElement = document.getElementById('view-' + id);
            const editElement = document.getElementById('edit-' + id);
            
            if (viewElement && editElement) {
                viewElement.classList.toggle('hidden');
                editElement.classList.toggle('hidden');
            }
        }
        
        function toggleNewQuestion() {
            const form = document.getElementById('new-question-form');
            if (form) {
                form.classList.toggle('hidden');
            }
        }
        
        function toggleNewOption(questionId) {
            const form = document.getElementById('new-option-form-' + questionId);
            if (form) {
                form.classList.toggle('hidden');
            }
        }
    </script>

</body>
</html>
