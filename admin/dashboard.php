<?php
/**
 * Admin Dashboard - Main Overview Page
 */

define('APP_ACCESS', true);
require_once dirname(__DIR__) . '/config/config.php';
require_once BASE_PATH . '/classes/Security.php';
require_once BASE_PATH . '/classes/Admin.php';
require_once BASE_PATH . '/classes/Result.php';

Security::setSecurityHeaders();
Security::requireAdmin();

$adminModel = new Admin();
$currentAdmin = $adminModel->getCurrentAdmin();

$resultModel = new Result();
$stats = $resultModel->getStatistics();
$recentResults = $resultModel->getAllResults(10, 0);
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - <?php echo APP_NAME; ?></title>
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
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-800">Dashboard</h1>
            <p class="text-gray-600 mt-1">Xoş gəlmisiniz, <?php echo Security::escape($currentAdmin['username']); ?></p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid md:grid-cols-4 gap-6 mb-8">
            
            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Ümumi Göndərişlər</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $stats['total_submissions']; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Orta Qiymət</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo number_format($stats['average_price'], 0); ?> ₼</p>
                    </div>
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Ən Çox Tövsiyə</p>
                        <p class="text-lg font-bold text-gray-800 mt-2"><?php echo Security::escape($stats['top_product']); ?></p>
                    </div>
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm">Son 7 Gün</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2"><?php echo $stats['recent_submissions']; ?></p>
                    </div>
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
            </div>

        </div>

        <!-- Recent Results -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="text-xl font-bold text-gray-800">Son Nəticələr</h2>
                <a href="results.php" class="text-depod-red hover:text-depod-orange transition-colors">
                    Hamısını Gör →
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Ad Soyad</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Telefon</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Qiymət</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Məhsul</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Tarix</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($recentResults)): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                    Hələ ki nəticə yoxdur
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentResults as $result): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-800">#<?php echo $result['id']; ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-800">
                                        <?php 
                                        $fullName = trim(($result['user_name'] ?? '') . ' ' . ($result['user_surname'] ?? ''));
                                        echo $fullName ?: '-';
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-800"><?php echo $result['phone_number'] ?? '-'; ?></td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800"><?php echo $result['calculated_price']; ?> ₼</td>
                                    <td class="px-6 py-4 text-sm text-gray-800"><?php echo Security::escape($result['product_name'] ?? '-'); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo date('d.m.Y H:i', strtotime($result['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</body>
</html>
