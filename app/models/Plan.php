<?php
/**
 * Plan Model
 */

class Plan {
    private $conn;
    private $table = 'plans';

    public $id;
    public $name;
    public $description;
    public $speed;
    public $data_limit;
    public $monthly_price;
    public $setup_fee;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all plans
     */
    public function getAll() {
        $query = "SELECT * FROM " . $this->table . " WHERE status = 'active' ORDER BY monthly_price ASC";
        return $this->conn->query($query);
    }

    /**
     * Get all plans (admin)
     */
    public function getAllAdmin() {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC";
        return $this->conn->query($query);
    }

    /**
     * Get plan by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Create new plan
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (name, description, speed, data_limit, monthly_price, setup_fee, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssids",
            $this->name,
            $this->description,
            $this->speed,
            $this->data_limit,
            $this->monthly_price,
            $this->setup_fee,
            $this->status
        );

        return $stmt->execute();
    }

    /**
     * Update plan
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET name = ?, description = ?, speed = ?, data_limit = ?, 
                      monthly_price = ?, setup_fee = ?, status = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ssssidsi",
            $this->name,
            $this->description,
            $this->speed,
            $this->data_limit,
            $this->monthly_price,
            $this->setup_fee,
            $this->status,
            $this->id
        );

        return $stmt->execute();
    }

    /**
     * Delete plan
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Get plan count
     */
    public function getCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE status = 'active'";
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }
}
?>
