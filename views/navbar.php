<nav class="pos-navbar">
    <ul>
        <li><a href="/index.php">Dashboard</a></li>
        <li><a href="/views/products.php">Products</a></li>
        <li><a href="/views/customers.php">Customers</a></li>
        <li><a href="/views/sales.php">Sales</a></li>
        <li><a href="/reports/index.php">Reports</a></li>
        <li><a href="/views/users.php">Users</a></li>
        <li style="margin-left:auto;">
            Logged in as: <strong><?= htmlspecialchars($_SESSION['full_name'] ?? '') ?></strong>
            | <a href="/controllers/AuthController.php?action=logout">Logout</a>
        </li>
    </ul>
</nav>