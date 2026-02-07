<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$lead_id = isset($_POST['lead_id']) ? intval($_POST['lead_id']) : 0;
$comment = isset($_POST['comment']) ? esc($_POST['comment']) : '';

if (!$lead_id || !$comment) {
    die(json_encode(['success' => false, 'message' => 'Lead ID and comment are required']));
}

// Check if lead exists
$check = $conn->query("SELECT id FROM leads WHERE id = $lead_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Lead not found']));
}

// Add comment
$sql = "INSERT INTO lead_comments (lead_id, user_id, comment) VALUES ($lead_id, " . $_SESSION['user_id'] . ", '$comment')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Comment added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding comment']);
}

$conn->close();
