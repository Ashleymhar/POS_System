<nav class="pos-navbar">
    <ul>
        <li><a href="<?= htmlspecialchars(appUrl('index.php'), ENT_QUOTES, 'UTF-8') ?>">Dashboard</a></li>
        <?php if (hasRole(['Administrator', 'Manager', 'Inventory Staff'])): ?>
            <li><a href="<?= htmlspecialchars(appUrl('views/products.php'), ENT_QUOTES, 'UTF-8') ?>">Products</a></li>
        <?php endif; ?>
        <?php if (hasRole(['Administrator'])): ?>
            <li><a href="<?= htmlspecialchars(appUrl('views/customers.php'), ENT_QUOTES, 'UTF-8') ?>">Customers</a></li>
        <?php endif; ?>
        <?php if (hasRole(['Administrator', 'Manager', 'Cashier'])): ?>
            <li><a href="<?= htmlspecialchars(appUrl('views/sales.php'), ENT_QUOTES, 'UTF-8') ?>">Sales</a></li>
        <?php endif; ?>
        <?php if (hasRole(['Administrator', 'Manager'])): ?>
            <li><a href="<?= htmlspecialchars(appUrl('reports/index.php'), ENT_QUOTES, 'UTF-8') ?>">Reports</a></li>
        <?php endif; ?>
        <?php if (hasRole(['Administrator'])): ?>
            <li><a href="<?= htmlspecialchars(appUrl('views/users.php'), ENT_QUOTES, 'UTF-8') ?>">Users</a></li>
            <li><a href="<?= htmlspecialchars(appUrl('views/courses.php'), ENT_QUOTES, 'UTF-8') ?>">Courses</a></li>
        <?php endif; ?>
        <li style="margin-left:auto;">
            Logged in as: <strong><?= htmlspecialchars($_SESSION['full_name'] ?? '') ?></strong>
            | <a href="<?= htmlspecialchars(appUrl('controllers/AuthController.php?action=logout'), ENT_QUOTES, 'UTF-8') ?>">Logout</a>
        </li>
    </ul>
</nav>