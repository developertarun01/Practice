<?php
require_once '../includes/config.php';

// Set JSON header early
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Session expired. Please login again.']));
}

// Check role
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['Admin', 'Sales', 'Support'])) {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Access denied']));
}

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$payment_id = isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0;
$status = isset($_POST['status']) ? esc($_POST['status']) : '';
$valid_statuses = ['Pending', 'Completed', 'Failed', 'Refunded'];

if (!$payment_id || !in_array($status, $valid_statuses)) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Invalid payment ID or status']));
}

// Check if payment exists
$check = $conn->query("SELECT id FROM payments WHERE id = $payment_id");
if (!$check) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]));
}

if ($check->num_rows == 0) {
    http_response_code(404);
    die(json_encode(['success' => false, 'message' => 'Payment not found']));
}

// Update payment status
$received_at = $status == 'Completed' ? 'NOW()' : 'NULL';
$sql = "UPDATE payments SET status = '$status', received_at = $received_at, updated_at = NOW(), updated_by = " . intval($_SESSION['user_id']) . " WHERE id = $payment_id";

if ($conn->query($sql) === TRUE) {
    ob_clean();
    echo json_encode(['success' => true, 'message' => 'Payment status updated']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error updating payment: ' . $conn->error]);
}

$conn->close();
