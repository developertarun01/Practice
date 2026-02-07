<?php
require_once '../includes/config.php';
require_role(['Admin']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$user_id) {
    die(json_encode(['success' => false, 'message' => 'User ID is required']));
}

// Get user details
$user_result = $conn->query("SELECT id, name, email, phone, role, enabled, created_at FROM users WHERE id = $user_id");

if ($user_result->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'User not found']));
}

$user = $user_result->fetch_assoc();

echo json_encode([
    'success' => true,
    'data' => $user
]);

$conn->close();
