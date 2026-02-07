<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

$booking_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$booking_id) {
    die(json_encode(['success' => false, 'message' => 'Booking ID is required']));
}

// Get booking details
$booking_result = $conn->query("SELECT * FROM bookings WHERE id = $booking_id");

if ($booking_result->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Booking not found']));
}

$booking = $booking_result->fetch_assoc();

echo json_encode([
    'success' => true,
    'data' => $booking
]);

$conn->close();
