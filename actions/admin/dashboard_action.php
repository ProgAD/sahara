<?php
session_start();
require_once '../../config/db.php';

header('Content-Type: application/json');

// 1. Security Gate: Only Admin can access this file
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

$action = $_POST['action'] ?? '';

// --- ACTION: FETCH DASHBOARD DATA ---
if ($action === 'get_data') {
    try {
        // Stats
        $live_count = $conn->query("SELECT COUNT(*) as total FROM campaigns WHERE status = 'approved' AND delete_flag = 0")->fetch_assoc()['total'];
        $pending_review = $conn->query("SELECT COUNT(*) as total FROM campaigns WHERE status = 'pending' AND delete_flag = 0")->fetch_assoc()['total'];
        $total_donations_count = $conn->query("SELECT COUNT(*) as total FROM donations")->fetch_assoc()['total'];
        $total_raised = $conn->query("SELECT SUM(amount) as total FROM donations")->fetch_assoc()['total'] ?? 0;

        // Recent Campaigns
        $campaigns = $conn->query("SELECT c.*, u.name as organizer 
                                   FROM campaigns c 
                                   JOIN users u ON c.user_id = u.id 
                                   WHERE c.delete_flag = 0 
                                   ORDER BY c.created_at DESC LIMIT 6")->fetch_all(MYSQLI_ASSOC);

        // Recent Donations
        $donations = $conn->query("SELECT d.*, u.name as donor, u.email, u.phone, c.title as campaign_title 
                                   FROM donations d 
                                   JOIN users u ON d.user_id = u.id 
                                   JOIN campaigns c ON d.campaign_id = c.id 
                                   ORDER BY d.created_at DESC LIMIT 6")->fetch_all(MYSQLI_ASSOC);

        echo json_encode([
            'status' => 'success',
            'stats' => [
                'live' => $live_count,
                'pending' => $pending_review,
                'donations' => $total_donations_count,
                'raised' => $total_raised
            ],
            'campaigns' => $campaigns,
            'donations' => $donations
        ]);
    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => 'Query Failed']);
    }
    exit;
}

// --- ACTION: UPDATE STATUS ---
elseif ($action === 'update_status') {
    $id = intval($_POST['id']);
    $type = $_POST['type']; 
    $new_status = mysqli_real_escape_string($conn, $_POST['status']);
    
    $table = ($type === 'campaign') ? 'campaigns' : 'donations';
    $timestamp_col = "";
    
    if ($type === 'campaign') {
        if ($new_status === 'approved') $timestamp_col = ", approved_at = NOW()";
        if ($new_status === 'rejected') $timestamp_col = ", rejected_at = NOW()";
    }

    $sql = "UPDATE $table SET status = '$new_status' $timestamp_col WHERE id = $id";
    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}

// --- ACTION: DELETE ---
elseif ($action === 'delete') {
    $id = intval($_POST['id']);
    $type = $_POST['type'];
    
    if ($type === 'campaign') {
        $sql = "UPDATE campaigns SET delete_flag = 1 WHERE id = $id";
    } else {
        $sql = "DELETE FROM donations WHERE id = $id";
    }

    if ($conn->query($sql)) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error']);
    }
    exit;
}