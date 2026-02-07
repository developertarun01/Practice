<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$booking_id = isset($_POST['booking_id']) ? intval($_POST['booking_id']) : 0;
$customer_name = isset($_POST['customer_name']) ? esc($_POST['customer_name']) : null;
$customer_email = isset($_POST['customer_email']) ? esc($_POST['customer_email']) : null;
$customer_phone = isset($_POST['customer_phone']) ? esc($_POST['customer_phone']) : null;
$full_address = isset($_POST['full_address']) ? esc($_POST['full_address']) : null;
$status = isset($_POST['status']) ? esc($_POST['status']) : null;
$starts_at = isset($_POST['starts_at']) ? esc($_POST['starts_at']) : null;
$job_hours = isset($_POST['job_hours']) ? intval($_POST['job_hours']) : null;
$salary_bracket = isset($_POST['salary_bracket']) ? esc($_POST['salary_bracket']) : null;

if (!$booking_id) {
    die(json_encode(['success' => false, 'message' => 'Booking ID is required']));
}

// Check if booking exists
$check = $conn->query("SELECT id FROM bookings WHERE id = $booking_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Booking not found']));
}

// Build update query
$updates = [];
if ($customer_name !== null) $updates[] = "customer_name = '$customer_name'";
if ($customer_email !== null) $updates[] = "customer_email = '$customer_email'";
if ($customer_phone !== null) $updates[] = "customer_phone = '$customer_phone'";
if ($full_address !== null) $updates[] = "full_address = '$full_address'";
if ($status !== null) $updates[] = "status = '$status'";
if ($starts_at !== null) $updates[] = "starts_at = '$starts_at'";
if ($job_hours !== null) $updates[] = "job_hours = $job_hours";
if ($salary_bracket !== null) $updates[] = "salary_bracket = '$salary_bracket'";

if (empty($updates)) {
    die(json_encode(['success' => false, 'message' => 'No fields to update']));
}

$updates[] = "updated_at = NOW()";
$sql = "UPDATE bookings SET " . implode(', ', $updates) . " WHERE id = $booking_id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Booking updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating booking']);
}

$conn->close();
