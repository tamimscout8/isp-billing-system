<?php
/**
 * Auth Controller
 */

require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $user;
    private $conn;

    public function __construct($db) {
        $this->user = new User($db);
        $this->conn = $db;
    }

    /**
     * Login user
     */
    public function login() {
        $result = ['success' => false, 'message' => ''];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']) ? true : false;

            // Validation
            if (empty($username) || empty($password)) {
                $result['message'] = 'Username and password are required';
                return $result;
            }

            // Get user
            $user = $this->user->getByUsername($username);
            
            if (!$user) {
                $result['message'] = 'Invalid username or password';
                return $result;
            }

            // Check status
            if ($user['status'] !== 'active') {
                $result['message'] = 'Your account is inactive';
                return $result;
            }

            // Verify password
            if (!$this->user->verifyPassword($password, $user['password'])) {
                $result['message'] = 'Invalid username or password';
                return $result;
            }

            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();

            // Remember me
            if ($remember) {
                setcookie('user_id', $user['id'], time() + (30 * 24 * 60 * 60), '/');
                setcookie('username', $user['username'], time() + (30 * 24 * 60 * 60), '/');
            }

            // Update last login
            $this->user->updateLastLogin($user['id']);

            $result['success'] = true;
            $result['message'] = 'Login successful';
            $result['redirect'] = '?page=dashboard';
        }

        return $result;
    }

    /**
     * Register user
     */
    public function register() {
        $result = ['success' => false, 'message' => ''];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $password_confirm = $_POST['password_confirm'] ?? '';

            // Validation
            if (empty($username) || empty($email) || empty($password)) {
                $result['message'] = 'All fields are required';
                return $result;
            }

            if (strlen($username) < 3) {
                $result['message'] = 'Username must be at least 3 characters';
                return $result;
            }

            if (strlen($password) < 6) {
                $result['message'] = 'Password must be at least 6 characters';
                return $result;
            }

            if ($password !== $password_confirm) {
                $result['message'] = 'Passwords do not match';
                return $result;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $result['message'] = 'Invalid email address';
                return $result;
            }

            // Check if username exists
            if ($this->user->usernameExists($username)) {
                $result['message'] = 'Username already exists';
                return $result;
            }

            // Check if email exists
            if ($this->user->emailExists($email)) {
                $result['message'] = 'Email already registered';
                return $result;
            }

            // Register user
            $this->user->username = $username;
            $this->user->email = $email;
            $this->user->password = $password;
            $this->user->role = 'admin'; // Default role
            $this->user->status = 'active';

            if ($this->user->register()) {
                $result['success'] = true;
                $result['message'] = 'Registration successful. Please login.';
                $result['redirect'] = '?page=login';
            } else {
                $result['message'] = 'Registration failed. Please try again.';
            }
        }

        return $result;
    }

    /**
     * Logout user
     */
    public function logout() {
        // Destroy session
        session_destroy();
        
        // Delete cookies
        setcookie('user_id', '', time() - 3600, '/');
        setcookie('username', '', time() - 3600, '/');
        
        return ['success' => true, 'message' => 'Logged out successfully'];
    }

    /**
     * Check if user is logged in
     */
    public static function isLoggedIn() {
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
    }

    /**
     * Get current user
     */
    public static function getCurrentUser() {
        if (self::isLoggedIn()) {
            return [
                'id' => $_SESSION['user_id'] ?? null,
                'username' => $_SESSION['username'] ?? null,
                'email' => $_SESSION['email'] ?? null,
                'role' => $_SESSION['role'] ?? null
            ];
        }
        return null;
    }
}
?>