<?php
/**
 * Admin Logout
 */

define('APP_ACCESS', true);
require_once dirname(__DIR__) . '/config/config.php';
require_once BASE_PATH . '/classes/Admin.php';

$adminModel = new Admin();
$adminModel->logout();

header('Location: /admin/login.php');
exit;
