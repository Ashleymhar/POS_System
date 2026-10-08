<?php
require_once '../config/database.php';
include 'header.php';
require_once '../config/role_check.php';
requireRole(['Administrator']);
include 'navbar.php';
?>
<main class="pos-content">
    <h2>Users</h2>
    <p>Users module — to be built in the next laboratory activity.</p>
</main>
<?php include 'footer.php'; ?>