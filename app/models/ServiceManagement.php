<?php
/**
 * Service Management Model
 */

class ServiceManagement {
    private $conn;
    private $table = 'service_management';

    public $id;
    public $customer_id;
    public $olt_port_id;
    public $mikrotik_id;
    public $service_type;
    public $username;
    public $password;
    public $ip_address;
    public $mac_address;
    public $status;
    public $sync_status;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Create service
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  (customer_id, olt_port_id, mikrotik_id, service_type, username, password, ip_address, mac_address, status, sync_status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iisssssss",
            $this->customer_id,
            $this->olt_port_id,
            $this->mikrotik_id,
            $this->service_type,
            $this->username,
            $this->password,
            $this->ip_address,
            $this->mac_address,
            $this->status,
            $this->sync_status
        );

        return $stmt->execute();
    }

    /**
     * Get customer service
     */
    public function getByCustomerId($customer_id) {
        $query = "SELECT sm.*, c.name as customer_name, c.email as customer_email
                  FROM " . $this->table . " sm
                  JOIN customers c ON sm.customer_id = c.id
                  WHERE sm.customer_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Update service status
     */
    public function updateStatus($service_id, $status) {
        $query = "UPDATE " . $this->table . " SET status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $service_id);
        return $stmt->execute();
    }

    /**
     * Suspend service
     */
    public function suspendService($customer_id) {
        $query = "UPDATE " . $this->table . " SET status = 'suspended' WHERE customer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $customer_id);
        return $stmt->execute();
    }

    /**
     * Activate service
     */
    public function activateService($customer_id) {
        $query = "UPDATE " . $this->table . " SET status = 'active' WHERE customer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $customer_id);
        return $stmt->execute();
    }

    /**
     * Block service
     */
    public function blockService($customer_id) {
        $query = "UPDATE " . $this->table . " SET status = 'blocked' WHERE customer_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $customer_id);
        return $stmt->execute();
    }
}
?>