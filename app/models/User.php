<?php
/**
 * User Authentication Model
 */

class User {
    private $conn;
    private $table = 'users';

    public $id;
    public $username;
    public $email;
    public $password;
    public $role;
    public $status;

    public function __construct($db) {
        $this->conn = $db;
    }

    /**
     * Get user by username
     */
    public function getByUsername($username) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Get user by email
     */
    public function getByEmail($email) {
        $query = "SELECT * FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    /**
     * Register new user
     */
    public function register() {
        $query = "INSERT INTO " . $this->table . "
                  (username, email, password, role, status)
                  VALUES (?, ?, ?, ?, ?)";
        
        $hashed_password = password_hash($this->password, PASSWORD_BCRYPT);
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("sssss",
            $this->username,
            $this->email,
            $hashed_password,
            $this->role,
            $this->status
        );

        return $stmt->execute();
    }

    /**
     * Verify password
     */
    public function verifyPassword($plain_password, $hashed_password) {
        return password_verify($plain_password, $hashed_password);
    }

    /**
     * Check if username exists
     */
    public function usernameExists($username) {
        $query = "SELECT id FROM " . $this->table . " WHERE username = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    /**
     * Check if email exists
     */
    public function emailExists($email) {
        $query = "SELECT id FROM " . $this->table . " WHERE email = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->num_rows > 0;
    }

    /**
     * Update last login
     */
    public function updateLastLogin($user_id) {
        $query = "UPDATE " . $this->table . " SET last_login = NOW() WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        return $stmt->execute();
    }
}
?>