<?php
session_start();
require_once __DIR__ . '/../models/User.php';

$action = $_REQUEST['action'] ?? '';

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $userModel = new User();
    $user = $userModel->getByUsername($username);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];
        header('Location: ../index.php');
        exit;
    } else {
        header('Location: ../views/login.php?error=' . urlencode('Invalid username or password.'));
        exit;
    }
}

if ($action === 'logout') {
    session_unset();
    session_destroy();
    header('Location: ../views/login.php');
    exit;
}