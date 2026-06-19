<?php
/**
 * Mikrotik Model
 */

class Mikrotik {
    private $conn;
    private $table = 'mikrotiks';

    public $id;
    public $name;
    public $ip_address;
    public $username;
    public $password;
    public $port;
    public $model;
    public $serial_number;
    public $location;
    public $status;
    public $api_enabled;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all mikrotiks
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        return $this->conn->query($query);
    }

    /**
     * Get mikrotik by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Create mikrotik
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  (name, ip_address, username, password, port, model, serial_number, location, status, api_enabled)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssissssi",
            $this->name,
            $this->ip_address,
            $this->username,
            $this->password,
            $this->port,
            $this->model,
            $this->serial_number,
            $this->location,
            $this->status,
            $this->api_enabled
        );

        return $stmt->execute();
    }

    /**
     * Update mikrotik
     */
    public function update() {
        $query = "UPDATE " . $this->table . "
                  SET name = ?, ip_address = ?, username = ?, password = ?, port = ?,
                      model = ?, serial_number = ?, location = ?, status = ?, api_enabled = ?
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssissssi",
            $this->name,
            $this->ip_address,
            $this->username,
            $this->password,
            $this->port,
            $this->model,
            $this->serial_number,
            $this->location,
            $this->status,
            $this->api_enabled,
            $this->id
        );

        return $stmt->execute();
    }

    /**
     * Test connection to mikrotik
     */
    public function testConnection() {
        // Using fsockopen to test connection
        $connection = @fsockopen($this->ip_address, $this->port, $errno, $errstr, 5);
        
        if ($connection) {
            fclose($connection);
            $this->updateStatus('online');
            return ['success' => true, 'message' => 'Connection successful'];
        } else {
            $this->updateStatus('offline');
            return ['success' => false, 'message' => 'Connection failed: ' . $errstr];
        }
    }

    /**
     * Update status
     */
    public function updateStatus($status) {
        $query = "UPDATE " . $this->table . " SET status = ?, last_sync = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $this->id);
        return $stmt->execute();
    }

    /**
     * Get online mikrotiks
     */
    public function getOnline() {
        $query = "SELECT * FROM " . $this->table . " WHERE status = 'online'";
        return $this->conn->query($query);
    }

    /**
     * Get queues for customer
     */
    public function getCustomerQueues($customer_id) {
        $query = "SELECT mq.*, m.name as mikrotik_name, m.ip_address
                  FROM mikrotik_queues mq
                  JOIN mikrotiks m ON mq.mikrotik_id = m.id
                  WHERE mq.customer_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>