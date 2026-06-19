<?php
/**
 * Customer Model
 */

class Customer {
    private $conn;
    private $table = 'customers';

    public $id;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $city;
    public $postal_code;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get all customers
     */
    public function getAll($limit = 10, $offset = 0) {
        $query = "SELECT * FROM " . $this->table . " ORDER BY created_at DESC LIMIT ?, ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("ii", $offset, $limit);
        $stmt->execute();
        return $stmt->get_result();
    }

    /**
     * Get customer by ID
     */
    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Create new customer
     */
    public function create() {
        $query = "INSERT INTO " . $this->table . " 
                  (name, email, phone, address, city, postal_code, status) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssss", 
            $this->name,
            $this->email,
            $this->phone,
            $this->address,
            $this->city,
            $this->postal_code,
            $this->status
        );

        return $stmt->execute();
    }

    /**
     * Update customer
     */
    public function update() {
        $query = "UPDATE " . $this->table . " 
                  SET name = ?, email = ?, phone = ?, address = ?, city = ?, postal_code = ?, status = ? 
                  WHERE id = ?";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssssssi",
            $this->name,
            $this->email,
            $this->phone,
            $this->address,
            $this->city,
            $this->postal_code,
            $this->status,
            $this->id
        );

        return $stmt->execute();
    }

    /**
     * Delete customer
     */
    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    /**
     * Get customer count
     */
    public function getCount() {
        $query = "SELECT COUNT(*) as count FROM " . $this->table;
        $result = $this->conn->query($query);
        return $result->fetch_assoc()['count'];
    }

    /**
     * Search customers
     */
    public function search($search_term) {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?
                  ORDER BY created_at DESC";
        
        $search = "%{$search_term}%";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sss", $search, $search, $search);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
