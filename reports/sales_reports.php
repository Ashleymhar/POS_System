<?php
require_once '../models/Sale.php';
include '../views/header.php';
require_once '../config/role_check.php';
requireRole(['Administrator', 'Manager']);
include '../views/navbar.php';

$saleModel = new Sale();

$dailyDate = $_GET['daily_date'] ?? date('Y-m-d');
$daily = $saleModel->dailyReport($dailyDate);

$monthYear = $_GET['month_year'] ?? date('Y-m');
[$year, $month] = explode('-', $monthYear);
$monthly = $saleModel->monthlyReport($year, $month);

$byProduct = $saleModel->salesByProduct();
$byCategory = $saleModel->salesByCategory();
$byCashier = $saleModel->salesByCashier();
$byCustomer = $saleModel->salesByCustomer();
$topSelling = $saleModel->topSellingProducts(5);
$paymentSummary = $saleModel->paymentMethodSummary();
?>

<main class="pos-content">
    <h2>Sales Reports</h2>

    <h3>Daily Sales Report</h3>
    <form method="GET">
        <label>Date:</label>
        <input type="date" name="daily_date" value="<?= htmlspecialchars($dailyDate) ?>">
        <button type="submit">View</button>
    </form>
    <p>Date: <?= date('F j, Y', strtotime($dailyDate)) ?></p>
    <p>Number of Transactions: <?= $daily['total_transactions'] ?></p>
    <p>Total Sales: ₱<?= number_format($daily['total_sales'], 2) ?></p>

    <hr>

    <h3>Monthly Sales Report</h3>
    <form method="GET">
        <label>Month:</label>
        <input type="month" name="month_year" value="<?= htmlspecialchars($monthYear) ?>">
        <button type="submit">View</button>
    </form>
    <p><?= date('F Y', strtotime($monthYear . '-01')) ?></p>
    <p>Total Transactions: <?= $monthly['total_transactions'] ?></p>
    <p>Total Sales: ₱<?= number_format($monthly['total_sales'], 2) ?></p>

    <hr>

    <h3>Sales by Product</h3>
    <table border="1" cellpadding="6">
        <tr><th>Product</th><th>Qty Sold</th><th>Total Sales</th></tr>
        <?php foreach ($byProduct as $r): ?>
        <tr><td><?= htmlspecialchars($r['product_name']) ?></td><td><?= $r['total_qty'] ?></td><td>₱<?= number_format($r['total_sales'], 2) ?></td></tr>
        <?php endforeach; ?>
    </table>

    <h3>Sales by Category</h3>
    <table border="1" cellpadding="6">
        <tr><th>Category</th><th>Total Sales</th></tr>
        <?php foreach ($byCategory as $r): ?>
        <tr><td><?= htmlspecialchars($r['category'] ?? 'Uncategorized') ?></td><td>₱<?= number_format($r['total_sales'], 2) ?></td></tr>
        <?php endforeach; ?>
    </table>

    <h3>Sales by Cashier</h3>
    <table border="1" cellpadding="6">
        <tr><th>Cashier</th><th>Transactions</th><th>Total Sales</th></tr>
        <?php foreach ($byCashier as $r): ?>
        <tr><td><?= htmlspecialchars($r['cashier_name']) ?></td><td><?= $r['total_transactions'] ?></td><td>₱<?= number_format($r['total_sales'], 2) ?></td></tr>
        <?php endforeach; ?>
    </table>

    <h3>Sales by Customer</h3>
    <table border="1" cellpadding="6">
        <tr><th>Customer</th><th>Transactions</th><th>Total Sales</th></tr>
        <?php foreach ($byCustomer as $r): ?>
        <tr><td><?= htmlspecialchars($r['customer_name']) ?></td><td><?= $r['total_transactions'] ?></td><td>₱<?= number_format($r['total_sales'], 2) ?></td></tr>
        <?php endforeach; ?>
    </table>

    <h3>Top-Selling Products</h3>
    <table border="1" cellpadding="6">
        <tr><th>Rank</th><th>Product</th><th>Qty Sold</th></tr>
        <?php foreach ($topSelling as $i => $r): ?>
        <tr><td><?= $i + 1 ?></td><td><?= htmlspecialchars($r['product_name']) ?></td><td><?= $r['total_qty'] ?></td></tr>
        <?php endforeach; ?>
    </table>

    <h3>Payment Method Summary</h3>
    <table border="1" cellpadding="6">
        <tr><th>Method</th><th>No. of Payments</th><th>Total Amount</th></tr>
        <?php foreach ($paymentSummary as $r): ?>
        <tr><td><?= htmlspecialchars($r['payment_method']) ?></td><td><?= $r['total_payments'] ?></td><td>₱<?= number_format($r['total_amount'], 2) ?></td></tr>
        <?php endforeach; ?>
    </table>
</main>

<?php include '../views/footer.php'; ?>