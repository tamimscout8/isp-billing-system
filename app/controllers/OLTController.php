<?php
/**
 * OLT Controller
 */

require_once __DIR__ . '/../models/OLT.php';
require_once __DIR__ . '/../models/OLTPort.php';

class OLTController {
    private $olt;
    private $oltPort;
    private $conn;

    public function __construct($db) {
        $this->olt = new OLT($db);
        $this->oltPort = new OLTPort($db);
        $this->conn = $db;
    }

    /**
     * Get all OLTs
     */
    public function index() {
        return $this->olt->getAll();
    }

    /**
     * Get OLT details
     */
    public function show($id) {
        return $this->olt->getById($id);
    }

    /**
     * Get OLT ports
     */
    public function getPorts($olt_id) {
        return $this->olt->getPorts($olt_id);
    }

    /**
     * Create OLT
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->olt->name = $_POST['name'] ?? '';
            $this->olt->ip_address = $_POST['ip_address'] ?? '';
            $this->olt->username = $_POST['username'] ?? '';
            $this->olt->password = $_POST['password'] ?? '';
            $this->olt->port = (int)($_POST['port'] ?? 23);
            $this->olt->model = $_POST['model'] ?? '';
            $this->olt->serial_number = $_POST['serial_number'] ?? '';
            $this->olt->vendor = $_POST['vendor'] ?? '';
            $this->olt->ports_count = (int)($_POST['ports_count'] ?? 32);
            $this->olt->location = $_POST['location'] ?? '';
            $this->olt->status = 'offline';

            if ($this->olt->create()) {
                // Create ports for OLT
                $olt_id = $this->conn->insert_id;
                $this->createOLTPorts($olt_id, $this->olt->ports_count);
                return ['success' => true, 'message' => 'OLT added successfully with ports', 'olt_id' => $olt_id];
            } else {
                return ['success' => false, 'message' => 'Error adding OLT'];
            }
        }
        return [];
    }

    /**
     * Create OLT ports
     */
    private function createOLTPorts($olt_id, $ports_count) {
        $query = "INSERT INTO olt_ports (olt_id, port_number, port_name, status) VALUES";
        $values = [];
        
        for ($i = 1; $i <= $ports_count; $i++) {
            $values[] = "($olt_id, $i, 'Port-$i', 'inactive')";
        }
        
        if (!empty($values)) {
            $query .= implode(",", $values);
            return $this->conn->query($query);
        }
        return false;
    }

    /**
     * Update OLT
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->olt->id = $id;
            $this->olt->name = $_POST['name'] ?? '';
            $this->olt->ip_address = $_POST['ip_address'] ?? '';
            $this->olt->username = $_POST['username'] ?? '';
            $this->olt->password = $_POST['password'] ?? '';
            $this->olt->port = (int)($_POST['port'] ?? 23);
            $this->olt->model = $_POST['model'] ?? '';
            $this->olt->serial_number = $_POST['serial_number'] ?? '';
            $this->olt->vendor = $_POST['vendor'] ?? '';
            $this->olt->ports_count = (int)($_POST['ports_count'] ?? 32);
            $this->olt->location = $_POST['location'] ?? '';

            if ($this->olt->update()) {
                return ['success' => true, 'message' => 'OLT updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Error updating OLT'];
            }
        }
        return [];
    }

    /**
     * Test connection
     */
    public function testConnection($id) {
        $olt = $this->olt->getById($id);
        if (!$olt) {
            return ['success' => false, 'message' => 'OLT not found'];
        }

        $this->olt->id = $id;
        $this->olt->ip_address = $olt['ip_address'];
        $this->olt->port = $olt['port'];

        return $this->olt->testConnection();
    }

    /**
     * Assign customer to OLT port
     */
    public function assignCustomer($port_id, $customer_id) {
        if ($this->oltPort->assignCustomer($port_id, $customer_id)) {
            return ['success' => true, 'message' => 'Customer assigned to port'];
        }
        return ['success' => false, 'message' => 'Error assigning customer'];
    }

    /**
     * Unassign customer from OLT port
     */
    public function unassignCustomer($port_id) {
        if ($this->oltPort->unassignCustomer($port_id)) {
            return ['success' => true, 'message' => 'Customer unassigned from port'];
        }
        return ['success' => false, 'message' => 'Error unassigning customer'];
    }

    /**
     * Get OLT statistics
     */
    public function getStatistics($olt_id) {
        $olt = $this->olt->getById($olt_id);
        $active_ports = $this->olt->getActivePortsCount($olt_id);
        $available_ports = $this->olt->getAvailablePortsCount($olt_id);

        return [
            'olt' => $olt,
            'active_ports' => $active_ports,
            'available_ports' => $available_ports,
            'total_ports' => $olt['ports_count'],
            'utilization' => round(($active_ports / $olt['ports_count']) * 100, 2)
        ];
    }
}
?>