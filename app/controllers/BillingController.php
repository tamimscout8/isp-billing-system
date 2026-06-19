<?php
/**
 * Billing Controller
 */

require_once __DIR__ . '/../models/Billing.php';

class BillingController {
    private $billing;
    private $conn;

    public function __construct($db) {
        $this->billing = new Billing($db);
        $this->conn = $db;
    }

    /**
     * Get all billings
     */
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        return $this->billing->getAll($limit, $offset);
    }

    /**
     * Get billing details
     */
    public function show($id) {
        return $this->billing->getById($id);
    }

    /**
     * Get customer billings
     */
    public function getByCustomer($customer_id) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        return $this->billing->getByCustomerId($customer_id, $limit, $offset);
    }

    /**
     * Create billing
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->billing->customer_id = (int)($_POST['customer_id'] ?? 0);
            $this->billing->customer_plan_id = (int)($_POST['customer_plan_id'] ?? 0);
            $this->billing->billing_date = $_POST['billing_date'] ?? date('Y-m-d');
            $this->billing->due_date = $_POST['due_date'] ?? date('Y-m-d', strtotime('+15 days'));
            $this->billing->amount = (float)($_POST['amount'] ?? 0);
            $this->billing->tax = (float)($_POST['tax'] ?? 0);
            $this->billing->total_amount = $this->billing->amount + $this->billing->tax;
            $this->billing->status = 'pending';

            if ($this->billing->create()) {
                return ['success' => true, 'message' => 'Billing created successfully'];
            } else {
                return ['success' => false, 'message' => 'Error creating billing'];
            }
        }
        return [];
    }

    /**
     * Update billing status
     */
    public function updateStatus($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $status = $_POST['status'] ?? 'pending';
            if ($this->billing->updateStatus($id, $status)) {
                return ['success' => true, 'message' => 'Billing status updated'];
            } else {
                return ['success' => false, 'message' => 'Error updating billing status'];
            }
        }
        return [];
    }

    /**
     * Generate monthly bills
     */
    public function generateMonthly() {
        if ($this->billing->generateMonthlyBills()) {
            return ['success' => true, 'message' => 'Monthly bills generated successfully'];
        } else {
            return ['success' => false, 'message' => 'Error generating monthly bills'];
        }
    }
}
?>