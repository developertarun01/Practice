<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$lead_id = isset($_POST['lead_id']) ? intval($_POST['lead_id']) : 0;
$name = isset($_POST['name']) ? esc($_POST['name']) : null;
$phone = isset($_POST['phone']) ? esc($_POST['phone']) : null;
$service = isset($_POST['service']) ? esc($_POST['service']) : null;
$status = isset($_POST['status']) ? esc($_POST['status']) : null;

if (!$lead_id) {
    die(json_encode(['success' => false, 'message' => 'Lead ID is required']));
}

// Check if lead exists
$check = $conn->query("SELECT id FROM leads WHERE id = $lead_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Lead not found']));
}

// Build update query
$updates = [];
if ($name !== null) $updates[] = "name = '$name'";
if ($phone !== null) $updates[] = "phone = '$phone'";
if ($service !== null) $updates[] = "service = '$service'";
if ($status !== null) $updates[] = "status = '$status'";

if (empty($updates)) {
    die(json_encode(['success' => false, 'message' => 'No fields to update']));
}

$updates[] = "updated_at = NOW()";
$updates[] = "updated_by = " . intval($_SESSION['user_id']);
$sql = "UPDATE leads SET " . implode(', ', $updates) . " WHERE id = $lead_id";

try {
    if ($conn->query($sql)) {
        die(json_encode(['success' => true, 'message' => 'Lead updated successfully']));
    } else {
        die(json_encode(['success' => false, 'message' => 'Update failed: ' . $conn->error]));
    }
} catch (Exception $e) {
    die(json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]));
}
