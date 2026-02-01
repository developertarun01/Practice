<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'servon_user');
define('DB_PASS', 'localhost');
define('DB_NAME', 'servon_db');

// Base URLs
define('BASE_URL', 'http://localhost/servon/'); // Change to your domain
define('ADMIN_URL', BASE_URL . 'admin/');
define('API_URL', BASE_URL . 'api/');

// Site Settings
define('SITE_NAME', 'Servon - Domestic Support Solution');
define('ADMIN_EMAIL', 'admin@servon.com');

// Session settings
ini_set('session.gc_maxlifetime', 86400); // 24 hours
session_start();

// Create database connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Database connection failed']));
}

// Set charset
$conn->set_charset('utf8mb4');

// Error handling
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', dirname(__FILE__) . '/../logs/error.log');

// Helper function to escape strings
function esc($string)
{
    global $conn;
    return $conn->real_escape_string($string);
}

// Helper function to return JSON
function json_response($success, $message, $data = [])
{
    header('Content-Type: application/json');
    return json_encode(array_merge(['success' => $success, 'message' => $message], $data));
}

// Check if user is logged in
function is_logged_in()
{
    return isset($_SESSION['user_id']);
}

// Check user role
function has_role($required_roles)
{
    if (!is_logged_in()) return false;
    if (!is_array($required_roles)) $required_roles = [$required_roles];
    return in_array($_SESSION['user_role'], $required_roles);
}

// Redirect if not logged in
function require_login()
{
    if (!is_logged_in()) {
        header('Location: ' . BASE_URL . 'login.php');
        exit;
    }
}

// Redirect if not authorized
function require_role($roles)
{
    require_login();
    if (!has_role($roles)) {
        header('Location: ' . ADMIN_URL . 'dashboard.php');
        exit;
    }
}
