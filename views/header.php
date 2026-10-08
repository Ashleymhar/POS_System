<?php
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
    <link rel="stylesheet" href="/POS_System/css/style.css">
</head>
<body>
<header class="pos-header">
    <h1>POS System</h1>
</header>
