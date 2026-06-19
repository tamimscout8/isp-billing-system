<?php
/**
 * Payment Model
 */

class Payment {
    private $conn;
    private $table = 'payments';

    public $id;
    public $billing_id;
    public $customer_id;
    public $amount;
    public $payment_method;
    public $transaction_id;
    public $reference_number;
    public $notes;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all payments
     */
    public function getAll($limit = 10, $offset = 0) {
        $query = "SELECT p.*, c.name as customer_name, b.billing_date, b.due_date
                  FROM " . $this->table . " p
                  JOIN customers c ON p.customer_id = c.id
                  JOIN billings b ON p.billing_id = b.id
                  ORDER BY p.payment_date DESC LIMIT ?, ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Get payment by ID
     */
    public function getById($id) {
        $query = "SELECT p.*, c.name as customer_name, b.amount as billing_amount
                  FROM " . $this->table . " p
                  JOIN customers c ON p.customer_id = c.id
                  JOIN billings b ON p.billing_id = b.id
                  WHERE p.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get customer payments
     */
    public function getByCustomerId($customer_id, $limit = 10, $offset = 0) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE customer_id = ? AND status = 'success'
                  ORDER BY payment_date DESC LIMIT ?, ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iii", $customer_id, $offset, $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Create new payment
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (billing_id, customer_id, amount, payment_method, transaction_id, reference_number, notes, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iidssss",
            $this->billing_id,
            $this->customer_id,
            $this->amount,
            $this->payment_method,
            $this->transaction_id,
            $this->reference_number,
            $this->notes,
            $this->status
        );

        if ($stmt->execute()) {
            // Update billing status to paid
            $this->updateBillingStatus();
            return true;
        }
        return false;
    }

    /**
     * Update billing status after successful payment
     */
    private function updateBillingStatus() {
        $query = "UPDATE billings SET status = 'paid' WHERE id = ? AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $this->billing_id);
        return $stmt->execute();
    }

    /**
     * Get total payments
     */
    public function getTotalPayments() {
        $query = "SELECT SUM(amount) as total FROM " . $this->table . " WHERE status = 'success'";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    /**
     * Get payment by billing ID
     */
    public function getByBillingId($billing_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE billing_id = ? ORDER BY payment_date DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $billing_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Check if billing is paid
     */
    public function isBillingPaid($billing_id) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " 
                  WHERE billing_id = ? AND status = 'success'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $billing_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'] > 0;
    }
}
?>
