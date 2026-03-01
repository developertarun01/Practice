<?php
require_once '../includes/config.php';
require_role(['Admin', 'Allocation']);

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die(json_encode(['success' => false, 'message' => 'Invalid request method']));
}

// Get input
$name = isset($_POST['name']) ? esc($_POST['name']) : '';
$phone = isset($_POST['phone']) ? esc($_POST['phone']) : '';
$email = isset($_POST['email']) ? esc($_POST['email']) : '';
$service = isset($_POST['service']) ? esc($_POST['service']) : '';
$gender = isset($_POST['gender']) ? esc($_POST['gender']) : '';
$experience = isset($_POST['experience']) ? intval($_POST['experience']) : 0;
$location = isset($_POST['location']) ? esc($_POST['location']) : '';
$status = isset($_POST['status']) ? esc($_POST['status']) : 'Active';
$hours = isset($_POST['hours']) ? intval($_POST['hours']) : 8;
$skills = isset($_POST['skills']) ? esc($_POST['skills']) : '';

// Validation
$errors = [];

if (!$name) $errors[] = 'Name is required';
if (!$phone || !preg_match('/^\d{10}$/', $phone)) $errors[] = 'Valid 10-digit phone is required';
if (!$service) $errors[] = 'Service is required';
if (!$gender) $errors[] = 'Gender is required';

if (!empty($errors)) {
    die(json_encode(['success' => false, 'message' => 'Validation failed', 'errors' => $errors]));
}

// Check if phone already exists
$check = $conn->query("SELECT id FROM professionals WHERE phone = '$phone'");
if ($check->num_rows > 0) {
    die(json_encode(['success' => false, 'message' => 'Phone number already exists']));
}

// Handle file uploads
$staff_image = '';
$id_proof_front = '';
$id_proof_back = '';
$police_verification = '';

// Create uploads directory if it doesn't exist
if (!is_dir('../uploads/professionals')) {
    mkdir('../uploads/professionals', 0755, true);
}

// Upload staff image
if (isset($_FILES['staff_image']) && $_FILES['staff_image']['error'] == 0) {
    $file = $_FILES['staff_image'];
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed) && $file['size'] <= 5000000) { // 5MB max
        $new_filename = 'staff_' . time() . '_' . uniqid() . '.' . $ext;
        $upload_path = '../uploads/professionals/' . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $staff_image = 'uploads/professionals/' . $new_filename;
        } else {
            die(json_encode(['success' => false, 'message' => 'Failed to upload staff image']));
        }
    } else {
        die(json_encode(['success' => false, 'message' => 'Invalid staff image format or size']));
    }
}

// Upload ID proof front
if (isset($_FILES['id_proof_front']) && $_FILES['id_proof_front']['error'] == 0) {
    $file = $_FILES['id_proof_front'];
    $allowed = ['jpg', 'jpeg', 'png'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed) && $file['size'] <= 5000000) { // 5MB max
        $new_filename = 'idproof_front_' . time() . '_' . uniqid() . '.' . $ext;
        $upload_path = '../uploads/professionals/' . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $id_proof_front = 'uploads/professionals/' . $new_filename;
        } else {
            die(json_encode(['success' => false, 'message' => 'Failed to upload ID proof front']));
        }
    } else {
        die(json_encode(['success' => false, 'message' => 'Invalid ID proof front format or size']));
    }
}

// Upload ID proof back
if (isset($_FILES['id_proof_back']) && $_FILES['id_proof_back']['error'] == 0) {
    $file = $_FILES['id_proof_back'];
    $allowed = ['jpg', 'jpeg', 'png'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed) && $file['size'] <= 5000000) { // 5MB max
        $new_filename = 'idproof_back_' . time() . '_' . uniqid() . '.' . $ext;
        $upload_path = '../uploads/professionals/' . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $id_proof_back = 'uploads/professionals/' . $new_filename;
        } else {
            die(json_encode(['success' => false, 'message' => 'Failed to upload ID proof back']));
        }
    } else {
        die(json_encode(['success' => false, 'message' => 'Invalid ID proof back format or size']));
    }
}

// Upload Police verification
if (isset($_FILES['police_verification']) && $_FILES['police_verification']['error'] == 0) {
    $file = $_FILES['police_verification'];
    $allowed = ['jpg', 'jpeg', 'png', 'pdf'];
    $filename = $file['name'];
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (in_array($ext, $allowed) && $file['size'] <= 5000000) { // 5MB max
        $new_filename = 'police_verification_' . time() . '_' . uniqid() . '.' . $ext;
        $upload_path = '../uploads/professionals/' . $new_filename;

        if (move_uploaded_file($file['tmp_name'], $upload_path)) {
            $police_verification = 'uploads/professionals/' . $new_filename;
        } else {
            die(json_encode(['success' => false, 'message' => 'Failed to upload police verification']));
        }
    } else {
        die(json_encode(['success' => false, 'message' => 'Invalid police verification format or size']));
    }
}

// Generate slug for public sharing
$professional_slug = strtolower(str_replace(' ', '-', $name)) . '-' . uniqid();

// Create professional
// Use IGNORE to handle cases where new columns don't exist yet
$sql = "INSERT INTO professionals (name, phone, email, service, gender, experience, location, status, verify_status, hours, skills, staff_image, id_proof_front, id_proof_back, police_verification, professional_slug, updated_by) 
        VALUES ('$name', '$phone', '$email', '$service', '$gender', $experience, '$location', '$status', 'Pending', $hours, '$skills', '$staff_image', '$id_proof_front', '$id_proof_back', '$police_verification', '$professional_slug', " . intval($_SESSION['user_id']) . ")";

if ($conn->query($sql) === TRUE) {
    $prof_id = $conn->insert_id;

    echo json_encode([
        'success' => true,
        'message' => 'Professional added successfully',
        'professional_id' => $prof_id
    ]);
} else {
    echo json_encode(['success' => false, 'message' => 'Error adding professional: ' . $conn->error]);
}

$conn->close();
