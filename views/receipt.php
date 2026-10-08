<?php
require_once '../models/Sale.php';
require_once '../models/Payment.php';
include 'header.php';
require_once '../config/role_check.php';
requireRole(['Administrator', 'Manager', 'Cashier']);
include 'navbar.php';

$saleModel = new Sale();
$paymentModel = new Payment();

$reprintNo = $_GET['transaction_no'] ?? '';
$saleId = $_GET['sale_id'] ?? 0;

if ($reprintNo !== '') {
    $sale = $saleModel->getByTransactionNo($reprintNo);
} else {
    $sale = $saleModel->getById($saleId);
}

$items = $sale ? $saleModel->getItems($sale['sale_id']) : [];
$payment = $sale ? $paymentModel->getBySaleId($sale['sale_id']) : null;
$change = $payment ? $payment['amount'] - $sale['total_amount'] : 0;
?>

<main class="pos-content">
    <h2>Receipt</h2>

    <form method="GET" style="margin-bottom:1rem;">
        <label>Reprint by Transaction No.:</label>
        <input type="text" name="transaction_no" placeholder="POS-2026-00001">
        <button type="submit">Find Receipt</button>
    </form>

    <?php if ($sale): ?>
    <div id="printable-receipt" style="font-family: monospace; width: 320px; border: 1px dashed #999; padding: 1rem;">
        <p style="text-align:center; margin:0;"><strong>ABC STORE</strong></p>
        <p style="text-align:center; margin:0;">Point-of-Sale System</p>
        <p>--------------------------------</p>
        <p>Transaction: <?= htmlspecialchars($sale['transaction_no']) ?></p>
        <p>Date: <?= date('M. j, Y', strtotime($sale['sale_date'])) ?></p>
        <p>Cashier: <?= htmlspecialchars($sale['cashier_name'] ?? 'N/A') ?></p>
        <p>--------------------------------</p>
        <?php foreach ($items as $item): ?>
        <p><?= htmlspecialchars($item['product_name']) ?>
            &nbsp;<?= $item['quantity'] ?> x ₱<?= number_format($item['unit_price'], 2) ?>
            = ₱<?= number_format($item['subtotal'], 2) ?></p>
        <?php endforeach; ?>
        <p>--------------------------------</p>
        <p>TOTAL &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ₱<?= number_format($sale['total_amount'], 2) ?></p>
        <?php if ($payment): ?>
        <p><?= strtoupper($payment['payment_method']) ?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ₱<?= number_format($payment['amount'], 2) ?></p>
        <p>CHANGE &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; ₱<?= number_format($change, 2) ?></p>
        <?php endif; ?>
        <p>--------------------------------</p>
        <p style="text-align:center;"><strong>THANK YOU!</strong></p>
    </div>

    <br>
    <button onclick="window.print()">Print Receipt</button>
    <a href="receipt.php?transaction_no=<?= urlencode($sale['transaction_no']) ?>"><button type="button">Reprint This Receipt</button></a>

    <?php else: ?>
    <p>No receipt found. Enter a valid Transaction No. above, or complete a sale first.</p>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>

<style>
@media print {
    .pos-header, .pos-navbar, form, button { display: none !important; }
    #printable-receipt { border: none; }
}
</style>