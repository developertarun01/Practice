<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$customer_number = isset($_POST['customer_number']) ? esc($_POST['customer_number']) : '';
$direction = isset($_POST['direction']) ? esc($_POST['direction']) : '';
$duration = isset($_POST['duration']) ? intval($_POST['duration']) : 0;
$tag = isset($_POST['tag']) ? esc($_POST['tag']) : null;
$agent_id = isset($_POST['agent_id']) ? intval($_POST['agent_id']) : null;

// Validation
$errors = [];

if (!$customer_number || !preg_match('/^\d{10}$/', $customer_number)) $errors[] = 'Valid 10-digit phone number is required';
if (!in_array($direction, ['Inbound', 'Outbound'])) $errors[] = 'Invalid direction';

if (!empty($errors)) {
    die(json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]));
}

// Create phone call
$sql = "INSERT INTO phone_calls (customer_number, direction, duration, tag, agent_id) 
        VALUES ('$customer_number', '$direction', $duration, " .
    ($tag ? "'$tag'" : "NULL") . ", " .
    ($agent_id ? $agent_id : "NULL") . ")";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Phone call record created successfully',
        'call_id' => $conn->insert_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error creating phone call']);
}

$conn->close();
