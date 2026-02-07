<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Support']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$payment_id = isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0;
$status = isset($_POST['status']) ? esc($_POST['status']) : '';
$valid_statuses = ['Pending', 'Completed', 'Failed', 'Refunded'];

if (!$payment_id || !in_array($status, $valid_statuses)) {
    die(json_encode(['success' => false, 'message' => 'Invalid payment ID or status']));
}

// Check if payment exists
$check = $conn->query("SELECT id FROM payments WHERE id = $payment_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Payment not found']));
}

// Update payment status
$received_at = $status == 'Completed' ? 'NOW()' : 'NULL';
$sql = "UPDATE payments SET status = '$status', received_at = $received_at, updated_at = NOW() WHERE id = $payment_id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Payment status updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating payment']);
}

$conn->close();
