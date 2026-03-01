<?php
require_once '../includes/config.php';
require_role(['Admin']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;

if (!$booking_id) {
    die(json_encode(['success' => false, 'message' => 'Booking ID is required']));
}

// Check if booking exists
$check = $conn->query("SELECT id FROM bookings WHERE id = $booking_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Booking not found']));
}

// Delete the booking
$result = $conn->query("DELETE FROM bookings WHERE id = $booking_id");

if ($result) {
    die(json_encode(['success' => true, 'message' => 'Booking deleted successfully']));
} else {
    die(json_encode(['success' => false, 'message' => 'Failed to delete booking: ' . $conn->error]));
}
?>
