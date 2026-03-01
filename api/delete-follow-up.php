<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

$follow_up_id = isset($_POST['follow_up_id']) ? intval($_POST['follow_up_id']) : 0;

if (!$follow_up_id) {
    die(json_encode(['success' => false, 'message' => 'Follow-up ID is required']));
}

// Check if follow-up exists
$check = $conn->query("SELECT id, user_id FROM follow_ups WHERE id = $follow_up_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Follow-up not found']));
}

$follow_up = $check->fetch_assoc();

// For non-admin users, they can only delete their own follow-ups
if ($_SESSION['user_role'] != 'Admin' && $follow_up['user_id'] != $_SESSION['user_id']) {
    die(json_encode(['success' => false, 'message' => 'You can only delete your own follow-ups']));
}

// Delete the follow-up (only admins can delete)
if ($_SESSION['user_role'] != 'Admin') {
    die(json_encode(['success' => false, 'message' => 'Only Admin users can delete follow-ups']));
}

$result = $conn->query("DELETE FROM follow_ups WHERE id = $follow_up_id");

if ($result) {
    die(json_encode(['success' => true, 'message' => 'Follow-up deleted successfully']));
} else {
    die(json_encode(['success' => false, 'message' => 'Failed to delete follow-up: ' . $conn->error]));
}
