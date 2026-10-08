<?php
require_once __DIR__ . '/../config/database.php';

class Sale {
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->connect();
    }

    public function generateTransactionNo() {
        $year = date('Y');
        $stmt = $this->conn->prepare("SELECT COUNT(*) FROM sales WHERE transaction_no LIKE :pattern");
        $pattern = "POS-$year-%";
        $stmt->bindParam(':pattern', $pattern);
        $stmt->execute();
        $count = $stmt->fetchColumn() + 1;
        return "POS-$year-" . str_pad($count, 5, '0', STR_PAD_LEFT);
    }

       public function checkout($customerId, $userId, $discount, $cartItems) {
        if (empty($cartItems)) {
            return ['success' => false, 'error' => 'Cart is empty.'];
        }

        require_once __DIR__ . '/Product.php';
        $productModel = new Product();

        $subtotal = 0;
        foreach ($cartItems as $item) {
            $subtotal += $item['price'] * $item['qty'];
        }
        $total = $subtotal - $discount;
        $transactionNo = $this->generateTransactionNo();

        try {
            $this->conn->beginTransaction();

            // Validate stock BEFORE saving anything
                        // Validate customer (if one was selected, it must exist)
            if ($customerId) {
                $stmt = $this->conn->prepare("SELECT COUNT(*) FROM customers WHERE customer_id = :id");
                $stmt->bindParam(':id', $customerId);
                $stmt->execute();
                if ($stmt->fetchColumn() == 0) {
                    throw new Exception("Invalid customer selected.");
                }
            }

            // Validate stock BEFORE saving anything
            foreach ($cartItems as $productId => $item) {
                $product = $productModel->getById($productId);
                if (!$product) {
                    throw new Exception("Product not found.");
                }
                if ($product['quantity'] < $item['qty']) {
                    throw new Exception(
                        "Insufficient stock.\n\nProduct: {$product['product_name']}\n" .
                        "Available quantity: {$product['quantity']}\n" .
                        "Requested quantity: {$item['qty']}"
                    );
                }
            }
            // Save Sale
            $stmt = $this->conn->prepare("INSERT INTO sales (transaction_no, customer_id, user_id, subtotal, discount, tax, total_amount, status)
                VALUES (:transaction_no, :customer_id, :user_id, :subtotal, :discount, 0, :total_amount, 'Completed')");
            $stmt->bindParam(':transaction_no', $transactionNo);
            $stmt->bindParam(':customer_id', $customerId);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':subtotal', $subtotal);
            $stmt->bindParam(':discount', $discount);
            $stmt->bindParam(':total_amount', $total);
            $stmt->execute();

            $saleId = $this->conn->lastInsertId();

            // Save Sale Items + Deduct Product Quantity
            $itemStmt = $this->conn->prepare("INSERT INTO sale_items (sale_id, product_id, quantity, unit_price, subtotal)
                VALUES (:sale_id, :product_id, :quantity, :unit_price, :item_subtotal)");

            foreach ($cartItems as $productId => $item) {
                $itemSubtotal = $item['price'] * $item['qty'];
                $itemStmt->bindParam(':sale_id', $saleId);
                $itemStmt->bindParam(':product_id', $productId);
                $itemStmt->bindParam(':quantity', $item['qty']);
                $itemStmt->bindParam(':unit_price', $item['price']);
                $itemStmt->bindParam(':item_subtotal', $itemSubtotal);
                $itemStmt->execute();

                $deducted = $productModel->deductStock($productId, $item['qty'], $this->conn);
                if (!$deducted) {
                    throw new Exception("Failed to deduct stock for product ID $productId.");
                }
            }

            // Commit Transaction
            $this->conn->commit();
            return ['success' => true, 'sale_id' => $saleId];

        } catch (Exception $e) {
            // Rollback Transaction
            $this->conn->rollBack();
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getById($id) {
        $stmt = $this->conn->prepare("SELECT s.*, c.customer_name, u.full_name AS cashier_name FROM sales s
            LEFT JOIN customers c ON s.customer_id = c.customer_id
            LEFT JOIN users u ON s.user_id = u.user_id
            WHERE s.sale_id = :id");
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByTransactionNo($transactionNo) {
        $stmt = $this->conn->prepare("SELECT s.*, c.customer_name, u.full_name AS cashier_name FROM sales s
            LEFT JOIN customers c ON s.customer_id = c.customer_id
            LEFT JOIN users u ON s.user_id = u.user_id
            WHERE s.transaction_no = :transaction_no");
        $stmt->bindParam(':transaction_no', $transactionNo);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getItems($saleId) {
        $stmt = $this->conn->prepare("SELECT si.*, p.product_name FROM sale_items si
            JOIN products p ON si.product_id = p.product_id
            WHERE si.sale_id = :sale_id");
        $stmt->bindParam(':sale_id', $saleId);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
        public function getAll($keyword = '', $dateFrom = '', $dateTo = '') {
        $sql = "SELECT s.*, c.customer_name, u.full_name AS cashier_name FROM sales s
                LEFT JOIN customers c ON s.customer_id = c.customer_id
                LEFT JOIN users u ON s.user_id = u.user_id
                WHERE 1=1";
        $params = [];

        if ($keyword !== '') {
            $sql .= " AND (s.transaction_no LIKE :keyword OR c.customer_name LIKE :keyword)";
            $params[':keyword'] = "%$keyword%";
        }
        if ($dateFrom !== '') {
            $sql .= " AND DATE(s.sale_date) >= :date_from";
            $params[':date_from'] = $dateFrom;
        }
        if ($dateTo !== '') {
            $sql .= " AND DATE(s.sale_date) <= :date_to";
            $params[':date_to'] = $dateTo;
        }

        $sql .= " ORDER BY s.sale_date DESC";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

        public function dailyReport($date) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total_transactions, COALESCE(SUM(total_amount),0) AS total_sales
            FROM sales WHERE DATE(sale_date) = :date AND status = 'Completed'");
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function monthlyReport($year, $month) {
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total_transactions, COALESCE(SUM(total_amount),0) AS total_sales
            FROM sales WHERE YEAR(sale_date) = :year AND MONTH(sale_date) = :month AND status = 'Completed'");
        $stmt->bindParam(':year', $year);
        $stmt->bindParam(':month', $month);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function salesByProduct() {
        $stmt = $this->conn->prepare("SELECT p.product_name, SUM(si.quantity) AS total_qty, SUM(si.subtotal) AS total_sales
            FROM sale_items si JOIN products p ON si.product_id = p.product_id
            JOIN sales s ON si.sale_id = s.sale_id WHERE s.status = 'Completed'
            GROUP BY p.product_id ORDER BY total_sales DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salesByCategory() {
        $stmt = $this->conn->prepare("SELECT p.category, SUM(si.subtotal) AS total_sales
            FROM sale_items si JOIN products p ON si.product_id = p.product_id
            JOIN sales s ON si.sale_id = s.sale_id WHERE s.status = 'Completed'
            GROUP BY p.category ORDER BY total_sales DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salesByCashier() {
        $stmt = $this->conn->prepare("SELECT u.full_name AS cashier_name, COUNT(*) AS total_transactions, SUM(s.total_amount) AS total_sales
            FROM sales s JOIN users u ON s.user_id = u.user_id WHERE s.status = 'Completed'
            GROUP BY u.user_id ORDER BY total_sales DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function salesByCustomer() {
        $stmt = $this->conn->prepare("SELECT COALESCE(c.customer_name, 'Walk-in') AS customer_name, COUNT(*) AS total_transactions, SUM(s.total_amount) AS total_sales
            FROM sales s LEFT JOIN customers c ON s.customer_id = c.customer_id WHERE s.status = 'Completed'
            GROUP BY s.customer_id ORDER BY total_sales DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function topSellingProducts($limit = 5) {
        $stmt = $this->conn->prepare("SELECT p.product_name, SUM(si.quantity) AS total_qty
            FROM sale_items si JOIN products p ON si.product_id = p.product_id
            JOIN sales s ON si.sale_id = s.sale_id WHERE s.status = 'Completed'
            GROUP BY p.product_id ORDER BY total_qty DESC LIMIT :limit");
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function paymentMethodSummary() {
        $stmt = $this->conn->prepare("SELECT payment_method, COUNT(*) AS total_payments, SUM(amount) AS total_amount
            FROM payments GROUP BY payment_method ORDER BY total_amount DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
        public function productsSoldToday() {
        $stmt = $this->conn->prepare("SELECT COALESCE(SUM(si.quantity),0) AS total_qty
            FROM sale_items si JOIN sales s ON si.sale_id = s.sale_id
            WHERE DATE(s.sale_date) = CURDATE() AND s.status = 'Completed'");
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    public function salesLastNDays($days = 7) {
        $stmt = $this->conn->prepare("SELECT DATE(sale_date) AS sale_day, SUM(total_amount) AS total_sales
            FROM sales
            WHERE sale_date >= DATE_SUB(CURDATE(), INTERVAL :days DAY) AND status = 'Completed'
            GROUP BY DATE(sale_date) ORDER BY sale_day ASC");
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}