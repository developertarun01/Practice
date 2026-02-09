<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$follow_up_id = isset($_POST['follow_up_id']) ? intval($_POST['follow_up_id']) : 0;
$direction = isset($_POST['direction']) ? esc($_POST['direction']) : null;
$channel = isset($_POST['channel']) ? esc($_POST['channel']) : null;
$comments = isset($_POST['comments']) ? esc($_POST['comments']) : null;
$reminder_at = isset($_POST['reminder_at']) ? esc($_POST['reminder_at']) : null;

if (!$follow_up_id) {
    die(json_encode(['success' => false, 'message' => 'Follow-up ID is required']));
}

// Check if follow-up exists
$check = $conn->query("SELECT id, user_id FROM follow_ups WHERE id = $follow_up_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Follow-up not found']));
}

// Verify user can edit (owner or admin)
$fu = $check->fetch_assoc();
if ($_SESSION['user_role'] != 'Admin' && $fu['user_id'] != $_SESSION['user_id']) {
    die(json_encode(['success' => false, 'message' => 'You can only edit your own follow-ups']));
}

// Build update query
$updates = [];
if ($direction !== null && in_array($direction, ['Inbound', 'Outbound'])) {
    $updates[] = "direction = '$direction'";
}
if ($channel !== null && in_array($channel, ['Phone', 'Email', 'WhatsApp', 'SMS'])) {
    $updates[] = "channel = '$channel'";
}
if ($comments !== null) {
    $updates[] = "comments = '$comments'";
}
if ($reminder_at !== null) {
    $updates[] = "reminder_at = " . ($reminder_at ? "'$reminder_at'" : "NULL");
}

if (empty($updates)) {
    die(json_encode(['success' => false, 'message' => 'No fields to update']));
}

$sql = "UPDATE follow_ups SET " . implode(', ', $updates) . " WHERE id = $follow_up_id";

try {
    if ($conn->query($sql)) {
        echo json_encode(['success' => true, 'message' => 'Follow-up updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Update failed: ' . $conn->error]);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

$conn->close();
?>
