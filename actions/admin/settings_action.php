<?php
session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

// Security Check: Ensure only logged-in admin can access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

if ($action === 'change_password') {
    $new_password = $_POST['password'] ?? '';

    // Validation
    if (strlen($new_password) < 8) {
        echo json_encode(['status' => 'error', 'message' => 'Password must be at least 8 characters']);
        exit;
    }

    // Securely hash the password
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

    // Update Database
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ? AND role = 'admin'");
    $stmt->bind_param("si", $hashed_password, $user_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Password updated successfully']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $conn->error]);
    }
    $stmt->close();
    exit;
}

echo json_encode(['status' => 'error', 'message' => 'Invalid action']);