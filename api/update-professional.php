<?php
require_once '../includes/config.php';
require_role(['Admin', 'Allocation']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$professional_id = isset($_POST['professional_id']) ? intval($_POST['professional_id']) : 0;
$name = isset($_POST['name']) ? esc($_POST['name']) : null;
$email = isset($_POST['email']) ? esc($_POST['email']) : null;
$phone = isset($_POST['phone']) ? esc($_POST['phone']) : null;
$experience = isset($_POST['experience']) ? intval($_POST['experience']) : null;
$status = isset($_POST['status']) ? esc($_POST['status']) : null;
$verify_status = isset($_POST['verify_status']) ? esc($_POST['verify_status']) : null;
$rating = isset($_POST['rating']) ? floatval($_POST['rating']) : null;

if (!$professional_id) {
    die(json_encode(['success' => false, 'message' => 'Professional ID is required']));
}

// Check if professional exists
$check = $conn->query("SELECT id FROM professionals WHERE id = $professional_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Professional not found']));
}

// Build update query
$updates = [];
if ($name !== null) $updates[] = "name = '$name'";
if ($email !== null) $updates[] = "email = '$email'";
if ($phone !== null) $updates[] = "phone = '$phone'";
if ($experience !== null) $updates[] = "experience = $experience";
if ($status !== null) $updates[] = "status = '$status'";
if ($verify_status !== null) $updates[] = "verify_status = '$verify_status'";
if ($rating !== null) $updates[] = "rating = $rating";

if (empty($updates)) {
    die(json_encode(['success' => false, 'message' => 'No fields to update']));
}

$updates[] = "updated_at = NOW()";
$sql = "UPDATE professionals SET " . implode(', ', $updates) . " WHERE id = $professional_id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Professional updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating professional']);
}

$conn->close();
