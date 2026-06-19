<?php
/**
 * Billing Model
 */

class Billing {
    private $conn;
    private $table = 'billings';

    public $id;
    public $customer_id;
    public $customer_plan_id;
    public $billing_date;
    public $due_date;
    public $amount;
    public $tax;
    public $total_amount;
    public $status;
    public $notes;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all billings
     */
    public function getAll($limit = 10, $offset = 0) {
        $query = "SELECT b.*, c.name as customer_name, c.email as customer_email 
                  FROM " . $this->table . " b
                  JOIN customers c ON b.customer_id = c.id
                  ORDER BY b.created_at DESC LIMIT ?, ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Get billing by ID
     */
    public function getById($id) {
        $query = "SELECT b.*, c.name as customer_name, c.email as customer_email, p.name as plan_name
                  FROM " . $this->table . " b
                  JOIN customers c ON b.customer_id = c.id
                  JOIN customer_plans cp ON b.customer_plan_id = cp.id
                  JOIN plans p ON cp.plan_id = p.id
                  WHERE b.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get customer billings
     */
    public function getByCustomerId($customer_id, $limit = 10, $offset = 0) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE customer_id = ? 
                  ORDER BY created_at DESC LIMIT ?, ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $customer_id, $offset, $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Create new billing
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (customer_id, customer_plan_id, billing_date, due_date, amount, tax, total_amount, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iissdds",
            $this->customer_id,
            $this->customer_plan_id,
            $this->billing_date,
            $this->due_date,
            $this->amount,
            $this->tax,
            $this->total_amount,
            $this->status
        );

        return $stmt->execute();
    }

    /**
     * Update billing status
     */
    public function updateStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    /**
     * Get pending billings
     */
    public function getPending() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE status = 'pending'";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    /**
     * Get overdue billings
     */
    public function getOverdue() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE status IN ('pending', 'overdue') AND due_date < CURDATE()";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    /**
     * Get total revenue
     */
    public function getTotalRevenue() {
        $query = "SELECT SUM(total_amount) as total FROM " . $this->table . " WHERE status = 'paid'";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    /**
     * Generate monthly bills for all active customers
     */
    public function generateMonthlyBills() {
        $query = "INSERT INTO " . $this->table . " 
                  (customer_id, customer_plan_id, billing_date, due_date, amount, tax, total_amount, status)
                  SELECT cp.customer_id, cp.id, CURDATE(), DATE_ADD(CURDATE(), INTERVAL 15 DAY), 
                         p.monthly_price, (p.monthly_price * 0.05), (p.monthly_price * 1.05), 'pending'
                  FROM customer_plans cp
                  JOIN plans p ON cp.plan_id = p.id
                  WHERE cp.status = 'active' 
                  AND NOT EXISTS (
                      SELECT 1 FROM " . $this->table . " b 
                      WHERE b.customer_plan_id = cp.id 
                      AND MONTH(b.billing_date) = MONTH(CURDATE()) 
                      AND YEAR(b.billing_date) = YEAR(CURDATE())
                  )";
        
        return $this->conn->query($query);
    }
}
?>
