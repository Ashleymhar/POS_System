<?php
require_once __DIR__ . '/../config/database.php';

class Payment {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function create($saleId, $paymentMethod, $amount) {
        $stmt = $this->conn->prepare("INSERT INTO payments (sale_id, payment_method, amount) VALUES (:sale_id, :payment_method, :amount)");
        $stmt->bindParam(':sale_id', $saleId);
        $stmt->bindParam(':payment_method', $paymentMethod);
        $stmt->bindParam(':amount', $amount);
        return $stmt->execute();
    }

    public function getBySaleId($saleId) {
        $stmt = $this->conn->prepare("SELECT * FROM payments WHERE sale_id = :sale_id");
        $stmt->bindParam(':sale_id', $saleId);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}