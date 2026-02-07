<?php
require_once '../includes/config.php';
require_role(['Admin', 'Allocation']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$name = isset($_POST['name']) ? esc($_POST['name']) : '';
$phone = isset($_POST['phone']) ? esc($_POST['phone']) : '';
$email = isset($_POST['email']) ? esc($_POST['email']) : '';
$service = isset($_POST['service']) ? esc($_POST['service']) : '';
$gender = isset($_POST['gender']) ? esc($_POST['gender']) : '';
$experience = isset($_POST['experience']) ? intval($_POST['experience']) : 0;
$location = isset($_POST['location']) ? esc($_POST['location']) : '';
$status = isset($_POST['status']) ? esc($_POST['status']) : 'Active';

// Validation
$errors = [];

if (!$name) $errors[] = 'Name is required';
if (!$phone || !preg_match('/^\d{10}$/', $phone)) $errors[] = 'Valid 10-digit phone is required';
if (!$service) $errors[] = 'Service is required';
if (!$gender) $errors[] = 'Gender is required';

if (!empty($errors)) {
    die(json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]));
}

// Check if phone already exists
$check = $conn->query("SELECT id FROM professionals WHERE phone = '$phone'");
if ($check->num_rows > 0) {
    die(json_encode(['success' => false, 'message' => 'Phone number already exists']));
}

// Create professional
$sql = "INSERT INTO professionals (name, phone, email, service, gender, experience, location, status, verify_status) 
        VALUES ('$name', '$phone', '$email', '$service', '$gender', $experience, '$location', '$status', 'Pending')";

if ($conn->query($sql) === TRUE) {
    $prof_id = $conn->insert_id;

    echo json_encode([
        'success' => true,
        'message' => 'Professional added successfully',
        'professional_id' => $prof_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding professional']);
}

$conn->close();
