<?php
session_start();
require_once __DIR__ . '/../models/Product.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_REQUEST['action'] ?? '';

if ($action === 'set_customer') {
    $_SESSION['selected_customer_id'] = $_GET['customer_id'] ?? 0;
}

if ($action === 'add' && isset($_GET['product_id'])) {
    $productModel = new Product();
    $product = $productModel->getById($_GET['product_id']);

    if ($product) {
        $pid = $product['product_id'];
        if (isset($_SESSION['cart'][$pid])) {
            $_SESSION['cart'][$pid]['qty'] += 1;
        } else {
            $_SESSION['cart'][$pid] = [
                'product_name' => $product['product_name'],
                'price' => $product['price'],
                'qty' => 1
            ];
        }
    }
}

if ($action === 'update_qty' && isset($_POST['product_id']) && isset($_POST['qty'])) {
    $pid = $_POST['product_id'];
    $qty = (int)$_POST['qty'];
    if (isset($_SESSION['cart'][$pid])) {
        if ($qty <= 0) {
            unset($_SESSION['cart'][$pid]);
        } else {
            $_SESSION['cart'][$pid]['qty'] = $qty;
        }
    }
}

if ($action === 'remove' && isset($_GET['product_id'])) {
    unset($_SESSION['cart'][$_GET['product_id']]);
}

if ($action === 'clear') {
    $_SESSION['cart'] = [];
}

header('Location: ../views/sales.php');
exit;