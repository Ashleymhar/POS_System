<?php
require_once __DIR__ . '/database.php';

$database = new Database();
$conn = $database->connect();

$accounts = [
    ['manager1', 'manager123', 'Store Manager', 'Manager'],
    ['cashier1', 'cashier123', 'Cashier', 'Cashier'],
    ['inventory1', 'inventory123', 'Inventory Staff', 'Inventory Staff']
];

$check = $conn->prepare("SELECT COUNT(*) FROM users WHERE username = :username");
$insert = $conn->prepare("INSERT INTO users (username, password, full_name, role) VALUES (:username, :password, :full_name, :role)");
$update = $conn->prepare("UPDATE users SET password = :password, full_name = :full_name, role = :role WHERE username = :username");

foreach ($accounts as $account) {
    [$username, $plainPassword, $fullName, $role] = $account;
    $password = password_hash($plainPassword, PASSWORD_DEFAULT);

    $check->bindValue(':username', $username);
    $check->execute();

    $statement = $check->fetchColumn() > 0 ? $update : $insert;
    $statement->bindValue(':username', $username);
    $statement->bindValue(':password', $password);
    $statement->bindValue(':full_name', $fullName);
    $statement->bindValue(':role', $role);
    $statement->execute();

    echo htmlspecialchars($username) . ' (' . htmlspecialchars($role) . ') synchronized.<br>';
}