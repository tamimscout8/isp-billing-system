<?php
/**
 * OLT Port Model
 */

class OLTPort {
    private $conn;
    private $table = 'olt_ports';

    public $id;
    public $olt_id;
    public $port_number;
    public $port_name;
    public $status;
    public $customer_id;
    public $onu_sn;
    public $signal_strength;
    public $rx_power;
    public $tx_power;
    public $distance_km;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get port by ID
     */
    public function getById($id) {
        $query = "SELECT op.*, c.name as customer_name, c.email as customer_email
                  FROM " . $this->table . " op
                  LEFT JOIN customers c ON op.customer_id = c.id
                  WHERE op.id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Assign customer to port
     */
    public function assignCustomer($port_id, $customer_id) {
        $query = "UPDATE " . $this->table . " SET customer_id = ?, status = 'active' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $customer_id, $port_id);
        return $stmt->execute();
    }

    /**
     * Unassign customer from port
     */
    public function unassignCustomer($port_id) {
        $query = "UPDATE " . $this->table . " SET customer_id = NULL, status = 'inactive' WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $port_id);
        return $stmt->execute();
    }

    /**
     * Update port status
     */
    public function updateStatus($port_id, $status) {
        $query = "UPDATE " . $this->table . " SET status = ?, last_checked = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("si", $status, $port_id);
        return $stmt->execute();
    }

    /**
     * Update port signal/power
     */
    public function updateSignalPower($port_id, $signal_strength, $rx_power, $tx_power, $distance_km) {
        $query = "UPDATE " . $this->table . "
                  SET signal_strength = ?, rx_power = ?, tx_power = ?, distance_km = ?, last_checked = NOW()
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("iddi", $signal_strength, $rx_power, $tx_power, $distance_km, $port_id);
        return $stmt->execute();
    }

    /**
     * Get customer port
     */
    public function getCustomerPort($customer_id) {
        $query = "SELECT op.*, o.name as olt_name, o.ip_address as olt_ip
                  FROM " . $this->table . " op
                  JOIN olts o ON op.olt_id = o.id
                  WHERE op.customer_id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }
}
?>