<?php
$currentPath = parse_url($_SERVER['SCRIPT_NAME'] ?? '', PHP_URL_PATH);
$localPrefix = '/' . basename(dirname(__DIR__));
if (strpos($currentPath, $localPrefix . '/') === 0) {
    $currentPath = substr($currentPath, strlen($localPrefix));
}
$currentPath = ltrim($currentPath, '/');
?>
<nav class="pos-navbar" aria-label="Main navigation">
    <div class="pos-nav-label">WORKSPACE</div>
    <ul>
        <li><a class="pos-nav-link<?= $currentPath === 'index.php' ? ' pos-nav-link-active' : '' ?>" href="<?= htmlspecialchars(appUrl('index.php'), ENT_QUOTES, 'UTF-8') ?>"><span class="pos-nav-icon" aria-hidden="true">▦</span>Dashboard</a></li>
        <?php if (hasRole(['Administrator', 'Manager', 'Inventory Staff'])): ?>
            <li><a class="pos-nav-link<?= $currentPath === 'views/products.php' ? ' pos-nav-link-active' : '' ?>" href="<?= htmlspecialchars(appUrl('views/products.php'), ENT_QUOTES, 'UTF-8') ?>"><span class="pos-nav-icon" aria-hidden="true">◈</span>Products</a></li>
        <?php endif; ?>
        <?php if (hasRole(['Administrator'])): ?>
            <li><a class="pos-nav-link<?= $currentPath === 'views/customers.php' ? ' pos-nav-link-active' : '' ?>" href="<?= htmlspecialchars(appUrl('views/customers.php'), ENT_QUOTES, 'UTF-8') ?>"><span class="pos-nav-icon" aria-hidden="true">♙</span>Customers</a></li>
        <?php endif; ?>
        <?php if (hasRole(['Administrator', 'Manager', 'Cashier'])): ?>
            <li><a class="pos-nav-link<?= $currentPath === 'views/sales.php' ? ' pos-nav-link-active' : '' ?>" href="<?= htmlspecialchars(appUrl('views/sales.php'), ENT_QUOTES, 'UTF-8') ?>"><span class="pos-nav-icon" aria-hidden="true">⇄</span>Sales</a></li>
        <?php endif; ?>
        <?php if (hasRole(['Administrator', 'Manager'])): ?>
            <li><a class="pos-nav-link<?= strpos($currentPath, 'reports/') === 0 ? ' pos-nav-link-active' : '' ?>" href="<?= htmlspecialchars(appUrl('reports/index.php'), ENT_QUOTES, 'UTF-8') ?>"><span class="pos-nav-icon" aria-hidden="true">▤</span>Reports</a></li>
        <?php endif; ?>
        <?php if (hasRole(['Administrator'])): ?>
            <li><a class="pos-nav-link<?= $currentPath === 'views/users.php' ? ' pos-nav-link-active' : '' ?>" href="<?= htmlspecialchars(appUrl('views/users.php'), ENT_QUOTES, 'UTF-8') ?>"><span class="pos-nav-icon" aria-hidden="true">♙</span>Users</a></li>
            <li><a class="pos-nav-link<?= $currentPath === 'views/courses.php' ? ' pos-nav-link-active' : '' ?>" href="<?= htmlspecialchars(appUrl('views/courses.php'), ENT_QUOTES, 'UTF-8') ?>"><span class="pos-nav-icon" aria-hidden="true">▧</span>Courses</a></li>
        <?php endif; ?>
    </ul>
    <div class="pos-nav-footer">RETAIL OPERATIONS</div>
</nav>