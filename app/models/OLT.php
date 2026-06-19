<?php
/**
 * OLT (Optical Line Terminal) Model
 */

class OLT {
    private $conn;
    private $table = 'olts';

    public $id;
    public $name;
    public $ip_address;
    public $username;
    public $password;
    public $port;
    public $model;
    public $serial_number;
    public $vendor;
    public $ports_count;
    public $location;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all OLTs
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        return $this->conn->query($query);
    }

    /**
     * Get OLT by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Create OLT
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . "
                  (name, ip_address, username, password, port, model, serial_number, vendor, ports_count, location, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssisssss",
            $this->name,
            $this->ip_address,
            $this->username,
            $this->password,
            $this->port,
            $this->model,
            $this->serial_number,
            $this->vendor,
            $this->ports_count,
            $this->location,
            $this->status
        );

        return $stmt->execute();
    }

    /**
     * Update OLT
     */
    public function update() {
        $query = "UPDATE " . $this->table . "
                  SET name = ?, ip_address = ?, username = ?, password = ?, port = ?,
                      model = ?, serial_number = ?, vendor = ?, ports_count = ?, location = ?, status = ?
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssissssi",
            $this->name,
            $this->ip_address,
            $this->username,
            $this->password,
            $this->port,
            $this->model,
            $this->serial_number,
            $this->vendor,
            $this->ports_count,
            $this->location,
            $this->status,
            $this->id
        );

        return $stmt->execute();
    }

    /**
     * Test connection to OLT
     */
    public function testConnection() {
        $connection = @fsockopen($this->ip_address, $this->port, $errno, $errstr, 5);
        
        if ($connection) {
            fclose($connection);
            $this->updateStatus('online');
            return ['success' => true, 'message' => 'OLT Connection successful'];
        } else {
            $this->updateStatus('offline');
            return ['success' => false, 'message' => 'OLT Connection failed: ' . $errstr];
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
     * Get OLT ports
     */
    public function getPorts($olt_id) {
        $query = "SELECT op.*, c.name as customer_name FROM olt_ports op
                  LEFT JOIN customers c ON op.customer_id = c.id
                  WHERE op.olt_id = ?
                  ORDER BY op.port_number";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $olt_id);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Get active ports count
     */
    public function getActivePortsCount($olt_id) {
        $query = "SELECT COUNT(*) as count FROM olt_ports WHERE olt_id = ? AND status = 'active'";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $olt_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }

    /**
     * Get available ports count
     */
    public function getAvailablePortsCount($olt_id) {
        $query = "SELECT COUNT(*) as count FROM olt_ports WHERE olt_id = ? AND customer_id IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $olt_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();
        return $result['count'];
    }
}
?>