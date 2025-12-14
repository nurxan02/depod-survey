<?php
/**
 * Survey Main Page - Steps 1-7
 */

define('APP_ACCESS', true);
require_once __DIR__ . '/config/config.php';
require_once BASE_PATH . '/classes/Question.php';
require_once BASE_PATH . '/classes/Security.php';
require_once BASE_PATH . '/classes/Settings.php';

Security::setSecurityHeaders();

$questionModel = new Question();
$settingsModel = new Settings();
$questions = $questionModel->getAllQuestionsWithOptions();
$totalSteps = count($questions);
$headerSubtitle = $settingsModel->getSetting('header_subtitle') ?? 'Sizin üçün ideal qulaqlığı seçək';
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Qulaqlıq Seçimi</title>
    <link rel="icon" type="image/x-icon" href="/favicon/favicon.ico">
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">
    <link rel="apple-touch-icon" href="/favicon/apple-touch-icon.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'depod-dark': '#1a1a1a',
                        'depod-gray': '#2d2d2d',
                        'depod-light': '#f5f5f5',
                    }
                }
            }
        }
    </script>
    <style>
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.4s ease-out;
        }
        .choice-card {
            transition: all 0.3s ease;
            background: white;
            border: 2px solid #e5e5e5;
        }
        .choice-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.12);
            border-color: #1a1a1a;
        }
        .choice-card.selected {
            border: 2px solid #1a1a1a;
            background: #f5f5f5;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        body {
            background: #fafafa;
        }
    </style>
