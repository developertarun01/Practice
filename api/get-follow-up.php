<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

$follow_up_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$follow_up_id) {
    die(json_encode(['success' => false, 'message' => 'Follow-up ID is required']));
}

$result = $conn->query("SELECT f.*, l.name as lead_name, l.phone as lead_phone, u.name as user_name FROM follow_ups f LEFT JOIN leads l ON f.lead_id = l.id LEFT JOIN users u ON f.user_id = u.id WHERE f.id = $follow_up_id");

if ($result->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Follow-up not found']));
}

$follow_up = $result->fetch_assoc();

echo json_encode([
    'success' => true,
    'data' => $follow_up
]);

$conn->close();
