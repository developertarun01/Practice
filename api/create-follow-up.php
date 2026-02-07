<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$lead_id = isset($_POST['lead_id']) ? intval($_POST['lead_id']) : 0;
$direction = isset($_POST['direction']) ? esc($_POST['direction']) : '';
$channel = isset($_POST['channel']) ? esc($_POST['channel']) : '';
$comments = isset($_POST['comments']) ? esc($_POST['comments']) : '';
$reminder_at = isset($_POST['reminder_at']) ? esc($_POST['reminder_at']) : null;

// Validation
$errors = [];

if (!$lead_id) $errors[] = 'Lead ID is required';
if (!in_array($direction, ['Inbound', 'Outbound'])) $errors[] = 'Invalid direction';
if (!in_array($channel, ['Phone', 'Email', 'WhatsApp', 'SMS'])) $errors[] = 'Invalid channel';

if (!empty($errors)) {
    die(json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]));
}

// Check if lead exists
$check = $conn->query("SELECT id FROM leads WHERE id = $lead_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Lead not found']));
}

// Create follow-up
$sql = "INSERT INTO follow_ups (lead_id, user_id, direction, channel, comments, reminder_at) 
        VALUES ($lead_id, " . $_SESSION['user_id'] . ", '$direction', '$channel', '$comments', " .
    ($reminder_at ? "'$reminder_at'" : "NULL") . ")";

if ($conn->query($sql) === TRUE) {
    echo json_encode([
        'success' => true,
        'message' => 'Follow-up created successfully',
        'follow_up_id' => $conn->insert_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error creating follow-up']);
}

$conn->close();
