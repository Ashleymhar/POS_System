<?php
require_once '../models/Sale.php';
include 'header.php';
require_once '../config/role_check.php';
requireRole(['Administrator', 'Manager', 'Cashier']);
include 'navbar.php';

$saleModel = new Sale();
$sale = $saleModel->getById($_GET['sale_id'] ?? 0);
$error = $_GET['error'] ?? '';
?>

<main class="pos-content">
    <h2>Payment</h2>

    <?php if ($error): ?>
        <p style="color:red;"><strong>Error:</strong> <?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <?php if ($sale): ?>
    <p><strong>Transaction No.:</strong> <?= htmlspecialchars($sale['transaction_no']) ?></p>
    <p><strong>TOTAL:</strong> ₱<?= number_format($sale['total_amount'], 2) ?></p>

    <form action="../controllers/PaymentController.php" method="POST">
        <input type="hidden" name="sale_id" value="<?= $sale['sale_id'] ?>">

        <label>Payment Method:</label>
        <select name="payment_method" required>
            <option value="">-- Select --</option>
            <option value="Cash">Cash</option>
            <option value="Card">Card</option>
            <option value="E-wallet">E-wallet</option>
            <option value="Other">Other</option>
        </select><br><br>

        <label>Payment Amount:</label>
        <input type="number" step="0.01" name="amount_paid" required><br><br>

        <button type="submit">COMPLETE SALE</button>
    </form>
    <?php else: ?>
    <p>Sale not found.</p>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>