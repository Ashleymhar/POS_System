<?php
session_start();
require_once '../models/Product.php';
require_once '../models/Customer.php';
include 'header.php';
require_once '../config/role_check.php';
requireRole(['Administrator', 'Manager', 'Cashier']);
include 'navbar.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$productModel = new Product();
$customerModel = new Customer();

$keyword = $_GET['keyword'] ?? '';
$searchResults = $keyword !== '' ? $productModel->search($keyword) : [];

$customers = $customerModel->getAll();

$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['price'] * $item['qty'];
}
?>

<main class="pos-content">
    <h2>Sales</h2>
    <p><a href="sales_history.php">View Sales History</a></p>
           <?php if (isset($_GET['error'])): ?>
           <pre style="color:red; border:1px solid red; padding:10px; white-space:pre-wrap;"><?= htmlspecialchars($_GET['error']) ?></pre>
       <?php endif; ?>

        <label>Customer:</label>
    <select name="customer_id" onchange="window.location.href='../controllers/CartController.php?action=set_customer&customer_id=' + this.value;">
        <option value="0" <?= ($_SESSION['selected_customer_id'] ?? 0) == 0 ? 'selected' : '' ?>>Walk-in Customer</option>
        <?php foreach ($customers as $c): ?>
            <option value="<?= $c['customer_id'] ?>" <?= ($_SESSION['selected_customer_id'] ?? 0) == $c['customer_id'] ? 'selected' : '' ?>>
                <?= htmlspecialchars($c['customer_name']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <hr>

    <form method="GET">
        <label>Product Search:</label>
        <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Product name or code">
        <button type="submit">Search</button>
    </form>

    <?php if ($keyword !== ''): ?>
    <table border="1" cellpadding="6">
        <tr><th>Product</th><th>Price</th><th>Stock</th><th>Action</th></tr>
        <?php foreach ($searchResults as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['product_name']) ?></td>
            <td><?= number_format($p['price'], 2) ?></td>
            <td><?= htmlspecialchars($p['quantity']) ?></td>
            <td><a href="../controllers/CartController.php?action=add&product_id=<?= $p['product_id'] ?>">Add</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($searchResults)): ?>
        <tr><td colspan="4">No products found.</td></tr>
        <?php endif; ?>
    </table>
    <?php endif; ?>

    <hr>

    <table border="1" cellpadding="6">
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
            <th>Action</th>
        </tr>
        <?php foreach ($_SESSION['cart'] as $pid => $item): ?>
        <?php $subtotal = $item['price'] * $item['qty']; ?>
        <tr>
            <td><?= htmlspecialchars($item['product_name']) ?></td>
            <td>
                <form action="../controllers/CartController.php?action=update_qty" method="POST" style="display:inline;">
                    <input type="hidden" name="product_id" value="<?= $pid ?>">
                    <input type="number" name="qty" value="<?= $item['qty'] ?>" min="0" style="width:50px;">
                    <button type="submit">Update</button>
                </form>
            </td>
            <td><?= number_format($item['price'], 2) ?></td>
            <td><?= number_format($subtotal, 2) ?></td>
            <td><a href="../controllers/CartController.php?action=remove&product_id=<?= $pid ?>">Remove</a></td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($_SESSION['cart'])): ?>
        <tr><td colspan="5">Cart is empty.</td></tr>
        <?php endif; ?>
        <tr>
            <td colspan="3" style="text-align:right;"><strong>TOTAL:</strong></td>
            <td colspan="2"><strong><?= number_format($total, 2) ?></strong></td>
        </tr>
    </table>

       <a href="../controllers/CartController.php?action=clear" onclick="return confirm('Clear the entire cart?')">
        <button type="button">Clear Cart</button>
    </a>

    <hr>

         <?php $_SESSION['checkout_token'] = bin2hex(random_bytes(16)); ?>
    <form action="../controllers/SaleController.php" method="POST">
        <input type="hidden" name="customer_id" value="<?= $_SESSION['selected_customer_id'] ?? 0 ?>">
        <input type="hidden" name="checkout_token" value="<?= $_SESSION['checkout_token'] ?>">
        <label>Discount:</label>
        <input type="number" step="0.01" name="discount" value="0" min="0"><br><br>
        <p>Subtotal: <?= number_format($total, 2) ?></p>
        <button type="submit" <?= empty($_SESSION['cart']) ? 'disabled' : '' ?>>Proceed to Payment</button>
    </form>
</main>

<?php include 'footer.php'; ?>