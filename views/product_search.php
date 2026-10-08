<?php
require_once '../models/Product.php';
include 'header.php';
include 'navbar.php';

$productModel = new Product();

$keyword = $_GET['keyword'] ?? '';
$category = $_GET['category'] ?? '';

if ($keyword !== '') {
    $results = $productModel->searchByName($keyword);
    if (empty($results)) {
        $results = $productModel->searchByCode($keyword);
    }
} elseif ($category !== '') {
    $results = $productModel->filterByCategory($category);
} else {
    $results = $productModel->getAll();
}

$categories = $productModel->getCategories();
$lowStock = $productModel->getLowStock();
?>

<main class="pos-content">
    <h2>Product Search</h2>

    <form method="GET">
        <label>Search Product:</label>
        <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Product name or code">

        <label>Category:</label>
        <select name="category">
            <option value="">-- All Categories --</option>
            <?php foreach ($categories as $cat): ?>
                <option value="<?= htmlspecialchars($cat) ?>" <?= $category === $cat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($cat) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <button type="submit">Search</button>
        <a href="product_search.php"><button type="button">Clear</button></a>
    </form>

    <table border="1" cellpadding="6">
        <tr>
            <th>Product Code</th>
            <th>Product</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Action</th>
        </tr>
        <?php foreach ($results as $p): ?>
        <?php $unavailable = $p['quantity'] <= 0; ?>
        <tr <?= $unavailable ? 'style="color:#999;"' : '' ?>>
            <td><?= htmlspecialchars($p['product_code']) ?></td>
            <td><?= htmlspecialchars($p['product_name']) ?></td>
            <td><?= number_format($p['price'], 2) ?></td>
            <td><?= htmlspecialchars($p['quantity']) ?></td>
            <td>
                <?php if ($unavailable): ?>
                    Out of Stock
                <?php else: ?>
                    <button type="button">Select</button>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($results)): ?>
        <tr><td colspan="5">No products found.</td></tr>
        <?php endif; ?>
    </table>

    <hr>

    <h3>Low Stock Alert</h3>
    <?php if (empty($lowStock)): ?>
        <p>No products are currently low on stock.</p>
    <?php else: ?>
        <table border="1" cellpadding="6">
            <tr>
                <th>Product Code</th>
                <th>Product</th>
                <th>Stock</th>
                <th>Reorder Level</th>
            </tr>
            <?php foreach ($lowStock as $p): ?>
            <tr style="color:#b00000;">
                <td><?= htmlspecialchars($p['product_code']) ?></td>
                <td><?= htmlspecialchars($p['product_name']) ?></td>
                <td><?= htmlspecialchars($p['quantity']) ?></td>
                <td><?= htmlspecialchars($p['reorder_level']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>