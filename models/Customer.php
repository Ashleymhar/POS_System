<?php
require_once __DIR__ . '/../config/database.php';

class Customer {
    private $conn;
    private $table = "customers";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY customer_id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE customer_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function search($keyword) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE customer_code LIKE :kw OR customer_name LIKE :kw ORDER BY customer_id DESC");
        $like = "%$keyword%";
        $stmt->bindParam(':kw', $like);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (customer_code, customer_name, contact_number, email, address)
            VALUES (:customer_code, :customer_name, :contact_number, :email, :address)");
        $stmt->bindParam(':customer_code', $data['customer_code']);
        $stmt->bindParam(':customer_name', $data['customer_name']);
        $stmt->bindParam(':contact_number', $data['contact_number']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':address', $data['address']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET
            customer_code = :customer_code,
            customer_name = :customer_name,
            contact_number = :contact_number,
            email = :email,
            address = :address
            WHERE customer_id = :id");
        $stmt->bindParam(':customer_code', $data['customer_code']);
        $stmt->bindParam(':customer_name', $data['customer_name']);
        $stmt->bindParam(':contact_number', $data['contact_number']);
        $stmt->bindParam(':email', $data['email']);
        $stmt->bindParam(':address', $data['address']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function deactivate($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE customer_id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}