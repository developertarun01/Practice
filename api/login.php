<?php
require_once '../includes/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

$email = isset($_POST['email']) ? esc($_POST['email']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';

if (empty($email) || empty($password)) {
    die(json_encode(['success' => false, 'message' => 'Email and password are required']));
}

// Query user
$sql = "SELECT id, name, email, password, role, enabled FROM users WHERE email = '$email' LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows == 0) {
    die(json_encode(['success' => false, 'message' => 'Invalid email or password']));
}

$user = $result->fetch_assoc();

// Check if account is enabled
if (!$user['enabled']) {
    die(json_encode(['success' => false, 'message' => 'Your account has been disabled']));
}

// Verify password
if (hash('sha256', $password) != $user['password']) {
    die(json_encode(['success' => false, 'message' => 'Invalid email or password']));
}

// Set session
$_SESSION['user_id'] = $user['id'];
$_SESSION['user_name'] = $user['name'];
$_SESSION['user_email'] = $user['email'];
$_SESSION['user_role'] = $user['role'];

echo json_encode([
    'success' => true,
    'message' => 'Login successful',
    'user' => [
        'id' => $user['id'],
        'name' => $user['name'],
        'role' => $user['role']
    ]
]);

$conn->close();
