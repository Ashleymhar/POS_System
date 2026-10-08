<?php
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Sale.php';

$saleModel = new Sale();
$paymentModel = new Payment();

$saleId = $_POST['sale_id'] ?? 0;
$paymentMethod = $_POST['payment_method'] ?? '';
$amountPaid = $_POST['amount_paid'] ?? '';

$sale = $saleModel->getById($saleId);
$errors = [];

// Validate: sale must exist
if (!$sale) {
    $errors[] = "Sale not found.";
}

// Validate: payment amount must be numeric
if (!is_numeric($amountPaid)) {
    $errors[] = "Payment amount must be numeric.";
}

// Validate: allowed payment method
$allowedMethods = ['Cash', 'Card', 'E-wallet', 'Other'];
if (!in_array($paymentMethod, $allowedMethods)) {
    $errors[] = "Invalid payment method.";
}

// Validate: payment must not be less than total
if (empty($errors) && (float)$amountPaid < (float)$sale['total_amount']) {
    $errors[] = "Payment amount cannot be less than the total.";
}

if (!empty($errors)) {
    header('Location: ../views/payment.php?sale_id=' . $saleId . '&error=' . urlencode(implode(' ', $errors)));
    exit;
}

$paymentModel->create($saleId, $paymentMethod, (float)$amountPaid);

header('Location: ../views/payment_complete.php?sale_id=' . $saleId);
exit;