</head>
<body class="bg-white min-h-screen">
    
    <!-- Header -->
    <header class="bg-depod-dark text-white shadow-lg">
        <div class="container mx-auto px-4 py-8">
            <a href="https://depod.az" target="_blank" class="flex items-center gap-4 hover:opacity-90 transition-opacity w-fit">
                <img src="/logo/logo.svg" alt="Depod.az Logo" class="h-12 w-12" style="filter: brightness(0) invert(1);">
                <div>
                    <h1 class="text-3xl font-bold">Depod.az</h1>
                    <p class="text-gray-300 mt-1"><?php echo Security::escape($headerSubtitle); ?></p>
                </div>
            </a>
        </div>
    </header>

    <!-- Progress Bar -->
    <div class="container mx-auto px-4 py-6">
        <div class="bg-gray-200 rounded-full h-3 overflow-hidden">
            <div id="progressBar" class="bg-depod-dark h-full transition-all duration-500" style="width: 0%"></div>
        </div>
        <div class="flex justify-between mt-2 text-sm text-gray-500">
            <span id="stepText">Addım 1 / <?php echo $totalSteps; ?></span>
            <span id="progressPercent">0%</span>
        </div>
    </div>

    <!-- Price Badge -->
    <div class="container mx-auto px-4 mt-6">
        <div class="max-w-4xl mx-auto bg-white border-2 border-gray-300 rounded-xl shadow-sm p-5 flex items-center justify-between">
            <span class="text-gray-700 font-medium">Cari Qiymət</span>
            <span class="text-3xl font-bold text-depod-dark"><span id="currentPrice">0</span> ₼</span>
        </div>
    </div>

    <!-- Survey Container -->
    <div class="container mx-auto px-4 py-8 max-w-4xl">
        <div id="surveyContent" class="fade-in">
            <!-- Questions will be loaded here dynamically -->
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between mt-8">
            <button id="prevBtn" class="bg-gray-200 text-gray-700 px-8 py-3 rounded-lg font-semibold hover:bg-gray-300 transition-colors disabled:opacity-50 disabled:cursor-not-allowed border-2 border-gray-300" disabled>
                ← Geri
            </button>
            <button id="nextBtn" class="bg-depod-dark text-white px-8 py-3 rounded-lg font-semibold hover:bg-depod-gray transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                Növbəti →
            </button>
        </div>
    </div>

    <script>
        // Questions data from PHP
        const questions = <?php echo json_encode($questions, JSON_UNESCAPED_UNICODE); ?>;
        const totalSteps = questions.length;
        let currentStep = 0;
        let selections = {};
        let currentPrice = 0;

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            renderQuestion(currentStep);
            updateProgress();
        });

        // Render question
        function renderQuestion(step) {
            const question = questions[step];
            const container = document.getElementById('surveyContent');
            
            let html = `
                <div class="fade-in">
                    <h2 class="text-2xl font-bold text-gray-800 mb-8">${question.question_text}</h2>
                    <div class="grid md:grid-cols-2 gap-6">
            `;
            
            question.options.forEach(option => {
                const isSelected = selections[question.id] === option.id;
                html += `
                    <div class="choice-card ${isSelected ? 'selected' : ''} rounded-xl p-6 cursor-pointer" 
                         onclick="selectOption(${question.id}, ${option.id}, ${option.price_value})">
                        <div class="flex items-start">
                            <div class="flex-1">
                                <p class="text-lg font-semibold text-gray-800">${option.option_text}</p>
                                ${option.is_premium ? '<span class="inline-block mt-2 bg-depod-dark text-white text-xs px-3 py-1 rounded-full font-medium">Premium</span>' : ''}
                            </div>
                            <div class="ml-4">
                                <span class="text-xl font-bold text-depod-dark">+${option.price_value} ₼</span>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += `
                    </div>
                </div>
            `;
            
            container.innerHTML = html;
            
            // Update button states
            updateButtons();
        }

        // Select option
        function selectOption(questionId, optionId, priceValue) {
            // Remove previous selection price
            if (selections[questionId]) {
                const prevOption = findOption(questionId, selections[questionId]);
                if (prevOption) {
                    currentPrice -= prevOption.price_value;
                }
            }
            
            // Add new selection
            selections[questionId] = optionId;
            currentPrice += priceValue;
            
            // Update display
            document.getElementById('currentPrice').textContent = currentPrice;
            
            // Re-render to update selected state
            renderQuestion(currentStep);
        }

        // Find option by ID
        function findOption(questionId, optionId) {
            const question = questions.find(q => q.id == questionId);
            if (question) {
                return question.options.find(o => o.id == optionId);
            }
            return null;
        }

        // Update buttons
        function updateButtons() {
            const prevBtn = document.getElementById('prevBtn');
            const nextBtn = document.getElementById('nextBtn');
            
            prevBtn.disabled = currentStep === 0;
            
            const currentQuestionId = questions[currentStep].id;
            nextBtn.disabled = !selections[currentQuestionId];
            
            if (currentStep === totalSteps - 1 && selections[currentQuestionId]) {
                nextBtn.textContent = 'Nəticəni Gör →';
            } else {
                nextBtn.textContent = 'Növbəti →';
            }
        }

        // Update progress
        function updateProgress() {
            const progress = ((currentStep + 1) / totalSteps) * 100;
            document.getElementById('progressBar').style.width = progress + '%';
            document.getElementById('stepText').textContent = `Addım ${currentStep + 1} / ${totalSteps}`;
            document.getElementById('progressPercent').textContent = Math.round(progress) + '%';
        }

        // Previous button
        document.getElementById('prevBtn').addEventListener('click', function() {
            if (currentStep > 0) {
                currentStep--;
                renderQuestion(currentStep);
                updateProgress();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        });

        // Next button
        document.getElementById('nextBtn').addEventListener('click', function() {
            if (currentStep < totalSteps - 1) {
                currentStep++;
                renderQuestion(currentStep);
                updateProgress();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            } else {
                // All questions answered, go to result page
                goToResult();
            }
        });

        // Go to result page
        function goToResult() {
            // Store selections in sessionStorage
            sessionStorage.setItem('selections', JSON.stringify(selections));
            sessionStorage.setItem('totalPrice', currentPrice);
            window.location.href = 'result.php';
        }
    </script>

    <!-- Footer -->
    <footer class="bg-gray-50 border-t border-gray-200 mt-16 py-8">
        <div class="container mx-auto px-4 text-center text-gray-600">
            <p>&copy; 2025 Depod.az - Bütün hüquqlar qorunur</p>
        </div>
    </footer>

</body>
</html>
