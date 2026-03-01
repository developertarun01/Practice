<?php
require_once '../includes/config.php';
require_role(['Admin']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

$professional_id = isset($_POST['professional_id']) ? intval($_POST['professional_id']) : 0;

if (!$professional_id) {
    die(json_encode(['success' => false, 'message' => 'Professional ID is required']));
}

// Check if professional exists
$check = $conn->query("SELECT id FROM professionals WHERE id = $professional_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Professional not found']));
}

// Delete the professional
$result = $conn->query("DELETE FROM professionals WHERE id = $professional_id");

if ($result) {
    die(json_encode(['success' => true, 'message' => 'Professional deleted successfully']));
} else {
    die(json_encode(['success' => false, 'message' => 'Failed to delete professional: ' . $conn->error]));
}
