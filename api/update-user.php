<?php
// Add error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/config.php';
require_role(['Admin']);

header('Content-Type: application/json');

// Log the request
error_log("update-user.php called");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get input
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$name = isset($_POST['name']) ? trim($_POST['name']) : null;
$email = isset($_POST['email']) ? trim($_POST['email']) : null;
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
$role = isset($_POST['role']) ? trim($_POST['role']) : null;
$enabled = isset($_POST['enabled']) ? intval($_POST['enabled']) : null;
$password = isset($_POST['password']) ? trim($_POST['password']) : null;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

// Validate required fields
$errors = [];
if ($name !== null && strlen($name) < 2) {
    $errors[] = "Name must be at least 2 characters";
}
if ($email !== null && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format";
}
if ($phone !== null && !preg_match('/^\d{10}$/', $phone)) {
    $errors[] = "Phone must be 10 digits";
}
if ($role !== null && !in_array($role, ['Admin', 'Sales', 'Allocation', 'Support'])) {
    $errors[] = "Invalid role selected";
}
if ($password !== null && $password !== '' && strlen($password) < 6) {
    $errors[] = "Password must be at least 6 characters";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

// Check if user exists using prepared statement
$check_sql = "SELECT id FROM users WHERE id = ?";
$check_stmt = $conn->prepare($check_sql);
if (!$check_stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$check_stmt->bind_param("i", $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

// Check if email already exists for another user (if email is being updated)
if ($email !== null) {
    $email_check_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $email_check_stmt = $conn->prepare($email_check_sql);
    if ($email_check_stmt) {
        $email_check_stmt->bind_param("si", $email, $user_id);
        $email_check_stmt->execute();
        $email_check_result = $email_check_stmt->get_result();
        if ($email_check_result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Email already exists for another user']);
            exit;
        }
    }
}

// Check if phone already exists for another user (if phone is being updated)
if ($phone !== null) {
    $phone_check_sql = "SELECT id FROM users WHERE phone = ? AND id != ?";
    $phone_check_stmt = $conn->prepare($phone_check_sql);
    if ($phone_check_stmt) {
        $phone_check_stmt->bind_param("si", $phone, $user_id);
        $phone_check_stmt->execute();
        $phone_check_result = $phone_check_stmt->get_result();
        if ($phone_check_result->num_rows > 0) {
            echo json_encode(['success' => false, 'message' => 'Phone number already exists for another user']);
            exit;
        }
    }
}

// Build update query dynamically
$updates = [];
$params = [];
$types = "";

if ($name !== null) {
    $updates[] = "name = ?";
    $params[] = $name;
    $types .= "s";
}
if ($email !== null) {
    $updates[] = "email = ?";
    $params[] = $email;
    $types .= "s";
}
if ($phone !== null) {
    $updates[] = "phone = ?";
    $params[] = $phone;
    $types .= "s";
}
if ($role !== null) {
    $updates[] = "role = ?";
    $params[] = $role;
    $types .= "s";
}
if ($enabled !== null) {
    $updates[] = "enabled = ?";
    $params[] = $enabled;
    $types .= "i";
}
if ($password !== null && $password !== '') {
    $hashed_password = hash('sha256', $password);
    $updates[] = "password = ?";
    $params[] = $hashed_password;
    $types .= "s";
}

if (empty($updates)) {
    echo json_encode(['success' => false, 'message' => 'No fields to update']);
    exit;
}

// Add updated_at and updated_by
$updates[] = "updated_at = NOW()";
$updates[] = "updated_by = ?";
$params[] = $_SESSION['user_id'];
$types .= "i";

// Add user_id to params for WHERE clause
$params[] = $user_id;
$types .= "i";

$sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating user: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
