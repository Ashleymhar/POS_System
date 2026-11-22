<?php
require_once 'config/role_check.php';
requireRole(['Administrator', 'Manager', 'Cashier', 'Inventory Staff']);
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

<main class="pos-content dashboard-content">
    <div class="dashboard-heading">
        <div>
            <p class="eyebrow">STORE OVERVIEW</p>
            <h2>Dashboard</h2>
        </div>
        <span class="dashboard-period"><span class="pos-status-dot"></span> Today</span>
    </div>

    <section class="dashboard-metrics" aria-label="Today's store metrics">
        <article class="metric-card metric-card-primary">
            <span class="metric-label">Today's Sales</span>
            <strong class="metric-value">₱<?= number_format($today['total_sales'], 2) ?></strong>
            <span class="metric-note">Total sales today</span>
        </article>
        <article class="metric-card">
            <span class="metric-label">Transactions</span>
            <strong class="metric-value"><?= number_format($today['total_transactions']) ?></strong>
            <span class="metric-note">Completed today</span>
        </article>
        <article class="metric-card">
            <span class="metric-label">Products Sold</span>
            <strong class="metric-value"><?= number_format($productsSoldToday) ?></strong>
            <span class="metric-note">Units sold today</span>
        </article>
        <article class="metric-card">
            <span class="metric-label">Low Stock</span>
            <strong class="metric-value<?= $lowStockCount > 0 ? ' metric-warning' : '' ?>"><?= number_format($lowStockCount) ?></strong>
            <span class="metric-note">Products to restock</span>
        </article>
    </section>

    <section class="dashboard-lower">
        <article class="dashboard-panel sales-panel">
            <div class="panel-heading">
                <div>
                    <p class="eyebrow">SALES ACTIVITY</p>
                    <h3>Daily sales</h3>
                </div>
                <span class="panel-caption">Last 7 days</span>
            </div>
            <div class="sales-chart">
        <?php foreach ($last7Days as $day): ?>
        <?php
            $barHeight = $maxSales > 0 ? ($day['total_sales'] / $maxSales) * 150 : 0;
        ?>
        <div class="chart-column">
            <span class="chart-value">₱<?= number_format($day['total_sales'], 0) ?></span>
            <div class="chart-bar-track">
                <div class="chart-bar" style="height:<?= $barHeight ?>px;"></div>
            </div>
            <span class="chart-label"><?= date('M j', strtotime($day['sale_day'])) ?></span>
        </div>
        <?php endforeach; ?>
        <?php if (empty($last7Days)): ?>
        <p class="chart-empty">No sales in the last 7 days.</p>
        <?php endif; ?>
            </div>
        </article>

        <article class="dashboard-panel top-products-panel">
            <div class="panel-heading">
                <div>
                    <p class="eyebrow">BEST PERFORMERS</p>
                    <h3>Top selling products</h3>
                </div>
            </div>
            <ol class="top-products-list">
                <?php foreach ($topSelling as $index => $p): ?>
                <li>
                    <span class="product-rank"><?= $index + 1 ?></span>
                    <span class="product-name"><?= htmlspecialchars($p['product_name']) ?></span>
                    <strong class="product-quantity"><?= number_format($p['total_qty']) ?> sold</strong>
                </li>
                <?php endforeach; ?>
                <?php if (empty($topSelling)): ?>
                <li class="empty-products">No sales data yet.</li>
                <?php endif; ?>
            </ol>
        </article>
    </section>
</main>

<?php include 'views/footer.php'; ?>