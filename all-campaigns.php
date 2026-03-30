<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['name'] ?? null;
$user_email = $_SESSION['email'] ?? null;
$user_phone = $_SESSION['phone'] ?? null;
$role = $_SESSION['role'] ?? null;

$current_page = "all-campaigns.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Campaigns – SAHARA</title>
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
            --radius-sm: 12px;
            --radius-md: 20px;
            --radius-lg: 32px;
            --radius-xl: 48px;
            --ease-out: cubic-bezier(0.16, 1, 0.3, 1);
            --ease-spring: cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
            font-size: 16px;
        }

        body {
            font-family: 'Outfit', sans-serif;
            background: var(--cream);
            color: var(--earth);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* Grain Texture Overlay */
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

        p {
            color: var(--stone);
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        img {
            max-width: 100%;
            display: block;
        }

        .container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 clamp(1.25rem, 5vw, 3rem);
        }

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
            box-shadow: 0 8px 32px -8px rgba(249, 115, 22, 0.5),
                        inset 0 1px 0 rgba(255,255,255,0.2);
        }

        .btn-sun:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 16px 48px -8px rgba(249, 115, 22, 0.6),
                        inset 0 1px 0 rgba(255,255,255,0.2);
        }

        .btn-sun:active {
            transform: translateY(-1px) scale(0.98);
        }

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

        .btn svg {
            width: 18px;
            height: 18px;
            transition: transform 0.3s var(--ease-out);
        }

        .btn:hover svg {
            transform: translateX(4px);
        }


        /* Page Hero */
        .page-hero {
            padding: 10rem 0 4rem;
            background: linear-gradient(180deg, var(--warm-white) 0%, var(--cream) 100%);
            position: relative;
            overflow: hidden;
        }

        .page-hero::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -20%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, var(--peach) 0%, transparent 70%);
            opacity: 0.5;
            filter: blur(80px);
        }

        .page-hero-content {
            position: relative;
            z-index: 1;
            text-align: center;
            max-width: 700px;
            margin: 0 auto;
        }

        .page-hero h1 {
            font-size: clamp(2.5rem, 6vw, 4rem);
            color: var(--earth);
            margin-bottom: 1rem;
        }

        .page-hero h1 .highlight {
            color: var(--sun);
            position: relative;
            display: inline-block;
        }

        .page-hero h1 .highlight::before {
            content: '';
            position: absolute;
            bottom: 0.1em;
            left: -0.05em;
            right: -0.05em;
            height: 0.35em;
            background: var(--peach);
            z-index: -1;
            border-radius: 4px;
            transform: skewX(-3deg);
        }

        .page-hero p {
            font-size: 1.15rem;
            color: var(--stone);
            margin-bottom: 2rem;
        }

        /* Search & Filter Section */
        .filter-section {
            padding: 1rem 0;
            background: white;
            border-bottom: 1px solid rgba(0,0,0,0.06);
            position: sticky;
            top: 80px;
            z-index: 100;
        }

        .filter-inner {
            display: flex;
            flex-wrap: wrap;
            gap: 0.75rem;
            align-items: center;
        }

        .filter-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
        }

        .search-box {
            flex: 1;
            min-width: 0;
            position: relative;
        }

        .search-box input {
            width: 100%;
            padding: 0.75rem 0.75rem 0.75rem 2.5rem;
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem;
            border: 1.5px solid rgba(0,0,0,0.1);
            border-radius: var(--radius-sm);
            background: var(--cream);
            transition: all 0.3s var(--ease-out);
        }

        .search-box input:focus {
            outline: none;
            border-color: var(--sun);
            background: white;
        }

        .search-box svg {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            width: 18px;
            height: 18px;
            color: var(--stone-light);
        }

        .filter-tags {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding-bottom: 0.5rem;
            -webkit-overflow-scrolling: touch;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }

        .filter-tags::-webkit-scrollbar {
            display: none;
        }

        .filter-tag {
            padding: 0.5rem 1rem;
            font-family: 'Outfit', sans-serif;
            font-size: 0.8rem;
            font-weight: 600;
            border: 1.5px solid rgba(0,0,0,0.1);
            border-radius: 100px;
            background: white;
            color: var(--stone);
            cursor: pointer;
            transition: all 0.3s var(--ease-out);
            white-space: nowrap;
            flex-shrink: 0;
        }

        .filter-tag:hover {
            border-color: var(--sun-light);
            color: var(--sun);
        }

        .filter-tag.active {
            background: linear-gradient(135deg, var(--sun) 0%, var(--amber) 100%);
            border-color: transparent;
            color: white;
        }

        /* Mobile Category Dropdown */
        .filter-dropdown {
            display: none;
            position: relative;
        }

        .filter-dropdown select {
            appearance: none;
            padding: 0.75rem 2.25rem 0.75rem 1rem;
            font-family: 'Outfit', sans-serif;
            font-size: 0.85rem;
            font-weight: 500;
            border: 1.5px solid rgba(0,0,0,0.1);
            border-radius: var(--radius-sm);
            background: var(--cream);
            color: var(--earth);
            cursor: pointer;
            transition: all 0.3s var(--ease-out);
            min-width: 130px;
        }

        .filter-dropdown select:focus {
            outline: none;
            border-color: var(--sun);
        }

        .filter-dropdown::after {
            content: '';
            position: absolute;
            right: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 5px solid var(--stone);
            pointer-events: none;
        }

        .sort-dropdown {
            position: relative;
            flex-shrink: 0;
        }

        .sort-dropdown select {
            appearance: none;
            padding: 0.75rem 2.25rem 0.75rem 1rem;
            font-family: 'Outfit', sans-serif;
            font-size: 0.85rem;
            font-weight: 500;
            border: 1.5px solid rgba(0,0,0,0.1);
            border-radius: var(--radius-sm);
            background: var(--cream);
            color: var(--earth);
            cursor: pointer;
            transition: all 0.3s var(--ease-out);
        }

        .sort-dropdown select:focus {
            outline: none;
            border-color: var(--sun);
        }

        .sort-dropdown::after {
            content: '';
            position: absolute;
            right: 0.875rem;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-top: 5px solid var(--stone);
            pointer-events: none;
        }

        /* Stats Bar - Compact Inline */
        .stats-bar {
            padding: 1rem 0;
            background: linear-gradient(135deg, var(--earth) 0%, #1C1917 100%);
        }

        .stats-bar-inner {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .stat-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(255,255,255,0.08);
            border-radius: 100px;
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s var(--ease-out);
        }

        .stat-pill:hover {
            background: rgba(255,255,255,0.12);
            border-color: rgba(249, 115, 22, 0.3);
        }

        .stat-pill-icon {
            font-size: 1rem;
        }

        .stat-pill-value {
            font-family: 'Outfit', sans-serif;
            font-size: 0.9rem;
            font-weight: 700;
            color: var(--sun-glow);
        }

        .stat-pill-label {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.6);
        }

        .stat-divider {
            width: 1px;
            height: 20px;
            background: rgba(255,255,255,0.15);
            margin: 0 0.25rem;
        }

        /* Campaigns Section */
        .campaigns-section {
            padding: 3rem 0 6rem;
        }

        .campaigns-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 340px), 1fr));
            gap: 1.5rem;
        }

        .campaign-card {
            background: white;
            border-radius: var(--radius-lg);
            overflow: hidden;
            border: 1px solid rgba(0,0,0,0.04);
            transition: all 0.5s var(--ease-out);
            position: relative;
        }

        .campaign-card::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: var(--radius-lg);
            padding: 2px;
            background: linear-gradient(135deg, var(--sun), var(--amber));
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.4s;
        }

        .campaign-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 24px 48px -12px rgba(249, 115, 22, 0.15);
        }

        .campaign-card:hover::before {
            opacity: 1;
        }

        .campaign-img {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .campaign-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.6s var(--ease-out);
        }

        .campaign-card:hover .campaign-img img {
            transform: scale(1.08);
        }

        .campaign-tag {
            position: absolute;
            top: 1rem;
            left: 1rem;
            padding: 0.5rem 1rem;
            background: white;
            border-radius: 100px;
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--sun);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }

        .campaign-urgent {
            position: absolute;
            top: 1rem;
            right: 1rem;
            padding: 0.5rem 0.875rem;
            background: #EF4444;
            border-radius: 100px;
            font-size: 0.65rem;
            font-weight: 700;
            color: white;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            display: flex;
            align-items: center;
            gap: 0.375rem;
        }

        .campaign-urgent::before {
            content: '';
            width: 6px;
            height: 6px;
            background: white;
            border-radius: 50%;
            animation: blink 1s ease-in-out infinite;
        }

        @keyframes blink {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.4; }
        }

        .campaign-status {
            position: absolute;
            bottom: 1rem;
            left: 1rem;
            padding: 0.4rem 0.875rem;
            border-radius: 100px;
            font-size: 0.65rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .campaign-status.completed {
            background: #10B981;
            color: white;
        }

        .campaign-status.active {
            background: var(--sun);
            color: white;
        }

        .campaign-body {
            padding: 1.5rem;
        }

        .campaign-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--earth);
            margin-bottom: 0.5rem;
            line-height: 1.4;
        }

        .campaign-desc {
            font-size: 0.875rem;
            color: var(--stone);
            margin-bottom: 1.25rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .campaign-meta {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
            font-size: 0.8rem;
            color: var(--stone-light);
        }

        .campaign-meta span {
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .campaign-meta svg {
            width: 14px;
            height: 14px;
        }

        .campaign-progress {
            margin-bottom: 1rem;
        }

        .progress-track {
            height: 8px;
            background: rgba(0,0,0,0.06);
            border-radius: 100px;
            overflow: hidden;
            margin-bottom: 0.75rem;
        }

        .progress-bar {
            height: 100%;
            background: linear-gradient(90deg, var(--sun), var(--amber));
            border-radius: 100px;
            position: relative;
            transition: width 1s var(--ease-out);
        }

        .progress-bar::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
            animation: shimmer 2s infinite;
        }

        .progress-bar.completed {
            background: linear-gradient(90deg, #10B981, #059669);
        }

        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.875rem;
        }

        .progress-raised {
            font-weight: 700;
            color: var(--sun);
        }

        .progress-raised.completed {
            color: #10B981;
        }

        .progress-goal {
            color: var(--stone-light);
        }

        .campaign-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-top: 1rem;
            border-top: 1px solid rgba(0,0,0,0.06);
        }

        .donors {
            display: flex;
            align-items: center;
        }

        .donor-avatars {
            display: flex;
            margin-right: 0.625rem;
        }

        .donor-avatar {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--peach), var(--sun-glow));
            border: 2px solid white;
            margin-left: -8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            font-weight: 700;
            color: var(--sun);
        }

        .donor-avatar:first-child {
            margin-left: 0;
        }

        .donor-count {
            font-size: 0.8rem;
            color: var(--stone-light);
        }

        .btn-donate {
            padding: 0.625rem 1.25rem;
            font-size: 0.8rem;
        }

        .btn-view {
            padding: 0.625rem 1.25rem;
            font-size: 0.8rem;
            background: var(--stone);
            color: white;
            border-radius: 100px;
            border: none;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s var(--ease-out);
        }

        .btn-view:hover {
            background: var(--earth);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state-icon {
            width: 80px;
            height: 80px;
            margin: 0 auto 1.5rem;
            background: var(--soft-orange);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
        }

        .empty-state h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.25rem;
            color: var(--earth);
            margin-bottom: 0.5rem;
        }

        .empty-state p {
            color: var(--stone-light);
            margin-bottom: 1.5rem;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 3rem;
        }

        .pagination button {
            width: 44px;
            height: 44px;
            border-radius: var(--radius-sm);
            border: 2px solid rgba(0,0,0,0.08);
            background: white;
            font-family: 'Outfit', sans-serif;
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--stone);
            cursor: pointer;
            transition: all 0.3s var(--ease-out);
        }

        .pagination button:hover {
            border-color: var(--sun);
            color: var(--sun);
        }

        .pagination button.active {
            background: linear-gradient(135deg, var(--sun), var(--amber));
            border-color: transparent;
            color: white;
        }

        .pagination button:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .pagination button svg {
            width: 18px;
            height: 18px;
        }

        /* Footer */
        .footer {
            background: linear-gradient(180deg, #1C1917 0%, #0C0A09 100%);
            color: white;
            padding: 3.5rem 0 1.5rem;
        }

        .footer-main {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            gap: 3rem;
            align-items: start;
        }

        .footer-brand {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .footer-brand-top {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 0.5rem;
        }

        .footer-brand-top img {
            width: 56px;
            height: 56px;
            border-radius: 50%;
            border: 2px solid rgba(249, 115, 22, 0.3);
        }

        .footer-brand h3 {
            font-size: 1.5rem;
            color: var(--peach);
            font-weight: 700;
        }

        .footer-brand-tagline {
            font-size: 0.95rem;
            color: var(--peach);
            font-weight: 500;
            margin-bottom: 0.25rem;
        }

        .footer-brand-sub {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.4);
            margin-bottom: 1.25rem;
        }

        .btn-join {
            padding: 0.75rem 2rem;
            background: #10B981;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.3s var(--ease-out);
        }

        .btn-join:hover {
            background: #059669;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px -4px rgba(16, 185, 129, 0.4);
        }

        .footer-col h4 {
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            font-weight: 600;
            color: white;
            margin-bottom: 1.25rem;
        }

        .footer-col ul {
            list-style: none;
        }

        .footer-col li {
            margin-bottom: 0.625rem;
        }

        .footer-col a {
            color: rgba(255,255,255,0.55);
            font-size: 0.875rem;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .footer-col a:hover {
            color: var(--sun-glow);
        }

        .footer-col a svg {
            width: 16px;
            height: 16px;
            flex-shrink: 0;
        }

        .footer-bottom {
            margin-top: 2.5rem;
            padding-top: 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .footer-bottom p {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.35);
            display: flex;
            align-items: center;
            gap: 0.35rem;
        }

        .footer-bottom .heart {
            color: #EF4444;
            animation: heartbeat 1.5s ease-in-out infinite;
        }

        @keyframes heartbeat {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.2); }
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .footer-main {
                grid-template-columns: repeat(2, 1fr);
            }
        }



        @media (max-width: 768px) {
            .page-hero {
                padding: 8rem 0 2rem;
            }

            .page-hero p {
                font-size: 1rem;
                margin-bottom: 1rem;
            }

            .filter-section {
                top: 72px;
                padding: 0.75rem 0;
            }

            .filter-inner {
                gap: 0.5rem;
            }

            .filter-row {
                gap: 0.5rem;
            }

            .search-box input {
                padding: 0.625rem 0.625rem 0.625rem 2.25rem;
                font-size: 0.85rem;
            }

            .sort-dropdown select {
                padding: 0.625rem 2rem 0.625rem 0.75rem;
                font-size: 0.8rem;
            }

            /* Hide desktop filter tags, show dropdown */
            .filter-tags {
                display: none;
            }

            .filter-dropdown {
                display: block;
            }

            .filter-dropdown select {
                padding: 0.625rem 2rem 0.625rem 0.75rem;
                font-size: 0.8rem;
            }

            .stats-bar {
                padding: 0.75rem 0;
            }

            .stats-bar-inner {
                gap: 0.375rem;
            }

            .stat-pill {
                padding: 0.375rem 0.75rem;
                gap: 0.375rem;
            }

            .stat-pill-icon {
                font-size: 0.85rem;
            }

            .stat-pill-value {
                font-size: 0.8rem;
            }

            .stat-pill-label {
                font-size: 0.7rem;
            }

            .stat-divider {
                display: none;
            }

            .footer-main {
                grid-template-columns: 1fr;
                gap: 2rem;
                text-align: center;
            }

            .footer-brand {
                align-items: center;
            }

            .footer-bottom {
                flex-direction: column;
                text-align: center;
            }
        }

        @media (max-width: 480px) {
            html {
                font-size: 15px;
            }

            .page-hero {
                padding: 7rem 0 1.5rem;
            }

            .page-hero h1 {
                font-size: 2rem;
            }

            .filter-section {
                top: 68px;
            }

            .stat-pill-label {
                display: none;
            }

            .stat-pill {
                padding: 0.35rem 0.6rem;
            }
        }
    </style>
