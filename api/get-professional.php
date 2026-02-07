<?php
require_once '../includes/config.php';
require_role(['Admin', 'Allocation']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

$professional_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$professional_id) {
    die(json_encode(['success' => false, 'message' => 'Professional ID is required']));
}

// Get professional details
$prof_result = $conn->query("SELECT * FROM professionals WHERE id = $professional_id");

if ($prof_result->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Professional not found']));
}

$professional = $prof_result->fetch_assoc();

echo json_encode([
    'success' => true,
    'data' => $professional
]);

$conn->close();
