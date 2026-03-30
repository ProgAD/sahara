<?php

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == '') {
    header('Location: login.html');
    exit;
}
require_once 'config/db.php';

$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['name'] ?? null;
$user_email = $_SESSION['email'] ?? null;
$user_phone = $_SESSION['phone'] ?? null;
$role = $_SESSION['role'] ?? null;
if ($role != 'user') {
    header('Location: actions/auth/logout.php');
    exit;
}


// 1. Fetch User Data
$user_stmt = $conn->prepare("SELECT name, email, phone, created_at FROM users WHERE id = ?");
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_data = $user_stmt->get_result()->fetch_assoc();

// 2. Fetch Stats
// Total Campaigns
$count_res = $conn->query("SELECT COUNT(*) as total FROM campaigns WHERE user_id = $user_id AND delete_flag = 0");
$total_campaigns = $count_res->fetch_assoc()['total'];

// Live Campaigns
$live_res = $conn->query("SELECT COUNT(*) as total FROM campaigns WHERE user_id = $user_id AND status = 'approved' AND delete_flag = 0");
$live_campaigns = $live_res->fetch_assoc()['total'];

// Total Donated by this user
$donated_res = $conn->query("SELECT SUM(amount) as total FROM donations WHERE user_id = $user_id");
$total_donated = $donated_res->fetch_assoc()['total'] ?? 0;

// Total Raised by this user's campaigns
$raised_res = $conn->query("SELECT SUM(d.amount) as total FROM donations d JOIN campaigns c ON d.campaign_id = c.id WHERE c.user_id = $user_id");
$total_raised = $raised_res->fetch_assoc()['total'] ?? 0;

