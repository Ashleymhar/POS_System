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
    <link rel="stylesheet" href="<?= htmlspecialchars(appUrl('css/style.css?v=2'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body>
<header class="pos-header">
    <a class="pos-brand" href="<?= htmlspecialchars(appUrl('index.php'), ENT_QUOTES, 'UTF-8') ?>">
        <span class="pos-brand-mark" aria-hidden="true">P</span>
        <span class="pos-brand-name">POS <small>STORE SYSTEM</small></span>
    </a>
    <div class="pos-account">
        <div class="pos-account-copy">
            <span>Signed in as</span>
            <strong><?= htmlspecialchars($_SESSION['full_name'] ?? '') ?></strong>
            <small><?= htmlspecialchars($_SESSION['role'] ?? '') ?></small>
        </div>
        <a class="pos-logout" href="<?= htmlspecialchars(appUrl('controllers/AuthController.php?action=logout'), ENT_QUOTES, 'UTF-8') ?>">Log out</a>
    </div>
</header>
