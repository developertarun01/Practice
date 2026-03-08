<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;

if (!$comment_id) {
    die(json_encode(['success' => false, 'message' => 'Comment ID is required']));
}

// Check if comment exists and user is the author
$check = $conn->query("SELECT id, user_id FROM lead_comments WHERE id = $comment_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Comment not found']));
}

$comment = $check->fetch_assoc();

// Allow deletion only by the comment author or admin
if ($comment['user_id'] != $_SESSION['user_id'] && $_SESSION['user_role'] != 'Admin') {
    die(json_encode(['success' => false, 'message' => 'You can only delete your own comments']));
}

// Delete comment
$sql = "DELETE FROM lead_comments WHERE id = $comment_id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Comment deleted successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error deleting comment']);
}

$conn->close();
