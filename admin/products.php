<?php
/**
 * Admin Products Management Page
 */

define('APP_ACCESS', true);
require_once dirname(__DIR__) . '/config/config.php';
require_once BASE_PATH . '/classes/Security.php';
require_once BASE_PATH . '/classes/Admin.php';
require_once BASE_PATH . '/classes/Product.php';

Security::setSecurityHeaders();
Security::requireAdmin();

$productModel = new Product();

$message = null;
$error = null;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Security::verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Təhlükəsizlik xətası.';
    } else {
        $action = $_POST['action'] ?? '';
        
        // Create New Product
        if ($action === 'create_product') {
            $productName = Security::sanitizeString($_POST['product_name']);
            $basePrice = (int)$_POST['base_price'];
            $description = Security::sanitizeString($_POST['description']);
            $productImage = Security::sanitizeString($_POST['product_image']);
            $birmarketLink = Security::sanitizeString($_POST['birmarket_link']);
            $features = Security::sanitizeString($_POST['features']);
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            $optimalFitScore = $_POST['optimal_fit_score'];
            
            // Validate JSON
            if (!json_decode($optimalFitScore)) {
                $error = 'Optimal Fit Score düzgün JSON formatında olmalıdır.';
            } else {
                if ($productModel->createProduct($productName, $basePrice, $optimalFitScore, $description, $productImage)) {
                    // Update the newly created product with additional fields
                    $newId = $productModel->db->lastInsertId();
                    $productModel->updateProduct($newId, $productName, $basePrice, $optimalFitScore, $description, $productImage, $birmarketLink, $features, $isActive);
                    $message = 'Yeni məhsul əlavə edildi.';
                } else {
                    $error = 'Xəta baş verdi.';
                }
            }
        }
        
        // Update Product
        if ($action === 'update_product') {
            $productId = (int)$_POST['product_id'];
            $productName = Security::sanitizeString($_POST['product_name']);
            $basePrice = (int)$_POST['base_price'];
            $description = Security::sanitizeString($_POST['description']);
            $productImage = Security::sanitizeString($_POST['product_image']);
            $birmarketLink = Security::sanitizeString($_POST['birmarket_link']);
            $features = Security::sanitizeString($_POST['features']);
            $isActive = isset($_POST['is_active']) ? 1 : 0;
            $optimalFitScore = $_POST['optimal_fit_score'];
            
            // Validate JSON
            if (!json_decode($optimalFitScore)) {
                $error = 'Optimal Fit Score düzgün JSON formatında olmalıdır.';
            } else {
                if ($productModel->updateProduct($productId, $productName, $basePrice, $optimalFitScore, $description, $productImage, $birmarketLink, $features, $isActive)) {
                    $message = 'Məhsul uğurla yeniləndi.';
                } else {
                    $error = 'Xəta baş verdi.';
                }
            }
        }
        
        // Delete Product
        if ($action === 'delete_product') {
            $productId = (int)$_POST['product_id'];
            
            if ($productModel->deleteProduct($productId)) {
                $message = 'Məhsul silindi.';
            } else {
                $error = 'Xəta baş verdi.';
            }
        }
    }
}

