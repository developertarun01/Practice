<?php
require_once '../includes/config.php';
require_role(['Admin']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

$lead_id = isset($_POST['lead_id']) ? intval($_POST['lead_id']) : 0;

if (!$lead_id) {
    die(json_encode(['success' => false, 'message' => 'Lead ID is required']));
}

// Check if lead exists
$check = $conn->query("SELECT id FROM leads WHERE id = $lead_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Lead not found']));
}

// Delete the lead
$result = $conn->query("DELETE FROM leads WHERE id = $lead_id");

if ($result) {
    die(json_encode(['success' => true, 'message' => 'Lead deleted successfully']));
} else {
    die(json_encode(['success' => false, 'message' => 'Failed to delete lead: ' . $conn->error]));
}
?>
