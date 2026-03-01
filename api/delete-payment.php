<?php
require_once '../includes/config.php';
require_role(['Admin']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

$payment_id = isset($_POST['payment_id']) ? intval($_POST['payment_id']) : 0;

if (!$payment_id) {
    die(json_encode(['success' => false, 'message' => 'Payment ID is required']));
}

// Check if payment exists
$check = $conn->query("SELECT id FROM payments WHERE id = $payment_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Payment not found']));
}

// Delete the payment
$result = $conn->query("DELETE FROM payments WHERE id = $payment_id");

if ($result) {
    die(json_encode(['success' => true, 'message' => 'Payment deleted successfully']));
} else {
    die(json_encode(['success' => false, 'message' => 'Failed to delete payment: ' . $conn->error]));
}
