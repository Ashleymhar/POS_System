<nav class="pos-navbar">
    <ul>
        <li><a href="/POS_System/index.php">Dashboard</a></li>
        <li><a href="/POS_System/views/products.php">Products</a></li>
        <li><a href="/POS_System/views/customers.php">Customers</a></li>
        <li><a href="/POS_System/views/sales.php">Sales</a></li>
        <li><a href="/POS_System/reports/index.php">Reports</a></li>
        <li><a href="/POS_System/views/users.php">Users</a></li>
        <li style="margin-left:auto;">
            Logged in as: <strong><?= htmlspecialchars($_SESSION['full_name'] ?? '') ?></strong>
            | <a href="/POS_System/controllers/AuthController.php?action=logout">Logout</a>
        </li>
    </ul>
</nav>