$current_page = "dashboard.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Dashboard – SAHARA</title>
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
            --error: #EF4444;
            --error-light: #FEE2E2;
            --success: #10B981;
            --success-light: #D1FAE5;
            --warning: #F59E0B;
            --warning-light: #FEF3C7;
            --info: #3B82F6;
            --info-light: #DBEAFE;
            --radius-sm: 12px;
            --radius-md: 20px;
            --radius-lg: 32px;
            --ease-out: cubic-bezier(0.16, 1, 0.3, 1);
            --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; font-size: 16px; }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--cream);
            color: var(--earth);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }
        #userAvatar, .profile-photo {
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
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

        h1, h2, h3, h4, h5 { font-family: 'Playfair Display', serif; font-weight: 700; line-height: 1.15; letter-spacing: -0.02em; }
        p { color: var(--stone); }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; display: block; }
        .container { width: 100%; max-width: 1400px; margin: 0 auto; padding: 0 clamp(1.25rem, 5vw, 3rem); }

        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
            padding: 0.875rem 1.75rem; font-family: 'Outfit', sans-serif; font-weight: 600; font-size: 0.9rem;
            border: none; border-radius: 100px; cursor: pointer; transition: all 0.4s var(--ease-out); white-space: nowrap;
        }
        .btn-sun { background: linear-gradient(135deg, var(--sun) 0%, var(--amber) 100%); color: white; box-shadow: 0 8px 32px -8px rgba(249, 115, 22, 0.5), inset 0 1px 0 rgba(255,255,255,0.2); }
        .btn-sun:hover { transform: translateY(-3px) scale(1.02); box-shadow: 0 16px 48px -8px rgba(249, 115, 22, 0.6); }
        .btn-ghost { background: transparent; color: var(--earth); border: 2px solid rgba(41, 37, 36, 0.15); }
        .btn-ghost:hover { border-color: var(--sun); color: var(--sun); background: rgba(249, 115, 22, 0.05); }
        .btn svg { width: 18px; height: 18px; }


        /* ===== DASHBOARD ===== */
        .page-content { padding-top: 100px; padding-bottom: 4rem; min-height: 100vh; }

        /* Dashboard Header */
        .dash-header { margin-bottom: 2rem; }
        .dash-header-top { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .dash-greeting h1 { font-size: clamp(1.5rem, 4vw, 2.25rem); color: var(--earth); margin-bottom: 0.25rem; }
        .dash-greeting p { font-size: 0.95rem; }
        .dash-user-badge {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.625rem 1.25rem; background: white; border-radius: 100px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.04);
        }
        .dash-user-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(135deg, var(--sun), var(--amber));
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 1rem;
        }
        .dash-user-name { font-weight: 600; font-size: 0.9rem; color: var(--earth); }
        .dash-user-email { font-size: 0.75rem; color: var(--stone-light); }

        /* Quick Stats */
        .dash-stats {
            display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2rem;
        }
        .dash-stat-card {
            background: white; border-radius: var(--radius-md); padding: 1.25rem;
            border: 1px solid rgba(0,0,0,0.04); transition: all 0.3s var(--ease-out);
            position: relative; overflow: hidden;
        }
        .dash-stat-card::before {
            content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
            background: linear-gradient(90deg, var(--sun), var(--amber)); border-radius: 3px 3px 0 0;
        }
        .dash-stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px -8px rgba(0,0,0,0.1); }
        .dash-stat-icon { font-size: 1.5rem; margin-bottom: 0.75rem; }
        .dash-stat-value { font-family: 'Playfair Display', serif; font-size: 1.5rem; font-weight: 700; color: var(--earth); margin-bottom: 0.125rem; }
        .dash-stat-label { font-size: 0.8rem; color: var(--stone-light); font-weight: 500; }

        /* Tab Navigation */
        .dash-tabs {
            display: flex; gap: 0.25rem; background: white; border-radius: var(--radius-md);
            padding: 0.375rem; box-shadow: 0 2px 12px rgba(0,0,0,0.04); margin-bottom: 2rem;
            overflow-x: auto; -webkit-overflow-scrolling: touch;
        }
        .dash-tabs::-webkit-scrollbar { display: none; }
        .dash-tab {
            flex: 1; min-width: 0; padding: 0.875rem 1.25rem; font-family: 'Outfit', sans-serif;
            font-size: 0.875rem; font-weight: 600; color: var(--stone); background: transparent;
            border: none; border-radius: var(--radius-sm); cursor: pointer; transition: all 0.3s var(--ease-out);
            white-space: nowrap; display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        }
        .dash-tab:hover { color: var(--sun); background: var(--soft-orange); }
        .dash-tab.active { background: linear-gradient(135deg, var(--sun), var(--amber)); color: white; box-shadow: 0 4px 16px -4px rgba(249, 115, 22, 0.4); }
        .dash-tab-icon { font-size: 1.1rem; }
        .dash-tab-badge { background: rgba(255,255,255,0.3); padding: 0.125rem 0.5rem; border-radius: 100px; font-size: 0.7rem; font-weight: 700; }
        .dash-tab.active .dash-tab-badge { background: rgba(255,255,255,0.3); }
        .dash-tab:not(.active) .dash-tab-badge { background: rgba(0,0,0,0.08); color: var(--stone); }

        /* Tab Panels */
        .tab-panel { display: none; }
        .tab-panel.active { display: block; animation: fadeIn 0.4s var(--ease-out); }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

        /* ===== MY CAMPAIGNS TAB ===== */
        .campaign-list { display: flex; flex-direction: column; gap: 1rem; }

        .campaign-row {
            background: white; border-radius: var(--radius-md); padding: 1.5rem;
            display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: center;
            border: 1px solid rgba(0,0,0,0.04); transition: all 0.3s var(--ease-out);
        }
        .campaign-row:hover { box-shadow: 0 8px 24px -8px rgba(0,0,0,0.08); transform: translateY(-2px); }

        .campaign-row-info { display: flex; flex-direction: column; gap: 0.5rem; min-width: 0; }
        .campaign-row-top { display: flex; align-items: center; gap: 0.75rem; flex-wrap: wrap; }
        .campaign-row-id { font-size: 0.7rem; font-weight: 700; color: var(--stone-light); background: var(--cream); padding: 0.25rem 0.625rem; border-radius: 100px; letter-spacing: 0.05em; }
        .campaign-row-title { font-family: 'Outfit', sans-serif; font-size: 1.05rem; font-weight: 700; color: var(--earth); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .campaign-row-meta { display: flex; gap: 1.5rem; flex-wrap: wrap; }
        .campaign-row-meta span { font-size: 0.8rem; color: var(--stone-light); display: flex; align-items: center; gap: 0.375rem; }
        .campaign-row-meta svg { width: 14px; height: 14px; }

        .campaign-row-progress { margin-top: 0.25rem; }
        .mini-progress { height: 6px; background: rgba(0,0,0,0.06); border-radius: 100px; overflow: hidden; width: 100%; max-width: 300px; }
        .mini-progress-fill { height: 100%; border-radius: 100px; transition: width 1s var(--ease-out); }
        .mini-progress-fill.active { background: linear-gradient(90deg, var(--sun), var(--amber)); }
        .mini-progress-fill.completed { background: linear-gradient(90deg, var(--success), #059669); }
        .progress-text { font-size: 0.75rem; color: var(--stone-light); margin-top: 0.25rem; }

        .campaign-row-right { display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem; }

        /* Status Badges */
        .status-badge {
            padding: 0.375rem 1rem; border-radius: 100px; font-size: 0.7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 0.375rem;
        }
        .status-badge.pending { background: var(--warning-light); color: #B45309; }
        .status-badge.approved { background: var(--info-light); color: #1D4ED8; }
        .status-badge.live { background: var(--success-light); color: #047857; }
        .status-badge.completed { background: #F3F4F6; color: var(--stone); }
        .status-badge.rejected { background: var(--error-light); color: #B91C1C; }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; }
        .status-badge.pending .status-dot { background: #B45309; }
        .status-badge.approved .status-dot { background: #1D4ED8; }
        .status-badge.live .status-dot { background: #047857; animation: pulse 1.5s infinite; }
        .status-badge.completed .status-dot { background: var(--stone); }
        .status-badge.rejected .status-dot { background: #B91C1C; }
        @keyframes pulse { 0%, 100% { opacity: 1; } 50% { opacity: 0.4; } }

        .campaign-row-date { font-size: 0.75rem; color: var(--stone-light); }

        /* Empty state */
        .empty-tab { text-align: center; padding: 4rem 2rem; }
        .empty-tab-icon { font-size: 3rem; margin-bottom: 1rem; }
        .empty-tab h3 { font-family: 'Outfit', sans-serif; font-size: 1.15rem; color: var(--earth); margin-bottom: 0.5rem; }
        .empty-tab p { font-size: 0.9rem; color: var(--stone-light); margin-bottom: 1.5rem; }

        /* ===== MY DONATIONS TAB ===== */
        .donation-list { display: flex; flex-direction: column; gap: 0.75rem; }

        .donation-row {
            background: white; border-radius: var(--radius-md); padding: 1.25rem 1.5rem;
            display: flex; align-items: center; gap: 1rem;
            border: 1px solid rgba(0,0,0,0.04); transition: all 0.3s var(--ease-out);
        }
        .donation-row:hover { box-shadow: 0 4px 16px -4px rgba(0,0,0,0.06); transform: translateX(4px); }

        .donation-icon {
            width: 44px; height: 44px; border-radius: var(--radius-sm);
            background: linear-gradient(135deg, var(--soft-orange), var(--peach));
            display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0;
        }
        .donation-info { flex: 1; min-width: 0; }
        .donation-title { font-weight: 600; font-size: 0.95rem; color: var(--earth); margin-bottom: 0.125rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .donation-campaign-id { font-size: 0.7rem; color: var(--stone-light); font-weight: 500; }
        .donation-date { font-size: 0.75rem; color: var(--stone-light); text-align: right; }
        .donation-amount { font-family: 'Playfair Display', serif; font-weight: 700; font-size: 1.1rem; color: var(--sun); text-align: right; white-space: nowrap; }
        .donation-status { font-size: 0.7rem; font-weight: 600; color: var(--success); text-align: right; }

        .donation-right { display: flex; flex-direction: column; align-items: flex-end; gap: 0.25rem; flex-shrink: 0; }

        /* ===== PROFILE TAB ===== */
        .profile-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; }

        .profile-card {
            background: white; border-radius: var(--radius-lg); padding: 2rem;
            box-shadow: 0 4px 24px rgba(0,0,0,0.04); border: 1px solid rgba(0,0,0,0.04);
        }

        .profile-card-header {
            display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.5rem;
            padding-bottom: 1rem; border-bottom: 2px solid var(--soft-orange);
        }
        .profile-card-icon { width: 44px; height: 44px; border-radius: var(--radius-sm); background: linear-gradient(135deg, var(--soft-orange), var(--peach)); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
        .profile-card-header h3 { font-family: 'Outfit', sans-serif; font-size: 1.1rem; font-weight: 700; color: var(--earth); }
        .profile-card-header p { font-size: 0.8rem; color: var(--stone-light); margin-top: 0.125rem; }

        .profile-field { margin-bottom: 1.25rem; }
        .profile-field:last-child { margin-bottom: 0; }
        .profile-label { font-size: 0.8rem; font-weight: 600; color: var(--stone-light); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.375rem; }

        .profile-value {
            font-size: 1rem; font-weight: 500; color: var(--earth); padding: 0.875rem 1rem;
            background: var(--cream); border-radius: var(--radius-sm); border: 2px solid transparent;
        }

        .profile-input {
            width: 100%; padding: 0.875rem 1rem; font-family: 'Outfit', sans-serif; font-size: 0.95rem;
            border: 2px solid rgba(0,0,0,0.08); border-radius: var(--radius-sm); background: var(--cream);
            color: var(--earth); transition: all 0.3s var(--ease-out);
        }
        .profile-input:focus { outline: none; border-color: var(--sun); background: white; box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1); }
        .profile-input::placeholder { color: var(--stone-light); }
        .profile-input.error { border-color: var(--error); }

        /* Profile Photo Upload */
        .profile-photo-section {
            display: flex; flex-direction: column; align-items: center; margin-bottom: 2rem;
            padding-bottom: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.06);
        }

        .profile-photo-wrapper {
            position: relative; width: 100px; height: 100px; border-radius: 50%; cursor: pointer;
            margin-bottom: 0.75rem;
        }

        .profile-photo-wrapper input[type="file"] { display: none; }

        .profile-photo {
            width: 100%; height: 100%; border-radius: 50%; object-fit: cover;
            border: 3px solid var(--soft-orange); transition: all 0.3s var(--ease-out);
        }

        .profile-photo-initials {
            width: 100%; height: 100%; border-radius: 50%;
            background: linear-gradient(135deg, var(--sun), var(--amber));
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 2rem; font-weight: 700;
            border: 3px solid var(--soft-orange); transition: all 0.3s var(--ease-out);
        }

        .profile-photo-overlay {
            position: absolute; inset: 0; border-radius: 50%;
            background: rgba(0,0,0,0.5); display: flex; flex-direction: column;
            align-items: center; justify-content: center; gap: 0.25rem;
            opacity: 0; transition: opacity 0.3s var(--ease-out);
        }

        .profile-photo-overlay svg { width: 24px; height: 24px; color: white; }
        .profile-photo-overlay span { font-size: 0.65rem; font-weight: 600; color: white; text-transform: uppercase; letter-spacing: 0.05em; }

        .profile-photo-wrapper:hover .profile-photo-overlay { opacity: 1; }
        .profile-photo-wrapper:hover .profile-photo,
        .profile-photo-wrapper:hover .profile-photo-initials { border-color: var(--sun); transform: scale(1.03); }

        .profile-photo-name { font-weight: 700; font-size: 1.1rem; color: var(--earth); margin-bottom: 0.125rem; }
        .profile-photo-role { font-size: 0.8rem; color: var(--stone-light); }
        .profile-photo-hint { font-size: 0.7rem; color: var(--stone-light); margin-top: 0.5rem; }

        .password-strength { height: 4px; border-radius: 2px; background: rgba(0,0,0,0.06); margin-top: 0.5rem; overflow: hidden; }
        .password-strength-fill { height: 100%; border-radius: 2px; transition: all 0.3s; width: 0%; }
        .password-strength-fill.weak { width: 33%; background: var(--error); }
        .password-strength-fill.medium { width: 66%; background: var(--warning); }
        .password-strength-fill.strong { width: 100%; background: var(--success); }
        .password-hint { font-size: 0.75rem; color: var(--stone-light); margin-top: 0.375rem; }
        .password-match { font-size: 0.75rem; margin-top: 0.375rem; }
        .password-match.match { color: var(--success); }
        .password-match.no-match { color: var(--error); }

        /* Editable Profile Field */
        .profile-field-editable {
            display: flex; align-items: center; gap: 0.75rem;
        }

        .profile-field-editable .profile-value {
            flex: 1;
        }

        .profile-field-editable .profile-input {
            flex: 1; display: none;
        }

        .profile-field-editable.editing .profile-value { display: none; }
        .profile-field-editable.editing .profile-input { display: block; }
        .profile-field-editable.editing .edit-btn { display: none; }
        .profile-field-editable.editing .save-btns { display: flex; }

        .edit-btn {
            width: 36px; height: 36px; border-radius: 50%; border: 2px solid rgba(0,0,0,0.08);
            background: white; cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: all 0.3s var(--ease-out); flex-shrink: 0;
        }
        .edit-btn:hover { border-color: var(--sun); background: var(--soft-orange); }
        .edit-btn svg { width: 16px; height: 16px; color: var(--stone); }
        .edit-btn:hover svg { color: var(--sun); }

        .save-btns { display: none; gap: 0.5rem; flex-shrink: 0; }
        .save-btn, .cancel-btn {
            width: 36px; height: 36px; border-radius: 50%; border: none;
            cursor: pointer; display: flex; align-items: center; justify-content: center;
            transition: all 0.3s var(--ease-out);
        }
        .save-btn { background: var(--success); color: white; }
        .save-btn:hover { background: #059669; transform: scale(1.1); }
        .cancel-btn { background: rgba(0,0,0,0.06); color: var(--stone); }
        .cancel-btn:hover { background: var(--error-light); color: var(--error); }
        .save-btn svg, .cancel-btn svg { width: 16px; height: 16px; }

        .profile-actions { margin-top: 1.5rem; display: flex; gap: 0.75rem; }
        .profile-actions .btn { padding: 0.75rem 1.5rem; font-size: 0.85rem; }

        /* Toast Notification */
        .toast {
            position: fixed; bottom: 2rem; right: 2rem; padding: 1rem 1.5rem;
            background: var(--earth); color: white; border-radius: var(--radius-sm);
            font-size: 0.9rem; font-weight: 500; display: flex; align-items: center; gap: 0.75rem;
            box-shadow: 0 16px 48px -12px rgba(0,0,0,0.3); z-index: 3000;
            transform: translateY(120%); opacity: 0; transition: all 0.4s var(--ease-spring);
        }
        .toast.show { transform: translateY(0); opacity: 1; }
        .toast-icon { font-size: 1.25rem; }

        /* ===== FOOTER ===== */
        .footer { background: linear-gradient(180deg, #1C1917 0%, #0C0A09 100%); color: white; padding: 3.5rem 0 1.5rem; }
        .footer-main { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 3rem; align-items: start; }
        .footer-brand { display: flex; flex-direction: column; align-items: flex-start; }
        .footer-brand-top { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 0.5rem; }
        .footer-brand-top img { width: 56px; height: 56px; border-radius: 50%; border: 2px solid rgba(249, 115, 22, 0.3); }
        .footer-brand h3 { font-size: 1.5rem; color: var(--peach); font-weight: 700; }
        .footer-brand-tagline { font-size: 0.95rem; color: var(--peach); font-weight: 500; margin-bottom: 0.25rem; }
        .footer-brand-sub { font-size: 0.75rem; color: rgba(255,255,255,0.4); margin-bottom: 1.25rem; }
        .btn-join { padding: 0.75rem 2rem; background: #10B981; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem; cursor: pointer; transition: all 0.3s var(--ease-out); }
        .btn-join:hover { background: #059669; transform: translateY(-2px); box-shadow: 0 8px 20px -4px rgba(16, 185, 129, 0.4); }
        .footer-col h4 { font-family: 'Outfit', sans-serif; font-size: 1rem; font-weight: 600; color: white; margin-bottom: 1.25rem; }
        .footer-col ul { list-style: none; }
        .footer-col li { margin-bottom: 0.625rem; }
        .footer-col a { color: rgba(255,255,255,0.55); font-size: 0.875rem; transition: all 0.3s; display: inline-flex; align-items: center; gap: 0.5rem; }
        .footer-col a:hover { color: var(--sun-glow); }
        .footer-col a svg { width: 16px; height: 16px; flex-shrink: 0; }
        .footer-bottom { margin-top: 2.5rem; padding-top: 1.25rem; border-top: 1px solid rgba(255,255,255,0.08); display: flex; justify-content: center; align-items: center; }
        .footer-bottom p { font-size: 0.8rem; color: rgba(255,255,255,0.35); display: flex; align-items: center; gap: 0.35rem; }
        .footer-bottom .heart { color: #EF4444; animation: heartbeat 1.5s ease-in-out infinite; }
        @keyframes heartbeat { 0%, 100% { transform: scale(1); } 50% { transform: scale(1.2); } }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1200px) {
            .footer-main { grid-template-columns: repeat(2, 1fr); }
            .dash-stats { grid-template-columns: repeat(2, 1fr); }
        }


        @media (max-width: 768px) {
            .footer-main { grid-template-columns: 1fr; gap: 2rem; text-align: center; }
            .footer-brand { align-items: center; }
            .footer-bottom { flex-direction: column; text-align: center; }
            .profile-grid { grid-template-columns: 1fr; }
            .campaign-row { grid-template-columns: 1fr; }
            .campaign-row-right { flex-direction: row; align-items: center; gap: 1rem; }
            .dash-header-top { flex-direction: column; align-items: flex-start; }
            .dash-tab { padding: 0.75rem 1rem; font-size: 0.8rem; }
            .dash-tab-icon { display: none; }
        }

        @media (max-width: 480px) {
            html { font-size: 15px; }
            .dash-stats { grid-template-columns: 1fr 1fr; gap: 0.75rem; }
            .dash-stat-card { padding: 1rem; }
            .dash-stat-value { font-size: 1.25rem; }
            .donation-row { flex-wrap: wrap; }
            .donation-right { width: 100%; flex-direction: row; justify-content: space-between; padding-top: 0.75rem; border-top: 1px solid rgba(0,0,0,0.06); }
            .profile-card { padding: 1.5rem; }
            .profile-actions { flex-direction: column; }
            .profile-actions .btn { width: 100%; justify-content: center; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <main class="page-content">
        <div class="container">
            <!-- Dashboard Header -->
            <div class="dash-header">
                <div class="dash-header-top">
                    <div class="dash-greeting">
                        <h1>My Dashboard</h1>
                        <p>Manage your campaigns, donations, and profile</p>
                    </div>
                    <div class="dash-user-badge">
                        <div class="dash-user-avatar" id="userAvatar">TS</div>
                        <div>
                            <div class="dash-user-name" id="userName">Tabish Shaikh</div>
                            <div class="dash-user-email" id="userEmail"><a href="/cdn-cgi/l/email-protection" class="__cf_email__" data-cfemail="196d787b706a71596a6d6c7d603770706d7437787a377077">[email&#160;protected]</a></div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats -->
                <div class="dash-stats">
                    <div class="dash-stat-card">
                        <div class="dash-stat-icon">📝</div>
                        <div class="dash-stat-value"><?php echo $total_campaigns; ?></div>
                        <div class="dash-stat-label">My Campaigns</div>
                    </div>
                    <div class="dash-stat-card">
                        <div class="dash-stat-icon">🟢</div>
                        <div class="dash-stat-value" id="statLive">NA</div>
                        <div class="dash-stat-label">Live Campaigns</div>
                    </div>
                    <div class="dash-stat-card">
                        <div class="dash-stat-icon">💸</div>
                        <div class="dash-stat-value">₹<?php echo number_format($total_donated); ?></div>
                        <div class="dash-stat-label">Total Donated</div>
                    </div>
                    <div class="dash-stat-card">
                        <div class="dash-stat-icon">🏆</div>
                        <div class="dash-stat-value">₹<?php echo number_format($total_raised); ?></div>
                        <div class="dash-stat-label">Total Raised</div>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation -->
            <div class="dash-tabs">
                <button class="dash-tab active" data-tab="campaigns">
                    <span class="dash-tab-icon">📋</span>
                    My Campaigns
                    <span class="dash-tab-badge">4</span>
                </button>
                <button class="dash-tab" data-tab="donations">
                    <span class="dash-tab-icon">💝</span>
                    My Donations
                    <span class="dash-tab-badge">6</span>
                </button>
                <button class="dash-tab" data-tab="profile">
                    <span class="dash-tab-icon">⚙️</span>
                    Profile & Settings
                </button>
            </div>

            <!-- Tab: My Campaigns -->
            <div class="tab-panel active" id="panel-campaigns">
                <div class="campaign-list" id="campaignList"></div>
            </div>

            <!-- Tab: My Donations -->
            <div class="tab-panel" id="panel-donations">
                <div class="donation-list" id="donationList"></div>
            </div>

            <!-- Tab: Profile & Settings -->
            <div class="tab-panel" id="panel-profile">
                <div class="profile-grid">
                    <!-- Profile Details -->
                    <div class="profile-card">
                        <div class="profile-card-header">
                            <div class="profile-card-icon">👤</div>
                            <div>
                                <h3>Profile Details</h3>
                                <p>Your personal information</p>
                            </div>
                        </div>
                        <!-- Profile Photo -->
                        <div class="profile-photo-section">
                            <div class="profile-photo-wrapper" id="photoWrapper" onclick="document.getElementById('photoInput').click()">
                                <div class="profile-photo-initials" id="photoInitials">TS</div>
                                <img class="profile-photo" id="photoPreview" style="display:none;" alt="Profile Photo">
                                <input type="file" id="photoInput" accept="image/*" onchange="handlePhotoUpload(event)">
                                <div class="profile-photo-overlay">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><circle cx="12" cy="13" r="3"/></svg>
                                    <span id="photoOverlayText">Upload</span>
                                </div>
                            </div>
                            <div class="profile-photo-name" id="photoName">Tabish Shaikh</div>
                            <div class="profile-photo-role">BS Data Science, Batch 2024</div>
                            <div class="profile-photo-hint">Click photo to upload or change</div>
                        </div>
                        <div class="profile-field">
                            <div class="profile-label">Full Name</div>
                            <div class="profile-value" id="profileName"><?php echo htmlspecialchars($user_data['name']); ?></div>
                        </div>
                        <div class="profile-field">
                            <div class="profile-label">Phone Number</div>
                            <div class="profile-field-editable" id="phoneEditable">
                                <div class="profile-value" id="profilePhone"><?php echo htmlspecialchars($user_data['phone'] ?? 'Not set'); ?></div>
                                <input type="tel" class="profile-input" id="phoneInput" value="<?php echo htmlspecialchars($user_data['phone'] ?? 'Not set'); ?>" placeholder="+91 XXXXX XXXXX">
                                <button class="edit-btn" onclick="startEditPhone()" title="Edit phone">
                                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
                                </button>
                                <div class="save-btns">
                                    <button class="save-btn" onclick="savePhone()" title="Save">
                                        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                    </button>
                                    <button class="cancel-btn" onclick="cancelEditPhone()" title="Cancel">
                                        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="profile-field">
                            <div class="profile-label">Email Address</div>
                            <div class="profile-value" id="profileEmail"><?php echo htmlspecialchars($user_data['email']); ?></div>
                        </div>
                        <div class="profile-field">
                            <div class="profile-label">Member Since</div>
                            <div class="profile-value"><?php echo date('F Y', strtotime($user_data['created_at'])); ?></div>
                        </div>
                    </div>

                    <!-- Change Password -->
                    <div class="profile-card">
                        <div class="profile-card-header">
                            <div class="profile-card-icon">🔒</div>
                            <div>
                                <h3>Change Password</h3>
                                <p>Update your account password</p>
                            </div>
                        </div>
                        <div class="profile-field">
                            <div class="profile-label">New Password</div>
                            <input type="password" id="newPassword" class="profile-input" placeholder="Enter new password" oninput="checkPasswordStrength()">
                            <div class="password-strength"><div class="password-strength-fill" id="strengthFill"></div></div>
                            <div class="password-hint" id="strengthText">Use 8+ characters with letters, numbers & symbols</div>
                        </div>
                        <div class="profile-field">
                            <div class="profile-label">Confirm Password</div>
                            <input type="password" id="confirmPassword" class="profile-input" placeholder="Confirm new password" oninput="checkPasswordMatch()">
                            <div class="password-match" id="matchText"></div>
                        </div>
                        <div class="profile-actions">
                            <button class="btn btn-sun" onclick="changePassword()">Update Password</button>
                            <button class="btn btn-ghost" onclick="clearPasswordFields()">Cancel</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Toast -->
    <div class="toast" id="toast">
        <span class="toast-icon" id="toastIcon">✅</span>
        <span id="toastText">Success!</span>
    </div>

    <!-- Footer -->
    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
    <script>
    // Global State
    let myCampaigns = [];
    let myDonations = [];

    // --- 1. INITIALIZATION ---
    document.addEventListener('DOMContentLoaded', () => {
        loadDashboardData();
        setupMobileMenu();
        setupHeaderScroll();
    });

    // --- 2. DATA FETCHING (DYNAMC) ---
    async function loadDashboardData() {
        const formData = new FormData();
        formData.append('action', 'get_data');
        
        try {
            const res = await fetch('actions/auth/update_user_profile.php', { method: 'POST', body: formData });
            const data = await res.json();
            
            myCampaigns = data.campaigns || [];
            myDonations = data.donations || [];
            
            // Handle Live Profile Photo UI
            if (data.photoUrl) {
                const preview = document.getElementById('photoPreview');
                const initials = document.getElementById('photoInitials');
                const navAvatar = document.getElementById('userAvatar'); // Dashboard header avatar
                
                preview.src = data.photoUrl;
                preview.style.display = 'block';
                initials.style.display = 'none';
                
                if (navAvatar) {
                    navAvatar.style.backgroundImage = `url(${data.photoUrl})`;
                    navAvatar.style.backgroundSize = 'cover';
                    navAvatar.style.backgroundPosition = 'center';
                    navAvatar.textContent = '';
                }
            }
            
            renderMyCampaigns();
            renderMyDonations();
            
            // Update Tab Badges dynamically
            document.querySelector('[data-tab="campaigns"] .dash-tab-badge').textContent = myCampaigns.length;
            document.querySelector('[data-tab="donations"] .dash-tab-badge').textContent = myDonations.length;
        } catch (e) {
            console.error("Dashboard Sync Error:", e);
            showToast('❌', 'Error loading dashboard data');
        }
    }

    // --- 3. AJAX PHOTO UPLOAD ---
    async function handlePhotoUpload(event) {
        const file = event.target.files[0];
        if (!file) return;

        if (!file.type.startsWith('image/')) {
            showToast('❌', 'Invalid file type');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'upload_photo');
        formData.append('photo', file);

        showToast('⏳', 'Uploading...');

        try {
            const res = await fetch('actions/auth/update_user_profile.php', { method: 'POST', body: formData });
            const data = await res.json();
            
            if (data.status === 'success') {
                showToast('✅', 'Profile updated!');
                
                // Update UI instantly with cache buster
                const preview = document.getElementById('photoPreview');
                preview.src = data.url;
                preview.style.display = 'block';
                document.getElementById('photoInitials').style.display = 'none';
                
                const navAvatar = document.getElementById('userAvatar');
                if (navAvatar) {
                    navAvatar.style.backgroundImage = `url(${data.url})`;
                    navAvatar.style.backgroundSize = 'cover';
                    navAvatar.textContent = '';
                }
            } else {
                showToast('❌', 'Upload failed');
            }
        } catch (e) {
            showToast('❌', 'Server error during upload');
        }
    }

    // --- 4. PROFILE UPDATES ---
    async function savePhone() {
        const input = document.getElementById('phoneInput');
        const display = document.getElementById('profilePhone');
        const container = document.getElementById('phoneEditable');
        const val = input.value.trim();

        if (val.length < 10) {
            showToast('⚠️', 'Please enter a valid phone number');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'update_phone');
        formData.append('phone', val);

        try {
            const res = await fetch('actions/auth/update_user_profile.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.status === 'success') {
                display.textContent = val;
                container.classList.remove('editing');
                showToast('✅', 'Phone number updated!');
            }
        } catch (e) {
            showToast('❌', 'Update failed');
        }
    }

    async function changePassword() {
        const pw = document.getElementById('newPassword').value;
        const confirm = document.getElementById('confirmPassword').value;

        if (pw.length < 8) {
            showToast('⚠️', 'Password too short');
            return;
        }
        if (pw !== confirm) {
            showToast('❌', 'Passwords do not match');
            return;
        }

        const formData = new FormData();
        formData.append('action', 'change_password');
        formData.append('password', pw);

        try {
            const res = await fetch('actions/auth/update_user_profile.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.status === 'success') {
                showToast('✅', 'Password updated successfully!');
                clearPasswordFields();
            }
        } catch (e) {
            showToast('❌', 'Password update failed');
        }
    }

    // --- 5. RENDERING FUNCTIONS ---
    function renderMyCampaigns() {
        const list = document.getElementById('campaignList');
        if (myCampaigns.length === 0) {
            list.innerHTML = `
                <div class="empty-tab">
                    <div class="empty-tab-icon">🏜️</div>
                    <h3>No campaigns yet</h3>
                    <p>Start a fundraiser to help someone today.</p>
                    <a href="fundraise-request.php" class="btn btn-sun">Start Fundraising</a>
                </div>`;
            return;
        }

        const statusLabels = { pending: 'Under Review', approved: 'Approved', live: 'Live', completed: 'Completed', rejected: 'Rejected' };

        list.innerHTML = myCampaigns.map(c => {
            const raised = parseFloat(c.raised || 0);
            const goal = parseFloat(c.goal || 0);
            const pct = goal > 0 ? Math.min(Math.round((raised / goal) * 100), 100) : 0;
            
            return `
                <div class="campaign-row">
                    <div class="campaign-row-info">
                        <div class="campaign-row-top">
                            <span class="campaign-row-id">ID: ${c.id}</span>
                            <span class="status-badge ${c.status}"><span class="status-dot"></span>${statusLabels[c.status] || c.status}</span>
                        </div>
                        <div class="campaign-row-title">${c.title}</div>
                        <div class="campaign-row-progress">
                            <div class="mini-progress"><div class="mini-progress-fill active" style="width:${pct}%"></div></div>
                            <div class="progress-text">₹${raised.toLocaleString('en-IN')} raised of ₹${goal.toLocaleString('en-IN')} (${pct}%)</div>
                        </div>
                    </div>
                    <div class="campaign-row-right">
                        <div class="campaign-row-date">Created ${new Date(c.created_at).toLocaleDateString('en-IN')}</div>
                    </div>
                </div>`;
        }).join('');
    }

    function renderMyDonations() {
        const list = document.getElementById('donationList');
        if (myDonations.length === 0) {
            list.innerHTML = `<div class="empty-tab"><h3>No donations yet</h3><p>Your contributions will appear here.</p></div>`;
            return;
        }

        list.innerHTML = myDonations.map(d => `
            <div class="donation-row">
                <div class="donation-icon">💝</div>
                <div class="donation-info">
                    <div class="donation-title">${d.campaignTitle}</div>
                    <div class="donation-campaign-id">Campaign ID: ${d.campaignId}</div>
                </div>
                <div class="donation-right">
                    <div class="donation-amount">₹${parseFloat(d.amount).toLocaleString('en-IN')}</div>
                    <div class="donation-date">${new Date(d.created_at).toLocaleDateString('en-IN')}</div>
                    <div class="donation-status">✓ Confirmed</div>
                </div>
            </div>`).join('');
    }

    // --- 6. UI HELPERS ---
    function setupMobileMenu() {
        const menuToggle = document.getElementById('menuToggle');
        const mobileNav = document.getElementById('mobileNav');
        if (!menuToggle) return;
        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle('active');
            mobileNav.classList.toggle('active');
        });
    }

    function setupHeaderScroll() {
        const header = document.getElementById('header');
        window.addEventListener('scroll', () => { if(header) header.classList.toggle('scrolled', window.scrollY > 50); });
    }

    const tabs = document.querySelectorAll('.dash-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
            document.getElementById('panel-' + tab.dataset.tab).classList.add('active');
        });
    });

    function showToast(icon, text) {
        const toast = document.getElementById('toast');
        document.getElementById('toastIcon').textContent = icon;
        document.getElementById('toastText').textContent = text;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }

    function startEditPhone() {
        document.getElementById('phoneEditable').classList.add('editing');
        document.getElementById('phoneInput').focus();
    }

    function cancelEditPhone() {
        document.getElementById('phoneEditable').classList.remove('editing');
        document.getElementById('phoneInput').value = document.getElementById('profilePhone').textContent;
    }

    function clearPasswordFields() {
        document.getElementById('newPassword').value = '';
        document.getElementById('confirmPassword').value = '';
        document.getElementById('strengthFill').style.width = '0%';
        document.getElementById('matchText').textContent = '';
    }

    function checkPasswordStrength() {
        const pw = document.getElementById('newPassword').value;
        const fill = document.getElementById('strengthFill');
        let strength = 0;
        if (pw.length >= 8) strength += 40;
        if (/[A-Z]/.test(pw)) strength += 20;
        if (/[0-9]/.test(pw)) strength += 20;
        if (/[^A-Za-z0-9]/.test(pw)) strength += 20;
        fill.style.width = strength + '%';
        fill.className = 'password-strength-fill ' + (strength < 50 ? 'weak' : strength < 80 ? 'medium' : 'strong');
    }

    function checkPasswordMatch() {
        const pw = document.getElementById('newPassword').value;
        const cpw = document.getElementById('confirmPassword').value;
        const txt = document.getElementById('matchText');
        if (cpw === "") { txt.textContent = ""; return; }
        if (pw === cpw) { txt.textContent = "✓ Passwords match"; txt.className = "password-match match"; }
        else { txt.textContent = "✗ Passwords do not match"; txt.className = "password-match no-match"; }
    }
</script>
</body>
</html>