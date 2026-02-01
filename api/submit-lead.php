<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get and validate input
$name = isset($_POST['name']) ? esc($_POST['name']) : '';
$phone = isset($_POST['phone']) ? esc($_POST['phone']) : '';
$service = isset($_POST['service']) ? esc($_POST['service']) : '';

// Validation
$errors = [];

if (empty($name)) {
    $errors['name'] = 'Name is required';
} elseif (strlen($name) < 3) {
    $errors['name'] = 'Name must be at least 3 characters';
}

if (empty($phone)) {
    $errors['phone'] = 'Phone number is required';
} elseif (!preg_match('/^\d{10}$/', $phone)) {
    $errors['phone'] = 'Phone number must be 10 digits';
}

if (empty($service)) {
    $errors['service'] = 'Service is required';
} else {
    $valid_services = ['All Rounder', 'Baby Caretaker', 'Cooking Maid', 'House Maid', 'Elderly Care', 'Security Guard'];
    if (!in_array($service, $valid_services)) {
        $errors['service'] = 'Invalid service selected';
    }
}

if (!empty($errors)) {
    die(json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]));
}

// Check if phone already exists with same service
$check_query = "SELECT id FROM leads WHERE phone = '$phone' AND service = '$service' AND status IN ('Fresh', 'In progress')";
$check_result = $conn->query($check_query);

if ($check_result->num_rows > 0) {
    die(json_encode(['success' => false, 'message' => 'You already have an active request for this service']));
}

// Insert lead
$sql = "INSERT INTO leads (name, phone, service, status) VALUES ('$name', '$phone', '$service', 'Fresh')";

if ($conn->query($sql) === TRUE) {
    $lead_id = $conn->insert_id;

    // Log activity
    error_log("New lead created: ID=$lead_id, Name=$name, Phone=$phone, Service=$service");

    echo json_encode([
        'success' => true,
        'message' => 'Lead submitted successfully',
        'lead_id' => $lead_id
    ]);
} else {
    error_log("Database error: " . $conn->error);
    echo json_encode([
        'success' => false,
        'message' => 'Error submitting lead. Please try again.'
    ]);
}

$conn->close();
