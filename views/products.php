<?php
require_once '../models/Product.php';
require_once '../config/role_check.php';
include 'header.php';
requireRole(['Administrator', 'Manager', 'Inventory Staff']);
include 'navbar.php';
 

$productModel = new Product();

// Handle search
$keyword = $_GET['keyword'] ?? '';
$products = $keyword !== '' ? $productModel->search($keyword) : $productModel->getAll();

// Handle edit mode (load product into form)
$editProduct = null;
if (isset($_GET['edit_id'])) {
    $editProduct = $productModel->getById($_GET['edit_id']);
}

// Handle view details mode
$viewProduct = null;
if (isset($_GET['view_id'])) {
    $viewProduct = $productModel->getById($_GET['view_id']);
}
?>

<main class="pos-content">
    <h2>Product Management</h2>

        <?php if (isset($_GET['errors'])): ?>
        <div style="color:red; border:1px solid red; padding:10px;">
            <strong>ERROR:</strong>
            <ul>
                <?php foreach (explode('|', $_GET['errors']) as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="../controllers/ProductController.php?action=<?= $editProduct ? 'edit' : 'add' ?>" method="POST">
        <?php if ($editProduct): ?>
            <input type="hidden" name="product_id" value="<?= htmlspecialchars($editProduct['product_id']) ?>">
        <?php endif; ?>
        <label>Product Code:</label>
        <input type="text" name="product_code" value="<?= htmlspecialchars($editProduct['product_code'] ?? '') ?>" required><br>

        <label>Product Name:</label>
        <input type="text" name="product_name" value="<?= htmlspecialchars($editProduct['product_name'] ?? '') ?>" required><br>

        <label>Category:</label>
        <input type="text" name="category" value="<?= htmlspecialchars($editProduct['category'] ?? '') ?>"><br>

        <label>Price:</label>
        <input type="number" step="0.01" name="price" value="<?= htmlspecialchars($editProduct['price'] ?? '') ?>" required><br>

        <label>Cost:</label>
        <input type="number" step="0.01" name="cost" value="<?= htmlspecialchars($editProduct['cost'] ?? '') ?>" required><br>

        <label>Quantity:</label>
        <input type="number" name="quantity" value="<?= htmlspecialchars($editProduct['quantity'] ?? '') ?>" required><br>

        <label>Reorder Level:</label>
        <input type="number" name="reorder_level" value="<?= htmlspecialchars($editProduct['reorder_level'] ?? '') ?>" required><br>

        <button type="submit"><?= $editProduct ? 'Update Product' : 'Save Product' ?></button>
        <a href="products.php"><button type="button">Clear</button></a>
    </form>

    <hr>

    <form method="GET">
        <label>Search:</label>
        <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Product code or name">
        <button type="submit">Search</button>
    </form>

    <table border="1" cellpadding="6">
        <tr>
            <th>Code</th>
            <th>Product</th>
            <th>Price</th>
            <th>Stock</th>
            <th>Action</th>
        </tr>
        <?php foreach ($products as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['product_code']) ?></td>
            <td><?= htmlspecialchars($p['product_name']) ?></td>
            <td><?= number_format($p['price'], 2) ?></td>
            <td><?= htmlspecialchars($p['quantity']) ?></td>
            <td>
                <a href="?edit_id=<?= $p['product_id'] ?>">Edit</a> |
                <a href="?view_id=<?= $p['product_id'] ?>">View</a> |
                <a href="../controllers/ProductController.php?action=deactivate&id=<?= $p['product_id'] ?>" onclick="return confirm('Deactivate this product?')">Deactivate</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>

    <?php if ($viewProduct): ?>
    <hr>
    <h3>Product Details</h3>
    <p><strong>Code:</strong> <?= htmlspecialchars($viewProduct['product_code']) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($viewProduct['product_name']) ?></p>
    <p><strong>Category:</strong> <?= htmlspecialchars($viewProduct['category']) ?></p>
    <p><strong>Price:</strong> <?= number_format($viewProduct['price'], 2) ?></p>
    <p><strong>Cost:</strong> <?= number_format($viewProduct['cost'], 2) ?></p>
    <p><strong>Quantity:</strong> <?= htmlspecialchars($viewProduct['quantity']) ?></p>
    <p><strong>Reorder Level:</strong> <?= htmlspecialchars($viewProduct['reorder_level']) ?></p>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>