<?php
/**
 * Customer Controller
 */

require_once __DIR__ . '/../models/Customer.php';

class CustomerController {
    private $customer;
    private $conn;

    public function __construct($db) {
        $this->customer = new Customer($db);
        $this->conn = $db;
    }

    /**
     * Get all customers
     */
    public function index() {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $offset = ($page - 1) * $limit;

        $result = $this->customer->getAll($limit, $offset);
        $total = $this->customer->getCount();
        $total_pages = ceil($total / $limit);

        return [
            'customers' => $result,
            'total' => $total,
            'page' => $page,
            'total_pages' => $total_pages
        ];
    }

    /**
     * Get customer details
     */
    public function show($id) {
        return $this->customer->getById($id);
    }

    /**
     * Create new customer
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->customer->name = $_POST['name'] ?? '';
            $this->customer->email = $_POST['email'] ?? '';
            $this->customer->phone = $_POST['phone'] ?? '';
            $this->customer->address = $_POST['address'] ?? '';
            $this->customer->city = $_POST['city'] ?? '';
            $this->customer->postal_code = $_POST['postal_code'] ?? '';
            $this->customer->status = 'active';

            if ($this->customer->create()) {
                return ['success' => true, 'message' => 'Customer created successfully'];
            } else {
                return ['success' => false, 'message' => 'Error creating customer'];
            }
        }
        return [];
    }

    /**
     * Update customer
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->customer->id = $id;
            $this->customer->name = $_POST['name'] ?? '';
            $this->customer->email = $_POST['email'] ?? '';
            $this->customer->phone = $_POST['phone'] ?? '';
            $this->customer->address = $_POST['address'] ?? '';
            $this->customer->city = $_POST['city'] ?? '';
            $this->customer->postal_code = $_POST['postal_code'] ?? '';
            $this->customer->status = $_POST['status'] ?? 'active';

            if ($this->customer->update()) {
                return ['success' => true, 'message' => 'Customer updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Error updating customer'];
            }
        }
        return [];
    }

    /**
     * Delete customer
     */
    public function delete($id) {
        if ($this->customer->delete($id)) {
            return ['success' => true, 'message' => 'Customer deleted successfully'];
        } else {
            return ['success' => false, 'message' => 'Error deleting customer'];
        }
    }

    /**
     * Search customers
     */
    public function search() {
        $search_term = $_GET['q'] ?? '';
        if (empty($search_term)) {
            return [];
        }
        return $this->customer->search($search_term);
    }
}
?>