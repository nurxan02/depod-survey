<?php
/**
 * Admin Login Page
 */

define('APP_ACCESS', true);
require_once dirname(__DIR__) . '/config/config.php';
require_once BASE_PATH . '/classes/Admin.php';
require_once BASE_PATH . '/classes/Security.php';

Security::setSecurityHeaders();

// Redirect if already logged in
if (Security::isAdminLoggedIn()) {
    header('Location: /admin/dashboard.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !Security::verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Təhlükəsizlik xətası.';
    } else {
        $username = Security::sanitizeString($_POST['username']);
        $password = $_POST['password'];
        
        if (empty($username) || empty($password)) {
            $error = 'İstifadəçi adı və şifrə tələb olunur.';
        } else {
            $adminModel = new Admin();
            if ($adminModel->authenticate($username, $password)) {
                header('Location: /admin/dashboard.php');
                exit;
            } else {
                $error = 'İstifadəçi adı və ya şifrə yanlışdır.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="az">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Giriş - <?php echo APP_NAME; ?></title>
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
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    
    <div class="bg-white rounded-2xl shadow-xl p-8 w-full max-w-md">
        
        <!-- Logo/Title -->
        <div class="text-center mb-8">
            <div class="flex items-center justify-center gap-3 mb-2">
                <img src="/logo/logo.svg" alt="Depod.az Logo" class="h-10 w-10">
                <h1 class="text-3xl font-bold text-gray-800">Depod.az</h1>
            </div>
            <p class="text-gray-600 mt-2">Admin Panel Girişi</p>
        </div>

        <?php if ($error): ?>
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <p class="text-red-700"><?php echo Security::escape($error); ?></p>
            </div>
        <?php endif; ?>

        <!-- Login Form -->
        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo Security::generateCSRFToken(); ?>">
            
            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">İstifadəçi Adı</label>
                <input type="text" name="username" required 
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-depod-dark"
                       placeholder="İstifadəçi adınızı daxil edin">
            </div>

            <div class="mb-6">
                <label class="block text-gray-700 font-medium mb-2">Şifrə</label>
                <input type="password" name="password" required 
                       class="w-full px-4 py-3 border-2 border-gray-300 rounded-lg focus:outline-none focus:border-depod-dark"
                       placeholder="Şifrənizi daxil edin">
            </div>

            <button type="submit" 
                    class="w-full bg-depod-dark text-white py-3 rounded-lg font-semibold hover:bg-depod-gray transition-colors">
                Daxil Ol
            </button>
        </form>

        <!-- Back to site -->
        <div class="text-center mt-6">
            <a href="/index.php" class="text-gray-600 hover:text-depod-red transition-colors">
                ← Sayta qayıt
            </a>
        </div>

    </div>

</body>
</html>
