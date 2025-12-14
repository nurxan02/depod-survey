<?php
/**
 * Result Page - Step 8: Final Result & Contact Form
 */

define('APP_ACCESS', true);
require_once __DIR__ . '/config/config.php';
require_once BASE_PATH . '/classes/Product.php';
require_once BASE_PATH . '/classes/Option.php';
require_once BASE_PATH . '/classes/Result.php';
require_once BASE_PATH . '/classes/Security.php';
require_once BASE_PATH . '/classes/Settings.php';

Security::setSecurityHeaders();

$settingsModel = new Settings();
$settings = $settingsModel->getAllSettings();

// Handle form submission
$submitted = false;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate CSRF token
    if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Təhlükəsizlik xətası. Zəhmət olmasa yenidən cəhd edin.';
    } else {
        // Get and sanitize form data
        $userName = !empty($_POST['user_name']) ? Security::sanitizeString($_POST['user_name']) : null;
        $userSurname = !empty($_POST['user_surname']) ? Security::sanitizeString($_POST['user_surname']) : null;
        $phoneNumber = !empty($_POST['phone_number']) ? Security::sanitizePhone($_POST['phone_number']) : null;
        
        // Validate phone if provided
        if ($phoneNumber && !Security::validatePhone($phoneNumber)) {
            $error = 'Telefon nömrəsi düzgün formatda deyil.';
        } else {
            // Get selection data from POST
            $selections = isset($_POST['selections']) ? json_decode($_POST['selections'], true) : [];
            $totalPrice = isset($_POST['total_price']) ? (int)$_POST['total_price'] : 0;
            $recommendedProductId = isset($_POST['recommended_product_id']) ? (int)$_POST['recommended_product_id'] : null;
            
            if (!empty($selections) && $totalPrice > 0) {
                // Save result
                $resultModel = new Result();
                $resultId = $resultModel->saveResult(
                    $userName,
                    $userSurname,
                    $phoneNumber,
                    $totalPrice,
                    $recommendedProductId,
                    $selections
                );
                
                if ($resultId) {
                    $submitted = true;
                } else {
                    $error = 'Məlumatlar saxlanılarkən xəta baş verdi.';
                }
            } else {
                $error = 'Seçimlər tapılmadı.';
            }
        }
    }
}

