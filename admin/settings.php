<?php
/**
 * Admin Settings Management Page
 */

define('APP_ACCESS', true);
require_once dirname(__DIR__) . '/config/config.php';
require_once BASE_PATH . '/classes/Security.php';
require_once BASE_PATH . '/classes/Admin.php';
require_once BASE_PATH . '/classes/Settings.php';

Security::setSecurityHeaders();
Security::requireAdmin();

$settingsModel = new Settings();

$message = null;
$error = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Təhlükəsizlik xətası.';
    } else {
        $settingsData = [
            'header_subtitle' => Security::sanitizeString($_POST['header_subtitle']),
            'instagram_link' => Security::sanitizeString($_POST['instagram_link']),
            'youtube_link' => Security::sanitizeString($_POST['youtube_link']),
            'tiktok_link' => Security::sanitizeString($_POST['tiktok_link']),
            'birmarket_link' => Security::sanitizeString($_POST['birmarket_link'])
        ];
        
        if ($settingsModel->updateSettings($settingsData)) {
            $message = 'Parametrlər uğurla yeniləndi.';
        } else {
            $error = 'Xəta baş verdi.';
        }
    }
}

// Get all settings
$settings = $settingsModel->getAllSettings();
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parametrlər - Admin Panel</title>
    <link rel="icon" type="image/x-icon" href="/favicon/favicon.ico">
    <link rel="icon" type="image/svg+xml" href="/favicon/favicon.svg">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'depod-dark': '#1a1a1a',
                        'depod-gray': '#2d2d2d',
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
        <div class="mb-6 lg:mb-8">
            <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Sistem Parametrləri</h1>
            <p class="text-sm lg:text-base text-gray-600 mt-1">Header və sosial media linklərini redaktə edin</p>
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

        <!-- Settings Form -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <form method="POST" action="">
                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                
                <div class="p-4 lg:p-6 space-y-6">
                    
                    <!-- Header Section -->
                    <div class="border-b border-gray-200 pb-6">
                        <h2 class="text-lg lg:text-xl font-bold text-gray-800 mb-4">Header Parametrləri</h2>
                        
                        <div>
                            <label class="block text-gray-700 font-medium mb-2">Header Alt Başlıq</label>
                            <input type="text" name="header_subtitle" value="<?php echo Security::escape($settings['header_subtitle'] ?? ''); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500">
                            <p class="text-sm text-gray-500 mt-1">Logo altında görünən mətn</p>
                        </div>
                    </div>

                    <!-- Social Media Section -->
                    <div>
                        <h2 class="text-lg lg:text-xl font-bold text-gray-800 mb-4">Sosial Media Linkləri</h2>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-gray-700 font-medium mb-2">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                                    </svg>
                                    Instagram
                                </label>
                                <input type="url" name="instagram_link" value="<?php echo Security::escape($settings['instagram_link'] ?? ''); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="https://instagram.com/...">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-2">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                                    </svg>
                                    YouTube
                                </label>
                                <input type="url" name="youtube_link" value="<?php echo Security::escape($settings['youtube_link'] ?? ''); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="https://youtube.com/@...">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-2">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12.525.02c1.31-.02 2.61-.01 3.91-.02.08 1.53.63 3.09 1.75 4.17 1.12 1.11 2.7 1.62 4.24 1.79v4.03c-1.44-.05-2.89-.35-4.2-.97-.57-.26-1.1-.59-1.62-.93-.01 2.92.01 5.84-.02 8.75-.08 1.4-.54 2.79-1.35 3.94-1.31 1.92-3.58 3.17-5.91 3.21-1.43.08-2.86-.31-4.08-1.03-2.02-1.19-3.44-3.37-3.65-5.71-.02-.5-.03-1-.01-1.49.18-1.9 1.12-3.72 2.58-4.96 1.66-1.44 3.98-2.13 6.15-1.72.02 1.48-.04 2.96-.04 4.44-.99-.32-2.15-.23-3.02.37-.63.41-1.11 1.04-1.36 1.75-.21.51-.15 1.07-.14 1.61.24 1.64 1.82 3.02 3.5 2.87 1.12-.01 2.19-.66 2.77-1.61.19-.33.4-.67.41-1.06.1-1.79.06-3.57.07-5.36.01-4.03-.01-8.05.02-12.07z"/>
                                    </svg>
                                    TikTok
                                </label>
                                <input type="url" name="tiktok_link" value="<?php echo Security::escape($settings['tiktok_link'] ?? ''); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="https://tiktok.com/@...">
                            </div>

                            <div>
                                <label class="block text-gray-700 font-medium mb-2">
                                    <svg class="w-5 h-5 inline-block mr-2" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M3.055 3H5.77l3 9.5L12 3h2.23l3.23 9.5L20.445 3h2.715l-4.77 15h-2.32L12 7.5 7.93 18H5.61L0.84 3h2.215zm18.11 0h-2.715l-3.23 9.5L12 3h-2.23L6.77 12.5 3.77 3H1.055l4.555 15h2.32L12 7.5 16.07 18h2.32l4.77-15z"/>
                                    </svg>
                                    Birmarket.az
                                </label>
                                <input type="url" name="birmarket_link" value="<?php echo Security::escape($settings['birmarket_link'] ?? ''); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-blue-500" placeholder="https://birmarket.az">
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Submit Button -->
                <div class="bg-gray-50 px-4 lg:px-6 py-4 border-t border-gray-200">
                    <button type="submit" class="w-full sm:w-auto bg-gradient-to-r from-blue-600 to-blue-700 text-white px-6 lg:px-8 py-2 lg:py-3 rounded-lg hover:opacity-90 font-medium text-sm lg:text-base">
                        Yadda Saxla
                    </button>
                </div>
            </form>
        </div>

    </div>

</body>
</html>
