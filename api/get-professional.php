<?php
// Add error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/config.php';
require_role(['Admin', 'Allocation']);

header('Content-Type: application/json');

// Log the request
error_log("get-professional.php called with ID: " . ($_GET['id'] ?? 'not set'));

if ($_SERVER['REQUEST_METHOD'] != 'GET') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$professional_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$professional_id) {
    echo json_encode(['success' => false, 'message' => 'Professional ID is required']);
    exit;
}

// Check if connection is valid
if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Get professional details with editor info and slug
$sql = "SELECT p.*, u.name as updated_by_name 
        FROM professionals p
        LEFT JOIN users u ON p.updated_by = u.id
        WHERE p.id = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $professional_id);
if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'message' => 'Execute failed: ' . $stmt->error]);
    exit;
}

$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Professional not found']);
    exit;
}

$professional = $result->fetch_assoc();

// Generate slug if not exists
if (empty($professional['professional_slug'])) {
    $slug = strtolower(str_replace(' ', '-', $professional['name'])) . '-' . uniqid();
    $update_slug = $conn->prepare("UPDATE professionals SET professional_slug = ? WHERE id = ?");
    $update_slug->bind_param("si", $slug, $professional_id);
    $update_slug->execute();
    $professional['professional_slug'] = $slug;
}

echo json_encode([
    'success' => true,
    'data' => $professional
]);

$stmt->close();
$conn->close();
