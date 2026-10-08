<?php
require_once __DIR__ . '/../models/Product.php';

$productModel = new Product();
$action = $_REQUEST['action'] ?? '';

function validateProduct($data, $productModel, $excludeId = null) {
    $errors = [];

    if (trim($data['product_name'] ?? '') === '') {
        $errors[] = "Product name cannot be empty.";
    }
    if (trim($data['product_code'] ?? '') === '') {
        $errors[] = "Product code cannot be empty.";
    } elseif ($productModel->codeExists($data['product_code'], $excludeId)) {
        $errors[] = "Product code '{$data['product_code']}' already exists.";
    }
    if (!is_numeric($data['price'] ?? '') || (float)$data['price'] < 0) {
        $errors[] = "Price must be a non-negative number.";
    }
    if (!is_numeric($data['quantity'] ?? '') || (int)$data['quantity'] < 0) {
        $errors[] = "Quantity cannot be negative.";
    }

    return $errors;
}

if ($action === 'add' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validateProduct($_POST, $productModel);
    if (!empty($errors)) {
        header('Location: ../views/products.php?errors=' . urlencode(implode('|', $errors)));
        exit;
    }
    try {
        $productModel->create($_POST);
    } catch (PDOException $e) {
        header('Location: ../views/products.php?errors=' . urlencode('A database error occurred while saving the product.'));
        exit;
    }
    header('Location: ../views/products.php');
    exit;
}

if ($action === 'edit' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $errors = validateProduct($_POST, $productModel, $_POST['product_id']);
    if (!empty($errors)) {
        header('Location: ../views/products.php?edit_id=' . $_POST['product_id'] . '&errors=' . urlencode(implode('|', $errors)));
        exit;
    }
    try {
        $productModel->update($_POST['product_id'], $_POST);
    } catch (PDOException $e) {
        header('Location: ../views/products.php?errors=' . urlencode('A database error occurred while updating the product.'));
        exit;
    }
    header('Location: ../views/products.php');
    exit;
}

if ($action === 'deactivate' && isset($_GET['id'])) {
    $productModel->deactivate($_GET['id']);
    header('Location: ../views/products.php');
    exit;
}