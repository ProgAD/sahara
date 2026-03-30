<?php
require_once '../../config/db.php';
header('Content-Type: application/json');

try {
    // Fetch top 6 approved campaigns with their donation totals
    $sql = "SELECT c.id, c.title, c.description, c.category, c.amount_needed, c.urgency, 
            COALESCE(SUM(d.amount), 0) as raised,
            COUNT(d.id) as donor_count
            FROM campaigns c
            LEFT JOIN donations d ON c.id = d.campaign_id
            WHERE c.status = 'approved' AND c.delete_flag = 0
            GROUP BY c.id
            ORDER BY c.urgency DESC, c.created_at DESC
            LIMIT 6";

    $result = $conn->query($sql);
    $campaigns = [];

    while($row = $result->fetch_assoc()) {
        // Find the first image in the campaign folder
        $campaign_id = $row['id'];
        $imagePath = "../../assets/campaigns/media/" . $campaign_id . "/*";
        $files = glob($imagePath);
        
        // Logic to pick the first image found, else use a placeholder
        $web_image = "https://images.unsplash.com/photo-1532629345422-7515f3d16bb8?w=500"; // Default
        foreach($files as $file) {
            $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
            if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $web_image = "assets/campaigns/media/" . $campaign_id . "/" . basename($file);
                break;
            }
        }
        
        $row['img'] = $web_image;
        $campaigns[] = $row;
    }

    echo json_encode($campaigns);

} catch (Exception $e) {
    echo json_encode([]);
}