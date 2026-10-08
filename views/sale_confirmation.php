<?php
require_once '../models/Sale.php';
include 'header.php';
include 'navbar.php';

$saleModel = new Sale();
$sale = $saleModel->getById($_GET['sale_id'] ?? 0);
?>

<main class="pos-content">
    <h2>Sale Confirmation</h2>

    <?php if ($sale): ?>
    <p><strong>Transaction No.:</strong> <?= htmlspecialchars($sale['transaction_no']) ?></p>
    <p><strong>Customer:</strong> <?= htmlspecialchars($sale['customer_name'] ?? 'Walk-in Customer') ?></p>
    <p><strong>Date:</strong> <?= htmlspecialchars($sale['sale_date']) ?></p>

    <hr>
    <p>Subtotal: ₱<?= number_format($sale['subtotal'], 2) ?></p>
    <p>Discount: ₱<?= number_format($sale['discount'], 2) ?></p>
    <p><strong>Total: ₱<?= number_format($sale['total_amount'], 2) ?></strong></p>

        <a href="payment.php?sale_id=<?= $sale['sale_id'] ?>"><button type="button">Proceed to Payment</button></a>
    <?php else: ?>
    <p>Sale not found.</p>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>