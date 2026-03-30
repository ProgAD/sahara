<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == '') {
    header('Location: ../login.html');
    exit;
}
require_once '../config/db.php';

$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['name'] ?? null;
$user_email = $_SESSION['email'] ?? null;
$user_phone = $_SESSION['phone'] ?? null;
$role = $_SESSION['role'] ?? null;
if ($role != 'admin') {
    header('Location: ../actions/auth/logout.php');
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard – SAHARA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            /* Admin Theme - Deep Slate */
            --sidebar-bg: #0F172A;
            --sidebar-hover: #1E293B;
            --sidebar-active: rgba(249, 115, 22, 0.12);
            --sidebar-border: rgba(255,255,255,0.06);
            --page-bg: #F1F5F9;
            --card-bg: #FFFFFF;
            --card-border: #E2E8F0;
            --text-primary: #0F172A;
            --text-secondary: #475569;
            --text-muted: #94A3B8;
            /* Accent (from SAHARA brand) */
            --accent: #F97316;
            --accent-light: #FB923C;
            --accent-glow: #FDBA74;
            --accent-bg: #FFF7ED;
            --accent-border: #FFEDD5;
            /* Status */
            --success: #10B981;
            --success-bg: #D1FAE5;
            --warning: #F59E0B;
            --warning-bg: #FEF3C7;
            --error: #EF4444;
            --error-bg: #FEE2E2;
            --info: #3B82F6;
            --info-bg: #DBEAFE;
            /* Spacing */
            --sidebar-width: 260px;
            --header-height: 72px;
            --radius-sm: 10px;
            --radius-md: 14px;
            --radius-lg: 20px;
            --ease-out: cubic-bezier(0.16, 1, 0.3, 1);
            --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        html { scroll-behavior: smooth; font-size: 16px; }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--page-bg);
            color: var(--text-primary);
            line-height: 1.6;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }

        h1, h2, h3, h4 { font-family: 'Playfair Display', serif; font-weight: 700; line-height: 1.2; }

        /* ===== SIDEBAR ===== */
        .sidebar {
            position: fixed; top: 0; left: 0; bottom: 0;
            width: var(--sidebar-width); background: var(--sidebar-bg);
            display: flex; flex-direction: column; z-index: 200;
            transition: transform 0.4s var(--ease-out);
            overflow-y: auto; overflow-x: hidden;
        }

        .sidebar-header {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid var(--sidebar-border);
            display: flex; align-items: center; gap: 0.75rem;
            flex-shrink: 0; position: relative;
        }

        .sidebar-close {
            display: none; position: absolute; top: 1.25rem; right: 1rem;
            width: 36px; height: 36px; border-radius: 50%;
            background: rgba(255,255,255,0.08); border: 1px solid rgba(255,255,255,0.1);
            cursor: pointer; align-items: center; justify-content: center;
            transition: all 0.25s var(--ease-out);
        }
        .sidebar-close svg { width: 18px; height: 18px; color: rgba(255,255,255,0.6); }
        .sidebar-close:hover { background: rgba(239, 68, 68, 0.2); border-color: rgba(239, 68, 68, 0.3); }
        .sidebar-close:hover svg { color: var(--error); }

        .sidebar-logo { width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0; }

        .sidebar-brand { display: flex; flex-direction: column; }
        .sidebar-brand strong { font-family: 'Playfair Display', serif; font-size: 1.2rem; font-weight: 700; color: var(--accent); line-height: 1.1; }
        .sidebar-brand span { font-size: 0.6rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.12em; font-weight: 500; }

        .sidebar-admin-tag {
            display: inline-block; margin-top: 0.375rem; padding: 0.2rem 0.625rem; border-radius: 100px;
            background: rgba(249, 115, 22, 0.15); color: var(--accent); font-size: 0.6rem;
            font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; width: fit-content;
        }

        .sidebar-nav { padding: 1rem 0.75rem; flex: 1; }
        .sidebar-label {
            font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.12em;
            color: var(--text-muted); padding: 0.75rem 0.75rem 0.5rem; margin-top: 0.5rem;
        }
        .sidebar-label:first-child { margin-top: 0; }

        .sidebar-link {
            display: flex; align-items: center; gap: 0.75rem;
            padding: 0.75rem; border-radius: var(--radius-sm); font-size: 0.875rem;
            font-weight: 500; color: rgba(255,255,255,0.55); cursor: pointer;
            transition: all 0.25s var(--ease-out); border: none; background: none; width: 100%; text-align: left;
        }
        .sidebar-link:hover { background: var(--sidebar-hover); color: rgba(255,255,255,0.9); }
        .sidebar-link.active { background: var(--sidebar-active); color: var(--accent); font-weight: 600; }
        .sidebar-link svg { width: 20px; height: 20px; flex-shrink: 0; opacity: 0.6; }
        .sidebar-link.active svg { opacity: 1; color: var(--accent); }

        .sidebar-link-text { }
        .sidebar-link-badge {
            margin-left: auto; padding: 0.15rem 0.5rem; border-radius: 100px;
            font-size: 0.65rem; font-weight: 700; background: var(--accent); color: white; min-width: 20px; text-align: center;
        }

        .sidebar-footer {
            padding: 1rem 1.25rem; border-top: 1px solid var(--sidebar-border);
            display: flex; align-items: center; gap: 0.75rem; flex-shrink: 0;
        }

        .sidebar-avatar {
            width: 36px; height: 36px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent), #F59E0B);
            display: flex; align-items: center; justify-content: center;
            color: white; font-weight: 700; font-size: 0.85rem; flex-shrink: 0;
        }
        .sidebar-user-name { font-size: 0.85rem; font-weight: 600; color: rgba(255,255,255,0.85); }
        .sidebar-user-role { font-size: 0.7rem; color: var(--text-muted); }

        .sidebar-logout {
            margin-left: auto; width: 32px; height: 32px; border-radius: 8px;
            border: 1px solid var(--sidebar-border); background: transparent;
            display: flex; align-items: center; justify-content: center; cursor: pointer;
            transition: all 0.25s var(--ease-out);
        }
        .sidebar-logout svg { width: 16px; height: 16px; color: var(--text-muted); }
        .sidebar-logout:hover { background: rgba(239, 68, 68, 0.15); border-color: rgba(239, 68, 68, 0.3); }
        .sidebar-logout:hover svg { color: var(--error); }

        /* ===== MOBILE HEADER ===== */
        .mobile-header {
            display: none; position: fixed; top: 0; left: 0; right: 0; height: var(--header-height);
            background: var(--sidebar-bg); z-index: 100; padding: 0 1rem;
            align-items: center; justify-content: space-between;
        }

        .mobile-header-brand { display: flex; align-items: center; gap: 0.625rem; }
        .mobile-header-brand img { width: 36px; height: 36px; border-radius: 10px; }
        .mobile-header-brand strong { font-family: 'Playfair Display', serif; font-size: 1.1rem; color: var(--accent); }

        .sidebar-toggle {
            width: 44px; height: 44px; border-radius: var(--radius-sm);
            background: var(--sidebar-hover); border: 1px solid var(--sidebar-border);
            cursor: pointer; display: flex; flex-direction: column; align-items: center;
            justify-content: center; gap: 5px;
        }
        .sidebar-toggle span { display: block; width: 20px; height: 2px; background: rgba(255,255,255,0.7); border-radius: 2px; transition: all 0.3s var(--ease-out); transform-origin: center; }

        .sidebar-overlay {
            display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.5);
            z-index: 150; opacity: 0; transition: opacity 0.3s;
        }
        .sidebar-overlay.show { display: block; opacity: 1; }

        /* ===== MAIN CONTENT ===== */
        .main-content {
            margin-left: var(--sidebar-width); min-height: 100vh;
            padding: 2rem 2rem 4rem; overflow-x: hidden;
        }

        /* Page Header */
        .page-header { display: flex; align-items: center; justify-content: space-between; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap; }
        .page-header h1 { font-size: 1.75rem; color: var(--text-primary); }
        .page-header p { font-size: 0.9rem; color: var(--text-secondary); margin-top: 0.25rem; }

        .header-actions { display: flex; gap: 0.75rem; align-items: center; }

        .btn-admin {
            display: inline-flex; align-items: center; gap: 0.5rem;
            padding: 0.75rem 1.5rem; font-family: 'Outfit', sans-serif; font-weight: 600;
            font-size: 0.85rem; border: none; border-radius: var(--radius-sm); cursor: pointer;
            transition: all 0.3s var(--ease-out);
        }
        .btn-admin svg { width: 16px; height: 16px; }
        .btn-accent { background: var(--accent); color: white; box-shadow: 0 4px 16px -4px rgba(249, 115, 22, 0.4); }
        .btn-accent:hover { transform: translateY(-2px); box-shadow: 0 8px 24px -4px rgba(249, 115, 22, 0.5); }
        .btn-outline { background: white; color: var(--text-primary); border: 1.5px solid var(--card-border); }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent); }

        .date-display {
            font-size: 0.85rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.5rem;
        }
        .date-display svg { width: 16px; height: 16px; }

        /* ===== STAT CARDS ===== */
        .stats-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 1.25rem; margin-bottom: 2rem; }

        .stat-card {
            background: var(--card-bg); border-radius: var(--radius-lg); padding: 1.5rem;
            border: 1px solid var(--card-border); position: relative; overflow: hidden;
            transition: all 0.3s var(--ease-out);
        }
        .stat-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px -12px rgba(0,0,0,0.1); }

        .stat-card-accent {
            position: absolute; top: 0; left: 0; right: 0; height: 3px;
        }

        .stat-card-top { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1rem; }

        .stat-icon-box {
            width: 48px; height: 48px; border-radius: var(--radius-sm);
            display: flex; align-items: center; justify-content: center; font-size: 1.5rem;
        }

        .stat-change {
            display: inline-flex; align-items: center; gap: 0.25rem; padding: 0.25rem 0.5rem;
            border-radius: 100px; font-size: 0.7rem; font-weight: 700;
        }
        .stat-change.up { background: var(--success-bg); color: #047857; }
        .stat-change.down { background: var(--error-bg); color: #B91C1C; }

        .stat-value { font-family: 'Playfair Display', serif; font-size: 2rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem; }
        .stat-label { font-size: 0.85rem; color: var(--text-muted); font-weight: 500; }

        /* ===== CONTENT GRID ===== */
        .content-grid { display: grid; grid-template-columns: 1fr; gap: 1.5rem; }

        /* ===== TABLE CARD ===== */
        .table-card {
            background: var(--card-bg); border-radius: var(--radius-lg); border: 1px solid var(--card-border);
            overflow: hidden;
        }

        .table-card-header {
            display: flex; align-items: center; justify-content: space-between; gap: 1rem;
            padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--card-border);
        }
        .table-card-header h3 { font-family: 'Outfit', sans-serif; font-size: 1rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem; }
        .table-card-header h3 span { font-size: 1.1rem; }

        .view-all-link {
            font-size: 0.8rem; font-weight: 600; color: var(--accent); cursor: pointer;
            display: flex; align-items: center; gap: 0.375rem; transition: gap 0.3s;
            background: none; border: none;
        }
        .view-all-link:hover { gap: 0.625rem; }
        .view-all-link svg { width: 14px; height: 14px; }

        /* Table */
        .admin-table { width: 100%; min-width: 700px; border-collapse: collapse; }
        .admin-table th {
            padding: 0.75rem 1.5rem; text-align: left; font-size: 0.7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted);
            background: var(--page-bg); border-bottom: 1px solid var(--card-border); white-space: nowrap;
        }
        .admin-table td {
            padding: 1rem 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.04);
            font-size: 0.875rem; vertical-align: middle; white-space: nowrap;
        }
        .admin-table td:first-child { white-space: normal; }
        .admin-table tbody tr { transition: background 0.2s; }
        .admin-table tbody tr:hover { background: rgba(249, 115, 22, 0.03); }
        .admin-table tbody tr:last-child td { border-bottom: none; }

        .table-wrap { overflow-x: auto; -webkit-overflow-scrolling: touch; }
        .table-wrap::-webkit-scrollbar { height: 6px; }
        .table-wrap::-webkit-scrollbar-track { background: var(--page-bg); }
        .table-wrap::-webkit-scrollbar-thumb { background: var(--card-border); border-radius: 3px; }
        .table-wrap::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }

        .table-campaign-title { font-weight: 600; color: var(--text-primary); margin-bottom: 0.125rem; max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .table-campaign-id { font-size: 0.7rem; color: var(--text-muted); font-weight: 500; }
        .table-organizer { font-size: 0.85rem; color: var(--text-secondary); }
        .table-amount { font-weight: 700; color: var(--text-primary); }
        .table-date { font-size: 0.8rem; color: var(--text-muted); }

        /* Status */
        .status-pill {
            display: inline-flex; align-items: center; gap: 0.375rem;
            padding: 0.3rem 0.75rem; border-radius: 100px; font-size: 0.7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.03em;
        }
        .status-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
        .status-pill.pending { background: var(--warning-bg); color: #92400E; }
        .status-pill.pending .status-dot { background: #92400E; }
        .status-pill.approved { background: var(--info-bg); color: #1E40AF; }
        .status-pill.approved .status-dot { background: #1E40AF; }
        .status-pill.live { background: var(--success-bg); color: #065F46; }
        .status-pill.live .status-dot { background: #065F46; animation: blink 1.5s infinite; }
        .status-pill.completed { background: #F1F5F9; color: var(--text-muted); }
        .status-pill.completed .status-dot { background: var(--text-muted); }
        .status-pill.rejected { background: var(--error-bg); color: #991B1B; }
        .status-pill.rejected .status-dot { background: #991B1B; }
        @keyframes blink { 0%,100% { opacity:1; } 50% { opacity:0.3; } }

        /* Action button */
        .btn-view {
            padding: 0.4rem 0.875rem; font-size: 0.75rem; font-weight: 600;
            font-family: 'Outfit', sans-serif; border-radius: 8px; cursor: pointer;
            transition: all 0.25s var(--ease-out); border: 1.5px solid var(--card-border);
            background: white; color: var(--text-primary); display: inline-flex; align-items: center; gap: 0.375rem;
            text-decoration: none;
        }
        .btn-view svg { width: 14px; height: 14px; }
        .btn-view:hover { border-color: var(--accent); color: var(--accent); background: var(--accent-bg); }

        /* ===== DONATIONS CARD ===== */
        .donation-item {
            display: flex; align-items: center; gap: 0.875rem;
            padding: 1rem 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.04);
            transition: background 0.2s;
        }
        .donation-item:last-child { border-bottom: none; }
        .donation-item:hover { background: rgba(249, 115, 22, 0.03); }

        .donation-avatar {
            width: 40px; height: 40px; border-radius: 50%;
            background: linear-gradient(135deg, var(--accent-bg), var(--accent-border));
            display: flex; align-items: center; justify-content: center;
            font-size: 0.9rem; font-weight: 700; color: var(--accent); flex-shrink: 0;
        }
        .donation-meta { flex: 1; min-width: 0; }
        .donation-name { font-weight: 600; font-size: 0.875rem; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .donation-campaign { font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .donation-right { text-align: right; flex-shrink: 0; }
        .donation-amt { font-weight: 700; font-size: 0.95rem; color: var(--accent); }
        .donation-time { font-size: 0.7rem; color: var(--text-muted); }

        /* ===== ACTIVITY TIMELINE (bottom) ===== */
        .activity-section { margin-top: 1.5rem; }
        .activity-card {
            background: var(--card-bg); border-radius: var(--radius-lg); border: 1px solid var(--card-border); overflow: hidden;
        }

        .activity-row {
            display: flex; align-items: center; gap: 1rem;
            padding: 1rem 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.04);
            transition: background 0.2s;
        }
        .activity-row:last-child { border-bottom: none; }
        .activity-row:hover { background: rgba(0,0,0,0.01); }

        .activity-icon-box {
            width: 36px; height: 36px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 1rem; flex-shrink: 0;
        }
        .activity-icon-box.new-request { background: var(--warning-bg); }
        .activity-icon-box.approved { background: var(--success-bg); }
        .activity-icon-box.donation { background: var(--accent-bg); }
        .activity-icon-box.rejected { background: var(--error-bg); }

        .activity-text { flex: 1; font-size: 0.85rem; color: var(--text-secondary); }
        .activity-text strong { color: var(--text-primary); }
        .activity-time { font-size: 0.75rem; color: var(--text-muted); white-space: nowrap; flex-shrink: 0; }

        /* ===== RESPONSIVE ===== */
        @media (max-width: 1200px) {
            .stats-grid { grid-template-columns: repeat(3, 1fr); }
        }

        @media (max-width: 1024px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.open { transform: translateX(0); box-shadow: 8px 0 32px rgba(0,0,0,0.3); }
            .mobile-header { display: flex; }
            .main-content { margin-left: 0; padding-top: calc(var(--header-height) + 1.5rem); }
            .sidebar-close { display: flex; }
        }

        @media (max-width: 768px) {
            .main-content { padding: calc(var(--header-height) + 1rem) 1rem 3rem; }
            .page-header { flex-direction: column; align-items: flex-start; }
            .stats-grid { gap: 0.75rem; }
            .stat-card { padding: 1.25rem; }
            .stat-value { font-size: 1.5rem; }
            .admin-table th, .admin-table td { padding: 0.75rem 1rem; }
            .table-card-header { padding: 1rem; }
        }

        @media (max-width: 480px) {
            html { font-size: 15px; }
            .stats-grid { grid-template-columns: 1fr 1fr; }
            .stat-icon-box { width: 40px; height: 40px; font-size: 1.25rem; }
            .stat-value { font-size: 1.35rem; }
            .page-header h1 { font-size: 1.35rem; }
            .header-actions { width: 100%; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <img src="logo.jpg" alt="SAHARA" class="sidebar-logo">
            <div class="sidebar-brand">
                <strong>SAHARA</strong>
                <span>IIT M BS Welfare</span>
                <div class="sidebar-admin-tag">Admin Panel</div>
            </div>
            <button class="sidebar-close" id="sidebarClose" aria-label="Close sidebar">
                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <nav class="sidebar-nav">
            <div class="sidebar-label">Main</div>
            <button class="sidebar-link active" data-page="dashboard">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 5a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM14 5a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1h-4a1 1 0 01-1-1V5zM4 16a1 1 0 011-1h4a1 1 0 011 1v3a1 1 0 01-1 1H5a1 1 0 01-1-1v-3zM14 13a1 1 0 011-1h4a1 1 0 011 1v5a1 1 0 01-1 1h-4a1 1 0 01-1-1v-5z"/></svg>
                <span class="sidebar-link-text">Dashboard</span>
            </button>
            <button class="sidebar-link" data-page="campaigns">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                <span class="sidebar-link-text">Fundraise Requests</span>
                <span class="sidebar-link-badge">3</span>
            </button>
            <button class="sidebar-link" data-page="donations">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                <span class="sidebar-link-text">Donation Requests</span>
                <span class="sidebar-link-badge">5</span>
            </button>

            <div class="sidebar-label">Management</div>
            <button class="sidebar-link" data-page="users">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="sidebar-link-text">Users</span>
            </button>
            <button class="sidebar-link" data-page="reports">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                <span class="sidebar-link-text">Reports</span>
            </button>
            <button class="sidebar-link" data-page="settings">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.066 2.573c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.573 1.066c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.066-2.573c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.573-1.066z"/><circle cx="12" cy="12" r="3"/></svg>
                <span class="sidebar-link-text">Settings</span>
            </button>
        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-avatar">A</div>
            <div>
                <div class="sidebar-user-name">Admin</div>
                <div class="sidebar-user-role">Super Admin</div>
            </div>
            <button class="sidebar-logout" title="Logout">
                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
            </button>
        </div>
    </aside>

    <!-- Mobile Header -->
    <div class="mobile-header">
        <div class="mobile-header-brand">
            <img src="logo.jpg" alt="SAHARA">
            <div>
                <strong>SAHARA</strong>
                <span style="display:block;font-size:0.55rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.1em;font-weight:500;">IIT M BS Welfare</span>
            </div>
        </div>
        <button class="sidebar-toggle" id="sidebarToggle">
            <span></span><span></span><span></span>
        </button>
    </div>

    <!-- Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <div>
                <h1>Dashboard</h1>
                <p>Overview of campaigns, donations, and platform activity</p>
            </div>
            <div class="header-actions">
                <div class="date-display">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <span id="currentDate"></span>
                </div>
                <a href="index.html" class="btn-admin btn-outline">
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                    View Site
                </a>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-card-accent" style="background: linear-gradient(90deg, #10B981, #34D399);"></div>
                <div class="stat-card-top">
                    <div class="stat-icon-box" style="background: var(--success-bg);">🟢</div>
                    <span class="stat-change up">↑ 8%</span>
                </div>
                <div class="stat-value">8</div>
                <div class="stat-label">Live Campaigns</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-accent" style="background: linear-gradient(90deg, #F59E0B, #FBBF24);"></div>
                <div class="stat-card-top">
                    <div class="stat-icon-box" style="background: var(--warning-bg);">⏳</div>
                    <span class="stat-change up">+3 new</span>
                </div>
                <div class="stat-value">3</div>
                <div class="stat-label">Pending for Review</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-accent" style="background: linear-gradient(90deg, #EF4444, #F87171);"></div>
                <div class="stat-card-top">
                    <div class="stat-icon-box" style="background: var(--error-bg);">💝</div>
                    <span class="stat-change up">+5 new</span>
                </div>
                <div class="stat-value">5</div>
                <div class="stat-label">Donations Pending</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-accent" style="background: linear-gradient(90deg, #3B82F6, #60A5FA);"></div>
                <div class="stat-card-top">
                    <div class="stat-icon-box" style="background: var(--info-bg);">📊</div>
                    <span class="stat-change up">↑ 18%</span>
                </div>
                <div class="stat-value">156</div>
                <div class="stat-label">Total Donations Done</div>
            </div>
            <div class="stat-card">
                <div class="stat-card-accent" style="background: linear-gradient(90deg, #F97316, #FB923C);"></div>
                <div class="stat-card-top">
                    <div class="stat-icon-box" style="background: var(--accent-bg);">💰</div>
                    <span class="stat-change up">↑ 24%</span>
                </div>
                <div class="stat-value">₹18.5L</div>
                <div class="stat-label">Total Raised Amount</div>
            </div>
        </div>

        <!-- Content Grid: Campaigns Table + Donations -->
        <div class="content-grid">
            <!-- Recent Fundraise Requests -->
            <div class="table-card">
                <div class="table-card-header">
                    <h3><span>📋</span> Recent Fundraise Requests</h3>
                    <button class="view-all-link">View All <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
                </div>
                <div class="table-wrap">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Campaign</th>
                            <th>Organizer</th>
                            <th>Goal</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="campaignTableBody"></tbody>
                </table>
                </div>
            </div>

            <!-- Recent Donations -->
            <div class="table-card">
                <div class="table-card-header">
                    <h3><span>💝</span> Recent Donations</h3>
                    <button class="view-all-link">View All <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
                </div>
                <div id="donationListAdmin"></div>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="activity-section">
            <div class="activity-card">
                <div class="table-card-header">
                    <h3><span>🔔</span> Recent Activity</h3>
                    <button class="view-all-link">View All <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>
                </div>
                <div id="activityTimeline"></div>
            </div>
        </div>
    </div>

    <script>
        // ===== SIDEBAR MOBILE =====
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function closeSidebar() {
            sidebar.classList.remove('open');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        function openSidebar() {
            sidebar.classList.add('open');
            sidebarOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        sidebarToggle.addEventListener('click', () => {
            if (sidebar.classList.contains('open')) { closeSidebar(); }
            else { openSidebar(); }
        });

        sidebarClose.addEventListener('click', closeSidebar);
        sidebarOverlay.addEventListener('click', closeSidebar);

        // Sidebar link highlight
        document.querySelectorAll('.sidebar-link').forEach(link => {
            link.addEventListener('click', () => {
                document.querySelectorAll('.sidebar-link').forEach(l => l.classList.remove('active'));
                link.classList.add('active');
                closeSidebar();
            });
        });

        // Current date
        document.getElementById('currentDate').textContent = new Date().toLocaleDateString('en-IN', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });

        // ===== DATA =====
        const fundraiseRequests = [
            { id: "SAH-2026-018", title: "Community Library Project", organizer: "Tabish Shaikh", category: "Community", goal: 250000, status: "pending", date: "2026-03-29" },
            { id: "SAH-2026-017", title: "Mental Health Helpline Setup", organizer: "Neha Gupta", category: "Health", goal: 180000, status: "pending", date: "2026-03-28" },
            { id: "SAH-2026-016", title: "Disaster Relief – Assam Floods", organizer: "Amit Verma", category: "Disaster", goal: 500000, status: "pending", date: "2026-03-27" },
            { id: "SAH-2026-015", title: "Medical Emergency – Rahul's Surgery", organizer: "Ananya Sharma", category: "Medical", goal: 500000, status: "live", date: "2026-03-25" },
            { id: "SAH-2026-014", title: "Laptop Fund for Merit Students", organizer: "Tabish Shaikh", category: "Education", goal: 300000, status: "live", date: "2026-03-20" },
            { id: "SAH-2026-013", title: "Women Safety Workshop Series", organizer: "Priya K", category: "Community", goal: 80000, status: "live", date: "2026-03-18" },
            { id: "SAH-2026-010", title: "Scholarship for Single Parent Kids", organizer: "Karthik R", category: "Education", goal: 200000, status: "completed", date: "2026-01-15" },
            { id: "SAH-2026-008", title: "Cancer Treatment – Meera's Fight", organizer: "Sneha G", category: "Medical", goal: 800000, status: "completed", date: "2026-02-01" }
        ];

        const recentDonations = [
            { donor: "Priya Mehta", campaignId: "SAH-2026-015", campaign: "Rahul's Surgery", amount: 10000, time: "2 hours ago" },
            { donor: "Anonymous", campaignId: "SAH-2026-014", campaign: "Laptop Fund", amount: 25000, time: "5 hours ago" },
            { donor: "Rahul Verma", campaignId: "SAH-2026-015", campaign: "Rahul's Surgery", amount: 5000, time: "8 hours ago" },
            { donor: "Amit Singh", campaignId: "SAH-2026-013", campaign: "Women Safety Workshop", amount: 2000, time: "12 hours ago" },
            { donor: "Sneha Gupta", campaignId: "SAH-2026-014", campaign: "Laptop Fund", amount: 1000, time: "1 day ago" },
            { donor: "Anonymous", campaignId: "SAH-2026-015", campaign: "Rahul's Surgery", amount: 15000, time: "1 day ago" },
            { donor: "Vikram Patel", campaignId: "SAH-2026-013", campaign: "Women Safety Workshop", amount: 3000, time: "2 days ago" }
        ];

        const activities = [
            { type: "new-request", icon: "📝", text: '<strong>Tabish Shaikh</strong> submitted a new fundraise request — Community Library Project', time: "2 hours ago" },
            { type: "donation", icon: "💝", text: '<strong>Priya Mehta</strong> donated ₹10,000 to Rahul\'s Surgery', time: "2 hours ago" },
            { type: "approved", icon: "✅", text: 'Admin <strong>approved</strong> Women Safety Workshop (SAH-2026-013)', time: "5 hours ago" },
            { type: "donation", icon: "💝", text: '<strong>Anonymous</strong> donated ₹25,000 to Laptop Fund', time: "5 hours ago" },
            { type: "new-request", icon: "📝", text: '<strong>Neha Gupta</strong> submitted — Mental Health Helpline Setup', time: "1 day ago" },
            { type: "rejected", icon: "❌", text: 'Admin <strong>rejected</strong> request SAH-2026-011 — insufficient documentation', time: "2 days ago" }
        ];

        // ===== RENDER CAMPAIGNS TABLE =====
        function renderCampaignTable() {
            const tbody = document.getElementById('campaignTableBody');
            const statusLabels = { pending: 'Pending', approved: 'Approved', live: 'Live', completed: 'Completed', rejected: 'Rejected' };

            tbody.innerHTML = fundraiseRequests.map(c => `
                <tr>
                    <td>
                        <div class="table-campaign-title">${c.title}</div>
                        <div class="table-campaign-id">${c.id}</div>
                    </td>
                    <td class="table-organizer">${c.organizer}</td>
                    <td class="table-amount">₹${(c.goal / 1000).toFixed(0)}K</td>
                    <td><span class="status-pill ${c.status}"><span class="status-dot"></span>${statusLabels[c.status]}</span></td>
                    <td class="table-date">${formatDate(c.date)}</td>
                    <td><a href="admin-request-view.html?id=${c.id}" target="_blank" class="btn-view"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>View</a></td>
                </tr>
            `).join('');
        }

        // ===== RENDER DONATIONS =====
        function renderDonations() {
            const list = document.getElementById('donationListAdmin');
            list.innerHTML = recentDonations.map(d => `
                <div class="donation-item">
                    <div class="donation-avatar">${d.donor === 'Anonymous' ? '?' : d.donor.charAt(0)}</div>
                    <div class="donation-meta">
                        <div class="donation-name">${d.donor}</div>
                        <div class="donation-campaign">${d.campaignId} · ${d.campaign}</div>
                    </div>
                    <div class="donation-right">
                        <div class="donation-amt">₹${d.amount.toLocaleString('en-IN')}</div>
                        <div class="donation-time">${d.time}</div>
                    </div>
                    <a href="admin-donation-view.html?campaign=${d.campaignId}&donor=${encodeURIComponent(d.donor)}" target="_blank" class="btn-view" style="flex-shrink:0;"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>View</a>
                </div>
            `).join('');
        }

        // ===== RENDER ACTIVITY =====
        function renderActivity() {
            const timeline = document.getElementById('activityTimeline');
            timeline.innerHTML = activities.map(a => `
                <div class="activity-row">
                    <div class="activity-icon-box ${a.type}">${a.icon}</div>
                    <div class="activity-text">${a.text}</div>
                    <div class="activity-time">${a.time}</div>
                </div>
            `).join('');
        }

        // ===== UTILS =====
        function formatDate(dateStr) {
            return new Date(dateStr).toLocaleDateString('en-IN', { day: 'numeric', month: 'short' });
        }

        // ===== INIT =====
        renderCampaignTable();
        renderDonations();
        renderActivity();
    </script>
</body>
</html>