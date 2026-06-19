<?php
/**
 * Dashboard Model
 */

class Dashboard {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get dashboard statistics
     */
    public function getStats() {
        return [
            'total_customers' => $this->getTotalCustomers(),
            'active_customers' => $this->getActiveCustomers(),
            'total_plans' => $this->getTotalPlans(),
            'pending_bills' => $this->getPendingBills(),
            'paid_bills' => $this->getPaidBills(),
            'overdue_bills' => $this->getOverdueBills(),
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'total_revenue' => $this->getTotalRevenue(),
        ];
    }

    /**
     * Get total customers
     */
    private function getTotalCustomers() {
        $query = "SELECT COUNT(*) as count FROM customers";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    /**
     * Get active customers
     */
    private function getActiveCustomers() {
        $query = "SELECT COUNT(*) as count FROM customers WHERE status = 'active'";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    /**
     * Get total plans
     */
    private function getTotalPlans() {
        $query = "SELECT COUNT(*) as count FROM plans WHERE status = 'active'";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    /**
     * Get pending bills
     */
    private function getPendingBills() {
        $query = "SELECT COUNT(*) as count FROM billings WHERE status = 'pending'";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    /**
     * Get paid bills
     */
    private function getPaidBills() {
        $query = "SELECT COUNT(*) as count FROM billings WHERE status = 'paid'";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    /**
     * Get overdue bills
     */
    private function getOverdueBills() {
        $query = "SELECT COUNT(*) as count FROM billings 
                  WHERE status IN ('pending', 'overdue') AND due_date < CURDATE()";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    /**
     * Get monthly revenue
     */
    private function getMonthlyRevenue() {
        $query = "SELECT SUM(amount) as total FROM payments 
                  WHERE status = 'success' 
                  AND MONTH(payment_date) = MONTH(CURDATE()) 
                  AND YEAR(payment_date) = YEAR(CURDATE())";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    /**
     * Get total revenue
     */
    private function getTotalRevenue() {
        $query = "SELECT SUM(amount) as total FROM payments WHERE status = 'success'";
        $result = $this->conn->query($query);
        $row = $result->fetch_assoc();
        return $row['total'] ?? 0;
    }

    /**
     * Get recent billings
     */
    public function getRecentBillings($limit = 5) {
        $query = "SELECT b.*, c.name as customer_name FROM billings b
                  JOIN customers c ON b.customer_id = c.id
                  ORDER BY b.created_at DESC LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Get recent payments
     */
    public function getRecentPayments($limit = 5) {
        $query = "SELECT p.*, c.name as customer_name FROM payments p
                  JOIN customers c ON p.customer_id = c.id
                  WHERE p.status = 'success'
                  ORDER BY p.payment_date DESC LIMIT ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Get monthly revenue trend
     */
    public function getMonthlyRevenueTrend($months = 6) {
        $query = "SELECT 
                    DATE_FORMAT(p.payment_date, '%Y-%m') as month,
                    SUM(p.amount) as revenue
                  FROM payments p
                  WHERE p.status = 'success'
                  AND p.payment_date >= DATE_SUB(CURDATE(), INTERVAL ? MONTH)
                  GROUP BY DATE_FORMAT(p.payment_date, '%Y-%m')
                  ORDER BY month DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $months);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
