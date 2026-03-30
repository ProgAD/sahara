<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$email = $_POST['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error']);
    exit;
}

$stmt = $conn->prepare("SELECT name, phone FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($user = $result->fetch_assoc()) {
    echo json_encode(['status' => 'found', 'name' => $user['name'], 'phone' => $user['phone']]);
} else {
    echo json_encode(['status' => 'not_found']);
}