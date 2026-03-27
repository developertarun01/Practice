<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

$lead_id = isset($_GET['lead_id']) ? intval($_GET['lead_id']) : 0;

if (!$lead_id) {
    die(json_encode(['success' => false, 'message' => 'Lead ID is required']));
}

// Check if lead exists
$check = $conn->query("SELECT id FROM leads WHERE id = $lead_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Lead not found']));
}

// Get the most recent comment by the current user
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT c.*, u.name as user_name FROM lead_comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.lead_id = $lead_id AND c.user_id = $user_id ORDER BY c.created_at DESC LIMIT 1");

if ($result->num_rows > 0) {
    $comment = $result->fetch_assoc();
    echo json_encode([
        'success' => true,
        'data' => $comment
    ]);
} else {
    echo json_encode([
        'success' => true,
        'data' => null
    ]);
}

$conn->close();
