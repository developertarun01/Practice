<?php
require_once '../includes/config.php';

// Set JSON header early
header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    die(json_encode(['success' => false, 'message' => 'Session expired. Please login again.']));
}

// Check role
if (!isset($_SESSION['user_role']) || !in_array($_SESSION['user_role'], ['Admin', 'Sales', 'Support'])) {
    http_response_code(403);
    die(json_encode(['success' => false, 'message' => 'Access denied']));
}

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    http_response_code(405);
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

$payment_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$payment_id) {
    http_response_code(400);
    die(json_encode(['success' => false, 'message' => 'Payment ID is required']));
}

// Get payment details with editor info
$payment_result = $conn->query("
    SELECT p.*, 
           u.name as updated_by_name
    FROM payments p
    LEFT JOIN users u ON p.updated_by = u.id
    WHERE p.id = $payment_id
");

if (!$payment_result) {
    http_response_code(500);
    die(json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]));
}

if ($payment_result->num_rows == 0) {
    http_response_code(404);
    die(json_encode(['success' => false, 'message' => 'Payment not found']));
}

$payment = $payment_result->fetch_assoc();

// Ensure no whitespace before JSON output
ob_clean();
echo json_encode([
    'success' => true,
    'data' => $payment
]);

$conn->close();
?>