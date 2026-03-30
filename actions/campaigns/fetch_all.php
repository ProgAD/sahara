<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

$category = $_GET['category'] ?? 'all';
$search = mysqli_real_escape_string($conn, $_GET['search'] ?? '');
$sort = $_GET['sort'] ?? 'newest';

// Base Query
$sql = "SELECT c.id, c.title, c.description, c.category, c.amount_needed, c.urgency, c.status, c.created_at,
        COALESCE(SUM(d.amount), 0) as raised,
        COUNT(d.id) as donor_count
        FROM campaigns c
        LEFT JOIN donations d ON c.id = d.campaign_id
        WHERE c.status IN ('approved', 'completed') AND c.delete_flag = 0";

// Filtering
if ($category !== 'all') {
    $sql .= " AND c.category = '" . mysqli_real_escape_string($conn, $category) . "'";
}
if (!empty($search)) {
    $sql .= " AND (c.title LIKE '%$search%' OR c.description LIKE '%$search%')";
}

$sql .= " GROUP BY c.id";

// Sorting
switch ($sort) {
    case 'urgent': $sql .= " ORDER BY c.urgency DESC, c.created_at DESC"; break;
    case 'mostFunded': $sql .= " ORDER BY raised DESC"; break;
    case 'leastFunded': $sql .= " ORDER BY raised ASC"; break;
    case 'newest': default: $sql .= " ORDER BY c.created_at DESC"; break;
}

$result = $conn->query($sql);
$campaigns = [];

while($row = $result->fetch_assoc()) {
    // Find first image
    $campaign_id = $row['id'];
    $files = glob("../../assets/campaigns/media/" . $campaign_id . "/*");
    $web_image = "https://images.unsplash.com/photo-1532629345422-7515f3d16bb8?w=500"; 
    foreach($files as $file) {
        if(in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['jpg','jpeg','png','webp'])) {
            $web_image = "assets/campaigns/media/" . $campaign_id . "/" . basename($file);
            break;
        }
    }
    $row['img'] = $web_image;
    $campaigns[] = $row;
}

// Fetch Global Stats for the top bar
$stats = $conn->query("SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status='approved' THEN 1 ELSE 0 END) as active,
    SUM(CASE WHEN status='completed' THEN 1 ELSE 0 END) as completed,
    (SELECT SUM(amount) FROM donations) as total_raised
    FROM campaigns WHERE delete_flag = 0 AND status IN ('approved', 'completed')")->fetch_assoc();

echo json_encode(['campaigns' => $campaigns, 'stats' => $stats]);