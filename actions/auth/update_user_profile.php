<?php
session_start();
require_once '../../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
    exit;
}

$user_id = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';

// --- ACTION: GET DASHBOARD DATA (Campaigns & Donations) ---
if ($action === 'get_data') {
    // 1. Fetch Campaigns (Using LEFT JOIN so campaigns with 0 donations still show)
    $c_sql = "SELECT c.id, c.title, c.category, c.status, c.amount_needed as goal, c.created_at, 
              COALESCE(SUM(d.amount), 0) as raised
              FROM campaigns c 
              LEFT JOIN donations d ON c.id = d.campaign_id 
              WHERE c.user_id = $user_id AND c.delete_flag = 0 
              GROUP BY c.id ORDER BY c.created_at DESC";
    $campaigns = $conn->query($c_sql)->fetch_all(MYSQLI_ASSOC);

    // 2. Fetch Donations
    $d_sql = "SELECT d.amount, d.created_at, c.title as campaignTitle, c.id as campaignId 
              FROM donations d JOIN campaigns c ON d.campaign_id = c.id 
              WHERE d.user_id = $user_id ORDER BY d.created_at DESC";
    $donations = $conn->query($d_sql)->fetch_all(MYSQLI_ASSOC);

    // 3. Check for existing profile photo
    $photoPath = "../../assets/users/profile/" . $user_id . ".*";
    $files = glob($photoPath);
    // Return relative URL for frontend + timestamp to bypass browser cache
    $photoUrl = !empty($files) ? 'assets/users/profile/' . basename($files[0]) . '?v=' . time() : null;

    echo json_encode(['campaigns' => $campaigns, 'donations' => $donations, 'photoUrl' => $photoUrl]);
    exit;
}

// --- ACTION: UPLOAD PROFILE PHOTO ---
if ($action === 'upload_photo') {
    if (!isset($_FILES['photo'])) exit;

    $targetDir = "../../assets/users/profile/";
    if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

    // Delete any old photo for this user ID regardless of extension
    $oldFiles = glob($targetDir . $user_id . ".*");
    foreach($oldFiles as $f) { if(is_file($f)) unlink($f); }

    $ext = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
    $fileName = $user_id . "." . $ext;
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
        echo json_encode(['status' => 'success', 'url' => 'assets/users/profile/' . $fileName . '?v=' . time()]);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

// --- ACTION: UPDATE PHONE ---
if ($action === 'update_phone') {
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $stmt = $conn->prepare("UPDATE users SET phone = ? WHERE id = ?");
    $stmt->bind_param("si", $phone, $user_id);
    echo json_encode(['status' => $stmt->execute() ? 'success' : 'error']);
    exit;
}

// --- ACTION: CHANGE PASSWORD ---
if ($action === 'change_password') {
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
    $stmt->bind_param("si", $password, $user_id);
    echo json_encode(['status' => $stmt->execute() ? 'success' : 'error']);
    exit;
}