<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$lead_id = isset($_POST['lead_id']) ? intval($_POST['lead_id']) : 0;
$new_status = isset($_POST['status']) ? esc($_POST['status']) : '';
$valid_statuses = ['Fresh', 'In progress', 'Converted', 'Dropped'];

if (!$lead_id || !in_array($new_status, $valid_statuses)) {
    die(json_encode(['success' => false, 'message' => 'Invalid lead or status']));
}

// Update lead status
$sql = "UPDATE leads SET status = '$new_status', updated_at = NOW() WHERE id = $lead_id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Lead status updated']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating status']);
}

$conn->close();
