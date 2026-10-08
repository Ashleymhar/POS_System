<?php
require_once __DIR__ . '/../config/database.php';

class Product {
    private $conn;
    private $table = "products";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE status = 'Active' ORDER BY product_id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE product_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function search($keyword) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE status = 'Active' AND (product_code LIKE :kw OR product_name LIKE :kw) ORDER BY product_id DESC");
        $like = "%$keyword%";
        $stmt->bindParam(':kw', $like);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (product_code, product_name, category, price, cost, quantity, reorder_level, status)
            VALUES (:product_code, :product_name, :category, :price, :cost, :quantity, :reorder_level, 'Active')");
        $stmt->bindParam(':product_code', $data['product_code']);
        $stmt->bindParam(':product_name', $data['product_name']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':cost', $data['cost']);
        $stmt->bindParam(':quantity', $data['quantity']);
        $stmt->bindParam(':reorder_level', $data['reorder_level']);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET
            product_code = :product_code,
            product_name = :product_name,
            category = :category,
            price = :price,
            cost = :cost,
            quantity = :quantity,
            reorder_level = :reorder_level
            WHERE product_id = :id");
        $stmt->bindParam(':product_code', $data['product_code']);
        $stmt->bindParam(':product_name', $data['product_name']);
        $stmt->bindParam(':category', $data['category']);
        $stmt->bindParam(':price', $data['price']);
        $stmt->bindParam(':cost', $data['cost']);
        $stmt->bindParam(':quantity', $data['quantity']);
        $stmt->bindParam(':reorder_level', $data['reorder_level']);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function deactivate($id) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET status = 'Inactive' WHERE product_id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    public function searchByName($name) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE status = 'Active' AND product_name LIKE :name ORDER BY product_name");
        $like = "%$name%";
        $stmt->bindParam(':name', $like);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function searchByCode($code) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE status = 'Active' AND product_code LIKE :code ORDER BY product_code");
        $like = "%$code%";
        $stmt->bindParam(':code', $like);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function filterByCategory($category) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE status = 'Active' AND category = :category ORDER BY product_name");
        $stmt->bindParam(':category', $category);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCategories() {
        $stmt = $this->conn->prepare("SELECT DISTINCT category FROM {$this->table} WHERE status = 'Active' AND category IS NOT NULL AND category != ''");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    public function getLowStock() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE status = 'Active' AND quantity <= reorder_level ORDER BY quantity ASC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }
        public function codeExists($code, $excludeId = null) {
        if ($excludeId) {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM {$this->table} WHERE product_code = :code AND product_id != :id");
            $stmt->bindParam(':id', $excludeId);
        } else {
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM {$this->table} WHERE product_code = :code");
        }
        $stmt->bindParam(':code', $code);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }
        public function countLowStock() {
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM {$this->table} WHERE status = 'Active' AND quantity <= reorder_level");
        $stmt->execute();
        return $stmt->fetchColumn();
    }
        public function deductStock($productId, $qty, $conn = null) {
        $connection = $conn ?? $this->conn;
        $stmt = $connection->prepare("UPDATE {$this->table} SET quantity = quantity - :qty WHERE product_id = :product_id AND quantity >= :qty");
        $stmt->bindParam(':qty', $qty);
        $stmt->bindParam(':product_id', $productId);
        return $stmt->execute() && $stmt->rowCount() > 0;
    }
}