</head>
<body>
    <!-- header  -->
    <?php include 'includes/header.php'; ?>

    <!-- Page Hero -->
    <section class="page-hero">
        <div class="container">
            <div class="page-hero-content">
                <h1>All <span class="highlight">Campaigns</span></h1>
                <p>Browse through verified fundraising campaigns and support causes that matter to you. Every contribution makes a difference.</p>
            </div>
        </div>
    </section>

    <!-- Filter Section -->
    <section class="filter-section">
        <div class="container">
            <div class="filter-inner">
                <div class="filter-row">
                    <div class="search-box">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="text" id="searchInput" placeholder="Search...">
                    </div>
                    <!-- Mobile Category Dropdown -->
                    <div class="filter-dropdown">
                        <select id="categorySelect">
                            <option value="all">All Categories</option>
                            <option value="Education">Education</option>
                            <option value="Medical">Medical</option>
                            <option value="Health">Health</option>
                            <option value="Disaster">Disaster</option>
                            <option value="Community">Community</option>
                        </select>
                    </div>
                    <div class="sort-dropdown">
                        <select id="sortSelect">
                            <option value="newest">Newest</option>
                            <option value="urgent">Urgent</option>
                            <option value="mostFunded">Most Funded</option>
                            <option value="leastFunded">Least Funded</option>
                            <option value="ending">Ending Soon</option>
                        </select>
                    </div>
                </div>
                <!-- Desktop Filter Tags -->
                <div class="filter-tags">
                    <button class="filter-tag active" data-filter="all">All</button>
                    <button class="filter-tag" data-filter="Education">Education</button>
                    <button class="filter-tag" data-filter="Medical">Medical</button>
                    <button class="filter-tag" data-filter="Health">Health</button>
                    <button class="filter-tag" data-filter="Disaster">Disaster</button>
                    <button class="filter-tag" data-filter="Community">Community</button>
                </div>
            </div>
        </div>
    </section>

    <!-- Stats Bar -->
    <section class="stats-bar">
        <div class="container">
            <div class="stats-bar-inner">
                <div class="stat-pill">
                    <span class="stat-pill-icon">📊</span>
                    <span class="stat-pill-value" id="totalCampaigns">12</span>
                    <span class="stat-pill-label">Campaigns</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-pill">
                    <span class="stat-pill-icon">🔥</span>
                    <span class="stat-pill-value" id="activeCampaigns">8</span>
                    <span class="stat-pill-label">Active</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-pill">
                    <span class="stat-pill-icon">✅</span>
                    <span class="stat-pill-value" id="completedCampaigns">4</span>
                    <span class="stat-pill-label">Funded</span>
                </div>
                <div class="stat-divider"></div>
                <div class="stat-pill">
                    <span class="stat-pill-icon">💰</span>
                    <span class="stat-pill-value" id="totalRaised">₹18.5L</span>
                    <span class="stat-pill-label">Raised</span>
                </div>
            </div>
        </div>
    </section>

    <!-- Campaigns Section -->
    <section class="campaigns-section">
        <div class="container">
            <div class="campaigns-grid" id="campaignsGrid"></div>
            
            <!-- Empty State -->
            <div class="empty-state" id="emptyState" style="display: none;">
                <div class="empty-state-icon">🔍</div>
                <h3>No campaigns found</h3>
                <p>Try adjusting your search or filter criteria</p>
                <button class="btn btn-sun" onclick="resetFilters()">Clear Filters</button>
            </div>

            <!-- Pagination -->
            <div class="pagination" id="pagination"></div>
        </div>
    </section>

 
    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script>
        // --- State Management ---
        let currentFilter = 'all';
        let currentSort = 'newest';
        let searchQuery = '';
        let currentPage = 1;
        const itemsPerPage = 9;
        let allLoadedCampaigns = []; // Stores the current filtered set

        // --- 1. DATA FETCHING ---
        async function loadCampaigns() {
            const grid = document.getElementById('campaignsGrid');
            const emptyState = document.getElementById('emptyState');
            
            // Construct the API URL with query parameters
            const params = new URLSearchParams({
                category: currentFilter,
                sort: currentSort,
                search: searchQuery
            });

            try {
                const response = await fetch(`actions/campaigns/fetch_all.php?${params.toString()}`);
                const data = await response.json();
                
                allLoadedCampaigns = data.campaigns || [];
                updateStatsBar(data.stats);

                if (allLoadedCampaigns.length === 0) {
                    grid.innerHTML = '';
                    emptyState.style.display = 'block';
                    document.getElementById('pagination').innerHTML = '';
                    return;
                }

                emptyState.style.display = 'none';
                renderCurrentPage();
            } catch (error) {
                console.error("Error fetching campaigns:", error);
                grid.innerHTML = `<p style="grid-column: 1/-1; text-align:center; color: var(--error); padding: 3rem;">Unable to load campaigns. Please refresh the page.</p>`;
            }
        }

        // --- 2. RENDERING LOGIC ---
        function renderCurrentPage() {
            const grid = document.getElementById('campaignsGrid');
            const totalPages = Math.ceil(allLoadedCampaigns.length / itemsPerPage);
            
            // Calculate slice for current page
            const start = (currentPage - 1) * itemsPerPage;
            const pageItems = allLoadedCampaigns.slice(start, start + itemsPerPage);

            grid.innerHTML = pageItems.map(c => {
                const raised = parseFloat(c.raised || 0);
                const goal = parseFloat(c.amount_needed || 0);
                const pct = goal > 0 ? Math.min(Math.round((raised / goal) * 100), 100) : 0;
                const isCompleted = c.status === 'completed' || pct >= 100;
                const isUrgent = c.urgency === 'high' && !isCompleted;

                return `
                <a href="campaign.php?id=${c.id}" target="_blank" style="text-decoration: none; display: block;">
                    <article class="campaign-card">
                        <div class="campaign-img">
                            <img src="${c.img}" alt="${c.title}" onerror="this.src='https://images.unsplash.com/photo-1532629345422-7515f3d16bb8?w=500'">
                            <span class="campaign-tag">${c.category}</span>
                            ${isUrgent ? '<span class="campaign-urgent">Urgent</span>' : ''}
                            <span class="campaign-status ${isCompleted ? 'completed' : 'active'}">
                                ${isCompleted ? 'Funded' : 'Active'}
                            </span>
                        </div>
                        <div class="campaign-body">
                            <h3 class="campaign-title">${c.title}</h3>
                            <p class="campaign-desc">${c.description}</p>
                            <div class="campaign-meta">
                                <span>🕒 ${isCompleted ? 'Goal Reached' : 'Ongoing'}</span>
                                <span>👥 ${c.donor_count || 0} donors</span>
                            </div>
                            <div class="campaign-progress">
                                <div class="progress-track"><div class="progress-bar ${isCompleted ? 'completed' : ''}" style="width:${pct}%"></div></div>
                                <div class="progress-info">
                                    <span class="progress-raised ${isCompleted ? 'completed' : ''}">₹${raised.toLocaleString('en-IN')}</span>
                                    <span class="progress-goal">of ₹${goal.toLocaleString('en-IN')} (${pct}%)</span>
                                </div>
                            </div>
                            <div class="campaign-footer">
                                <button class="${isCompleted ? 'btn-view' : 'btn btn-sun btn-donate'}" style="width: 100%; text-align: center; pointer-events: none;">
                                    ${isCompleted ? 'View Impact' : 'Donate Now'}
                                </button>
                            </div>
                        </div>
                    </article>
                </a>`;
            }).join('');

            renderPagination(totalPages);
        }

        function updateStatsBar(stats) {
            if (!stats) return;
            document.getElementById('totalCampaigns').textContent = stats.total || 0;
            document.getElementById('activeCampaigns').textContent = stats.active || 0;
            document.getElementById('completedCampaigns').textContent = stats.completed || 0;
            
            const raised = parseFloat(stats.total_raised || 0);
            document.getElementById('totalRaised').textContent = raised >= 100000 
                ? `₹${(raised/100000).toFixed(1)}L` 
                : `₹${raised.toLocaleString('en-IN')}`;
        }

        // --- 3. FILTER & UI CONTROLS ---
        function setFilter(filter) {
            currentFilter = filter;
            currentPage = 1;
            
            // Update Tags UI
            document.querySelectorAll('.filter-tag').forEach(tag => {
                tag.classList.toggle('active', tag.dataset.filter === filter);
            });
            
            // Update Select UI
            document.getElementById('categorySelect').value = filter;
            
            loadCampaigns();
        }

        function resetFilters() {
            document.getElementById('searchInput').value = '';
            searchQuery = '';
            setFilter('all');
        }

        // --- 4. PAGINATION ---
        function renderPagination(totalPages) {
            const nav = document.getElementById('pagination');
            if (totalPages <= 1) {
                nav.innerHTML = '';
                return;
            }

            let html = '';
            for (let i = 1; i <= totalPages; i++) {
                html += `<button class="${i === currentPage ? 'active' : ''}" onclick="changePage(${i})">${i}</button>`;
            }
            nav.innerHTML = html;
        }

        window.changePage = function(page) {
            currentPage = page;
            renderCurrentPage();
            window.scrollTo({ top: 450, behavior: 'smooth' });
        };

        // --- 5. EVENT LISTENERS ---
        document.getElementById('searchInput').addEventListener('input', (e) => {
            searchQuery = e.target.value;
            currentPage = 1;
            // Optional: debounce this if you have thousands of records
            loadCampaigns();
        });

        document.getElementById('sortSelect').addEventListener('change', (e) => {
            currentSort = e.target.value;
            currentPage = 1;
            loadCampaigns();
        });

        document.getElementById('categorySelect').addEventListener('change', (e) => {
            setFilter(e.target.value);
        });

        document.querySelectorAll('.filter-tag').forEach(tag => {
            tag.addEventListener('click', () => setFilter(tag.dataset.filter));
        });

        // Mobile Menu Toggle
        const menuToggle = document.getElementById('menuToggle');
        const mobileNav = document.getElementById('mobileNav');
        if (menuToggle) {
            menuToggle.addEventListener('click', () => {
                menuToggle.classList.toggle('active');
                mobileNav.classList.toggle('active');
                document.body.style.overflow = mobileNav.classList.contains('active') ? 'hidden' : '';
            });
        }

        // Header Scroll
        window.addEventListener('scroll', () => {
            const header = document.getElementById('header');
            if (header) header.classList.toggle('scrolled', window.scrollY > 50);
        });

        // --- INITIALIZE ---
        document.addEventListener('DOMContentLoaded', loadCampaigns);

    </script>
</body>
</html>