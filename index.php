<?php
require_once 'config/database.php';
require_once 'models/Sale.php';
require_once 'models/Product.php';
include 'views/header.php';
include 'views/navbar.php';

$saleModel = new Sale();
$productModel = new Product();

$today = $saleModel->dailyReport(date('Y-m-d'));
$productsSoldToday = $saleModel->productsSoldToday();
$lowStockCount = $productModel->countLowStock();
$last7Days = $saleModel->salesLastNDays(7);
$topSelling = $saleModel->topSellingProducts(3);

$maxSales = 0;
foreach ($last7Days as $day) {
    if ($day['total_sales'] > $maxSales) $maxSales = $day['total_sales'];
}
?>

<main class="pos-content">
    <h2>POS Dashboard</h2>

    <table border="1" cellpadding="10" style="width:100%; text-align:center;">
        <tr>
            <td>
                <strong>Today's Sales</strong><br>
                <span style="font-size:1.5em;">₱<?= number_format($today['total_sales'], 2) ?></span>
            </td>
            <td>
                <strong>Transactions</strong><br>
                <span style="font-size:1.5em;"><?= $today['total_transactions'] ?></span>
            </td>
        </tr>
        <tr>
            <td>
                <strong>Products Sold</strong><br>
                <span style="font-size:1.5em;"><?= $productsSoldToday ?></span>
            </td>
            <td>
                <strong>Low Stock</strong><br>
                <span style="font-size:1.5em; color:<?= $lowStockCount > 0 ? '#b00000' : 'inherit' ?>;"><?= $lowStockCount ?></span>
            </td>
        </tr>
    </table>

    <hr>

    <h3>Daily Sales Chart (Last 7 Days)</h3>
    <div style="display:flex; align-items:flex-end; gap:12px; height:180px; border-bottom:2px solid #333; padding:0 10px;">
        <?php foreach ($last7Days as $day): ?>
        <?php
            $barHeight = $maxSales > 0 ? ($day['total_sales'] / $maxSales) * 150 : 0;
        ?>
        <div style="display:flex; flex-direction:column; align-items:center;">
            <div style="font-size:0.8em;">₱<?= number_format($day['total_sales'], 0) ?></div>
            <div style="width:40px; height:<?= $barHeight ?>px; background:#1F3864;"></div>
            <div style="font-size:0.8em;"><?= date('M j', strtotime($day['sale_day'])) ?></div>
        </div>
        <?php endforeach; ?>
        <?php if (empty($last7Days)): ?>
        <p>No sales in the last 7 days.</p>
        <?php endif; ?>
    </div>

    <hr>

    <h3>Top Selling Products</h3>
    <ol>
        <?php foreach ($topSelling as $p): ?>
        <li><?= htmlspecialchars($p['product_name']) ?> (<?= $p['total_qty'] ?> sold)</li>
        <?php endforeach; ?>
        <?php if (empty($topSelling)): ?>
        <li>No sales data yet.</li>
        <?php endif; ?>
    </ol>
</main>

<?php include 'views/footer.php'; ?>