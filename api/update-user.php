<?php
require_once '../includes/config.php';
require_role(['Admin']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$name = isset($_POST['name']) ? esc($_POST['name']) : null;
$email = isset($_POST['email']) ? esc($_POST['email']) : null;
$phone = isset($_POST['phone']) ? esc($_POST['phone']) : null;
$role = isset($_POST['role']) ? esc($_POST['role']) : null;
$enabled = isset($_POST['enabled']) ? intval($_POST['enabled']) : null;
$password = isset($_POST['password']) ? $_POST['password'] : null;

if (!$user_id) {
    die(json_encode(['success' => false, 'message' => 'User ID is required']));
}

// Check if user exists
$check = $conn->query("SELECT id FROM users WHERE id = $user_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'User not found']));
}

// Build update query
$updates = [];
if ($name !== null) $updates[] = "name = '$name'";
if ($email !== null) $updates[] = "email = '$email'";
if ($phone !== null) $updates[] = "phone = '$phone'";
if ($role !== null) $updates[] = "role = '$role'";
if ($enabled !== null) $updates[] = "enabled = $enabled";
if ($password !== null && strlen($password) >= 6) {
    $hashed_password = hash('sha256', $password);
    $updates[] = "password = '$hashed_password'";
}

if (empty($updates)) {
    die(json_encode(['success' => false, 'message' => 'No fields to update']));
}

$updates[] = "updated_at = NOW()";
$sql = "UPDATE users SET " . implode(', ', $updates) . " WHERE id = $user_id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating user']);
}

$conn->close();
