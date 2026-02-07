<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Support']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

$payment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$payment_id) {
    die(json_encode(['success' => false, 'message' => 'Payment ID is required']));
}

// Get payment details
$payment_result = $conn->query("SELECT * FROM payments WHERE id = $payment_id");

if ($payment_result->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Payment not found']));
}

$payment = $payment_result->fetch_assoc();

echo json_encode([
    'success' => true,
    'data' => $payment
]);

$conn->close();
