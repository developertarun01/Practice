<?php
require_once '../includes/config.php';
require_role(['Admin']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

if (!$user_id) {
    die(json_encode(['success' => false, 'message' => 'User ID is required']));
}

// Prevent self-deletion
if ($user_id == $_SESSION['user_id']) {
    die(json_encode(['success' => false, 'message' => 'Cannot delete your own user account']));
}

// Check if user exists
$check = $conn->query("SELECT id FROM users WHERE id = $user_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'User not found']));
}

// Delete the user
$result = $conn->query("DELETE FROM users WHERE id = $user_id");

if ($result) {
    die(json_encode(['success' => true, 'message' => 'User deleted successfully']));
} else {
    die(json_encode(['success' => false, 'message' => 'Failed to delete user: ' . $conn->error]));
}
