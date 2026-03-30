<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['name'] ?? null;
$user_email = $_SESSION['email'] ?? null;
$user_phone = $_SESSION['phone'] ?? null;
$role = $_SESSION['role'] ?? null;

$current_page = "campaign.php";

require_once 'config/db.php';

// 1. Get ID and redirect if missing
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id <= 0) {
    header('Location: all-campaigns.php');
    exit;
}

// 2. Fetch Campaign Data with Organizer info
$query = "SELECT c.*, u.name as organizer_name, u.role as organizer_role, u.created_at as organizer_since 
          FROM campaigns c 
          JOIN users u ON c.user_id = u.id 
          WHERE c.id = ? AND c.delete_flag = 0 AND c.status != 'pending' LIMIT 1";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$campaign = $stmt->get_result()->fetch_assoc();

// 3. Handle "Not Found"
if (!$campaign) {
    echo "<div style='text-align:center; padding:100px; font-family:sans-serif;'><h1>Campaign Not Found</h1><a href='all-campaigns.php'>Back to Campaigns</a></div>";
    exit;
}


// campaign variables 
$campaign_name = $campaign['name'];
$campaign_description = $campaign['description'];
$campaign_amount_needed = $campaign['amount_needed'];
$campaign_status = $campaign['status'];
$campaign_created_at = $campaign['created_at'];
$campaign_organizer_name = $campaign['organizer_name'];
$campaign_organizer_role = $campaign['organizer_role'];
$campaign_organizer_since = $campaign['organizer_since'];

// 4. Fetch Donations and Donor Info
$donations_query = "SELECT d.amount, d.created_at, u.name as donor_name 
                   FROM donations d 
                   JOIN users u ON d.user_id = u.id 
                   WHERE d.campaign_id = ? 
                   ORDER BY d.created_at DESC";
$stmt_don = $conn->prepare($donations_query);
$stmt_don->bind_param("i", $id);
$stmt_don->execute();
$donations_res = $stmt_don->get_result();
$donations = $donations_res->fetch_all(MYSQLI_ASSOC);

// 5. Calculations
$total_raised = 0;
foreach($donations as $don) { $total_raised += $don['amount']; }
$goal = $campaign['amount_needed'];
$remaining = $goal - $total_raised;
$donor_count = count($donations);
$is_completed = ($total_raised >= $goal || $campaign['status'] == 'completed');

