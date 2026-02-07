<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$lead_id = isset($_POST['lead_id']) ? intval($_POST['lead_id']) : 0;
$customer_name = isset($_POST['customer_name']) ? esc($_POST['customer_name']) : '';
$customer_email = isset($_POST['customer_email']) ? esc($_POST['customer_email']) : '';
$customer_phone = isset($_POST['customer_phone']) ? esc($_POST['customer_phone']) : '';
$service = isset($_POST['service']) ? esc($_POST['service']) : '';
$full_address = isset($_POST['full_address']) ? esc($_POST['full_address']) : '';
$starts_at = isset($_POST['starts_at']) ? esc($_POST['starts_at']) : null;
$job_hours = isset($_POST['job_hours']) ? intval($_POST['job_hours']) : 0;
$salary_bracket = isset($_POST['salary_bracket']) ? esc($_POST['salary_bracket']) : '';

// Validation
$errors = [];

if (!$customer_name) $errors[] = 'Customer name is required';
if (!$customer_phone || !preg_match('/^\d{10}$/', $customer_phone)) $errors[] = 'Valid 10-digit phone is required';
if (!$service) $errors[] = 'Service is required';

if (!empty($errors)) {
    die(json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]));
}

// Check if lead exists
if ($lead_id > 0) {
    $check = $conn->query("SELECT id FROM leads WHERE id = $lead_id");
    if ($check->num_rows == 0) {
        die(json_encode(['success' => false, 'message' => 'Lead not found']));
    }
}

// Create booking
$sql = "INSERT INTO bookings (lead_id, customer_name, customer_email, customer_phone, service, full_address, starts_at, job_hours, salary_bracket, status) 
        VALUES ($lead_id, '$customer_name', '$customer_email', '$customer_phone', '$service', '$full_address', " .
    ($starts_at ? "'$starts_at'" : "NULL") . ", $job_hours, '$salary_bracket', 'In progress')";

if ($conn->query($sql) === TRUE) {
    $booking_id = $conn->insert_id;

    // If linked to a lead, update lead status
    if ($lead_id > 0) {
        $conn->query("UPDATE leads SET status = 'In progress' WHERE id = $lead_id");
    }

    echo json_encode([
        'success' => true,
        'message' => 'Booking created successfully',
        'booking_id' => $booking_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error creating booking']);
}

$conn->close();
