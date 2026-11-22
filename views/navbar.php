<nav class="pos-navbar">
    <ul>
        <li><a href="/POS_System/index.php">Dashboard</a></li>
        <?php if (hasRole(['Administrator', 'Manager', 'Inventory Staff'])): ?>
            <li><a href="/POS_System/views/products.php">Products</a></li>
        <?php endif; ?>
        <?php if (hasRole(['Administrator'])): ?>
            <li><a href="/POS_System/views/customers.php">Customers</a></li>
        <?php endif; ?>
        <?php if (hasRole(['Administrator', 'Manager', 'Cashier'])): ?>
            <li><a href="/POS_System/views/sales.php">Sales</a></li>
        <?php endif; ?>
        <?php if (hasRole(['Administrator', 'Manager'])): ?>
            <li><a href="/POS_System/reports/index.php">Reports</a></li>
        <?php endif; ?>
        <?php if (hasRole(['Administrator'])): ?>
            <li><a href="/POS_System/views/users.php">Users</a></li>
            <li><a href="/POS_System/views/courses.php">Courses</a></li>
        <?php endif; ?>
        <li style="margin-left:auto;">
            Logged in as: <strong><?= htmlspecialchars($_SESSION['full_name'] ?? '') ?></strong>
            | <a href="/POS_System/controllers/AuthController.php?action=logout">Logout</a>
        </li>
    </ul>
</nav>