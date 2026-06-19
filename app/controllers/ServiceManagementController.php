<?php
/**
 * Service Management Controller
 */

require_once __DIR__ . '/../models/ServiceManagement.php';

class ServiceManagementController {
    private $service;
    private $conn;

    public function __construct($db) {
        $this->service = new ServiceManagement($db);
        $this->conn = $db;
    }

    /**
     * Create service
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->service->customer_id = (int)($_POST['customer_id'] ?? 0);
            $this->service->olt_port_id = (int)($_POST['olt_port_id'] ?? 0);
            $this->service->mikrotik_id = (int)($_POST['mikrotik_id'] ?? 0);
            $this->service->service_type = $_POST['service_type'] ?? 'pppoe';
            $this->service->username = $_POST['username'] ?? '';
            $this->service->password = $_POST['password'] ?? '';
            $this->service->ip_address = $_POST['ip_address'] ?? '';
            $this->service->mac_address = $_POST['mac_address'] ?? '';
            $this->service->status = 'inactive';
            $this->service->sync_status = 'pending';

            if ($this->service->create()) {
                return ['success' => true, 'message' => 'Service created successfully'];
            } else {
                return ['success' => false, 'message' => 'Error creating service'];
            }
        }
        return [];
    }

    /**
     * Get customer service
     */
    public function getService($customer_id) {
        return $this->service->getByCustomerId($customer_id);
    }

    /**
     * Suspend customer service
     */
    public function suspendService($customer_id) {
        if ($this->service->suspendService($customer_id)) {
            return ['success' => true, 'message' => 'Service suspended'];
        }
        return ['success' => false, 'message' => 'Error suspending service'];
    }

    /**
     * Activate customer service
     */
    public function activateService($customer_id) {
        if ($this->service->activateService($customer_id)) {
            return ['success' => true, 'message' => 'Service activated'];
        }
        return ['success' => false, 'message' => 'Error activating service'];
    }

    /**
     * Block customer service
     */
    public function blockService($customer_id) {
        if ($this->service->blockService($customer_id)) {
            return ['success' => true, 'message' => 'Service blocked'];
        }
        return ['success' => false, 'message' => 'Error blocking service'];
    }
}
?>