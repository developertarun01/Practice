<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

$lead_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$lead_id) {
    die(json_encode(['success' => false, 'message' => 'Lead ID is required']));
}

// Get lead details
$lead_result = $conn->query("SELECT l.*, u.name as responder_name FROM leads l LEFT JOIN users u ON l.responder_id = u.id WHERE l.id = $lead_id");

if ($lead_result->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Lead not found']));
}

$lead = $lead_result->fetch_assoc();

// Get comments
$comments_result = $conn->query("SELECT c.*, u.name as user_name FROM lead_comments c LEFT JOIN users u ON c.user_id = u.id WHERE c.lead_id = $lead_id ORDER BY c.created_at DESC");
$comments = [];
while ($comment = $comments_result->fetch_assoc()) {
    $comments[] = $comment;
}

echo json_encode([
    'success' => true,
    'data' => [
        'lead' => $lead,
        'comments' => $comments
    ]
]);

$conn->close();
