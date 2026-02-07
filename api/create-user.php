<?php
require_once '../includes/config.php';
require_role(['Admin']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$name = isset($_POST['name']) ? esc($_POST['name']) : '';
$email = isset($_POST['email']) ? esc($_POST['email']) : '';
$phone = isset($_POST['phone']) ? esc($_POST['phone']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$role = isset($_POST['role']) ? esc($_POST['role']) : '';
$valid_roles = ['Admin', 'Sales', 'Allocation', 'Support'];

// Validation
$errors = [];

if (!$name) $errors[] = 'Name is required';
if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid email is required';
if (!$phone || !preg_match('/^\d{10}$/', $phone)) $errors[] = 'Valid 10-digit phone is required';
if (!$password || strlen($password) < 6) $errors[] = 'Password must be at least 6 characters';
if (!in_array($role, $valid_roles)) $errors[] = 'Invalid role';

if (!empty($errors)) {
    die(json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]));
}

// Check if email already exists
$check = $conn->query("SELECT id FROM users WHERE email = '$email'");
if ($check->num_rows > 0) {
    die(json_encode(['success' => false, 'message' => 'Email already exists']));
}

// Hash password
$hashed_password = hash('sha256', $password);

// Create user
$sql = "INSERT INTO users (name, email, phone, password, role, enabled) 
        VALUES ('$name', '$email', '$phone', '$hashed_password', '$role', 1)";

if ($conn->query($sql) === TRUE) {
    $user_id = $conn->insert_id;

    echo json_encode([
        'success' => true,
        'message' => 'User created successfully',
        'user_id' => $user_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error creating user']);
}

$conn->close();
