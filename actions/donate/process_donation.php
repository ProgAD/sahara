<?php
session_start();
require_once '../../config/db.php';

// Clear any accidental whitespace or errors before sending JSON
ob_clean();
header('Content-Type: application/json');

$campaign_id = isset($_POST['campaign_id']) ? intval($_POST['campaign_id']) : 0;
$amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
$email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
$name = mysqli_real_escape_string($conn, $_POST['name'] ?? '');
$phone = mysqli_real_escape_string($conn, $_POST['phone'] ?? '');

if ($campaign_id <= 0 || $amount < 100 || empty($email)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data provided.']);
    exit;
}

try {
    $conn->begin_transaction();

    // 1. Calculate remaining
    $calc_sql = "SELECT c.amount_needed, COALESCE(SUM(d.amount), 0) as raised 
                 FROM campaigns c 
                 LEFT JOIN donations d ON c.id = d.campaign_id 
                 WHERE c.id = $campaign_id 
                 GROUP BY c.id";
    $campaign_data = $conn->query($calc_sql)->fetch_assoc();

    if (!$campaign_data) throw new Exception("Campaign not found.");

    $remaining = $campaign_data['amount_needed'] - $campaign_data['raised'];

    if ($amount > $remaining) {
        echo json_encode(['status' => 'error', 'message' => 'Exceeds goal. Max allowed: ₹' . $remaining]);
        $conn->rollback();
        exit;
    }

    // 2. User Logic
    $res = $conn->query("SELECT id FROM users WHERE email = '$email' LIMIT 1");
    if ($res->num_rows > 0) {
        $final_user_id = $res->fetch_assoc()['id'];
    } else {
        $conn->query("INSERT INTO users (name, email, phone, password, role) VALUES ('$name', '$email', '$phone', '', 'user')");
        $final_user_id = $conn->insert_id;
    }

    // 3. Donation Logic
    $stmt_don = $conn->prepare("INSERT INTO donations (campaign_id, user_id, amount, status) VALUES (?, ?, ?, 'pending')");
    $stmt_don->bind_param("iid", $campaign_id, $final_user_id, $amount);
    
    if ($stmt_don->execute()) {
        $donation_id = $conn->insert_id;
        $conn->commit();
        echo json_encode(['status' => 'success', 'donation_id' => $donation_id]);
    } else {
        throw new Exception("Database insertion failed");
    }

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}