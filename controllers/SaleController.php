<?php
session_start();
require_once __DIR__ . '/../models/Sale.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header('Location: ../views/sales.php?error=' . urlencode("ERROR: Cart is empty. Add at least one product before checking out."));
    exit;
}

// Prevent duplicate transaction: block resubmission of the same form token
$submittedToken = $_POST['checkout_token'] ?? '';
if (!isset($_SESSION['checkout_token']) || $submittedToken !== $_SESSION['checkout_token']) {
    header('Location: ../views/sales.php?error=' . urlencode("ERROR: This transaction was already processed or has expired."));
    exit;
}
unset($_SESSION['checkout_token']);

$customerId = ($_POST['customer_id'] ?? 0) > 0 ? $_POST['customer_id'] : null;
$discount = isset($_POST['discount']) ? (float)$_POST['discount'] : 0;

// TEMPORARY: hardcoded cashier until Lab 15 (User Authentication) is built
$userId = $_SESSION['user_id'];

$saleModel = new Sale();

try {
    $result = $saleModel->checkout($customerId, $userId, $discount, $_SESSION['cart']);
} catch (PDOException $e) {
    header('Location: ../views/sales.php?error=' . urlencode("ERROR: A database error occurred. Please try again."));
    exit;
}

if ($result['success']) {
    $_SESSION['cart'] = [];
    header('Location: ../views/sale_confirmation.php?sale_id=' . $result['sale_id']);
    exit;
} else {
    header('Location: ../views/sales.php?error=' . urlencode($result['error']));
    exit;
}