// Get all products
$products = $productModel->getAllProducts(false); // Get all, including inactive
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Məhsullar - Admin Panel</title>
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
                <h1 class="text-3xl font-bold text-gray-800">Məhsulların İdarə Edilməsi</h1>
                <p class="text-gray-600 mt-1">Məhsul məlumatları və tövsiyə parametrlərini redaktə edin</p>
            </div>
            <button onclick="toggleNewProduct()" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-3 rounded-lg hover:opacity-90 font-medium">
                + Yeni Məhsul Əlavə Et
            </button>
        </div>

        <!-- New Product Form -->
        <div id="new-product-form" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6 hidden">
            <div class="bg-green-50 px-6 py-4 border-b border-gray-200">
                <h3 class="font-bold text-gray-800">Yeni Məhsul</h3>
            </div>
            <div class="p-6">
                <form method="POST" action="" class="space-y-4">
                    <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                    <input type="hidden" name="action" value="create_product">
                    
                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Məhsul Adı</label>
                        <input type="text" name="product_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Baza Qiyməti</label>
                        <input type="number" name="base_price" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Təsvir</label>
                        <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600"></textarea>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Məhsul Şəkli URL</label>
                        <input type="text" name="product_image" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Birmarket.az Linki</label>
                        <input type="text" name="birmarket_link" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600">
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Məhsul Xüsusiyyətləri</label>
                        <textarea name="features" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600"></textarea>
                        <p class="text-xs text-gray-500 mt-1">Hər xüsusiyyət yeni sətirdə</p>
                    </div>

                    <div>
                        <label class="block text-gray-700 font-medium mb-2">Optimal Fit Score (JSON)</label>
                        <textarea name="optimal_fit_score" rows="4" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-green-600 font-mono text-sm">{"min_price": 0, "max_price": 50, "anc_required": false}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Nümunə: {"min_price": 0, "max_price": 70, "anc_required": false}</p>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_active" id="new-active" checked class="w-4 h-4 text-green-600">
                        <label for="new-active" class="ml-2 text-gray-700">Aktiv</label>
                    </div>

                    <div class="flex gap-3 pt-2">
                        <button type="submit" class="bg-gradient-to-r from-green-600 to-green-700 text-white px-6 py-2 rounded-lg hover:opacity-90">
                            Əlavə Et
                        </button>
                        <button type="button" onclick="toggleNewProduct()" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
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

        <!-- Products Grid -->
        <div class="grid md:grid-cols-2 gap-6">
            <?php foreach ($products as $product): ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    
                    <!-- Product Header -->
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg"><?php echo Security::escape($product['product_name']); ?></h3>
                            <p class="text-sm text-gray-600">Qiymət: <?php echo $product['base_price']; ?> ₼</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <?php if ($product['is_active']): ?>
                                <span class="bg-green-100 text-green-700 text-xs px-3 py-1 rounded-full">Aktiv</span>
                            <?php else: ?>
                                <span class="bg-gray-100 text-gray-700 text-xs px-3 py-1 rounded-full">Deaktiv</span>
                            <?php endif; ?>
                            <button onclick="toggleEdit('product-<?php echo $product['id']; ?>')" class="text-blue-600 hover:text-blue-700">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path>
                                </svg>
                            </button>
                            <form method="POST" action="" class="inline" onsubmit="return confirm('Bu məhsulu silmək istədiyinizə əminsiniz?');">
                                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                <input type="hidden" name="action" value="delete_product">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                <button type="submit" class="text-red-600 hover:text-red-700">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Product Content -->
                    <div class="p-6">
                        
                        <!-- View Mode -->
                        <div id="view-product-<?php echo $product['id']; ?>">
                            <p class="text-gray-700 mb-4"><?php echo Security::escape($product['description']); ?></p>
                            <div class="bg-gray-50 rounded-lg p-4">
                                <p class="text-sm font-medium text-gray-700 mb-2">Tövsiyə Parametrləri:</p>
                                <pre class="text-xs text-gray-600 overflow-x-auto"><?php echo Security::escape($product['optimal_fit_score']); ?></pre>
                            </div>
                        </div>

                        <!-- Edit Mode -->
                        <div id="edit-product-<?php echo $product['id']; ?>" class="hidden">
                            <form method="POST" action="" class="space-y-4">
                                <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
                                <input type="hidden" name="action" value="update_product">
                                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                
                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Məhsul Adı</label>
                                    <input type="text" name="product_name" value="<?php echo Security::escape($product['product_name']); ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-depod-red">
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Baza Qiyməti</label>
                                    <input type="number" name="base_price" value="<?php echo $product['base_price']; ?>" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-depod-red">
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Təsvir</label>
                                    <textarea name="description" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-depod-red"><?php echo Security::escape($product['description']); ?></textarea>
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Məhsul Şəkli URL</label>
                                    <input type="text" name="product_image" value="<?php echo Security::escape($product['product_image']); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-depod-red">
                                    <p class="text-xs text-gray-500 mt-1">Məhsulun şəkilinin URL-i</p>
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Birmarket.az Linki</label>
                                    <input type="text" name="birmarket_link" value="<?php echo Security::escape($product['birmarket_link']); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-depod-red">
                                    <p class="text-xs text-gray-500 mt-1">Birmarket.az-dakı məhsul linki</p>
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Məhsul Xüsusiyyətləri</label>
                                    <textarea name="features" rows="3" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-depod-red"><?php echo Security::escape($product['features']); ?></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Məhsulun əsas xüsusiyyətləri (hər xüsusiyyət yeni sətirdə)</p>
                                </div>

                                <div>
                                    <label class="block text-gray-700 font-medium mb-2">Optimal Fit Score (JSON)</label>
                                    <textarea name="optimal_fit_score" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-depod-red font-mono text-sm"><?php echo Security::escape($product['optimal_fit_score']); ?></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Nümunə: {"min_price": 0, "max_price": 70, "anc_required": false}</p>
                                </div>

                                <div class="flex items-center">
                                    <input type="checkbox" name="is_active" id="active-<?php echo $product['id']; ?>" <?php echo $product['is_active'] ? 'checked' : ''; ?> class="w-4 h-4 text-depod-red">
                                    <label for="active-<?php echo $product['id']; ?>" class="ml-2 text-gray-700">Aktiv</label>
                                </div>

                                <div class="flex gap-3 pt-2">
                                    <button type="submit" class="bg-gradient-to-r from-depod-red to-depod-orange text-white px-6 py-2 rounded-lg hover:opacity-90">
                                        Yadda Saxla
                                    </button>
                                    <button type="button" onclick="toggleEdit('product-<?php echo $product['id']; ?>')" class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                                        Ləğv Et
                                    </button>
                                </div>
                            </form>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Help Box -->
        <div class="mt-8 bg-blue-50 border-l-4 border-blue-500 p-6 rounded-lg">
            <h3 class="font-bold text-blue-900 mb-2">Tövsiyə Parametrləri Haqqında</h3>
            <p class="text-blue-800 text-sm mb-3">Optimal Fit Score JSON formatında olmalıdır və aşağıdakı parametrləri daxil edə bilər:</p>
            <ul class="text-blue-800 text-sm space-y-1 list-disc list-inside">
                <li><code class="bg-blue-100 px-1 rounded">min_price</code> və <code class="bg-blue-100 px-1 rounded">max_price</code>: Qiymət diapazonu</li>
                <li><code class="bg-blue-100 px-1 rounded">anc_required</code>: ANC tələb olunurmu? (true/false)</li>
                <li><code class="bg-blue-100 px-1 rounded">premium</code>: Premium məhsuldur? (true/false)</li>
            </ul>
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
        
        function toggleNewProduct() {
            const form = document.getElementById('new-product-form');
            if (form) {
                form.classList.toggle('hidden');
            }
        }
    </script>

</body>
</html>
