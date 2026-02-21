<?php
// Add error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/config.php';
require_role(['Admin']);

header('Content-Type: application/json');

// Log the request for debugging
error_log("get-user.php called with ID: " . ($_GET['id'] ?? 'not set'));

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$user_id) {
    echo json_encode(['success' => false, 'message' => 'User ID is required']);
    exit;
}

// Check database connection
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Use prepared statement to prevent SQL injection
$sql = "SELECT u.id, u.name, u.email, u.phone, u.role, u.enabled, u.created_at,
               editor.name as updated_by_name
        FROM users u
        LEFT JOIN users editor ON u.updated_by = editor.id
        WHERE u.id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit;
}

$user = $result->fetch_assoc();

// Convert enabled to boolean for consistency
$user['enabled'] = (bool)$user['enabled'];

echo json_encode([
    'success' => true,
    'data' => $user
]);

$stmt->close();
$conn->close();
?>