// Get selections from session storage (via JavaScript)
// This page expects JavaScript to populate the form with data
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo APP_NAME; ?> - Nəticə</title>
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
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .fade-in {
            animation: fadeIn 0.6s ease-out;
        }
        @keyframes scaleIn {
            from { transform: scale(0.9); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        .scale-in {
            animation: scaleIn 0.5s ease-out;
        }
    </style>
</head>
<body class="bg-white min-h-screen">

    <!-- Header -->
    <header class="bg-depod-dark text-white shadow-lg">
        <div class="container mx-auto px-4 py-8">
            <a href="https://depod.az" target="_blank" class="flex items-center gap-4 hover:opacity-90 transition-opacity w-fit">
                <img src="/logo/logo.svg" alt="Depod.az Logo" class="h-12 w-12" style="filter: brightness(0) invert(1);">
                <h1 class="text-3xl font-bold">Depod.az</h1>
            </a>
        </div>
    </header>

    <?php if ($submitted): ?>
        <!-- Success Message -->
        <div class="container mx-auto px-4 py-16 max-w-2xl">
            <div class="text-center scale-in">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-4">Təşəkkürlər!</h2>
                <p class="text-gray-600 text-lg mb-8">
                    Məlumatlarınız uğurla qeydə alındı. Tezliklə sizinlə əlaqə saxlanılacaq.
                </p>
                <a href="index.php" class="inline-block bg-depod-dark text-white px-8 py-3 rounded-lg font-semibold hover:bg-depod-gray transition-colors">
                    Yenidən Başla
                </a>
            </div>
        </div>
    <?php else: ?>
        <!-- Result Display -->
        <div class="container mx-auto px-4 py-12 max-w-4xl">
            
            <?php if ($error): ?>
                <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                    <p class="text-red-700"><?php echo Security::escape($error); ?></p>
                </div>
            <?php endif; ?>

            <!-- Loading State -->
            <div id="loadingState" class="text-center py-16">
                <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-depod-dark mx-auto"></div>
                <p class="text-gray-600 mt-4">Tövsiyə hazırlanır...</p>
            </div>

            <!-- Result Content -->
            <div id="resultContent" class="hidden">
                
                <!-- Final Price -->
                <div class="bg-depod-dark text-white rounded-xl p-8 text-center mb-8 fade-in shadow-lg">
                    <p class="text-lg opacity-80 mb-2">Sizin üçün hesablanmış qiymət</p>
                    <p class="text-6xl font-bold"><span id="finalPrice">0</span> ₼</p>
                </div>

                <!-- Recommended Product -->
                <div id="recommendedProduct" class="bg-white border-2 border-gray-200 rounded-2xl p-8 mb-8 fade-in">
                    <!-- Product details will be inserted here -->
                </div>

                <!-- Social Media Links -->
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 border border-gray-200 rounded-xl px-6 py-4 mb-8 fade-in">
                    <div class="flex flex-wrap items-center justify-center gap-6">
                        <span class="text-gray-700 font-medium">Bizi izləyin:</span>
                        
                        <?php if (!empty($settings['instagram_link'])): ?>
                        <a href="<?php echo Security::escape($settings['instagram_link']); ?>" target="_blank" class="flex items-center gap-2 text-gray-700 hover:text-pink-600 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                            <span class="font-medium">Instagram</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($settings['youtube_link'])): ?>
                        <a href="<?php echo Security::escape($settings['youtube_link']); ?>" target="_blank" class="flex items-center gap-2 text-gray-700 hover:text-red-600 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                            </svg>
                            <span class="font-medium">YouTube</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($settings['tiktok_link'])): ?>
                        <a href="<?php echo Security::escape($settings['tiktok_link']); ?>" target="_blank" class="flex items-center gap-2 text-gray-700 hover:text-black transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                            </svg>
                            <span class="font-medium">TikTok</span>
                        </a>
                        <?php endif; ?>
                        
                        <?php if (!empty($settings['birmarket_link'])): ?>
                        <a href="<?php echo Security::escape($settings['birmarket_link']); ?>" target="_blank" class="flex items-center gap-2 text-gray-700 hover:text-purple-600 transition-colors">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M7 18c-1.1 0-1.99.9-1.99 2S5.9 22 7 22s2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12.9-1.63h7.45c.75 0 1.41-.41 1.75-1.03l3.58-6.49c.08-.14.12-.31.12-.48 0-.55-.45-1-1-1H5.21l-.94-2H1zm16 16c-1.1 0-1.99.9-1.99 2s.89 2 1.99 2 2-.9 2-2-.9-2-2-2z"/>
                            </svg>
                            <span class="font-medium">Birmarket.az</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Contact Form -->
                <div class="bg-white border-2 border-gray-200 rounded-xl p-8 fade-in">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Əlaqə Məlumatları</h3>
                    <p class="text-gray-600 mb-6">Sizinlə əlaqə saxlamağımız üçün məlumatlarınızı daxil edin (məcburi deyil).</p>
                    
                    <form method="POST" action="" id="contactForm">
                        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                        <input type="hidden" name="selections" id="selectionsInput">
                        <input type="hidden" name="total_price" id="totalPriceInput">
                        <input type="hidden" name="recommended_product_id" id="recommendedProductIdInput">
                        
                        <div class="grid md:grid-cols-2 gap-6 mb-6">
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Ad</label>
                                <input type="text" name="user_name" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-depod-dark" placeholder="Adınız">
                            </div>
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">Soyad</label>
                                <input type="text" name="user_surname" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-depod-dark" placeholder="Soyadınız">
                            </div>
                        </div>
                        
                        <div class="mb-6">
                            <label class="block text-gray-700 font-medium mb-2">Mobil Nömrə</label>
                            <input type="tel" name="phone_number" class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-depod-dark" placeholder="+994XX XXX XX XX">
                        </div>
                        
                        <div class="flex gap-4">
                            <button type="submit" class="flex-1 bg-depod-dark text-white px-8 py-4 rounded-lg font-semibold text-lg hover:bg-depod-gray transition-colors">
                                Göndər
                            </button>
                            <button type="button" onclick="skipContact()" class="px-8 py-4 border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 transition-colors">
                                Keç
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    <?php endif; ?>

    <script>
        // Get data from sessionStorage
        const selections = JSON.parse(sessionStorage.getItem('selections') || '{}');
        const totalPrice = parseInt(sessionStorage.getItem('totalPrice') || '0');

        if (Object.keys(selections).length === 0 || totalPrice === 0) {
            // No data, redirect back
            window.location.href = 'index.php';
        } else {
            // Fetch recommendation from server
            fetchRecommendation();
        }

        async function fetchRecommendation() {
            try {
                const response = await fetch('api/get_recommendation.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        selections: selections,
                        total_price: totalPrice
                    })
                });

                const data = await response.json();
                
                if (data.success) {
                    displayResult(data);
                } else {
                    showError('Tövsiyə alınarkən xəta baş verdi.');
                }
            } catch (error) {
                showError('Serverlə əlaqə xətası.');
            }
        }

        function displayResult(data) {
            // Hide loading
            document.getElementById('loadingState').classList.add('hidden');
            document.getElementById('resultContent').classList.remove('hidden');

            // Display final price
            document.getElementById('finalPrice').textContent = totalPrice;

            // Display recommended product
            const product = data.product;
            
            let featuresHtml = '';
            if (product.features) {
                const featuresList = product.features.split('\n').filter(f => f.trim());
                if (featuresList.length > 0) {
                    featuresHtml = '<div class="mt-6 text-left"><h4 class="font-semibold text-gray-800 mb-3">Əsas Xüsusiyyətlər:</h4><ul class="space-y-2">';
                    featuresList.forEach(feature => {
                        featuresHtml += `<li class="flex items-start"><span class="text-depod-dark mr-2">•</span><span class="text-gray-700">${feature.trim()}</span></li>`;
                    });
                    featuresHtml += '</ul></div>';
                }
            }
            
            const imageHtml = product.product_image ? `<img src="${product.product_image}" alt="${product.product_name}" class="mx-auto rounded-xl shadow-lg mb-6 max-w-md w-full object-cover">` : '';
            
            const birmarketButton = product.birmarket_link ? `<a href="${product.birmarket_link}" target="_blank" class="inline-flex items-center justify-center mt-6 px-8 py-4 rounded-xl font-semibold hover:opacity-90 transition-all shadow-md" style="background-color: #ea207e;"><svg width="140" height="30" viewBox="0 0 140 30" fill="none" xmlns="http://www.w3.org/2000/svg" style="filter: brightness(0) invert(1);"><path d="M44.061 4.27197C45.1347 4.27197 45.9347 4.93564 45.9347 5.93115V6.11781C45.9347 7.12368 45.1347 7.77698 44.061 7.77698C42.9873 7.77698 42.1558 7.11331 42.1558 6.11781V5.93115C42.1558 4.92527 42.9873 4.27197 44.061 4.27197Z" fill="currentColor"></path><path d="M131.53 15.4919C131.53 11.8003 129.12 9.073 125.288 9.073C121.456 9.073 118.667 11.9247 118.667 15.7201V16.6948C118.667 20.4176 121.404 23.373 125.414 23.373C128.067 23.373 130.183 22.2116 131.52 19.754L129.13 18.437C128.162 20.0754 127.014 20.7909 125.383 20.7909C123.13 20.7909 121.604 19.4221 121.467 16.9126H131.52V15.4919H131.53ZM121.593 14.6727C121.941 12.6921 123.204 11.5618 125.288 11.5618C127.593 11.5618 128.562 13.1691 128.614 14.6727H121.593Z" fill="currentColor"></path><path d="M91.006 9.39446V11.0536C90.0376 9.86111 88.4586 9.073 86.585 9.073C82.9955 9.073 80.1323 11.8729 80.1323 15.7823V16.6845C80.1323 20.5939 82.9955 23.3938 86.585 23.3938C88.4586 23.3938 90.0376 22.6057 91.006 21.4131V23.0723H93.9534V9.39446H91.006ZM91.006 16.5497C91.006 19.1318 89.4797 20.8013 87.0692 20.8013C84.6586 20.8013 83.1323 19.1421 83.1323 16.5497V15.9171C83.1323 13.335 84.6586 11.6655 87.0692 11.6655C89.4797 11.6655 91.006 13.3246 91.006 15.9171V16.5497Z" fill="currentColor"></path><path d="M34.2088 9.08339C32.3352 9.08339 30.7564 9.87146 29.788 11.0639V4.59351H26.8408V16.5804C26.8408 20.8525 29.8722 23.393 33.7036 23.393C37.7454 23.393 40.6716 20.5725 40.6716 16.6841V15.813C40.6716 11.8831 37.8086 9.08339 34.2193 9.08339H34.2088ZM37.7244 16.6322C37.7244 19.1105 36.1666 20.8006 33.7562 20.8006C31.3458 20.8006 29.788 19.1623 29.788 16.6322V15.9167C29.788 13.3348 31.3458 11.6653 33.7562 11.6653C36.1666 11.6653 37.7244 13.3244 37.7244 15.9167V16.6322Z" fill="currentColor"></path><path d="M109.225 15.2531L114.825 9.39447H118.656L113.088 14.942L119.193 23.0715H115.572L111.078 16.9225L109.225 18.7372V23.0715H106.278V4.59351H109.225V15.2531Z" fill="currentColor"></path><path d="M102.858 9.08337C103.658 9.08337 104.384 9.27003 104.973 9.63297L104.279 12.2773C103.742 12.0388 103.205 11.8832 102.563 11.8832C100.689 11.8832 99.2682 13.3039 99.2682 15.689V23.0827H96.3208V9.39446H99.2682V11.2921C100.068 9.68482 101.416 9.073 102.858 9.073V9.08337Z" fill="currentColor"></path><path d="M73.4483 9.08337C76.6061 9.08337 78.5428 11.1987 78.5428 14.8383V23.0715H75.5956V14.9109C75.5956 12.9096 74.6799 11.6653 72.9747 11.6653C71.0695 11.6653 69.9749 12.9822 69.9749 15.0975V23.0715H67.0277V15.0975C67.0277 12.9822 66.2277 11.6653 64.3752 11.6653C62.6069 11.6653 61.3964 12.9822 61.3964 15.1805V23.0715H58.4492V9.39444H61.3964V11.1054C62.1437 9.97512 63.3752 9.073 65.2593 9.073C67.1329 9.073 68.5539 10.0996 69.196 11.5824C69.9222 10.2344 71.2906 9.073 73.4589 9.073L73.4483 9.08337Z" fill="currentColor"></path><path d="M54.9128 9.08337C55.7128 9.08337 56.4391 9.27003 57.0286 9.63297L56.3339 12.2773C55.797 12.0388 55.2602 11.8832 54.6181 11.8832C52.7444 11.8832 51.3233 13.3039 51.3233 15.689V23.0827H48.376V9.39446H51.3233V11.2921C52.1233 9.68482 53.4707 9.073 54.9128 9.073V9.08337Z" fill="currentColor"></path><path d="M45.5348 9.39453H42.5874V23.0724H45.5348V9.39453Z" fill="currentColor"></path><path d="M137.466 9.39467H140.002V11.9766H137.466V14.7245V19.2558C137.466 20.2617 137.918 20.5727 138.75 20.5727C139.013 20.5727 139.181 20.5416 139.371 20.4898L139.665 22.968C139.286 23.1028 138.865 23.1547 138.35 23.1547C135.887 23.1547 134.518 21.8067 134.518 19.0899V17.659V11.9662H132.087V9.38431H134.518V5.4751H137.466V9.38431V9.39467Z" fill="currentColor"></path><path d="M17.683 13.1271L10.315 0.694345C9.76766 -0.228519 8.42037 -0.228519 7.87303 0.694345L0.515569 13.1271C-0.168601 14.2781 -0.168601 15.709 0.515569 16.8704L7.88356 29.3135C8.4309 30.2363 9.77819 30.2363 10.3255 29.3135L17.6935 16.8704C18.3777 15.7194 18.3777 14.2884 17.6935 13.1271H17.683ZM13.9148 16.0616L9.39926 23.5585C9.26243 23.7867 8.9256 23.7867 8.78877 23.5585L4.27324 16.0616C3.87327 15.4083 3.87327 14.5891 4.27324 13.9359L8.78877 6.43891C8.9256 6.21079 9.26243 6.21079 9.39926 6.43891L13.9148 13.9359C14.3148 14.5891 14.3148 15.4083 13.9148 16.0616Z" fill="currentColor"></path></svg></a>` : '';
            
            const productHtml = `
                <div class="max-w-2xl mx-auto text-center">
                    ${imageHtml}
                    <h3 class="text-3xl font-bold text-gray-800 mb-4">Sizin üçün tövsiyə: ${product.product_name}</h3>
                    <p class="text-gray-600 text-lg mb-6">${product.description || ''}</p>
                    ${featuresHtml}
                    <div class="bg-white border-2 border-gray-200 rounded-xl px-8 py-6 mt-6 inline-block">
                        <p class="text-sm text-gray-600 mb-1">Məhsul Qiyməti</p>
                        <p class="text-5xl font-bold text-depod-dark">${product.base_price} ₼</p>
                    </div>
                    <div class="mt-3">
                        ${birmarketButton}
                    </div>
                </div>
            `;
            document.getElementById('recommendedProduct').innerHTML = productHtml;

            // Set hidden form values
            document.getElementById('selectionsInput').value = JSON.stringify(selections);
            document.getElementById('totalPriceInput').value = totalPrice;
            document.getElementById('recommendedProductIdInput').value = product.id;
        }

        function showError(message) {
            document.getElementById('loadingState').innerHTML = `
                <div class="text-center py-16">
                    <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-800 mb-4">Xəta</h3>
                    <p class="text-gray-600 mb-6">${message}</p>
                    <a href="index.php" class="inline-block bg-gradient-to-r from-depod-red to-depod-orange text-white px-8 py-3 rounded-lg font-medium hover:opacity-90 transition-all">
                        Əvvələ Qayıt
                    </a>
                </div>
            `;
        }

        function skipContact() {
            // Submit form with empty fields
            document.getElementById('contactForm').submit();
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
