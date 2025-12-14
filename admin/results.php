<?php
/**
 * Admin Results Management Page
 */

define('APP_ACCESS', true);
require_once dirname(__DIR__) . '/config/config.php';
require_once BASE_PATH . '/classes/Security.php';
require_once BASE_PATH . '/classes/Admin.php';
require_once BASE_PATH . '/classes/Result.php';
require_once BASE_PATH . '/classes/Question.php';
require_once BASE_PATH . '/classes/Option.php';

Security::setSecurityHeaders();
Security::requireAdmin();

$resultModel = new Result();
$questionModel = new Question();
$optionModel = new Option();

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'delete') {
    if (Security::verifyCSRFToken($_POST['csrf_token'])) {
        $resultId = (int)$_POST['result_id'];
        $resultModel->deleteResult($resultId);
        header('Location: results.php?deleted=1');
        exit;
    }
}

// Get all results
$results = $resultModel->getAllResults(100, 0);
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nəticələr - Admin Panel</title>
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
        <div class="mb-6 lg:mb-8">
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-800">Sorğu Nəticələri</h1>
                <p class="text-sm lg:text-base text-gray-600 mt-1">Bütün istifadəçi cavabları</p>
            </div>
        </div>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="bg-green-50 border-l-4 border-green-500 p-4 mb-6">
                <p class="text-green-700">Nəticə uğurla silindi.</p>
            </div>
        <?php endif; ?>

        <!-- Results - Desktop Table (hidden on mobile) -->
        <div class="hidden lg:block bg-white rounded-xl shadow-sm border border-gray-200">
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
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase">Əməliyyat</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php if (empty($results)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                    Hələ ki nəticə yoxdur
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($results as $result): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 text-sm text-gray-800">#<?php echo $result['id']; ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-800">
                                        <?php 
                                        $fullName = trim(($result['user_name'] ?? '') . ' ' . ($result['user_surname'] ?? ''));
                                        echo Security::escape($fullName ?: '-');
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-800"><?php echo Security::escape($result['phone_number'] ?? '-'); ?></td>
                                    <td class="px-6 py-4 text-sm font-medium text-gray-800"><?php echo $result['calculated_price']; ?> ₼</td>
                                    <td class="px-6 py-4 text-sm text-gray-800"><?php echo Security::escape($result['product_name'] ?? '-'); ?></td>
                                    <td class="px-6 py-4 text-sm text-gray-600"><?php echo date('d.m.Y H:i', strtotime($result['created_at'])); ?></td>
                                    <td class="px-6 py-4 text-sm">
                                        <button onclick="viewDetails(<?php echo $result['id']; ?>)" class="text-blue-600 hover:text-blue-800 mr-3">
                                            Bax
                                        </button>
                                        <button onclick="confirmDelete(<?php echo $result['id']; ?>)" class="text-red-600 hover:text-red-800">
                                            Sil
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Results - Mobile Cards (visible on mobile only) -->
        <div class="lg:hidden space-y-4">
            <?php if (empty($results)): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center text-gray-500">
                    Hələ ki nəticə yoxdur
                </div>
            <?php else: ?>
                <?php foreach ($results as $result): ?>
                    <?php 
                    $fullName = trim(($result['user_name'] ?? '') . ' ' . ($result['user_surname'] ?? ''));
                    ?>
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4">
                        <!-- Card Header -->
                        <div class="flex items-start justify-between mb-3 pb-3 border-b border-gray-200">
                            <div>
                                <span class="text-xs font-semibold text-gray-500">ID</span>
                                <p class="text-lg font-bold text-gray-800">#<?php echo $result['id']; ?></p>
                            </div>
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-1 rounded-full">
                                <?php echo date('d.m.Y', strtotime($result['created_at'])); ?>
                            </span>
                        </div>

                        <!-- Card Content -->
                        <div class="space-y-2 mb-4">
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 uppercase">Ad Soyad</span>
                                <span class="text-sm font-medium text-gray-800"><?php echo Security::escape($fullName ?: '-'); ?></span>
                            </div>
                            
                            <?php if (!empty($result['phone_number'])): ?>
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 uppercase">Telefon</span>
                                <a href="tel:<?php echo Security::escape($result['phone_number']); ?>" class="text-sm font-medium text-blue-600">
                                    <?php echo Security::escape($result['phone_number']); ?>
                                </a>
                            </div>
                            <?php endif; ?>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 uppercase">Qiymət</span>
                                <span class="text-lg font-bold text-green-600"><?php echo $result['calculated_price']; ?> ₼</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 uppercase">Məhsul</span>
                                <span class="text-sm font-medium text-gray-800"><?php echo Security::escape($result['product_name'] ?? '-'); ?></span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-xs text-gray-500 uppercase">Tarix</span>
                                <span class="text-xs text-gray-600"><?php echo date('d.m.Y H:i', strtotime($result['created_at'])); ?></span>
                            </div>
                        </div>

                        <!-- Card Actions -->
                        <div class="flex gap-2 pt-3 border-t border-gray-200">
                            <button onclick="viewDetails(<?php echo $result['id']; ?>)" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700 transition-colors">
                                Bax
                            </button>
                            <button onclick="confirmDelete(<?php echo $result['id']; ?>)" class="flex-1 bg-red-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-red-700 transition-colors">
                                Sil
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

    </div>

    <!-- Delete Form (hidden) -->
    <form id="deleteForm" method="POST" style="display: none;">
        <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
        <input type="hidden" name="action" value="delete">
        <input type="hidden" name="result_id" id="deleteResultId">
    </form>

    <!-- Modal for viewing details -->
    <div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 flex items-center justify-between">
                <h3 class="text-2xl font-bold text-gray-800">Nəticə Təfərrüatları</h3>
                <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div id="modalContent" class="p-6">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(resultId) {
            if (confirm('Bu nəticəni silmək istədiyinizə əminsiniz?')) {
                document.getElementById('deleteResultId').value = resultId;
                document.getElementById('deleteForm').submit();
            }
        }

        async function viewDetails(resultId) {
            document.getElementById('detailsModal').classList.remove('hidden');
            document.getElementById('detailsModal').classList.add('flex');
            document.getElementById('modalContent').innerHTML = '<div class="text-center py-8"><div class="animate-spin rounded-full h-12 w-12 border-t-4 border-depod-red mx-auto"></div></div>';

            try {
                const response = await fetch(`api/get_result_details.php?id=${resultId}`);
                const data = await response.json();
                
                if (data.success) {
                    displayDetails(data.result);
                } else {
                    document.getElementById('modalContent').innerHTML = '<p class="text-red-600">Xəta baş verdi.</p>';
                }
            } catch (error) {
                document.getElementById('modalContent').innerHTML = '<p class="text-red-600">Serverlə əlaqə xətası.</p>';
            }
        }

        function displayDetails(result) {
            let html = `
                <div class="space-y-6">
                    <div>
                        <h4 class="font-bold text-gray-700 mb-2">İstifadəçi Məlumatları</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p><span class="font-medium">Ad Soyad:</span> ${result.user_name || '-'} ${result.user_surname || ''}</p>
                            <p><span class="font-medium">Telefon:</span> ${result.phone_number || '-'}</p>
                            <p><span class="font-medium">Tarix:</span> ${result.created_at}</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-gray-700 mb-2">Qiymət və Məhsul</h4>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p><span class="font-medium">Hesablanmış Qiymət:</span> ${result.calculated_price} ₼</p>
                            <p><span class="font-medium">Tövsiyə olunan məhsul:</span> ${result.product_name || '-'}</p>
                            <p><span class="font-medium">Məhsul qiyməti:</span> ${result.product_base_price || '-'} ₼</p>
                        </div>
                    </div>

                    <div>
                        <h4 class="font-bold text-gray-700 mb-2">Seçimlər</h4>
                        <div class="space-y-3">
            `;

            if (result.selections && result.selections.length > 0) {
                result.selections.forEach((selection, index) => {
                    html += `
                        <div class="bg-gray-50 rounded-lg p-4">
                            <p class="font-medium text-gray-800 mb-1">Sual ${index + 1}: ${selection.question}</p>
                            <p class="text-gray-700">→ ${selection.option}</p>
                            <p class="text-sm text-gray-600 mt-1">Qiymət: +${selection.price} ₼</p>
                        </div>
                    `;
                });
            }

            html += `
                        </div>
                    </div>
                </div>
            `;

            document.getElementById('modalContent').innerHTML = html;
        }

        function closeModal() {
            document.getElementById('detailsModal').classList.add('hidden');
            document.getElementById('detailsModal').classList.remove('flex');
        }

        // Close modal on outside click
        document.getElementById('detailsModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>

</body>
</html>
