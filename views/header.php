<?php
require_once __DIR__ . '/../config/app_url.php';
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
    require_once __DIR__ . '/../config/auth_check.php';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POS System</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(appUrl('css/style.css'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
<header class="pos-header">
    <h1>POS System</h1>
</header>
