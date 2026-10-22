<?php
require_once __DIR__ . '/../config/database.php';

class Course {
    private $conn;
    private $table = 'courses';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
        self::ensureSchema($this->conn);
    }

    public static function ensureSchema($conn) {
        $conn->exec("CREATE TABLE IF NOT EXISTS courses (
            course_id INT AUTO_INCREMENT PRIMARY KEY,
            course_name VARCHAR(120) NOT NULL UNIQUE,
            description TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");

        $stmt = $conn->query("SELECT COUNT(*) FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'customers'
            AND COLUMN_NAME = 'course_id'");
        if ((int) $stmt->fetchColumn() === 0) {
            $conn->exec("ALTER TABLE customers ADD COLUMN course_id INT NULL");
        }

        $stmt = $conn->query("SELECT COUNT(*) FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'customers'
            AND COLUMN_NAME = 'course_id'
            AND REFERENCED_TABLE_NAME = 'courses'
            AND REFERENCED_COLUMN_NAME = 'course_id'");
        if ((int) $stmt->fetchColumn() === 0) {
            $conn->exec("ALTER TABLE customers
                ADD CONSTRAINT fk_customers_course
                FOREIGN KEY (course_id) REFERENCES courses(course_id)
                ON DELETE SET NULL");
        }
    }

    public function getAll() {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} ORDER BY course_name");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT * FROM {$this->table} WHERE course_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data) {
        $stmt = $this->conn->prepare("INSERT INTO {$this->table} (course_name, description)
            VALUES (:course_name, :description)");
        $stmt->bindValue(':course_name', trim($data['course_name']));
        $stmt->bindValue(':description', trim($data['description'] ?? ''));
        return $stmt->execute();
    }

    public function update($id, $data) {
        $stmt = $this->conn->prepare("UPDATE {$this->table} SET
            course_name = :course_name,
            description = :description
            WHERE course_id = :id");
        $stmt->bindValue(':course_name', trim($data['course_name']));
        $stmt->bindValue(':description', trim($data['description'] ?? ''));
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM {$this->table} WHERE course_id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
