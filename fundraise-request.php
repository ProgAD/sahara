<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['name'] ?? null;
$user_email = $_SESSION['email'] ?? null;
$user_phone = $_SESSION['phone'] ?? null;
$role = $_SESSION['role'] ?? null;

// Determine if we should bypass verification
$is_logged_in = (!empty($user_name) && !empty($user_email) && $role === 'user');

$current_page = "fundraise-request.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Start a Fundraiser – SAHARA</title>
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
            --radius-sm: 12px;
            --radius-md: 20px;
            --radius-lg: 32px;
            --radius-xl: 48px;
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

        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 256 256' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noise'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.85' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noise)'/%3E%3C/svg%3E");
            opacity: 0.03;
            pointer-events: none;
            z-index: 9999;
        }

        h1, h2, h3, h4, h5 {
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            line-height: 1.15;
            letter-spacing: -0.02em;
        }

        p { color: var(--stone); }
        a { text-decoration: none; color: inherit; }
        img { max-width: 100%; display: block; }

        .container { width: 100%; max-width: 1400px; margin: 0 auto; padding: 0 clamp(1.25rem, 5vw, 3rem); }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 1rem 2rem;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 0.95rem;
            border: none;
            border-radius: 100px;
            cursor: pointer;
            transition: all 0.4s var(--ease-out);
            white-space: nowrap;
        }

        .btn-sun {
            background: linear-gradient(135deg, var(--sun) 0%, var(--amber) 100%);
            color: white;
            box-shadow: 0 8px 32px -8px rgba(249, 115, 22, 0.5), inset 0 1px 0 rgba(255,255,255,0.2);
        }

        .btn-sun:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 16px 48px -8px rgba(249, 115, 22, 0.6), inset 0 1px 0 rgba(255,255,255,0.2);
        }

        .btn-sun:active { transform: translateY(-1px) scale(0.98); }

        .btn-ghost {
            background: transparent;
            color: var(--earth);
            border: 2px solid rgba(41, 37, 36, 0.15);
        }

        .btn-ghost:hover {
            border-color: var(--sun);
            color: var(--sun);
            background: rgba(249, 115, 22, 0.05);
        }

        .btn svg { width: 18px; height: 18px; transition: transform 0.3s var(--ease-out); }
        .btn:hover svg { transform: translateX(4px); }

        

        /* Page Content */
        .page-content { padding-top: 100px; padding-bottom: 4rem; }

        /* Page Hero */
        .page-hero { text-align: center; padding: 3rem 0; position: relative; }

        .page-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, var(--peach) 0%, transparent 70%);
            opacity: 0.4;
            filter: blur(80px);
            pointer-events: none;
        }

        .page-hero h1 { font-size: clamp(2rem, 5vw, 3rem); color: var(--earth); margin-bottom: 0.75rem; position: relative; }
        .page-hero h1 .highlight { color: var(--sun); }
        .page-hero p { font-size: 1.1rem; color: var(--stone); max-width: 550px; margin: 0 auto; }

        /* Progress Steps */
        .progress-steps { display: flex; justify-content: center; gap: 0.5rem; margin: 2rem 0 3rem; flex-wrap: wrap; }

        .progress-step {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: white;
            border-radius: 100px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--stone-light);
            border: 2px solid rgba(0,0,0,0.06);
            transition: all 0.3s var(--ease-out);
        }

        .progress-step.active { background: var(--soft-orange); border-color: var(--sun); color: var(--sun); }
        .progress-step.completed { background: var(--success-light); border-color: var(--success); color: var(--success); }

        .step-number {
            width: 26px; height: 26px; border-radius: 50%; background: var(--stone-light); color: white;
            display: flex; align-items: center; justify-content: center; font-size: 0.75rem; font-weight: 700; flex-shrink: 0;
        }
        .progress-step.active .step-number { background: var(--sun); }
        .progress-step.completed .step-number { background: var(--success); }

        /* Form Container */
        .form-container { max-width: 800px; margin: 0 auto; }

        /* Form Card */
        .form-card {
            background: white;
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            box-shadow: 0 8px 40px -12px rgba(0,0,0,0.1);
            border: 1px solid rgba(0,0,0,0.04);
            margin-bottom: 1.5rem;
        }

        .form-section-header { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1.75rem; padding-bottom: 1rem; border-bottom: 2px solid var(--soft-orange); }
        .form-section-icon { width: 48px; height: 48px; border-radius: var(--radius-sm); background: linear-gradient(135deg, var(--soft-orange), var(--peach)); display: flex; align-items: center; justify-content: center; font-size: 1.5rem; flex-shrink: 0; }
        .form-section-header h3 { font-family: 'Outfit', sans-serif; font-size: 1.25rem; font-weight: 700; color: var(--earth); }
        .form-section-header p { font-size: 0.875rem; color: var(--stone-light); margin-top: 0.125rem; }

        /* Form Grid */
        .form-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.25rem; }
        .form-group { display: flex; flex-direction: column; gap: 0.5rem; }
        .form-group.full-width { grid-column: 1 / -1; }
        .form-label { font-size: 0.875rem; font-weight: 600; color: var(--earth); display: flex; align-items: center; gap: 0.375rem; }
        .form-label .required { color: var(--error); }

        .form-input, .form-select, .form-textarea {
            width: 100%; padding: 0.875rem 1rem; font-family: 'Outfit', sans-serif; font-size: 0.95rem;
            border: 2px solid rgba(0,0,0,0.08); border-radius: var(--radius-sm); background: var(--cream); color: var(--earth); transition: all 0.3s var(--ease-out);
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus { outline: none; border-color: var(--sun); background: white; box-shadow: 0 0 0 4px rgba(249, 115, 22, 0.1); }
        .form-input::placeholder, .form-textarea::placeholder { color: var(--stone-light); }
        .form-input.error, .form-select.error, .form-textarea.error { border-color: var(--error); background: var(--error-light); }

        .form-select {
            cursor: pointer; appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%23A8A29E'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
            background-repeat: no-repeat; background-position: right 1rem center; background-size: 16px; padding-right: 2.5rem;
        }
        .form-textarea { min-height: 120px; resize: vertical; }
        .input-hint { font-size: 0.8rem; color: var(--stone-light); }
        .error-message { font-size: 0.8rem; color: var(--error); display: none; }
        .error-message.show { display: flex; align-items: center; gap: 0.375rem; }

        /* Amount Input */
        .amount-wrapper { position: relative; }
        .amount-wrapper .currency { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); font-weight: 600; color: var(--stone); }
        .amount-wrapper .form-input { padding-left: 2.25rem; }

        /* Urgency Options */
        .urgency-options { display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.75rem; }
        .urgency-option input { display: none; }
        .urgency-option label {
            display: flex; flex-direction: column; align-items: center; gap: 0.5rem; padding: 1.25rem 1rem;
            background: var(--cream); border: 2px solid rgba(0,0,0,0.08); border-radius: var(--radius-sm);
            cursor: pointer; transition: all 0.3s var(--ease-out); text-align: center;
        }
        .urgency-option label:hover { border-color: var(--sun-light); transform: translateY(-2px); }
        .urgency-option input:checked + label { background: var(--soft-orange); border-color: var(--sun); box-shadow: 0 8px 24px -8px rgba(249, 115, 22, 0.3); }
        .urgency-icon { font-size: 2rem; }
        .urgency-label { font-size: 0.9rem; font-weight: 600; color: var(--earth); }
        .urgency-desc { font-size: 0.75rem; color: var(--stone-light); }

        /* File Upload */
        .upload-area { border: 2px dashed rgba(0,0,0,0.12); border-radius: var(--radius-md); padding: 2rem; text-align: center; background: var(--cream); transition: all 0.3s var(--ease-out); cursor: pointer; }
        .upload-area:hover, .upload-area.dragover { border-color: var(--sun); background: var(--soft-orange); }
        .upload-area input { display: none; }
        .upload-icon { width: 56px; height: 56px; margin: 0 auto 1rem; background: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 24px rgba(0,0,0,0.08); }
        .upload-icon svg { width: 28px; height: 28px; color: var(--sun); }
        .upload-text { font-size: 0.95rem; color: var(--earth); margin-bottom: 0.25rem; }
        .upload-text span { color: var(--sun); font-weight: 600; }
        .upload-hint { font-size: 0.8rem; color: var(--stone-light); }

        /* File Preview */
        .file-preview { display: flex; flex-wrap: wrap; gap: 0.75rem; margin-top: 1rem; }
        .file-item { position: relative; width: 100px; height: 100px; border-radius: var(--radius-sm); overflow: hidden; border: 2px solid rgba(0,0,0,0.08); }
        .file-item img, .file-item video { width: 100%; height: 100%; object-fit: cover; }
        .file-item .remove-btn { position: absolute; top: 4px; right: 4px; width: 24px; height: 24px; background: var(--error); color: white; border: none; border-radius: 50%; cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 1rem; line-height: 1; transition: transform 0.2s; }
        .file-item .remove-btn:hover { transform: scale(1.1); }
        .file-item.video-item::after { content: '▶'; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 36px; height: 36px; background: rgba(0,0,0,0.6); color: white; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.9rem; pointer-events: none; }

        /* Upload Progress */
        .upload-progress-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem 1rem; background: var(--cream); border-radius: var(--radius-sm); margin-top: 0.75rem; border: 1px solid rgba(0,0,0,0.06); }
        .upload-progress-item .file-icon { width: 40px; height: 40px; background: white; border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 1.25rem; flex-shrink: 0; }
        .upload-progress-item .file-info { flex: 1; min-width: 0; }
        .upload-progress-item .file-name { font-size: 0.85rem; font-weight: 500; color: var(--earth); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .upload-progress-item .file-size { font-size: 0.75rem; color: var(--stone-light); }
        .progress-bar-container { width: 100%; height: 6px; background: rgba(0,0,0,0.08); border-radius: 3px; overflow: hidden; margin-top: 0.375rem; }
        .progress-bar { height: 100%; background: linear-gradient(90deg, var(--sun), var(--amber)); border-radius: 3px; width: 0%; transition: width 0.3s ease; }
        .upload-progress-item .progress-text { font-size: 0.75rem; color: var(--sun); font-weight: 600; flex-shrink: 0; }

        /* Review State */
        .review-state { display: none; }
        .review-state.show { display: block; }
        .review-header { text-align: center; margin-bottom: 2rem; }
        .review-icon { font-size: 3rem; margin-bottom: 1rem; }
        .review-header h2 { font-size: 1.75rem; color: var(--earth); margin-bottom: 0.5rem; }
        .review-header p { color: var(--stone); font-size: 1rem; }

        .review-card { background: white; border-radius: var(--radius-lg); padding: 2rem; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 1px solid rgba(0,0,0,0.04); margin-bottom: 2rem; }
        .review-section { padding-bottom: 1.5rem; margin-bottom: 1.5rem; border-bottom: 1px solid rgba(0,0,0,0.06); }
        .review-section:last-child { padding-bottom: 0; margin-bottom: 0; border-bottom: none; }
        .review-section h4 { font-size: 1rem; font-weight: 600; color: var(--earth); margin-bottom: 1rem; }
        .review-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .review-item { display: flex; flex-direction: column; gap: 0.25rem; }
        .review-item.full { grid-column: 1 / -1; }
        .review-label { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--stone-light); font-weight: 600; }
        .review-value { font-size: 0.95rem; color: var(--earth); word-break: break-word; }
        .review-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }

        /* Success State */
        .success-state { display: none; text-align: center; padding: 3rem 2rem; background: linear-gradient(135deg, var(--warm-white), var(--cream)); border-radius: var(--radius-lg); border: 1px solid rgba(0,0,0,0.04); }
        .success-state.show { display: block; }
        .success-icon { width: 80px; height: 80px; background: linear-gradient(135deg, var(--success), #10b981); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; animation: successPop 0.5s ease; }
        @keyframes successPop { 0% { transform: scale(0); } 50% { transform: scale(1.1); } 100% { transform: scale(1); } }
        .success-icon svg { width: 40px; height: 40px; color: white; }
        .success-state h2 { font-size: 1.75rem; color: var(--earth); margin-bottom: 0.75rem; }
        .success-state .success-message { font-size: 1rem; color: var(--stone); margin-bottom: 2rem; max-width: 500px; margin-left: auto; margin-right: auto; }

        .workflow-reminder { background: white; border-radius: var(--radius-md); padding: 1.5rem; margin-bottom: 2rem; border: 1px solid rgba(0,0,0,0.06); text-align: left; }
        .workflow-reminder h4 { font-size: 0.9rem; font-weight: 600; color: var(--earth); margin-bottom: 1rem; display: flex; align-items: center; gap: 0.5rem; }
        .workflow-steps-mini { display: flex; flex-direction: column; gap: 0.75rem; }
        .workflow-step-mini { display: flex; align-items: center; gap: 0.75rem; font-size: 0.875rem; color: var(--stone); }
        .workflow-step-mini .step-badge { width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; font-weight: 700; flex-shrink: 0; }
        .workflow-step-mini .step-badge.done { background: var(--success); color: white; }
        .workflow-step-mini .step-badge.current { background: var(--sun); color: white; }
        .workflow-step-mini .step-badge.pending { background: var(--stone-light); color: white; }
        .workflow-step-mini.completed { color: var(--success); }
        .workflow-step-mini.active { color: var(--sun); font-weight: 500; }
        .success-actions { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; }

        /* Info Box */
        .info-box { background: linear-gradient(135deg, var(--soft-orange), rgba(254, 215, 170, 0.5)); border: 1px solid var(--peach); border-radius: var(--radius-sm); padding: 1rem 1.25rem; display: flex; gap: 0.75rem; margin-bottom: 1.5rem; }
        .info-box svg { width: 20px; height: 20px; color: var(--sun); flex-shrink: 0; margin-top: 0.125rem; }
        .info-box p { font-size: 0.875rem; color: var(--stone); }

        /* Submit Section */
        .submit-section { text-align: center; padding: 2rem; background: linear-gradient(135deg, var(--warm-white), var(--cream)); border-radius: var(--radius-lg); border: 1px solid rgba(0,0,0,0.04); }
        .terms-note { font-size: 0.875rem; color: var(--stone); margin-bottom: 1.5rem; }
        .terms-note a { color: var(--sun); font-weight: 500; }
        .btn-submit { padding: 1rem 3rem; font-size: 1.05rem; }
        .btn-submit .spinner { width: 20px; height: 20px; border: 2px solid rgba(255,255,255,0.3); border-top-color: white; border-radius: 50%; animation: spin 0.8s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ===== FOOTER (matches campaigns.html) ===== */
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
        }


        @media (max-width: 768px) {
            .form-card { padding: 1.75rem; }
            .form-grid { grid-template-columns: 1fr; }
            .urgency-options { grid-template-columns: 1fr; }
            .progress-step span:not(.step-number) { display: none; }
            .progress-step { padding: 0.5rem; }
            .review-grid { grid-template-columns: 1fr; }
            .review-card { padding: 1.5rem; }
            .review-actions { flex-direction: column; }
            .review-actions .btn { width: 100%; justify-content: center; }
            .success-actions { flex-direction: column; }
            .success-actions .btn { width: 100%; justify-content: center; }
            .page-hero p { font-size: 1rem; }
            .footer-main { grid-template-columns: 1fr; gap: 2rem; text-align: center; }
            .footer-brand { align-items: center; }
            .footer-bottom { flex-direction: column; text-align: center; }
        }

        @media (max-width: 480px) {
            html { font-size: 15px; }
            .form-card { padding: 1.25rem; }
            .page-hero { padding: 2rem 0; }
            .page-hero h1 { font-size: 1.75rem; }
            .page-hero p { font-size: 0.9rem; }
            .review-card { padding: 1rem; }
            .workflow-reminder { padding: 1rem; }
            .submit-section { padding: 1.5rem 1rem; }
            .upload-area { padding: 1.5rem 1rem; }
            .upload-icon { width: 44px; height: 44px; }
            .upload-icon svg { width: 22px; height: 22px; }
            .file-item { width: 80px; height: 80px; }
            .form-section-icon { width: 40px; height: 40px; font-size: 1.25rem; }
            .form-section-header h3 { font-size: 1.1rem; }
            .btn-submit { padding: 1rem 2rem; font-size: 0.95rem; }
            .info-box { flex-direction: column; gap: 0.5rem; }
        }
    </style>

    <!-- Email Verification & Form Lock Styles -->
    <style>
        .email-verify-row {
            display: flex; gap: 0.75rem; align-items: flex-start;
        }
        .email-verify-row .form-input {
            flex: 1;
        }
        .btn-verify {
            padding: 0.875rem 1.75rem; border: none; border-radius: var(--radius-sm);
            font-family: 'Outfit', sans-serif; font-weight: 700; font-size: 0.85rem;
            cursor: pointer; transition: all 0.3s var(--ease-out); white-space: nowrap;
            background: var(--sun); color: white; letter-spacing: 0.02em;
            flex-shrink: 0;
        }
        .btn-verify:hover:not(:disabled) {
            background: var(--sun-light); transform: translateY(-1px);
            box-shadow: 0 4px 16px rgba(249, 115, 22, 0.35);
        }
        .btn-verify:disabled {
            background: var(--stone-light); cursor: not-allowed; opacity: 0.6;
        }
        .btn-verify.verifying {
            background: var(--stone-light); pointer-events: none; position: relative;
        }
        .btn-verify.verifying::after {
            content: ''; position: absolute; width: 18px; height: 18px;
            border: 2.5px solid rgba(255,255,255,0.3); border-top-color: white;
            border-radius: 50%; animation: verifySpin 0.7s linear infinite;
        }
        .btn-verify.verifying span { visibility: hidden; }
        @keyframes verifySpin { to { transform: rotate(360deg); } }

        .btn-verify.verified {
            background: var(--success); pointer-events: none;
        }

        .email-verified-badge {
            display: none; align-items: center; gap: 0.5rem;
            margin-top: 0.5rem; padding: 0.625rem 1rem; border-radius: var(--radius-sm);
            background: var(--success-light); color: #065F46;
            font-size: 0.85rem; font-weight: 600;
            animation: badgeSlideIn 0.4s var(--ease-spring);
        }
        .email-verified-badge.show { display: flex; }
        .email-verified-badge svg { width: 18px; height: 18px; flex-shrink: 0; color: var(--success); }
        @keyframes badgeSlideIn {
            from { opacity: 0; transform: translateY(-8px); }
            to { opacity: 1; transform: translateY(0); }
        }

        #nameGroup {
            animation: nameSlideIn 0.5s var(--ease-spring);
        }
        @keyframes nameSlideIn {
            from { opacity: 0; transform: translateY(12px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Locked / Disabled Cards */
        .form-card-locked {
            position: relative; pointer-events: none; user-select: none;
        }
        .form-card-locked::after {
            content: '🔒 Verify your email to unlock';
            position: absolute; inset: 0; z-index: 5;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255, 251, 245, 0.85); backdrop-filter: blur(2px);
            border-radius: var(--radius-md); font-family: 'Outfit', sans-serif;
            font-size: 0.9rem; font-weight: 600; color: var(--stone-light);
            letter-spacing: 0.01em;
        }
        .form-card-locked > * { opacity: 0.3; }

        /* Unlocked state */
        .form-card-unlocked {
            pointer-events: auto; user-select: auto;
        }
        .form-card-unlocked::after { display: none !important; }
        .form-card-unlocked > * { opacity: 1; }

        /* Email input verified state */
        .form-input.input-verified {
            border-color: var(--success) !important; background: var(--success-light) !important;
            color: #065F46; pointer-events: none;
        }

        @media (max-width: 480px) {
            .email-verify-row { flex-direction: column; }
            .btn-verify { width: 100%; justify-content: center; display: flex; align-items: center; }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <main class="page-content">
        <div class="container">
            <div class="page-hero">
                <h1>Start a <span class="highlight">Fundraiser</span></h1>
                <p>Fill in the details below and submit your request. Our admin team will review your campaign, and once approved, it will go live for donations!</p>
            </div>

            <div class="progress-steps">
                <div class="progress-step active"><span class="step-number">1</span><span>Fill Details</span></div>
                <div class="progress-step"><span class="step-number">2</span><span>Submit Request</span></div>
                <div class="progress-step"><span class="step-number">3</span><span>Admin Review</span></div>
                <div class="progress-step"><span class="step-number">4</span><span>Go Live</span></div>
            </div>

            <form id="fundraiserForm" class="form-container" novalidate>
                <!-- Fundraiser Details -->
                <div class="form-card" id="fundraiserCard">
                    <div class="form-section-header">
                        <div class="form-section-icon">👤</div>
                        <div>
                            <h3>Fundraiser Details</h3>
                            <p>Verify your email to begin filling the form</p>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group full-width" id="emailGroup">
                            <label class="form-label" for="fundraiserEmail">Your Email <span class="required">*</span></label>
                            <div class="email-verify-row">
                                <input type="email" id="fundraiserEmail" class="form-input" 
                                    value="<?php echo $user_email; ?>" 
                                    <?php echo $is_logged_in ? 'readonly' : ''; ?> required>
                                
                                <?php if (!$is_logged_in): ?>
                                    <button type="button" class="btn-verify" id="verifyEmailBtn" disabled>Verify</button>
                                <?php else: ?>
                                    <button type="button" class="btn-verify verified" id="verifyEmailBtn" disabled>✓ Verified</button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group full-width" id="nameGroup" style="<?php echo $is_logged_in ? 'display:block;' : 'display:none;'; ?>">
                            <label class="form-label" for="fundraiserName">Your Name <span class="required">*</span></label>
                            <input type="text" id="fundraiserName" class="form-input" 
                                value="<?php echo $user_name; ?>" 
                                <?php echo $is_logged_in ? 'readonly' : ''; ?> required>
                        </div>
                    </div>
                </div>

                <!-- Campaign Information -->
                <div class="form-card form-card-locked" id="campaignCard">
                    <div class="form-section-header">
                        <div class="form-section-icon">📝</div>
                        <div>
                            <h3>Campaign Information</h3>
                            <p>Details about your fundraising campaign</p>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label" for="campaignTitle">Campaign Title <span class="required">*</span></label>
                            <input type="text" id="campaignTitle" class="form-input" placeholder="e.g., Medical Emergency for Rahul's Surgery" required>
                            <span class="error-message" id="titleError">Please enter a campaign title</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="category">Category <span class="required">*</span></label>
                            <select id="category" class="form-select" required>
                                <option value="">Select category</option>
                                <option value="Education">Education</option>
                                <option value="Medical">Medical</option>
                                <option value="Health">Health</option>
                                <option value="Disaster">Disaster Relief</option>
                                <option value="Community">Community</option>
                            </select>
                            <span class="error-message" id="categoryError">Please select a category</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="amount">Funding Goal (₹) <span class="required">*</span></label>
                            <div class="amount-wrapper">
                                <span class="currency">₹</span>
                                <input type="number" id="amount" class="form-input" placeholder="50000" min="1000" required>
                            </div>
                            <span class="input-hint">Minimum ₹1,000</span>
                            <span class="error-message" id="amountError">Please enter a valid amount (min ₹1,000)</span>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Urgency Level <span class="required">*</span></label>
                            <div class="urgency-options">
                                <div class="urgency-option">
                                    <input type="radio" name="urgency" id="urgencyLow" value="low">
                                    <label for="urgencyLow"><span class="urgency-icon">🟢</span><span class="urgency-label">Low</span><span class="urgency-desc">30+ days needed</span></label>
                                </div>
                                <div class="urgency-option">
                                    <input type="radio" name="urgency" id="urgencyMedium" value="medium">
                                    <label for="urgencyMedium"><span class="urgency-icon">🟡</span><span class="urgency-label">Medium</span><span class="urgency-desc">15-30 days needed</span></label>
                                </div>
                                <div class="urgency-option">
                                    <input type="radio" name="urgency" id="urgencyHigh" value="high" checked>
                                    <label for="urgencyHigh"><span class="urgency-icon">🔴</span><span class="urgency-label">Urgent</span><span class="urgency-desc">Less than 15 days</span></label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label" for="description">Campaign Description <span class="required">*</span></label>
                            <textarea id="description" class="form-textarea" placeholder="Describe the situation, why funds are needed, and how they will be used..." required></textarea>
                            <span class="input-hint">Minimum 100 characters. Be detailed and specific for faster approval.</span>
                            <span class="error-message" id="descError">Please provide a detailed description (min 100 characters)</span>
                        </div>
                    </div>
                </div>

                <!-- Beneficiary Details -->
                <div class="form-card form-card-locked" id="beneficiaryCard">
                    <div class="form-section-header">
                        <div class="form-section-icon">🤝</div>
                        <div>
                            <h3>Beneficiary Details</h3>
                            <p>Information about who will receive the funds</p>
                        </div>
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="beneficiaryName">Beneficiary Name <span class="required">*</span></label>
                            <input type="text" id="beneficiaryName" class="form-input" placeholder="Full name of the beneficiary" required>
                            <span class="error-message" id="benNameError">Please enter beneficiary name</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="beneficiaryRelation">Your Relation <span class="required">*</span></label>
                            <select id="beneficiaryRelation" class="form-select" required>
                                <option value="">Select relation</option>
                                <option value="self">Self</option>
                                <option value="family">Family Member</option>
                                <option value="friend">Friend</option>
                                <option value="colleague">Colleague/Classmate</option>
                                <option value="community">Community Member</option>
                            </select>
                            <span class="error-message" id="relationError">Please select your relation</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="beneficiaryContact">Contact Number </label>
                            <input type="tel" id="beneficiaryContact" class="form-input" placeholder="+91 XXXXX XXXXX">
                            <span class="error-message" id="contactError">Please enter a valid contact number</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="beneficiaryLocation">Location/City </label>
                            <input type="text" id="beneficiaryLocation" class="form-input" placeholder="City, State">
                            <span class="error-message" id="locationError">Please enter location</span>
                        </div>
                    </div>
                </div>

                <!-- Supporting Documents -->
                <div class="form-card form-card-locked" id="documentsCard">
                    <div class="form-section-header">
                        <div class="form-section-icon">📎</div>
                        <div>
                            <h3>Supporting Documents</h3>
                            <p>Upload images and video to verify your campaign</p>
                        </div>
                    </div>
                    <div class="info-box">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p>Upload supporting images (medical reports, bills, ID proof, etc.) and an optional video to help verify your campaign. Clear documentation speeds up the approval process.</p>
                    </div>
                    <div class="form-grid">
                        <div class="form-group full-width">
                            <label class="form-label">Images (Max 5) <span class="required">*</span></label>
                            <div class="upload-area" id="imageUploadArea">
                                <input type="file" id="imageInput" accept="image/*" multiple>
                                <div class="upload-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg></div>
                                <p class="upload-text"><span>Click to upload</span> or drag and drop</p>
                                <p class="upload-hint">PNG, JPG up to 5MB each (Max 5 images)</p>
                            </div>
                            <div class="file-preview" id="imagePreview"></div>
                            <span class="error-message" id="imageError">Please upload at least 1 image</span>
                        </div>
                        <div class="form-group full-width">
                            <label class="form-label">Video (Optional, Max 1)</label>
                            <div class="upload-area" id="videoUploadArea">
                                <input type="file" id="videoInput" accept="video/mp4">
                                <div class="upload-icon"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></div>
                                <p class="upload-text"><span>Click to upload</span> or drag and drop</p>
                                <p class="upload-hint">MP4 only, up to 50MB</p>
                            </div>
                            <div class="file-preview" id="videoPreview"></div>
                            <span class="error-message" id="videoError">Please upload MP4 format only (max 50MB)</span>
                        </div>
                    </div>
                </div>

                <!-- Submit -->
                <div class="submit-section form-card-locked" id="submitSection">
                    <p class="terms-note">By submitting, you agree to our <a href="#">Terms of Service</a> and confirm all information provided is accurate and truthful.</p>
                    <button type="submit" class="btn btn-sun btn-submit" id="submitBtn">
                        <span>Review & Submit</span>
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                    </button>
                </div>
            </form>

            <!-- Review State -->
            <div class="review-state" id="reviewState">
                <div class="review-header"><div class="review-icon">📋</div><h2>Review Your Details</h2><p>Please verify all information before confirming your submission.</p></div>
                <div class="review-card">
                    <div class="review-section"><h4>👤 Fundraiser Details</h4><div class="review-grid"><div class="review-item"><span class="review-label">Name</span><span class="review-value" id="reviewFundraiserName">-</span></div><div class="review-item"><span class="review-label">Email</span><span class="review-value" id="reviewFundraiserEmail">-</span></div></div></div>
                    <div class="review-section"><h4>📝 Campaign Information</h4><div class="review-grid"><div class="review-item full"><span class="review-label">Campaign Title</span><span class="review-value" id="reviewTitle">-</span></div><div class="review-item"><span class="review-label">Category</span><span class="review-value" id="reviewCategory">-</span></div><div class="review-item"><span class="review-label">Funding Goal</span><span class="review-value" id="reviewAmount">-</span></div><div class="review-item"><span class="review-label">Urgency</span><span class="review-value" id="reviewUrgency">-</span></div><div class="review-item full"><span class="review-label">Description</span><span class="review-value" id="reviewDescription">-</span></div></div></div>
                    <div class="review-section"><h4>🤝 Beneficiary Details</h4><div class="review-grid"><div class="review-item"><span class="review-label">Name</span><span class="review-value" id="reviewBenName">-</span></div><div class="review-item"><span class="review-label">Relation</span><span class="review-value" id="reviewRelation">-</span></div><div class="review-item"><span class="review-label">Contact</span><span class="review-value" id="reviewContact">-</span></div><div class="review-item"><span class="review-label">Location</span><span class="review-value" id="reviewLocation">-</span></div></div></div>
                    <div class="review-section"><h4>📎 Supporting Documents</h4><div class="review-grid"><div class="review-item"><span class="review-label">Images</span><span class="review-value" id="reviewImages">-</span></div><div class="review-item"><span class="review-label">Video</span><span class="review-value" id="reviewVideo">-</span></div></div></div>
                </div>
                <div class="review-actions">
                    <button type="button" class="btn btn-ghost" id="backToEditBtn"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg> Back to Edit</button>
                    <button type="button" class="btn btn-sun" id="confirmBtn"><span>Confirm & Submit</span><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></button>
                </div>
            </div>

            <!-- Success State -->
            <div class="success-state" id="successState">
                <div class="success-icon"><svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg></div>
                <h2>Request Submitted Successfully! 🎉</h2>
                <p class="success-message">Thank you for submitting your fundraiser request. Our team will review it and get back to you within 24-48 hours.</p>
                <div class="workflow-reminder">
                    <h4>📋 What happens next?</h4>
                    <div class="workflow-steps-mini">
                        <div class="workflow-step-mini completed"><span class="step-badge done">✓</span><span><strong>Step 1:</strong> Fill Details — Completed</span></div>
                        <div class="workflow-step-mini completed"><span class="step-badge done">✓</span><span><strong>Step 2:</strong> Submit Request — Completed</span></div>
                        <div class="workflow-step-mini active"><span class="step-badge current">3</span><span><strong>Step 3:</strong> Admin Review — In progress (24-48 hrs)</span></div>
                        <div class="workflow-step-mini"><span class="step-badge pending">4</span><span><strong>Step 4:</strong> Go Live — After approval</span></div>
                    </div>
                </div>
                <div class="success-actions"><a href="index.html" class="btn btn-sun"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="width:18px;height:18px;"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg> Back to Home</a></div>
            </div>
        </div>
    </main>

    <!-- footer  -->
    <?php include 'includes/footer.php'; ?>

    <script>
    // --- State Management ---
    let currentStep = 'fill';
    let emailVerified = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    const isSessionUser = <?php echo $is_logged_in ? 'true' : 'false'; ?>;
    let uploadedImages = [];
    let uploadedVideo = null;

    // Elements
    const fundraiserForm = document.getElementById('fundraiserForm');
    const emailInput = document.getElementById('fundraiserEmail');
    const nameInput = document.getElementById('fundraiserName');
    const verifyBtn = document.getElementById('verifyEmailBtn');
    const nameGroup = document.getElementById('nameGroup');
    const progressSteps = document.querySelectorAll('.progress-step');

    // File Upload Elements
    const imageInput = document.getElementById('imageInput');
    const imageUploadArea = document.getElementById('imageUploadArea');
    const imagePreview = document.getElementById('imagePreview');
    const videoInput = document.getElementById('videoInput');
    const videoUploadArea = document.getElementById('videoUploadArea');
    const videoPreview = document.getElementById('videoPreview');

    // Initialize View
    if (isSessionUser) {
        unlockAllCards();
    }

    // --- 1. Email Verification Logic ---
    emailInput.addEventListener('input', () => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        verifyBtn.disabled = !emailRegex.test(emailInput.value.trim());
    });

    verifyBtn.addEventListener('click', async () => {
        verifyBtn.classList.add('verifying');
        verifyBtn.innerHTML = '<span>Checking...</span>';

        const formData = new FormData();
        formData.append('action', 'check_email');
        formData.append('email', emailInput.value.trim());

        try {
            const res = await fetch('actions/campaigns/request_action.php', { method: 'POST', body: formData });
            const data = await res.json();

            verifyBtn.classList.remove('verifying');
            verifyBtn.classList.add('verified');
            verifyBtn.textContent = '✓ Verified';
            emailVerified = true;
            emailInput.readOnly = true;

            if (data.status === 'exists') {
                nameInput.value = data.name;
                nameInput.readOnly = true;
            } else {
                nameInput.value = '';
                nameInput.readOnly = false;
            }

            nameGroup.style.display = 'block';
            unlockAllCards();
        } catch (e) {
            alert("Error checking email.");
            verifyBtn.classList.remove('verifying');
            verifyBtn.innerHTML = 'Verify';
        }
    });

    // --- 2. RESTORED: File Upload Logic ---

    // Trigger Click
    imageUploadArea.addEventListener('click', () => imageInput.click());
    videoUploadArea.addEventListener('click', () => videoInput.click());

    // File selection change
    imageInput.addEventListener('change', (e) => handleImageFiles(e.target.files));
    videoInput.addEventListener('change', (e) => handleVideoFile(e.target.files[0]));

    async function handleImageFiles(files) {
        const remainingSlots = 5 - uploadedImages.length;
        const filesToAdd = Array.from(files).slice(0, remainingSlots);
        
        for (const file of filesToAdd) {
            if (file.type.startsWith('image/') && file.size <= 5 * 1024 * 1024) {
                await simulateUploadProgress(file, () => {
                    uploadedImages.push(file);
                    addImagePreview(file);
                });
            } else if (file.size > 5 * 1024 * 1024) {
                alert(`Image "${file.name}" exceeds 5MB limit`);
            }
        }
        if (uploadedImages.length >= 5) imageUploadArea.style.display = 'none';
    }

    async function handleVideoFile(file) {
        if (!file) return;
        const isMP4 = file.type === 'video/mp4' || file.name.toLowerCase().endsWith('.mp4');
        if (!isMP4) { alert('Only MP4 format is allowed'); return; }
        if (file.size > 50 * 1024 * 1024) { alert('Video exceeds 50MB limit'); return; }
        
        videoUploadArea.style.display = 'none';
        await simulateUploadProgress(file, () => {
            uploadedVideo = file;
            addVideoPreview(file);
        });
    }

    function addImagePreview(file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            const div = document.createElement('div');
            div.className = 'file-item';
            div.innerHTML = `<img src="${e.target.result}" alt="Preview"><button type="button" class="remove-btn" onclick="removeImage(this, '${file.name}')">×</button>`;
            imagePreview.appendChild(div);
        };
        reader.readAsDataURL(file);
    }

    window.removeImage = function(btn, filename) {
        uploadedImages = uploadedImages.filter(f => f.name !== filename);
        btn.parentElement.remove();
        imageUploadArea.style.display = 'block';
    };

    function addVideoPreview(file) {
        const div = document.createElement('div');
        div.className = 'file-item video-item';
        div.innerHTML = `<video src="${URL.createObjectURL(file)}"></video><button type="button" class="remove-btn" onclick="removeVideo()">×</button>`;
        videoPreview.appendChild(div);
    }

    window.removeVideo = function() {
        uploadedVideo = null;
        videoPreview.innerHTML = '';
        videoUploadArea.style.display = 'block';
    };

    function simulateUploadProgress(file, onComplete) {
        return new Promise((resolve) => {
            const progressId = 'progress-' + Math.random().toString(36).substr(2, 9);
            const isImage = file.type.startsWith('image/');
            const container = isImage ? imagePreview : videoPreview;
            const progressItem = document.createElement('div');
            progressItem.className = 'upload-progress-item';
            progressItem.innerHTML = `
                <div class="file-icon">${isImage ? '🖼️' : '🎬'}</div>
                <div class="file-info">
                    <div class="file-name">${file.name}</div>
                    <div class="progress-bar-container"><div class="progress-bar" id="bar-${progressId}"></div></div>
                </div>
            `;
            container.appendChild(progressItem);
            
            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                document.getElementById('bar-' + progressId).style.width = progress + '%';
                if (progress >= 100) {
                    clearInterval(interval);
                    progressItem.remove();
                    onComplete();
                    resolve();
                }
            }, 100);
        });
    }

    // --- 3. Review and Submission ---

    fundraiserForm.addEventListener('submit', (e) => {
        e.preventDefault();
        if (!emailVerified) { alert('Verify email first'); return; }
        
        // Show Review State
        populateReviewData();
        fundraiserForm.style.display = 'none';
        document.getElementById('reviewState').classList.add('show');
        updateProgressSteps(1);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    function populateReviewData() {
        document.getElementById('reviewFundraiserName').textContent = nameInput.value;
        document.getElementById('reviewFundraiserEmail').textContent = emailInput.value;
        document.getElementById('reviewTitle').textContent = document.getElementById('campaignTitle').value;
        document.getElementById('reviewCategory').textContent = document.getElementById('category').value;
        document.getElementById('reviewAmount').textContent = '₹' + document.getElementById('amount').value;
        document.getElementById('reviewImages').textContent = uploadedImages.length + ' image(s) uploaded';
        document.getElementById('reviewVideo').textContent = uploadedVideo ? '1 video uploaded' : 'No video';
        document.getElementById('reviewDescription').textContent = document.getElementById('description').value.substring(0, 100) + '...';
        document.getElementById('reviewBenName').textContent = document.getElementById('beneficiaryName').value;
        document.getElementById('reviewRelation').textContent = document.getElementById('beneficiaryRelation').value;
        document.getElementById('reviewContact').textContent = document.getElementById('beneficiaryContact').value;
        document.getElementById('reviewLocation').textContent = document.getElementById('beneficiaryLocation').value;
    }

    document.getElementById('backToEditBtn').addEventListener('click', () => {
        document.getElementById('reviewState').classList.remove('show');
        fundraiserForm.style.display = 'block';
        updateProgressSteps(0);
    });

    const confirmBtn = document.getElementById('confirmBtn');
    confirmBtn.addEventListener('click', async () => {
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<div class="spinner"></div><span>Submitting...</span>';

        const formData = new FormData();
        formData.append('action', 'submit_campaign');
        formData.append('email', emailInput.value);
        formData.append('name', nameInput.value);
        formData.append('title', document.getElementById('campaignTitle').value);
        formData.append('category', document.getElementById('category').value);
        formData.append('amount', document.getElementById('amount').value);
        formData.append('urgency', document.querySelector('input[name="urgency"]:checked').value);
        formData.append('description', document.getElementById('description').value);
        formData.append('ben_name', document.getElementById('beneficiaryName').value);
        formData.append('ben_relation', document.getElementById('beneficiaryRelation').value);
        formData.append('ben_phone', document.getElementById('beneficiaryContact').value);
        formData.append('ben_city', document.getElementById('beneficiaryLocation').value);

        uploadedImages.forEach(file => formData.append('images[]', file));
        if (uploadedVideo) formData.append('video', uploadedVideo);

        try {
            const res = await fetch('actions/campaigns/request_action.php', { method: 'POST', body: formData });
            const data = await res.json();
            if (data.status === 'success') {
                document.getElementById('reviewState').classList.remove('show');
                document.getElementById('successState').classList.add('show');
                updateProgressSteps(2);
            } else {
                alert("Error: " + data.message);
                confirmBtn.disabled = false;
            }
        } catch (e) {
            alert("Submission failed.");
            confirmBtn.disabled = false;
        }
    });

    // Helper functions
    function unlockAllCards() {
        ['campaignCard', 'beneficiaryCard', 'documentsCard', 'submitSection'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.classList.remove('form-card-locked');
                el.classList.add('form-card-unlocked');
            }
        });
    }

    function updateProgressSteps(activeIndex) {
        progressSteps.forEach((step, index) => {
            step.classList.remove('active', 'completed');
            if (index < activeIndex) step.classList.add('completed');
            else if (index === activeIndex) step.classList.add('active');
        });
    }
</script>
</body>
</html>