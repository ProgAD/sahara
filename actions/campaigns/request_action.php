<?php
session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

$action = $_POST['action'] ?? '';

if ($action === 'check_email') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $result = $conn->query("SELECT name FROM users WHERE email = '$email' LIMIT 1");
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        echo json_encode(['status' => 'exists', 'name' => $user['name']]);
    } else {
        echo json_encode(['status' => 'new']);
    }
    exit;
}

if ($action === 'submit_campaign') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    
    // 1. Get or Create User ID
    $res = $conn->query("SELECT id FROM users WHERE email = '$email'");
    if ($res->num_rows > 0) {
        $user_id = $res->fetch_assoc()['id'];
    } else {
        $dummy_pass = password_hash(bin2hex(random_bytes(8)), PASSWORD_BCRYPT);
        $conn->query("INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$dummy_pass')");
        $user_id = $conn->insert_id;
    }

    // 2. Collect Campaign Data
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $amount = (float)$_POST['amount'];
    $urgency = mysqli_real_escape_string($conn, $_POST['urgency']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $ben_name = mysqli_real_escape_string($conn, $_POST['ben_name']);
    $ben_relation = mysqli_real_escape_string($conn, $_POST['ben_relation']);
    $ben_phone = mysqli_real_escape_string($conn, $_POST['ben_phone']);
    $ben_city = mysqli_real_escape_string($conn, $_POST['ben_city']);

    // 3. Insert Campaign to get the ID
    $sql = "INSERT INTO campaigns (user_id, title, description, amount_needed, category, beneficiary_name, beneficiary_phone, beneficiary_relation, beneficiary_city, urgency, status) 
            VALUES ('$user_id', '$title', '$description', '$amount', '$category', '$ben_name', '$ben_phone', '$ben_relation', '$ben_city', '$urgency', 'pending')";

    if ($conn->query($sql)) {
        $campaign_id = $conn->insert_id;
        
        // 4. Handle Media Uploads
        $uploadBaseDir = "../../assets/campaigns/media/" . $campaign_id . "/";
        
        if (!is_dir($uploadBaseDir)) {
            mkdir($uploadBaseDir, 0777, true);
        }

        // Process Images
        if (isset($_FILES['images'])) {
            foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
                $file_name = "img_" . time() . "_" . $_FILES['images']['name'][$key];
                move_uploaded_file($tmp_name, $uploadBaseDir . $file_name);
            }
        }

        // Process Video
        if (isset($_FILES['video']) && $_FILES['video']['error'] == 0) {
            $video_name = "vid_" . time() . "_" . $_FILES['video']['name'];
            move_uploaded_file($_FILES['video']['tmp_name'], $uploadBaseDir . $video_name);
        }

        echo json_encode(['status' => 'success', 'id' => $campaign_id]);
    } else {
        echo json_encode(['status' => 'error', 'message' => $conn->error]);
    }
    exit;
}