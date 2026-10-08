<?php
require_once '../models/Sale.php';
require_once '../models/Payment.php';
include 'header.php';
include 'navbar.php';

$saleModel = new Sale();
$paymentModel = new Payment();

$sale = $saleModel->getById($_GET['sale_id'] ?? 0);
$payment = $paymentModel->getBySaleId($_GET['sale_id'] ?? 0);
$change = $payment ? $payment['amount'] - $sale['total_amount'] : 0;
?>

<main class="pos-content">
    <h2>Sale Complete</h2>

    <?php if ($sale && $payment): ?>
    <p><strong>Transaction No.:</strong> <?= htmlspecialchars($sale['transaction_no']) ?></p>
    <p><strong>Payment Method:</strong> <?= htmlspecialchars($payment['payment_method']) ?></p>

    <hr>
    <p>TOTAL: &nbsp;&nbsp;&nbsp; ₱<?= number_format($sale['total_amount'], 2) ?></p>
    <p>PAYMENT: &nbsp; ₱<?= number_format($payment['amount'], 2) ?></p>
    <p><strong>CHANGE: &nbsp;&nbsp; ₱<?= number_format($change, 2) ?></strong></p>
       <br>
    <a href="receipt.php?sale_id=<?= $sale['sale_id'] ?>"><button type="button">View Receipt</button></a>
    <?php else: ?>
    <p>Payment record not found.</p>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>