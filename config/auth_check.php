<?php
if (!isset($_SESSION['user_id'])) {
    $current = basename($_SERVER['PHP_SELF']);
    if ($current !== 'login.php') {
        header('Location: /POS_System/views/login.php');
        exit;
    }
}