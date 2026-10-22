<?php
require_once __DIR__ . '/../config/database.php';

class Customer {
    private $conn;
    private $table = "customers";

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
        require_once __DIR__ . '/Course.php';
        Course::ensureSchema($this->conn);
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT c.*, co.course_name
            FROM {$this->table} c
            LEFT JOIN courses co ON c.course_id = co.course_id
            ORDER BY c.customer_id DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT c.*, co.course_name
            FROM {$this->table} c
            LEFT JOIN courses co ON c.course_id = co.course_id
            WHERE c.customer_id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function search($keyword) {
        $stmt = $this->conn->prepare("SELECT c.*, co.course_name
            FROM {$this->table} c
            LEFT JOIN courses co ON c.course_id = co.course_id
            WHERE c.customer_code LIKE :kw OR c.customer_name LIKE :kw OR co.course_name LIKE :kw
            ORDER BY c.customer_id DESC");
        $like = "%$keyword%";
        $stmt->bindParam(':kw', $like);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function courseExists($courseId) {
        $stmt = $this->conn->prepare("SELECT 1 FROM courses WHERE course_id = :id");
        $stmt->bindValue(':id', $courseId, PDO::PARAM_INT);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    public function create($data) {
        $courseId = isset($data['course_id']) && $data['course_id'] !== '' ? (int) $data['course_id'] : null;
        $stmt = $this->conn->prepare("INSERT INTO {$this->table}
            (customer_code, customer_name, contact_number, email, address, course_id)
            VALUES (:customer_code, :customer_name, :contact_number, :email, :address, :course_id)");
        $stmt->bindValue(':customer_code', $data['customer_code']);
        $stmt->bindValue(':customer_name', $data['customer_name']);
        $stmt->bindValue(':contact_number', $data['contact_number'] ?? '');
        $stmt->bindValue(':email', $data['email'] ?? '');
        $stmt->bindValue(':address', $data['address'] ?? '');
        $stmt->bindValue(':course_id', $courseId, $courseId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function update($id, $data) {
        $courseId = isset($data['course_id']) && $data['course_id'] !== '' ? (int) $data['course_id'] : null;
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET
            customer_code = :customer_code,
            customer_name = :customer_name,
            contact_number = :contact_number,
            email = :email,
            address = :address,
            course_id = :course_id
            WHERE customer_id = :id");
        $stmt->bindValue(':customer_code', $data['customer_code']);
        $stmt->bindValue(':customer_name', $data['customer_name']);
        $stmt->bindValue(':contact_number', $data['contact_number'] ?? '');
        $stmt->bindValue(':email', $data['email'] ?? '');
        $stmt->bindValue(':address', $data['address'] ?? '');
        $stmt->bindValue(':course_id', $courseId, $courseId === null ? PDO::PARAM_NULL : PDO::PARAM_INT);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deactivate($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE customer_id = :id");
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}