// 6. Media Fetch
$media_path = "assets/campaigns/media/" . $id . "/";
$media_files = is_dir($media_path) ? array_diff(scandir($media_path), array('.', '..')) : [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campaign Details – SAHARA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sun: #F97316;
            --sun-light: #FB923C;
            --sun-glow: #FDBA74;
            --amber: #F59E0B;
            --cream: #FFFBF5;
            --warm-white: #FEF7ED;
            --peach: #FED7AA;
            --soft-orange: #FFEDD5;
            --earth: #292524;
            --stone: #57534E;
            --stone-light: #A8A29E;
            --success: #22C55E;
            --success-light: #DCFCE7;
            --error: #EF4444;
            --radius-sm: 12px;
            --radius-md: 20px;
            --radius-lg: 32px;
            --radius-xl: 48px;
            --ease-out: cubic-bezier(0.16, 1, 0.3, 1);
            --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
            --font-heading: 'Playfair Display', serif;
            --font-body: 'Outfit', sans-serif;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; font-size: 16px; }

        body {
            font-family: var(--font-body);
            background: var(--cream);
            color: var(--earth);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
            opacity: 0.03;
            pointer-events: none;
            z-index: 9999;
        }

        h1, h2, h3, h4, h5 { font-family: var(--font-heading); font-weight: 700; line-height: 1.15; letter-spacing: -0.02em; }
        p { color: var(--stone); }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; display: block; }

        .container { width: 100%; max-width: 1400px; margin: 0 auto; padding: 0 clamp(1.25rem, 5vw, 3rem); }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            font-family: var(--font-body);
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            border-radius: 100px;
            cursor: pointer;
            transition: all 0.4s var(--ease-out);
        }

        .btn-sun {
            background: linear-gradient(135deg, var(--sun) 0%, var(--amber) 100%);
            color: white;
            box-shadow: 0 8px 32px -8px rgba(249, 115, 22, 0.5);
        }

        .btn-sun:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 40px -8px rgba(249, 115, 22, 0.6);
        }

        .btn-sun:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .btn-ghost {
            background: transparent;
            color: var(--earth);
            border: 2px solid rgba(0,0,0,0.1);
        }

        .btn-ghost:hover { border-color: var(--sun); color: var(--sun); }


        .page-content { padding-top: 80px; min-height: 100vh; }

        /* Campaign Hero Header */
        .campaign-hero {
            background: linear-gradient(135deg, var(--earth) 0%, #1C1917 100%);
            padding: 2.5rem 0;
            margin-bottom: 2rem;
        }

        .campaign-hero-content { display: flex; flex-wrap: wrap; align-items: center; gap: 1.5rem; }

        .campaign-badges { display: flex; gap: 0.5rem; flex-wrap: wrap; }

        .badge {
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 100px;
            background: rgba(255,255,255,0.15);
            color: white;
        }

        .badge.urgent { background: var(--error); }
        .badge.completed { background: var(--success); }
        .badge.category { background: var(--sun); }

        /* Campaign ID Badge */
        .badge.campaign-id {
            background: rgba(255,255,255,0.12);
            border: 1px solid rgba(255,255,255,0.25);
            font-family: var(--font-body);
            font-size: 0.7rem;
            letter-spacing: 0.08em;
            color: rgba(255,255,255,0.85);
        }

        .campaign-hero-title { flex: 1; min-width: 280px; }
        .campaign-hero-title h1 { font-size: clamp(1.5rem, 4vw, 2.25rem); color: white; margin-bottom: 0.5rem; }

        .campaign-meta-row { display: flex; flex-wrap: wrap; gap: 1.5rem; }
        .meta-item { display: flex; align-items: center; gap: 0.5rem; font-size: 0.9rem; color: rgba(255,255,255,0.7); }
        .meta-item svg { width: 18px; height: 18px; color: var(--sun); }

        /* Campaign Layout */
        .campaign-layout { display: grid; grid-template-columns: 1fr 380px; gap: 2rem; padding-bottom: 4rem; }
        .campaign-main { display: flex; flex-direction: column; gap: 2rem; }

        /* Media Section */
        .media-section { background: white; border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: 0 4px 24px rgba(0,0,0,0.04); }

        .media-slider {
            position: relative;
            border-radius: var(--radius-md);
            overflow: hidden;
            aspect-ratio: 16/9;
            background: var(--stone-light);
            margin-bottom: 1rem;
        }

        .media-slider-track { display: flex; height: 100%; transition: transform 0.5s var(--ease-out); }
        .media-slide { min-width: 100%; height: 100%; position: relative; }
        .media-slide img, .media-slide video { width: 100%; height: 100%; object-fit: cover; }
        .media-slide video { background: black; }

        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: white;
            border: none;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10;
            transition: all 0.3s var(--ease-out);
        }

        .slider-btn:hover { transform: translateY(-50%) scale(1.1); box-shadow: 0 6px 20px rgba(0,0,0,0.2); }
        .slider-btn svg { width: 24px; height: 24px; color: var(--earth); }
        .slider-btn.prev { left: 1rem; }
        .slider-btn.next { right: 1rem; }

        .slider-dots { position: absolute; bottom: 1rem; left: 50%; transform: translateX(-50%); display: flex; gap: 0.5rem; }
        .slider-dot { width: 10px; height: 10px; border-radius: 50%; background: rgba(255,255,255,0.5); border: none; cursor: pointer; transition: all 0.3s; }
        .slider-dot.active { background: white; transform: scale(1.2); }

        .media-thumbnails { display: grid; grid-template-columns: repeat(auto-fill, minmax(80px, 1fr)); gap: 0.75rem; }

        .media-thumb {
            aspect-ratio: 1;
            border-radius: var(--radius-sm);
            overflow: hidden;
            cursor: pointer;
            border: 3px solid transparent;
            transition: all 0.3s var(--ease-out);
            position: relative;
        }

        .media-thumb:hover { transform: scale(1.05); }
        .media-thumb.active { border-color: var(--sun); }
        .media-thumb img, .media-thumb video { width: 100%; height: 100%; object-fit: cover; }

        .media-thumb.video-thumb::after {
            content: '▶';
            position: absolute;
            top: 50%; left: 50%;
            transform: translate(-50%, -50%);
            width: 28px; height: 28px;
            background: rgba(0,0,0,0.7);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
        }

        /* Campaign Description */
        .campaign-description { background: white; border-radius: var(--radius-lg); padding: 2rem; box-shadow: 0 4px 24px rgba(0,0,0,0.04); }
        .campaign-description h3 { font-size: 1.25rem; margin-bottom: 1rem; color: var(--earth); }
        .campaign-description p { font-size: 1rem; line-height: 1.8; margin-bottom: 1rem; }
        .campaign-description p:last-child { margin-bottom: 0; }

        /* Beneficiary Card */
        .beneficiary-card {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            padding: 1.25rem;
            background: var(--warm-white);
            border-radius: var(--radius-md);
            border-left: 4px solid var(--sun);
            margin-top: 1.5rem;
        }

        .beneficiary-avatar {
            width: 52px; height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--sun-glow), var(--peach));
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .beneficiary-avatar svg { width: 24px; height: 24px; color: var(--sun); }

        .beneficiary-info { flex: 1; min-width: 0; }
        .beneficiary-label {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--sun);
            margin-bottom: 0.375rem;
        }
        .beneficiary-name { font-family: var(--font-body); font-size: 1rem; font-weight: 600; color: var(--earth); margin-bottom: 0.25rem; }
        .beneficiary-details { display: flex; flex-wrap: wrap; gap: 1rem; }
        .beneficiary-detail { display: flex; align-items: center; gap: 0.375rem; font-size: 0.85rem; color: var(--stone); }
        .beneficiary-detail svg { width: 14px; height: 14px; color: var(--stone-light); flex-shrink: 0; }

        /* Organizer Section */
        .organizer-section { background: white; border-radius: var(--radius-lg); padding: 2rem; box-shadow: 0 4px 24px rgba(0,0,0,0.04); }
        .organizer-section h3 { font-size: 1.25rem; margin-bottom: 1.5rem; color: var(--earth); }
        .organizer-card { display: flex; align-items: center; gap: 1rem; }
        .organizer-avatar { width: 56px; height: 56px; border-radius: 50%; background: linear-gradient(135deg, var(--sun), var(--amber)); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.25rem; font-weight: 700; }
        .organizer-info h4 { font-family: var(--font-body); font-size: 1rem; font-weight: 600; color: var(--earth); margin-bottom: 0.25rem; }
        .organizer-info p { font-size: 0.85rem; color: var(--stone-light); }
        .verified-badge { display: inline-flex; align-items: center; gap: 0.25rem; font-size: 0.75rem; color: var(--success); font-weight: 600; margin-top: 0.25rem; }
        .verified-badge svg { width: 14px; height: 14px; }

        /* Donors Section */
        .donors-section { background: white; border-radius: var(--radius-lg); padding: 2rem; box-shadow: 0 4px 24px rgba(0,0,0,0.04); }
        .donors-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
        .donors-header h3 { font-size: 1.25rem; color: var(--earth); }
        .donors-count { font-size: 0.9rem; color: var(--stone-light); }
        .donors-list { display: flex; flex-direction: column; gap: 1rem; }

        .donor-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--cream);
            border-radius: var(--radius-sm);
            transition: transform 0.3s var(--ease-out);
        }

        .donor-item:hover { transform: translateX(4px); }

        .donor-avatar {
            width: 44px; height: 44px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--peach), var(--soft-orange));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            font-weight: 600;
            color: var(--sun);
            flex-shrink: 0;
        }

        .donor-info { flex: 1; min-width: 0; }
        .donor-name { font-weight: 600; font-size: 0.95rem; color: var(--earth); margin-bottom: 0.125rem; }
        .donor-time { font-size: 0.8rem; color: var(--stone-light); }
        .donor-amount { font-weight: 700; font-size: 1rem; color: var(--sun); }

        .show-more-donors {
            width: 100%;
            padding: 0.875rem;
            margin-top: 1rem;
            background: var(--cream);
            border: 2px dashed rgba(0,0,0,0.1);
            border-radius: var(--radius-sm);
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--stone);
            cursor: pointer;
            transition: all 0.3s var(--ease-out);
        }

        .show-more-donors:hover { border-color: var(--sun); color: var(--sun); }

        /* Sidebar */
        .campaign-sidebar { display: flex; flex-direction: column; gap: 1.5rem; }

        .donation-card { background: white; border-radius: var(--radius-lg); padding: 2rem; box-shadow: 0 8px 32px rgba(0,0,0,0.08); }

        .progress-section { margin-bottom: 1.5rem; }
        .progress-amounts { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 0.75rem; }
        .raised-amount { font-size: 1.75rem; font-weight: 800; color: var(--sun); }
        .goal-amount { font-size: 0.9rem; color: var(--stone-light); }

        .progress-bar-bg { height: 12px; background: var(--soft-orange); border-radius: 100px; overflow: hidden; margin-bottom: 0.75rem; }

        .progress-bar-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--sun), var(--amber));
            border-radius: 100px;
            position: relative;
            transition: width 1s var(--ease-out);
        }

        .progress-bar-fill::after {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes shimmer { 0% { transform: translateX(-100%); } 100% { transform: translateX(100%); } }

        .progress-stats { display: flex; justify-content: space-between; }
        .stat-item { text-align: center; }
        .stat-value { font-size: 1.25rem; font-weight: 700; color: var(--earth); }
        .stat-label { font-size: 0.75rem; color: var(--stone-light); text-transform: uppercase; letter-spacing: 0.5px; }

        .donation-amounts { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; margin-bottom: 1rem; }

        .amount-option {
            padding: 0.875rem;
            background: var(--cream);
            border: 2px solid transparent;
            border-radius: var(--radius-sm);
            font-size: 1rem;
            font-weight: 600;
            color: var(--earth);
            cursor: pointer;
            transition: all 0.3s var(--ease-out);
            text-align: center;
        }

        .amount-option:hover { border-color: var(--sun-light); }
        .amount-option.selected { background: var(--soft-orange); border-color: var(--sun); color: var(--sun); }

        .custom-amount { margin-bottom: 1.25rem; }
        .custom-amount label { display: block; font-size: 0.85rem; font-weight: 500; color: var(--stone); margin-bottom: 0.5rem; }
        .amount-input-wrapper { position: relative; }
        .amount-input-wrapper .currency { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-weight: 600; color: var(--stone); }

        .amount-input {
            width: 100%;
            padding: 1rem 1rem 1rem 2.25rem;
            font-size: 1.1rem;
            font-weight: 600;
            border: 2px solid rgba(0,0,0,0.08);
            border-radius: var(--radius-sm);
            background: white;
            color: var(--earth);
            transition: all 0.3s var(--ease-out);
        }

        .amount-input:focus { outline: none; border-color: var(--sun); box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1); }

        /* Donor Info Form */
        .donor-form { margin-bottom: 1.5rem; }
        .donor-form-title {
            font-family: var(--font-body);
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--earth);
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .donor-form-title svg { width: 16px; height: 16px; color: var(--sun); }

        .form-group { margin-bottom: 0.75rem; }
        .form-group:last-child { margin-bottom: 0; }

        .form-input {
            width: 100%;
            padding: 0.875rem 1rem;
            font-family: var(--font-body);
            font-size: 0.95rem;
            font-weight: 500;
            border: 2px solid rgba(0,0,0,0.08);
            border-radius: var(--radius-sm);
            background: white;
            color: var(--earth);
            transition: all 0.3s var(--ease-out);
        }

        .form-input::placeholder { color: var(--stone-light); font-weight: 400; }
        .form-input:focus { outline: none; border-color: var(--sun); box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1); }
        .form-input.error { border-color: var(--error); box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.1); }

        .form-error {
            font-size: 0.75rem;
            color: var(--error);
            margin-top: 0.25rem;
            display: none;
        }

        .form-error.show { display: block; }

        .donate-btn { width: 100%; padding: 1.125rem; font-size: 1.1rem; }

        /* Share Section */
        .share-section { background: white; border-radius: var(--radius-lg); padding: 1.5rem; box-shadow: 0 4px 24px rgba(0,0,0,0.04); }
        .share-section h4 { font-family: var(--font-body); font-size: 0.9rem; font-weight: 600; color: var(--earth); margin-bottom: 1rem; text-align: center; }
        .share-buttons { display: flex; justify-content: center; gap: 0.75rem; }

        .share-btn {
            width: 44px; height: 44px;
            border-radius: 50%;
            border: 2px solid rgba(0,0,0,0.08);
            background: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s var(--ease-out);
        }

        .share-btn svg { width: 18px; height: 18px; color: var(--stone); transition: color 0.3s; }
        .share-btn:hover { transform: translateY(-2px); border-color: var(--sun); }
        .share-btn:hover svg { color: var(--sun); }
        .share-btn.whatsapp:hover { border-color: #25D366; }
        .share-btn.whatsapp:hover svg { color: #25D366; }
        .share-btn.twitter:hover { border-color: #1DA1F2; }
        .share-btn.twitter:hover svg { color: #1DA1F2; }
        .share-btn.facebook:hover { border-color: #1877F2; }
        .share-btn.facebook:hover svg { color: #1877F2; }
        .share-btn.instagram:hover { border-color: #E4405F; }
        .share-btn.instagram:hover svg { color: #E4405F; }

        /* Donation Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.6);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 2000;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s var(--ease-out);
            padding: 1rem;
        }

        .modal-overlay.show { opacity: 1; visibility: visible; }

        .modal-content {
            background: white;
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            max-width: 420px;
            width: 100%;
            text-align: center;
            transform: scale(0.9);
            transition: transform 0.4s var(--ease-spring);
        }

        .modal-overlay.show .modal-content { transform: scale(1); }

        .modal-icon {
            width: 80px; height: 80px;
            background: linear-gradient(135deg, var(--success), #10b981);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
        }

        .modal-icon svg { width: 40px; height: 40px; color: white; }
        .modal-content h2 { font-size: 1.5rem; color: var(--earth); margin-bottom: 0.75rem; }
        .modal-content p { font-size: 1rem; color: var(--stone); margin-bottom: 1.5rem; }
        .modal-amount { font-size: 2rem; font-weight: 800; color: var(--sun); margin-bottom: 1rem; }
        .modal-donor-info { background: var(--cream); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1rem; text-align: left; }
        .modal-donor-info p { font-size: 0.85rem; color: var(--stone); margin: 0.25rem 0; }
        .modal-donor-info strong { color: var(--earth); }
        .modal-note { background: var(--soft-orange); border-radius: var(--radius-sm); padding: 1rem; margin-bottom: 1.5rem; }
        .modal-note p { font-size: 0.9rem; color: var(--stone); margin: 0; }
        .modal-btn { width: 100%; }

        /* Footer (matches campaigns.html) */
        .footer {
            background: linear-gradient(180deg, #1C1917 0%, #0C0A09 100%);
            color: white;
            padding: 3.5rem 0 1.5rem;
        }

        .footer-main { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 3rem; align-items: start; }
        .footer-brand { display: flex; flex-direction: column; align-items: flex-start; }
        .footer-brand-top { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; }
        .footer-brand-top img { width: 56px; height: 56px; border-radius: 50%; border: 2px solid rgba(249, 115, 22, 0.3); }
        .footer-brand h3 { font-size: 1.5rem; color: var(--peach); font-weight: 700; }
        .footer-brand-tagline { font-size: 0.95rem; color: var(--peach); font-weight: 500; margin-bottom: 0.25rem; }
        .footer-brand-sub { font-size: 0.75rem; color: rgba(255,255,255,0.4); margin-bottom: 1.25rem; }
        .btn-join { padding: 0.75rem 2rem; background: #10B981; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: all 0.3s var(--ease-out); }
        .btn-join:hover { background: #059669; transform: translateY(-2px); box-shadow: 0 8px 20px -4px rgba(16, 185, 129, 0.4); }

        .footer-col h4 { font-family: var(--font-body); font-size: 1rem; font-weight: 600; color: white; margin-bottom: 1.25rem; }
        .footer-col ul { list-style: none; }
        .footer-col li { margin-bottom: 0.625rem; }
        .footer-col a { color: rgba(255,255,255,0.55); font-size: 0.875rem; transition: all 0.3s; display: inline-flex; align-items: center; gap: 0.5rem; }
        .footer-col a:hover { color: var(--sun-glow); }
        .footer-col a svg { width: 16px; height: 16px; flex-shrink: 0; }
        .footer-bottom { margin-top: 2.5rem; padding-top: 1.25rem; border-top: 1px solid rgba(255,255,255,0.08); display: flex; justify-content: center; align-items: center; }
        .footer-bottom p { font-size: 0.8rem; color: rgba(255,255,255,0.35); display: flex; align-items: center; gap: 0.35rem; }
        .footer-bottom .heart { color: #EF4444; animation: heartbeat 1.5s ease-in-out infinite; }
        @keyframes heartbeat { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }

        /* Responsive */
        @media (max-width: 1200px) {
            .footer-main { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 1024px) {
            .campaign-layout { grid-template-columns: 1fr; }
            .campaign-sidebar { order: -1; }
        }

        @media (max-width: 768px) {
            .footer-main { grid-template-columns: 1fr; gap: 2rem; text-align: center; }
            .footer-brand { align-items: center; }
            .footer-bottom { flex-direction: column; text-align: center; }
            .campaign-hero { padding: 1.5rem 0; }
            .campaign-hero-title h1 { font-size: 1.5rem; }
            .campaign-meta-row { gap: 1rem; }
            .donation-amounts { grid-template-columns: repeat(2, 1fr); }
            .media-thumbnails { grid-template-columns: repeat(auto-fill, minmax(60px, 1fr)); }
        }

        @media (max-width: 480px) {
            html { font-size: 15px; }
            .campaign-meta-row { flex-direction: column; gap: 0.5rem; }
            .progress-stats { flex-wrap: wrap; gap: 1rem; }
            .stat-item { flex: 1; min-width: 80px; }
            .slider-btn { width: 36px; height: 36px; }
            .slider-btn svg { width: 18px; height: 18px; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <main class="page-content">
        <!-- Campaign Hero Header -->
        <div class="campaign-hero">
            <div class="container">
                <div class="campaign-hero-content">
                    <div class="campaign-badges">
                        <span class="badge campaign-id">ID: #<?php echo $id; ?></span>
                        <span class="badge category"><?php echo htmlspecialchars($campaign['category']); ?></span>
                        <?php if($campaign['urgency'] == 'high'): ?>
                            <span class="badge urgent">🔥 Urgent</span>
                        <?php endif; ?>
                        <?php if($is_completed): ?>
                            <span class="badge completed">✓ Completed</span>
                        <?php endif; ?>
                    </div>
                    <div class="campaign-hero-title">
                        <h1 id="campaignTitle">Medical Emergency – Rahul's Surgery</h1>
                        <div class="campaign-meta-row">
                            <div class="meta-item">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Started <span id="campaignDate">March 25, 2026</span></span>
                            </div>
                            <!-- <div class="meta-item">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span><span id="daysLeft">5</span> days left</span>
                            </div> -->
                            <div class="meta-item">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span id="campaignLocation">Chennai, Tamil Nadu</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="container">
            <div class="campaign-layout">
                <!-- Main Content -->
                <div class="campaign-main">
                    <!-- Media Section -->
                    <div class="media-section">
                        <div class="media-slider">
                            <div class="media-slider-track" id="sliderTrack"></div>
                            <button class="slider-btn prev" id="prevBtn">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg>
                            </button>
                            <button class="slider-btn next" id="nextBtn">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg>
                            </button>
                            <div class="slider-dots" id="sliderDots"></div>
                        </div>
                        <div class="media-thumbnails" id="mediaThumbnails"></div>
                    </div>

                    <!-- Campaign Description -->
                    <div class="campaign-description">
                        <h3>About this Campaign</h3>
                        <div id="campaignDescription">
                            <?php echo nl2br(htmlspecialchars($campaign['description'])); ?>
                        </div>

                        <!-- Beneficiary Details -->
                        <div class="beneficiary-card">
                            <div class="beneficiary-avatar">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="beneficiary-info">
                                <div class="beneficiary-label">Beneficiary Details</div>
                                <div class="beneficiary-name"><?php echo htmlspecialchars($campaign['beneficiary_name']); ?></div>
                                <div class="beneficiary-details">
                                    <div class="beneficiary-detail">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                        <span>Relation: <?php echo htmlspecialchars($campaign['beneficiary_relation']); ?></span>
                                    </div>
                                    <div class="beneficiary-detail">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span><?php echo htmlspecialchars($campaign['beneficiary_phone']); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Organizer Section -->
                    <div class="organizer-section">
                        <h3>Campaign Organizer</h3>
                        <div class="organizer-card">
                            <div class="organizer-avatar">
                                <?php echo strtoupper(substr($campaign['organizer_name'], 0, 1)); ?>
                            </div>
                            <div class="organizer-info">
                                <h4><?php echo htmlspecialchars($campaign['organizer_name']); ?></h4>
                                <p>Member since <?php echo date('M Y', strtotime($campaign['organizer_since'])); ?></p>
                                <div class="verified-badge">✓ Verified Organizer</div>
                            </div>
                        </div>
                    </div>

                    <!-- Donors Section -->
                    <div class="donors-section">
                        <div class="donors-header">
                            <h3>Recent Donors</h3>
                            <span class="donors-count" id="totalDonors">312 people have donated</span>
                        </div>
                        <div class="donors-list" id="donorsList"></div>
                        <button class="show-more-donors" id="showMoreDonors">Show All Donors</button>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="campaign-sidebar">
                    <!-- Donation Card -->
                    <div class="donation-card">
                        <div class="progress-section">
                            <div class="progress-amounts">
                                <span class="raised-amount" id="raisedAmount">₹<?php echo number_format($total_raised); ?></span>
                                <span class="goal-amount">raised of <span style="color: var(--sun); font-weight: bold; font-size: 1.1rem;">₹<?php echo number_format($goal); ?></span></span>
                            </div>
                            <div class="progress-bar-bg">
                                <?php $percent_val = $goal > 0 ? min(100, round(($total_raised / $goal) * 100)) : 0; ?>
                                <div class="progress-bar-fill" id="progressBar" style="width: <?php echo $percent_val; ?>%"></div>
                            </div>
                            <div class="progress-stats">
                                <div class="stat-item">
                                    <div class="stat-value" id="donorCount"><?php echo $donor_count; ?></div>
                                    <div class="stat-label">Donors</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value" id="percentFunded"><?php echo $percent_val; ?>%</div>
                                    <div class="stat-label">Funded</div>
                                </div>
                            </div>
                        </div>

                        <?php if (!$is_completed): ?>
                            <div class="donation-amounts">
                                <?php 
                                    $presets = [500, 1000, 2000, 5000, 10000, 25000];
                                    foreach($presets as $amt) {
                                        // Only show preset if it's less than or equal to the remaining balance
                                        if ($amt <= $remaining) {
                                            echo "<button class='amount-option' data-amount='$amt'>₹".number_format($amt)."</button>";
                                        }
                                    }
                                ?>
                            </div>

                            <div class="custom-amount">
                                <label for="customAmount">Or enter custom amount</label>
                                <div class="amount-input-wrapper">
                                    <span class="currency">₹</span>
                                    <input type="number" id="customAmount" class="amount-input" placeholder="Min ₹100" min="100" max="<?php echo $remaining; ?>">
                                </div>
                            </div>

                            <div class="donor-form">
                                <div class="donor-form-title">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    Your Details
                                </div>
                                
                                <div class="form-group" style="position: relative;">
                                    <input type="email" id="donorEmail" class="form-input" placeholder="Email Address" 
                                        value="<?php echo htmlspecialchars($user_email ?? ''); ?>" 
                                        <?php echo !empty($user_email) ? 'disabled' : ''; ?> required>
                                    <div class="form-error" id="emailError">Please enter a valid email</div>
                                </div>

                                <div class="form-group">
                                    <input type="text" id="donorName" class="form-input" placeholder="Full Name" 
                                        value="<?php echo htmlspecialchars($user_name ?? ''); ?>" 
                                        <?php echo !empty($user_name) ? 'disabled' : ''; ?> required>
                                    <div class="form-error" id="nameError">Please enter your name</div>
                                </div>

                                <div class="form-group">
                                    <input type="tel" id="donorPhone" class="form-input" placeholder="Phone Number (10 digits)" 
                                        value="<?php echo htmlspecialchars($user_phone ?? ''); ?>" 
                                        <?php echo (!empty($user_phone)) ? 'disabled' : ''; ?> required>
                                    <div class="form-error" id="phoneError">Please enter a 10-digit phone number</div>
                                </div>
                            </div>

                            <button class="btn btn-sun donate-btn" id="donateBtn">
                                <span>Donate Now</span>
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:20px;height:20px;"><path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            </button>

                        <?php else: ?>
                            <div style="text-align: center; padding: 1.5rem; background: var(--success-light); border-radius: var(--radius-md); border: 1px solid var(--success);">
                                <div style="font-size: 2rem; margin-bottom: 0.5rem;">🎉</div>
                                <h4 style="color: #065F46; font-family: var(--font-body); font-weight: 700;">Goal Reached!</h4>
                                <p style="font-size: 0.85rem; color: #065F46; margin-top: 0.5rem;">This campaign has successfully raised its target amount. Thank you to all the donors!</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Share Section -->
                    <div class="share-section">
                        <h4>Share this Campaign</h4>
                        <div class="share-buttons">
                            <button class="share-btn whatsapp" title="Share on WhatsApp" onclick="shareWhatsApp()">
                                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                            </button>
                            <button class="share-btn twitter" title="Share on X/Twitter" onclick="shareTwitter()">
                                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                            </button>
                            <button class="share-btn facebook" title="Share on Facebook" onclick="shareFacebook()">
                                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                            </button>
                            <button class="share-btn instagram" title="Share on Instagram" onclick="shareInstagram()">
                                <svg fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                            </button>
                            <button class="share-btn copy" title="Copy Link" onclick="copyLink()">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Donation Modal -->
    <div class="modal-overlay" id="donationModal">
        <div class="modal-content">
            <div class="modal-icon">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
            </div>
            <h2>Donation Request Received!</h2>
            <div class="modal-amount" id="modalAmount">₹2,000</div>
            <div class="modal-donor-info" id="modalDonorInfo">
                <p><strong>Donation ID:</strong> <span id="modalDonationId" style="color: var(--sun); font-weight: bold;"></span></p> <p><strong>Name:</strong> <span id="modalDonorName"></span></p>
                <p><strong>Phone:</strong> <span id="modalDonorPhone"></span></p>
                <p><strong>Email:</strong> <span id="modalDonorEmail"></span></p>
                <p><strong>Campaign ID:</strong> <span id="modalCampaignId"></span></p>
            </div>
            <div class="modal-note">
                <p>🙏 <strong>SAHARA team will reach out to you soon</strong> with payment details for your generous donation.</p>
            </div>
            <p>Thank you for supporting this campaign. Your kindness makes a difference!</p>
            <button class="btn btn-sun modal-btn" id="closeModal" onclick="window.location.reload()">Got it!</button>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script>
        // Add these to your goToSlide function or share functions
        function shareTwitter() {
            const text = encodeURIComponent(`Supporting ${document.getElementById('campaignTitle').innerText} on SAHARA!`);
            window.open(`https://twitter.com/intent/tweet?text=${text}&url=${encodeURIComponent(window.location.href)}`, '_blank');
        }

        function shareFacebook() {
            window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(window.location.href)}`, '_blank');
        }

        // Data from PHP
        const mediaFiles = <?php echo json_encode(array_values($media_files)); ?>;
        const campaignId = <?php echo $id; ?>;
        const donors = <?php echo json_encode($donations); ?>;

        // 1. Media Logic
        const sliderTrack = document.getElementById('sliderTrack');
        const thumbnails = document.getElementById('mediaThumbnails');
        const mediaPath = `assets/campaigns/media/${campaignId}/`;

        if (mediaFiles.length > 0) {
            sliderTrack.innerHTML = mediaFiles.map(file => {
                const isVideo = file.toLowerCase().endsWith('.mp4');
                return `<div class="media-slide">
                    ${isVideo ? `<video src="${mediaPath}${file}" controls></video>` : `<img src="${mediaPath}${file}">`}
                </div>`;
            }).join('');

            thumbnails.innerHTML = mediaFiles.map((file, i) => `
                <div class="media-thumb ${i === 0 ? 'active' : ''}" data-index="${i}">
                    <img src="${mediaPath}${file}">
                </div>`).join('');
        }

        // 2. Donation List Rendering
        function renderDonors(showAll = false) {
            const list = document.getElementById('donorsList');
            const display = showAll ? donors : donors.slice(0, 5);
            if (donors.length === 0) {
                list.innerHTML = "<p>No donations yet. Be the first to support!</p>";
                return;
            }
            list.innerHTML = display.map(d => `
                <div class="donor-item">
                    <div class="donor-avatar">${d.donor_name.charAt(0)}</div>
                    <div class="donor-info">
                        <div class="donor-name">${d.donor_name}</div>
                        <div class="donor-time">${new Date(d.created_at).toLocaleDateString()}</div>
                    </div>
                    <div class="donor-amount">₹${parseFloat(d.amount).toLocaleString('en-IN')}</div>
                </div>`).join('');
        }
        renderDonors();

        // 3. Share Functionality
        function shareWhatsApp() {
            const text = encodeURIComponent(`Help support ${document.getElementById('campaignTitle').innerText} on SAHARA! ` + window.location.href);
            window.open(`https://wa.me/?text=${text}`, '_blank');
        }
        
        function copyLink() {
            navigator.clipboard.writeText(window.location.href);
            alert("Link copied to clipboard!");
        }

        // 4. Amount Selection
        document.querySelectorAll('.amount-option').forEach(btn => {
            btn.onclick = () => {
                document.querySelectorAll('.amount-option').forEach(b => b.classList.remove('selected'));
                btn.classList.add('selected');
                document.getElementById('customAmount').value = btn.dataset.amount;
            };
        });

        // 5. Form Pre-fill
        // --- DYNAMIC DONOR LOGIC ---
        const dEmail = document.getElementById('donorEmail');
        const dName = document.getElementById('donorName');
        const dPhone = document.getElementById('donorPhone');
        const donateBtn = document.getElementById('donateBtn');

        // 1. Function to handle auto-disabling
        function updateFieldStates() {
            if (dEmail.value.trim() !== "") {
                // If email is filled (either by session or guest typing), check for name/phone
                if (dName.value.trim() !== "") dName.disabled = true;
                if (dPhone.value.trim() !== "") dPhone.disabled = true;
            }
        }

        // 2. Fetch details for guest users on email input
        dEmail.addEventListener('blur', async () => {
            const email = dEmail.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (emailRegex.test(email) && dEmail.disabled === false) {
                const formData = new FormData();
                formData.append('email', email);

                try {
                    const res = await fetch('actions/donate/check_donor.php', { method: 'POST', body: formData });
                    const data = await res.json();

                    if (data.status === 'found') {
                        if (data.name) {
                            dName.value = data.name;
                            dName.disabled = true;
                        }
                        if (data.phone) {
                            dPhone.value = data.phone;
                            dPhone.disabled = true;
                        } else {
                            dPhone.disabled = false; // Compulsory to fill if missing
                            dPhone.focus();
                        }
                    } else {
                        // New user - allow filling everything
                        dName.disabled = false;
                        dPhone.disabled = false;
                    }
                } catch (e) { console.error("Verify error"); }
            }
        });

        // 3. Final Validation on Click
        donateBtn.addEventListener('click', () => {
            let isValid = true;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            const phoneRegex = /^[6-9]\d{9}$/;

            // Reset errors
            document.querySelectorAll('.form-error').forEach(e => e.style.display = 'none');

            if (!emailRegex.test(dEmail.value)) {
                document.getElementById('emailError').style.display = 'block';
                isValid = false;
            }
            if (dName.value.trim().length < 2) {
                document.getElementById('nameError').style.display = 'block';
                isValid = false;
            }
            if (!phoneRegex.test(dPhone.value)) {
                document.getElementById('phoneError').style.display = 'block';
                isValid = false;
            }

            if (isValid) {
                const amt = parseFloat(document.getElementById('customAmount').value);
                const remainingGoal = <?php echo $remaining; ?>; // This variable already exists in your PHP header

                if (!amt || amt < 100) {
                    alert("Minimum donation is ₹100");
                    return;
                }

                // --- NEW CHECK ---
                if (amt > remainingGoal) {
                    alert(`The goal is nearly reached! Please enter an amount equal to or less than ₹${remainingGoal.toLocaleString('en-IN')}.`);
                    document.getElementById('customAmount').value = remainingGoal; // Optional: auto-fill with max possible
                    return;
                }

                donateBtn.disabled = true;
                donateBtn.innerHTML = "Processing...";

                const formData = new FormData();
                formData.append('campaign_id', campaignId);
                formData.append('amount', amt);
                formData.append('email', dEmail.value.trim());
                formData.append('name', dName.value.trim());
                formData.append('phone', dPhone.value.trim());

                // Replace the current fetch block inside donateBtn.addEventListener with this:

                fetch('actions/donate/process_donation.php', { method: 'POST', body: formData })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'success') {
                        // 1. Populate Modal with Form Values
                        document.getElementById('modalAmount').textContent = '₹' + amt.toLocaleString('en-IN');
                        document.getElementById('modalDonorName').textContent = dName.value.trim();
                        document.getElementById('modalDonorPhone').textContent = dPhone.value.trim();
                        document.getElementById('modalDonorEmail').textContent = dEmail.value.trim();
                        
                        // 2. Populate Campaign Reference ID (Format: SAH-YYYY-ID)
                        const campaignRef = "SAH-2026-" + campaignId.toString().padStart(3, '0');
                        document.getElementById('modalCampaignId').textContent = campaignRef;

                        // 3. Populate Donation ID from Server Response
                        // Note: You need to add <span id="modalDonationId"></span> in your HTML modal to see this
                        const donIdEl = document.getElementById('modalDonationId');
                        if (donIdEl) {
                            donIdEl.textContent = "#DON-" + data.donation_id;
                        }

                        // 4. Show Modal
                        document.getElementById('donationModal').classList.add('show');
                    } else {
                        alert(data.message);
                        donateBtn.disabled = false;
                        donateBtn.innerHTML = "Donate Now";
                    }
                })
                .catch(err => {
                    alert("Server connection failed.");
                    donateBtn.disabled = false;
                    donateBtn.innerHTML = "Donate Now";
                });
            }
        });

        // Run on init for logged in users
        updateFieldStates();

        // Slider controls (keep your existing prev/next button logic)
    </script>
</body>
</html>