<?php
require_once '../models/Sale.php';
include 'header.php';
require_once '../config/role_check.php';
requireRole(['Administrator', 'Manager']);
include 'navbar.php';

$saleModel = new Sale();

$keyword = $_GET['keyword'] ?? '';
$dateFrom = $_GET['date_from'] ?? '';
$dateTo = $_GET['date_to'] ?? '';

$sales = $saleModel->getAll($keyword, $dateFrom, $dateTo);

$viewSale = null;
$viewItems = [];
if (isset($_GET['view_id'])) {
    $viewSale = $saleModel->getById($_GET['view_id']);
    $viewItems = $saleModel->getItems($_GET['view_id']);
}
?>

<main class="pos-content">
    <h2>Sales Transaction History</h2>

    <form method="GET">
        <label>Search:</label>
        <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Transaction No. or Customer">

        <label>From:</label>
        <input type="date" name="date_from" value="<?= htmlspecialchars($dateFrom) ?>">

        <label>To:</label>
        <input type="date" name="date_to" value="<?= htmlspecialchars($dateTo) ?>">

        <button type="submit">Search</button>
        <a href="sales_history.php"><button type="button">Clear</button></a>
    </form>

    <table border="1" cellpadding="6">
        <tr>
            <th>Transaction</th>
            <th>Date</th>
            <th>Customer</th>
            <th>Cashier</th>
            <th>Total</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
        <?php foreach ($sales as $s): ?>
        <tr>
            <td><?= htmlspecialchars($s['transaction_no']) ?></td>
            <td><?= date('n/j', strtotime($s['sale_date'])) ?></td>
            <td><?= htmlspecialchars($s['customer_name'] ?? 'Walk-in') ?></td>
            <td><?= htmlspecialchars($s['cashier_name'] ?? 'N/A') ?></td>
            <td><?= number_format($s['total_amount'], 2) ?></td>
            <td><?= htmlspecialchars($s['status']) ?></td>
            <td>
                <a href="?view_id=<?= $s['sale_id'] ?>&keyword=<?= urlencode($keyword) ?>&date_from=<?= urlencode($dateFrom) ?>&date_to=<?= urlencode($dateTo) ?>">View</a> |
                <a href="receipt.php?sale_id=<?= $s['sale_id'] ?>">View Receipt</a> |
                <a href="receipt.php?transaction_no=<?= urlencode($s['transaction_no']) ?>">Reprint</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($sales)): ?>
        <tr><td colspan="7">No transactions found.</td></tr>
        <?php endif; ?>
    </table>

    <?php if ($viewSale): ?>
    <hr>
    <h3>Transaction Details — <?= htmlspecialchars($viewSale['transaction_no']) ?></h3>
    <p><strong>Date:</strong> <?= htmlspecialchars($viewSale['sale_date']) ?></p>
    <p><strong>Customer:</strong> <?= htmlspecialchars($viewSale['customer_name'] ?? 'Walk-in') ?></p>
    <p><strong>Cashier:</strong> <?= htmlspecialchars($viewSale['cashier_name'] ?? 'N/A') ?></p>

    <table border="1" cellpadding="6">
        <tr><th>Product</th><th>Qty</th><th>Unit Price</th><th>Subtotal</th></tr>
        <?php foreach ($viewItems as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td><?= htmlspecialchars($item['quantity']) ?></td>
            <td><?= number_format($item['unit_price'], 2) ?></td>
            <td><?= number_format($item['subtotal'], 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>

    <p>Subtotal: ₱<?= number_format($viewSale['subtotal'], 2) ?></p>
    <p>Discount: ₱<?= number_format($viewSale['discount'], 2) ?></p>
    <p><strong>Total: ₱<?= number_format($viewSale['total_amount'], 2) ?></strong></p>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>