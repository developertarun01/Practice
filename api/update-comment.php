<?php
require_once '../includes/config.php';
require_role(['Admin', 'Sales', 'Allocation', 'Support']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$comment_id = isset($_POST['comment_id']) ? intval($_POST['comment_id']) : 0;
$comment_text = isset($_POST['comment']) ? esc($_POST['comment']) : '';

if (!$comment_id || !$comment_text) {
    die(json_encode(['success' => false, 'message' => 'Comment ID and text are required']));
}

// Check if comment exists and user is the author
$check = $conn->query("SELECT id, user_id FROM lead_comments WHERE id = $comment_id");
if ($check->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Comment not found']));
}

$comment = $check->fetch_assoc();

// Allow editing only by the comment author or admin
if ($comment['user_id'] != $_SESSION['user_id'] && $_SESSION['user_role'] != 'Admin') {
    die(json_encode(['success' => false, 'message' => 'You can only edit your own comments']));
}

// Update comment
$sql = "UPDATE lead_comments SET comment = '$comment_text' WHERE id = $comment_id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(['success' => true, 'message' => 'Comment updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating comment']);
}

$conn->close();
