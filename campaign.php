<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['name'] ?? null;
$user_email = $_SESSION['email'] ?? null;
$user_phone = $_SESSION['phone'] ?? null;
$role = $_SESSION['role'] ?? null;

$current_page = "campaign.php";
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
                        <span class="badge campaign-id" id="campaignIdBadge">ID: SAH-2026-002</span>
                        <span class="badge category" id="categoryBadge">🏥 Medical</span>
                        <span class="badge urgent" id="urgentBadge">🔥 Urgent</span>
                    </div>
                    <div class="campaign-hero-title">
                        <h1 id="campaignTitle">Medical Emergency – Rahul's Surgery</h1>
                        <div class="campaign-meta-row">
                            <div class="meta-item">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span>Started <span id="campaignDate">March 25, 2026</span></span>
                            </div>
                            <div class="meta-item">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span><span id="daysLeft">5</span> days left</span>
                            </div>
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
                            <p>Rahul, a 22-year-old BS Data Science student at IIT Madras, met with a severe accident last month that left him with critical spinal injuries. The doctors have recommended an urgent surgery to prevent permanent paralysis.</p>
                            <p>The estimated cost for the surgery and post-operative care is ₹5,00,000. Rahul's family, who are daily wage workers from a small village in Bihar, cannot afford this amount. Despite the hardship, Rahul has always been a brilliant student with dreams of becoming a data scientist.</p>
                            <p>Your contribution, no matter how small, can help save Rahul's future. All funds raised will go directly towards his medical treatment, surgery, and rehabilitation. Let's come together as a community to support one of our own.</p>
                        </div>

                        <!-- Beneficiary Details -->
                        <div class="beneficiary-card">
                            <div class="beneficiary-avatar">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            </div>
                            <div class="beneficiary-info">
                                <div class="beneficiary-label">Beneficiary Details</div>
                                <div class="beneficiary-name" id="beneficiaryName">Rahul Kumar</div>
                                <div class="beneficiary-details">
                                    <div class="beneficiary-detail">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                        <span id="beneficiaryContact">+91 98765 43210</span>
                                    </div>
                                    <div class="beneficiary-detail">
                                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                        <span id="beneficiaryCity">Patna, Bihar</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Organizer Section -->
                    <div class="organizer-section">
                        <h3>Campaign Organizer</h3>
                        <div class="organizer-card">
                            <div class="organizer-avatar" id="organizerInitial">A</div>
                            <div class="organizer-info">
                                <h4 id="organizerName">Ananya Sharma</h4>
                                <p>BS Data Science, Batch 2024</p>
                                <div class="verified-badge">
                                    <svg fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                                    Verified Organizer
                                </div>
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
                                <span class="raised-amount" id="raisedAmount">₹4,20,000</span>
                                <span class="goal-amount">raised of <span id="goalAmount">₹5,00,000</span></span>
                            </div>
                            <div class="progress-bar-bg">
                                <div class="progress-bar-fill" id="progressBar" style="width: 84%"></div>
                            </div>
                            <div class="progress-stats">
                                <div class="stat-item">
                                    <div class="stat-value" id="donorCount">312</div>
                                    <div class="stat-label">Donors</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value" id="daysLeftStat">5</div>
                                    <div class="stat-label">Days Left</div>
                                </div>
                                <div class="stat-item">
                                    <div class="stat-value" id="percentFunded">84%</div>
                                    <div class="stat-label">Funded</div>
                                </div>
                            </div>
                        </div>

                        <div class="donation-amounts">
                            <button class="amount-option" data-amount="500">₹500</button>
                            <button class="amount-option" data-amount="1000">₹1,000</button>
                            <button class="amount-option selected" data-amount="2000">₹2,000</button>
                            <button class="amount-option" data-amount="5000">₹5,000</button>
                            <button class="amount-option" data-amount="10000">₹10,000</button>
                            <button class="amount-option" data-amount="25000">₹25,000</button>
                        </div>

                        <div class="custom-amount">
                            <label for="customAmount">Or enter custom amount</label>
                            <div class="amount-input-wrapper">
                                <span class="currency">₹</span>
                                <input type="number" id="customAmount" class="amount-input" placeholder="Enter amount" min="100">
                            </div>
                        </div>

                        <!-- Donor Info Form -->
                        <div class="donor-form">
                            <div class="donor-form-title">
                                <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Your Details <span style="color: var(--error); font-size: 0.75rem;">(Required)</span>
                            </div>
                            <div class="form-group">
                                <input type="text" id="donorName" class="form-input" placeholder="Full Name" required>
                                <div class="form-error" id="nameError">Please enter your name</div>
                            </div>
                            <div class="form-group">
                                <input type="tel" id="donorPhone" class="form-input" placeholder="Phone Number" required>
                                <div class="form-error" id="phoneError">Please enter a valid phone number</div>
                            </div>
                            <div class="form-group">
                                <input type="email" id="donorEmail" class="form-input" placeholder="Email Address" required>
                                <div class="form-error" id="emailError">Please enter a valid email address</div>
                            </div>
                        </div>

                        <button class="btn btn-sun donate-btn" id="donateBtn">
                            <span>Donate Now</span>
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:20px;height:20px;"><path stroke-linecap="round" stroke-linejoin="round" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        </button>
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
                <p><strong>Name:</strong> <span id="modalDonorName"></span></p>
                <p><strong>Phone:</strong> <span id="modalDonorPhone"></span></p>
                <p><strong>Email:</strong> <span id="modalDonorEmail"></span></p>
                <p><strong>Campaign ID:</strong> <span id="modalCampaignId"></span></p>
            </div>
            <div class="modal-note">
                <p>🙏 <strong>SAHARA team will reach out to you soon</strong> with payment details for your generous donation.</p>
            </div>
            <p>Thank you for supporting this campaign. Your kindness makes a difference!</p>
            <button class="btn btn-sun modal-btn" id="closeModal">Got it!</button>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script>
        // Mobile Menu
        const menuToggle = document.getElementById('menuToggle');
        const mobileNav = document.getElementById('mobileNav');

        menuToggle.addEventListener('click', () => {
            menuToggle.classList.toggle('active');
            mobileNav.classList.toggle('active');
            document.body.style.overflow = mobileNav.classList.contains('active') ? 'hidden' : '';
        });

        document.querySelectorAll('[data-close]').forEach(link => {
            link.addEventListener('click', () => {
                menuToggle.classList.remove('active');
                mobileNav.classList.remove('active');
                document.body.style.overflow = '';
            });
        });

        // Header Scroll
        const header = document.getElementById('header');
        window.addEventListener('scroll', () => header.classList.toggle('scrolled', window.scrollY > 50));

        // Sample Campaign Data
        const campaign = {
            id: 2,
            campaignId: "SAH-2026-002",
            title: "Medical Emergency – Rahul's Surgery",
            category: "Medical",
            raised: 420000,
            goal: 500000,
            donors: 312,
            urgent: true,
            completed: false,
            daysLeft: 5,
            date: "2026-03-25",
            location: "Chennai, Tamil Nadu",
            organizer: { name: "Ananya Sharma", batch: "BS Data Science, Batch 2024" },
            beneficiary: { name: "Rahul Kumar", contact: "+91 98765 43210", city: "Patna, Bihar" }
        };

        // Sample Media Data
        const mediaItems = [
            { type: 'image', src: 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=800', thumb: 'https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=200' },
            { type: 'image', src: 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=800', thumb: 'https://images.unsplash.com/photo-1576091160550-2173dba999ef?w=200' },
            { type: 'video', src: 'https://www.w3schools.com/html/mov_bbb.mp4', thumb: 'https://images.unsplash.com/photo-1559757148-5c350d0d3c56?w=200' },
            { type: 'image', src: 'https://images.unsplash.com/photo-1551190822-a9333d879b1f?w=800', thumb: 'https://images.unsplash.com/photo-1551190822-a9333d879b1f?w=200' },
            { type: 'image', src: 'https://images.unsplash.com/photo-1538108149393-fbbd81895907?w=800', thumb: 'https://images.unsplash.com/photo-1538108149393-fbbd81895907?w=200' }
        ];

        // Sample Donors Data
        const donors = [
            { name: "Priya Mehta", amount: 10000, time: "2 hours ago", anonymous: false },
            { name: "Rahul Verma", amount: 5000, time: "5 hours ago", anonymous: false },
            { name: "Anonymous", amount: 25000, time: "8 hours ago", anonymous: true },
            { name: "Amit Singh", amount: 2000, time: "12 hours ago", anonymous: false },
            { name: "Sneha Gupta", amount: 1000, time: "1 day ago", anonymous: false },
            { name: "Anonymous", amount: 15000, time: "1 day ago", anonymous: true },
            { name: "Vikram Patel", amount: 3000, time: "2 days ago", anonymous: false },
            { name: "Neha Sharma", amount: 500, time: "2 days ago", anonymous: false },
            { name: "Karthik R", amount: 7500, time: "3 days ago", anonymous: false },
            { name: "Anonymous", amount: 50000, time: "3 days ago", anonymous: true }
        ];

        // Media Slider
        let currentSlide = 0;
        const sliderTrack = document.getElementById('sliderTrack');
        const sliderDots = document.getElementById('sliderDots');
        const thumbnails = document.getElementById('mediaThumbnails');

        function renderMedia() {
            sliderTrack.innerHTML = mediaItems.map((item, i) => `
                <div class="media-slide">
                    ${item.type === 'video' ? `<video src="${item.src}" controls></video>` : `<img src="${item.src}" alt="Campaign media ${i + 1}">`}
                </div>
            `).join('');

            sliderDots.innerHTML = mediaItems.map((_, i) => `<button class="slider-dot ${i === 0 ? 'active' : ''}" data-index="${i}"></button>`).join('');

            thumbnails.innerHTML = mediaItems.map((item, i) => `
                <div class="media-thumb ${item.type === 'video' ? 'video-thumb' : ''} ${i === 0 ? 'active' : ''}" data-index="${i}">
                    <img src="${item.thumb}" alt="Thumbnail ${i + 1}">
                </div>
            `).join('');

            document.querySelectorAll('.slider-dot').forEach(dot => dot.addEventListener('click', () => goToSlide(parseInt(dot.dataset.index))));
            document.querySelectorAll('.media-thumb').forEach(thumb => thumb.addEventListener('click', () => goToSlide(parseInt(thumb.dataset.index))));
        }

        function goToSlide(index) {
            currentSlide = index;
            sliderTrack.style.transform = `translateX(-${index * 100}%)`;
            document.querySelectorAll('.slider-dot').forEach((dot, i) => dot.classList.toggle('active', i === index));
            document.querySelectorAll('.media-thumb').forEach((thumb, i) => thumb.classList.toggle('active', i === index));
            document.querySelectorAll('.media-slide video').forEach((video, i) => { if (i !== index) video.pause(); });
        }

        document.getElementById('prevBtn').addEventListener('click', () => goToSlide(currentSlide === 0 ? mediaItems.length - 1 : currentSlide - 1));
        document.getElementById('nextBtn').addEventListener('click', () => goToSlide(currentSlide === mediaItems.length - 1 ? 0 : currentSlide + 1));

        let autoSlideInterval;
        function startAutoSlide() { autoSlideInterval = setInterval(() => goToSlide(currentSlide === mediaItems.length - 1 ? 0 : currentSlide + 1), 5000); }
        function stopAutoSlide() { clearInterval(autoSlideInterval); }

        renderMedia();
        startAutoSlide();

        document.querySelector('.media-slider').addEventListener('mouseenter', stopAutoSlide);
        document.querySelector('.media-slider').addEventListener('mouseleave', startAutoSlide);

        // Render Donors
        function renderDonors(showAll = false) {
            const donorsList = document.getElementById('donorsList');
            const displayDonors = showAll ? donors : donors.slice(0, 5);
            donorsList.innerHTML = displayDonors.map(donor => `
                <div class="donor-item">
                    <div class="donor-avatar">${donor.anonymous ? '?' : donor.name.charAt(0)}</div>
                    <div class="donor-info">
                        <div class="donor-name">${donor.anonymous ? 'Anonymous Donor' : donor.name}</div>
                        <div class="donor-time">${donor.time}</div>
                    </div>
                    <div class="donor-amount">₹${donor.amount.toLocaleString('en-IN')}</div>
                </div>
            `).join('');
        }

        renderDonors();

        let showingAllDonors = false;
        document.getElementById('showMoreDonors').addEventListener('click', () => {
            showingAllDonors = !showingAllDonors;
            renderDonors(showingAllDonors);
            document.getElementById('showMoreDonors').textContent = showingAllDonors ? 'Show Less' : 'Show All Donors';
        });

        // Donation Amount Selection
        const amountOptions = document.querySelectorAll('.amount-option');
        const customAmountInput = document.getElementById('customAmount');
        let selectedAmount = 2000;

        amountOptions.forEach(option => {
            option.addEventListener('click', () => {
                amountOptions.forEach(o => o.classList.remove('selected'));
                option.classList.add('selected');
                selectedAmount = parseInt(option.dataset.amount);
                customAmountInput.value = '';
            });
        });

        customAmountInput.addEventListener('input', () => {
            if (customAmountInput.value) {
                amountOptions.forEach(o => o.classList.remove('selected'));
                selectedAmount = parseInt(customAmountInput.value) || 0;
            }
        });

        // Form Validation
        const donorNameInput = document.getElementById('donorName');
        const donorPhoneInput = document.getElementById('donorPhone');
        const donorEmailInput = document.getElementById('donorEmail');

        function validateForm() {
            let isValid = true;

            // Name validation
            const name = donorNameInput.value.trim();
            const nameError = document.getElementById('nameError');
            if (!name) {
                donorNameInput.classList.add('error');
                nameError.classList.add('show');
                isValid = false;
            } else {
                donorNameInput.classList.remove('error');
                nameError.classList.remove('show');
            }

            // Phone validation
            const phone = donorPhoneInput.value.trim();
            const phoneError = document.getElementById('phoneError');
            const phoneRegex = /^[6-9]\d{9}$/;
            if (!phone || !phoneRegex.test(phone)) {
                donorPhoneInput.classList.add('error');
                phoneError.classList.add('show');
                isValid = false;
            } else {
                donorPhoneInput.classList.remove('error');
                phoneError.classList.remove('show');
            }

            // Email validation
            const email = donorEmailInput.value.trim();
            const emailError = document.getElementById('emailError');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!email || !emailRegex.test(email)) {
                donorEmailInput.classList.add('error');
                emailError.classList.add('show');
                isValid = false;
            } else {
                donorEmailInput.classList.remove('error');
                emailError.classList.remove('show');
            }

            return isValid;
        }

        // Clear error on input
        donorNameInput.addEventListener('input', () => { donorNameInput.classList.remove('error'); document.getElementById('nameError').classList.remove('show'); });
        donorPhoneInput.addEventListener('input', () => { donorPhoneInput.classList.remove('error'); document.getElementById('phoneError').classList.remove('show'); });
        donorEmailInput.addEventListener('input', () => { donorEmailInput.classList.remove('error'); document.getElementById('emailError').classList.remove('show'); });

        // Donation Modal
        const donationModal = document.getElementById('donationModal');

        document.getElementById('donateBtn').addEventListener('click', () => {
            if (selectedAmount < 100) { alert('Minimum donation amount is ₹100'); return; }
            if (!validateForm()) return;

            document.getElementById('modalAmount').textContent = '₹' + selectedAmount.toLocaleString('en-IN');
            document.getElementById('modalDonorName').textContent = donorNameInput.value.trim();
            document.getElementById('modalDonorPhone').textContent = donorPhoneInput.value.trim();
            document.getElementById('modalDonorEmail').textContent = donorEmailInput.value.trim();
            document.getElementById('modalCampaignId').textContent = campaign.campaignId;
            donationModal.classList.add('show');
        });

        document.getElementById('closeModal').addEventListener('click', () => donationModal.classList.remove('show'));
        donationModal.addEventListener('click', (e) => { if (e.target === donationModal) donationModal.classList.remove('show'); });

        // Share Functions
        const shareUrl = window.location.href;
        const shareText = `Help support ${campaign.title} (${campaign.campaignId}). Every contribution makes a difference!`;

        function shareWhatsApp() { window.open(`https://wa.me/?text=${encodeURIComponent(shareText + ' ' + shareUrl)}`, '_blank'); }
        function shareTwitter() { window.open(`https://twitter.com/intent/tweet?text=${encodeURIComponent(shareText)}&url=${encodeURIComponent(shareUrl)}`, '_blank'); }
        function shareFacebook() { window.open(`https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(shareUrl)}`, '_blank'); }
        function shareInstagram() {
            // Instagram doesn't support direct URL sharing, so copy link and notify
            navigator.clipboard.writeText(shareText + ' ' + shareUrl).then(() => {
                alert('Link copied! Open Instagram and paste it in your story or DM to share.');
            });
        }
        function copyLink() { navigator.clipboard.writeText(shareUrl).then(() => alert('Link copied to clipboard!')); }

        const categoryIcons = { 'Education': '📚', 'Medical': '🏥', 'Health': '💚', 'Disaster': '🆘', 'Community': '🤝' };
        function formatCurrency(amount) { return '₹' + amount.toLocaleString('en-IN'); }
        function formatDate(dateStr) { return new Date(dateStr).toLocaleDateString('en-IN', { year: 'numeric', month: 'long', day: 'numeric' }); }

        function updatePage() {
            document.getElementById('campaignTitle').textContent = campaign.title;
            document.getElementById('campaignIdBadge').textContent = 'ID: ' + campaign.campaignId;
            document.getElementById('categoryBadge').innerHTML = `${categoryIcons[campaign.category] || '📋'} ${campaign.category}`;
            document.getElementById('campaignDate').textContent = formatDate(campaign.date);
            document.getElementById('daysLeft').textContent = campaign.daysLeft;
            document.getElementById('campaignLocation').textContent = campaign.location;

            const urgentBadge = document.getElementById('urgentBadge');
            if (campaign.completed) { urgentBadge.className = 'badge completed'; urgentBadge.textContent = '✓ Completed'; }
            else if (campaign.urgent) { urgentBadge.className = 'badge urgent'; urgentBadge.textContent = '🔥 Urgent'; }
            else { urgentBadge.style.display = 'none'; }

            const percent = Math.round((campaign.raised / campaign.goal) * 100);
            document.getElementById('raisedAmount').textContent = formatCurrency(campaign.raised);
            document.getElementById('goalAmount').textContent = formatCurrency(campaign.goal);
            document.getElementById('progressBar').style.width = Math.min(percent, 100) + '%';
            document.getElementById('donorCount').textContent = campaign.donors;
            document.getElementById('daysLeftStat').textContent = campaign.daysLeft;
            document.getElementById('percentFunded').textContent = percent + '%';
            document.getElementById('totalDonors').textContent = `${campaign.donors} people have donated`;
            document.getElementById('organizerName').textContent = campaign.organizer.name;
            document.getElementById('organizerInitial').textContent = campaign.organizer.name.charAt(0);

            document.getElementById('beneficiaryName').textContent = campaign.beneficiary.name;
            document.getElementById('beneficiaryContact').textContent = campaign.beneficiary.contact;
            document.getElementById('beneficiaryCity').textContent = campaign.beneficiary.city;
        }

        updatePage();
    </script>
</body>
</html>