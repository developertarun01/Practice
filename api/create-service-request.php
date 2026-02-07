<?php
require_once '../includes/config.php';
require_role(['Admin', 'Support']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
$professional_id = isset($_POST['professional_id']) ? intval($_POST['professional_id']) : 0;
$remarks = isset($_POST['remarks']) ? esc($_POST['remarks']) : '';

// Validation
$errors = [];

if (!$booking_id) $errors[] = 'Booking ID is required';
if (!$professional_id) $errors[] = 'Professional ID is required';

if (!empty($errors)) {
    die(json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]));
}

// Check if booking exists
$check_booking = $conn->query("SELECT id FROM bookings WHERE id = $booking_id");
if ($check_booking->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Booking not found']));
}

// Check if professional exists
$check_prof = $conn->query("SELECT id FROM professionals WHERE id = $professional_id");
if ($check_prof->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Professional not found']));
}

// Create service request
$sql = "INSERT INTO service_requests (booking_id, professional_id, remarks, created_by, status) 
        VALUES ($booking_id, $professional_id, '$remarks', " . $_SESSION['user_id'] . ", 'Open')";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Service request created successfully',
        'request_id' => $conn->insert_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error creating service request']);
}

$conn->close();
