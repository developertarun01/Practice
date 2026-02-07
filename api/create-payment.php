<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Support']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
$customer_name = isset($_POST['customer_name']) ? esc($_POST['customer_name']) : '';
$customer_email = isset($_POST['customer_email']) ? esc($_POST['customer_email']) : '';
$customer_phone = isset($_POST['customer_phone']) ? esc($_POST['customer_phone']) : '';
$service = isset($_POST['service']) ? esc($_POST['service']) : '';
$purpose = isset($_POST['purpose']) ? esc($_POST['purpose']) : '';
$total_amount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
$payment_method = isset($_POST['payment_method']) ? esc($_POST['payment_method']) : '';

// Validation
$errors = [];

if (!$customer_name) $errors[] = 'Customer name is required';
if (!$customer_phone || !preg_match('/^\d{10}$/', $customer_phone)) $errors[] = 'Valid 10-digit phone is required';
if ($total_amount <= 0) $errors[] = 'Amount must be greater than 0';

if (!empty($errors)) {
    die(json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]));
}

// Create payment
$sql = "INSERT INTO payments (booking_id, customer_name, customer_email, customer_phone, service, purpose, total_amount, payment_method, status) 
        VALUES (" . ($booking_id > 0 ? $booking_id : "NULL") . ", '$customer_name', '$customer_email', '$customer_phone', '$service', '$purpose', $total_amount, '$payment_method', 'Pending')";

if ($conn->query($sql) === TRUE) {
    $payment_id = $conn->insert_id;

    echo json_encode([
        'success' => true,
        'message' => 'Payment record created successfully',
        'payment_id' => $payment_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error creating payment']);
}

$conn->close();
