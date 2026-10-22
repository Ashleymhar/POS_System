<?php
require_once '../config/role_check.php';
requireRole(['Administrator', 'Manager', 'Cashier']);
require_once '../models/Customer.php';
require_once '../models/Course.php';
include 'header.php';
include 'navbar.php';

$customerModel = new Customer();
$courseModel = new Course();
$courses = $courseModel->getAll();

$keyword = $_GET['keyword'] ?? '';
$customers = $keyword !== '' ? $customerModel->search($keyword) : $customerModel->getAll();

$editCustomer = null;
if (isset($_GET['edit_id'])) {
    $editCustomer = $customerModel->getById($_GET['edit_id']);
}

$viewCustomer = null;
if (isset($_GET['view_id'])) {
    $viewCustomer = $customerModel->getById($_GET['view_id']);
}
?>

<main class="pos-content">
    <h2>Customer Management</h2>

    <?php if (($_GET['error'] ?? '') === 'invalid_course'): ?>
        <p role="alert">Select a valid course before saving the customer.</p>
    <?php endif; ?>

    <form action="../controllers/CustomerController.php?action=<?= $editCustomer ? 'edit' : 'add' ?>" method="POST">
        <?php if ($editCustomer): ?>
            <input type="hidden" name="customer_id" value="<?= htmlspecialchars($editCustomer['customer_id']) ?>">
        <?php endif; ?>
        <label>Customer Code:</label>
        <input type="text" name="customer_code" value="<?= htmlspecialchars($editCustomer['customer_code'] ?? '') ?>" required><br>

        <label>Customer Name:</label>
        <input type="text" name="customer_name" value="<?= htmlspecialchars($editCustomer['customer_name'] ?? '') ?>" required><br>

        <label>Contact Number:</label>
        <input type="text" name="contact_number" value="<?= htmlspecialchars($editCustomer['contact_number'] ?? '') ?>"><br>

        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($editCustomer['email'] ?? '') ?>"><br>

        <label>Address:</label>
        <input type="text" name="address" value="<?= htmlspecialchars($editCustomer['address'] ?? '') ?>"><br>

        <label for="course_id">Course:</label>
        <select id="course_id" name="course_id" required>
            <option value="" disabled <?= empty($editCustomer['course_id']) ? 'selected' : '' ?>>Select a course</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?= (int) $course['course_id'] ?>"
                    <?= (int) ($editCustomer['course_id'] ?? 0) === (int) $course['course_id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($course['course_name']) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php if (empty($courses)): ?>
            <p>No courses are available. <a href="courses.php">Add a course</a> before registering a customer.</p>
        <?php endif; ?>
        <br>

        <button type="submit"><?= $editCustomer ? 'Update Customer' : 'Register Customer' ?></button>
        <a href="customers.php"><button type="button">Clear</button></a>
    </form>

    <hr>

    <form method="GET">
        <label>Search:</label>
        <input type="text" name="keyword" value="<?= htmlspecialchars($keyword) ?>" placeholder="Customer code, name, or course">
        <button type="submit">Search</button>
    </form>

    <table border="1" cellpadding="6">
        <tr>
            <th>Code</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Email</th>
            <th>Course</th>
            <th>Action</th>
        </tr>
        <?php foreach ($customers as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['customer_code']) ?></td>
            <td><?= htmlspecialchars($c['customer_name']) ?></td>
            <td><?= htmlspecialchars($c['contact_number']) ?></td>
            <td><?= htmlspecialchars($c['email']) ?></td>
            <td><?= htmlspecialchars($c['course_name'] ?? '') ?></td>
            <td>
                <a href="?edit_id=<?= $c['customer_id'] ?>">Edit</a> |
                <a href="?view_id=<?= $c['customer_id'] ?>">View</a> |
                <a href="../controllers/CustomerController.php?action=deactivate&id=<?= $c['customer_id'] ?>" onclick="return confirm('Deactivate this customer?')">Deactivate</a>
            </td>
        </tr>
        <?php endforeach; ?>
        <?php if (empty($customers)): ?>
        <tr><td colspan="6">No customers found.</td></tr>
        <?php endif; ?>
    </table>

    <?php if ($viewCustomer): ?>
    <hr>
    <h3>Customer Details</h3>
    <p><strong>Code:</strong> <?= htmlspecialchars($viewCustomer['customer_code']) ?></p>
    <p><strong>Name:</strong> <?= htmlspecialchars($viewCustomer['customer_name']) ?></p>
    <p><strong>Contact Number:</strong> <?= htmlspecialchars($viewCustomer['contact_number']) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($viewCustomer['email']) ?></p>
    <p><strong>Address:</strong> <?= htmlspecialchars($viewCustomer['address']) ?></p>
    <p><strong>Course:</strong> <?= htmlspecialchars($viewCustomer['course_name'] ?? '') ?></p>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>