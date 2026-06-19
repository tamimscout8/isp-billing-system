<?php
/**
 * Mikrotik Controller
 */

require_once __DIR__ . '/../models/Mikrotik.php';

class MikrotikController {
    private $mikrotik;
    private $conn;

    public function __construct($db) {
        $this->mikrotik = new Mikrotik($db);
        $this->conn = $db;
    }

    /**
     * Get all mikrotiks
     */
    public function index() {
        return $this->mikrotik->getAll();
    }

    /**
     * Get mikrotik details
     */
    public function show($id) {
        return $this->mikrotik->getById($id);
    }

    /**
     * Create mikrotik
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->mikrotik->name = $_POST['name'] ?? '';
            $this->mikrotik->ip_address = $_POST['ip_address'] ?? '';
            $this->mikrotik->username = $_POST['username'] ?? '';
            $this->mikrotik->password = $_POST['password'] ?? '';
            $this->mikrotik->port = (int)($_POST['port'] ?? 8728);
            $this->mikrotik->model = $_POST['model'] ?? '';
            $this->mikrotik->serial_number = $_POST['serial_number'] ?? '';
            $this->mikrotik->location = $_POST['location'] ?? '';
            $this->mikrotik->status = 'offline';
            $this->mikrotik->api_enabled = isset($_POST['api_enabled']) ? 1 : 0;

            if ($this->mikrotik->create()) {
                return ['success' => true, 'message' => 'Mikrotik added successfully'];
            } else {
                return ['success' => false, 'message' => 'Error adding mikrotik'];
            }
        }
        return [];
    }

    /**
     * Update mikrotik
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->mikrotik->id = $id;
            $this->mikrotik->name = $_POST['name'] ?? '';
            $this->mikrotik->ip_address = $_POST['ip_address'] ?? '';
            $this->mikrotik->username = $_POST['username'] ?? '';
            $this->mikrotik->password = $_POST['password'] ?? '';
            $this->mikrotik->port = (int)($_POST['port'] ?? 8728);
            $this->mikrotik->model = $_POST['model'] ?? '';
            $this->mikrotik->serial_number = $_POST['serial_number'] ?? '';
            $this->mikrotik->location = $_POST['location'] ?? '';
            $this->mikrotik->api_enabled = isset($_POST['api_enabled']) ? 1 : 0;

            if ($this->mikrotik->update()) {
                return ['success' => true, 'message' => 'Mikrotik updated successfully'];
            } else {
                return ['success' => false, 'message' => 'Error updating mikrotik'];
            }
        }
        return [];
    }

    /**
     * Test connection
     */
    public function testConnection($id) {
        $mikrotik = $this->mikrotik->getById($id);
        if (!$mikrotik) {
            return ['success' => false, 'message' => 'Mikrotik not found'];
        }

        $this->mikrotik->id = $id;
        $this->mikrotik->ip_address = $mikrotik['ip_address'];
        $this->mikrotik->port = $mikrotik['port'];

        return $this->mikrotik->testConnection();
    }

    /**
     * Get online mikrotiks
     */
    public function getOnline() {
        return $this->mikrotik->getOnline();
    }
}
?>