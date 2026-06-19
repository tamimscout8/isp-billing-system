<?php
/**
 * Session Configuration and Middleware
 */

// Start session
session_start();

// Session timeout (30 minutes)
define('SESSION_TIMEOUT', 30 * 60);

/**
 * Check session timeout
 */
function checkSessionTimeout() {
    if (isset($_SESSION['login_time'])) {
        $elapsed_time = time() - $_SESSION['login_time'];
        
        if ($elapsed_time > SESSION_TIMEOUT) {
            session_destroy();
            header('Location: ?page=login&expired=true');
            exit();
        }
        
        // Update login time on each request
        $_SESSION['login_time'] = time();
    }
}

/**
 * Check if user is logged in
 */
function isLoggedIn() {
    return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

/**
 * Require login
 */
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ?page=login&redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit();
    }
}

/**
 * Require logout (for login/register pages)
 */
function requireLogout() {
    if (isLoggedIn()) {
        header('Location: ?page=dashboard');
        exit();
    }
}

/**
 * Get current user
 */
function getCurrentUser() {
    if (isLoggedIn()) {
        return [
            'id' => $_SESSION['user_id'] ?? null,
            'username' => $_SESSION['username'] ?? null,
            'email' => $_SESSION['email'] ?? null,
            'role' => $_SESSION['role'] ?? null
        ];
    }
    return null;
}

/**
 * Check user role
 */
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

/**
 * Check if user is admin
 */
function isAdmin() {
    return hasRole('admin');
}
?>