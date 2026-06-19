<?php
/**
 * Payment Controller
 */

require_once __DIR__ . '/../models/Payment.php';

class PaymentController {
    private $payment;

    public function __construct($db) {
        $this->payment = new Payment($db);
    }

    /**
     * Get all payments
     */
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        return $this->payment->getAll($limit, $offset);
    }

    /**
     * Get payment details
     */
    public function show($id) {
        return $this->payment->getById($id);
    }

    /**
     * Get customer payments
     */
    public function getByCustomer($customer_id) {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        return $this->payment->getByCustomerId($customer_id, $limit, $offset);
    }

    /**
     * Process payment
     */
    public function processPayment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->payment->billing_id = (int)($_POST['billing_id'] ?? 0);
            $this->payment->customer_id = (int)($_POST['customer_id'] ?? 0);
            $this->payment->amount = (float)($_POST['amount'] ?? 0);
            $this->payment->payment_method = $_POST['payment_method'] ?? 'card';
            $this->payment->transaction_id = $_POST['transaction_id'] ?? '';
            $this->payment->reference_number = $_POST['reference_number'] ?? '';
            $this->payment->notes = $_POST['notes'] ?? '';
            $this->payment->status = 'success';

            if ($this->payment->create()) {
                return ['success' => true, 'message' => 'Payment processed successfully'];
            } else {
                return ['success' => false, 'message' => 'Error processing payment'];
            }
        }
        return [];
    }
}
?>