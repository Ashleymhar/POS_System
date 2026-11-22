<?php
session_start();
require_once __DIR__ . '/../config/app_url.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POS System Login</title>
    <link rel="stylesheet" href="<?= htmlspecialchars(appUrl('css/style.css?v=2'), ENT_QUOTES, 'UTF-8') ?>">
</head>
<body class="login-page">

<div class="login-card">
    <h2>POS SYSTEM LOGIN</h2>
    <hr>

    <?php if (isset($_GET['error'])): ?>
        <p style="color:red;"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>

    <form action="../controllers/AuthController.php?action=login" method="POST">
        <label>Username:</label><br>
        <input type="text" name="username" required style="width:100%; margin-bottom:10px;"><br>

        <label>Password:</label><br>
        <input type="password" name="password" required style="width:100%; margin-bottom:10px;"><br>

        <div style="text-align:center;">
            <button type="submit">LOGIN</button>
        </div>
    </form>
    <hr>
    <section aria-label="Demo login credentials" style="text-align:left; font-size:0.9rem;">
        <strong>Demo Login Credentials</strong>
        <p style="margin:0.5rem 0;">
            <strong>Admin</strong><br>
            Username: admin1<br>
            Password: admin123
        </p>
        <p style="margin:0.5rem 0 0;">
            <strong>Cashier</strong><br>
            Username: cashier1<br>
            Password: cashier123
        </p>
        <p style="margin:0.5rem 0 0;">
            <strong>Manager</strong><br>
            Username: manager1<br>
            Password: manager123
        </p>
        <p style="margin:0.5rem 0 0;">
            <strong>Inventory Staff</strong><br>
            Username: inventory1<br>
            Password: inventory123
        </p>
    </section>
</div>

</body>
</html>