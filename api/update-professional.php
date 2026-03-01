<?php
// Add error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../includes/config.php';
require_role(['Admin', 'Allocation']);

header('Content-Type: application/json');

// Log the request
error_log("update-professional.php called");

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Get input
$professional_id = isset($_POST['professional_id']) ? intval($_POST['professional_id']) : 0;
$name = isset($_POST['name']) ? trim($_POST['name']) : null;
$email = isset($_POST['email']) ? trim($_POST['email']) : null;
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : null;
$experience = isset($_POST['experience']) ? intval($_POST['experience']) : null;
$status = isset($_POST['status']) ? trim($_POST['status']) : null;
$verify_status = isset($_POST['verify_status']) ? trim($_POST['verify_status']) : null;
$rating = isset($_POST['rating']) ? floatval($_POST['rating']) : null;
$hours = isset($_POST['hours']) ? intval($_POST['hours']) : null;
$skills = isset($_POST['skills']) ? trim($_POST['skills']) : null;

if (!$professional_id) {
    echo json_encode(['success' => false, 'message' => 'Professional ID is required']);
    exit;
}

// Check if professional exists
$check = $conn->prepare("SELECT id, staff_image, id_proof_front, id_proof_back, police_verification FROM professionals WHERE id = ?");
$check->bind_param("i", $professional_id);
$check->execute();
$result = $check->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Professional not found']);
    exit;
}

$professional = $result->fetch_assoc();

// Build update query
$updates = [];
$params = [];
$types = "";

if ($name !== null && $name !== '') {
    $updates[] = "name = ?";
    $params[] = $name;
    $types .= "s";
}
if ($email !== null) {
    $updates[] = "email = ?";
    $params[] = $email;
    $types .= "s";
}
if ($phone !== null && $phone !== '') {
    $updates[] = "phone = ?";
    $params[] = $phone;
    $types .= "s";
}
if ($experience !== null) {
    $updates[] = "experience = ?";
    $params[] = $experience;
    $types .= "i";
}
if ($status !== null && $status !== '') {
    $updates[] = "status = ?";
    $params[] = $status;
    $types .= "s";
}
if ($verify_status !== null && $verify_status !== '') {
    $updates[] = "verify_status = ?";
    $params[] = $verify_status;
    $types .= "s";
}
if ($rating !== null) {
    $updates[] = "rating = ?";
    $params[] = $rating;
    $types .= "d";
}
if ($hours !== null) {
    $updates[] = "hours = ?";
    $params[] = $hours;
    $types .= "i";
}
if ($skills !== null) {
    $updates[] = "skills = ?";
    $params[] = $skills;
    $types .= "s";
}

// Handle file uploads
if (isset($_FILES['staff_image']) && $_FILES['staff_image']['error'] == 0) {
    $file = $_FILES['staff_image'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid staff image format. Allowed: jpg, png, gif']);
        exit;
    }

    if ($file['size'] > 5000000) {
        echo json_encode(['success' => false, 'message' => 'Staff image too large. Max 5MB']);
        exit;
    }

    $upload_dir = '../uploads/professionals/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Delete old staff image
    if (!empty($professional['staff_image']) && file_exists('../' . $professional['staff_image'])) {
        unlink('../' . $professional['staff_image']);
    }

    $new_filename = 'staff_' . time() . '_' . uniqid() . '.' . $ext;
    $upload_path = $upload_dir . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $staff_image = 'uploads/professionals/' . $new_filename;
        $updates[] = "staff_image = ?";
        $params[] = $staff_image;
        $types .= "s";
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload staff image']);
        exit;
    }
}

if (isset($_FILES['id_proof_front']) && $_FILES['id_proof_front']['error'] == 0) {
    $file = $_FILES['id_proof_front'];
    $allowed = ['jpg', 'jpeg', 'png'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID proof front format. Allowed: jpg, png']);
        exit;
    }

    if ($file['size'] > 5000000) {
        echo json_encode(['success' => false, 'message' => 'ID proof front too large. Max 5MB']);
        exit;
    }

    $upload_dir = '../uploads/professionals/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Delete old ID proof front
    if (!empty($professional['id_proof_front']) && file_exists('../' . $professional['id_proof_front'])) {
        unlink('../' . $professional['id_proof_front']);
    }

    $new_filename = 'idproof_front_' . time() . '_' . uniqid() . '.' . $ext;
    $upload_path = $upload_dir . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $id_proof_front = 'uploads/professionals/' . $new_filename;
        $updates[] = "id_proof_front = ?";
        $params[] = $id_proof_front;
        $types .= "s";
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload ID proof front']);
        exit;
    }
}

if (isset($_FILES['id_proof_back']) && $_FILES['id_proof_back']['error'] == 0) {
    $file = $_FILES['id_proof_back'];
    $allowed = ['jpg', 'jpeg', 'png'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid ID proof back format. Allowed: jpg, png']);
        exit;
    }

    if ($file['size'] > 5000000) {
        echo json_encode(['success' => false, 'message' => 'ID proof back too large. Max 5MB']);
        exit;
    }

    $upload_dir = '../uploads/professionals/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Delete old ID proof back
    if (!empty($professional['id_proof_back']) && file_exists('../' . $professional['id_proof_back'])) {
        unlink('../' . $professional['id_proof_back']);
    }

    $new_filename = 'idproof_back_' . time() . '_' . uniqid() . '.' . $ext;
    $upload_path = $upload_dir . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $id_proof_back = 'uploads/professionals/' . $new_filename;
        $updates[] = "id_proof_back = ?";
        $params[] = $id_proof_back;
        $types .= "s";
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload ID proof back']);
        exit;
    }
}

if (isset($_FILES['police_verification']) && $_FILES['police_verification']['error'] == 0) {
    $file = $_FILES['police_verification'];
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Invalid police verification format. Allowed: jpg, png, pdf']);
        exit;
    }

    if ($file['size'] > 5000000) {
        echo json_encode(['success' => false, 'message' => 'Police verification too large. Max 5MB']);
        exit;
    }

    $upload_dir = '../uploads/professionals/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }

    // Delete old police verification
    if (!empty($professional['police_verification']) && file_exists('../' . $professional['police_verification'])) {
        unlink('../' . $professional['police_verification']);
    }

    $new_filename = 'police_verification_' . time() . '_' . uniqid() . '.' . $ext;
    $upload_path = $upload_dir . $new_filename;

    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        $police_verification = 'uploads/professionals/' . $new_filename;
        $updates[] = "police_verification = ?";
        $params[] = $police_verification;
        $types .= "s";
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to upload police verification']);
        exit;
    }
}

if (empty($updates)) {
    echo json_encode(['success' => false, 'message' => 'No fields to update']);
    exit;
}

// Add updated_at and updated_by
$updates[] = "updated_at = NOW()";
$updates[] = "updated_by = ?";
$params[] = $_SESSION['user_id'];
$types .= "i";

// Add professional_id to params for WHERE clause
$params[] = $professional_id;
$types .= "i";

$sql = "UPDATE professionals SET " . implode(', ', $updates) . " WHERE id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param($types, ...$params);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Professional updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating professional: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
