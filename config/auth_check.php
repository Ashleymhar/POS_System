<?php
require_once __DIR__ . '/app_url.php';
if (!isset($_SESSION['user_id'])) {
    $current = basename($_SERVER['PHP_SELF']);
    if ($current !== 'login.php') {
        header('Location: ' . appUrl('views/login.php'));
        exit;
    }
}