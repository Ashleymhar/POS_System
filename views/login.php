<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>POS System Login</title>
    <link rel="stylesheet" href="/POS_System/css/style.css">
</head>
<body>

<div style="max-width:350px; margin:100px auto; border:2px solid #1F3864; padding:2rem; text-align:center; font-family: monospace;">
    <h2>POS SYSTEM LOGIN</h2>
    <hr>

    <?php if (isset($_GET['error'])): ?>
        <p style="color:red;"><?= htmlspecialchars($_GET['error']) ?></p>
    <?php endif; ?>

    <form action="../controllers/AuthController.php?action=login" method="POST" style="text-align:left;">
        <label>Username:</label><br>
        <input type="text" name="username" required style="width:100%; margin-bottom:10px;"><br>

        <label>Password:</label><br>
        <input type="password" name="password" required style="width:100%; margin-bottom:10px;"><br>

        <div style="text-align:center;">
            <button type="submit">LOGIN</button>
        </div>
    </form>
    <hr>
</div>

</body>
</html>