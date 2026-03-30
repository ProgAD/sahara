<?php
session_start();
$user_id = $_SESSION['user_id'] ?? null;
$user_name = $_SESSION['name'] ?? null;
$user_email = $_SESSION['email'] ?? null;
$user_phone = $_SESSION['phone'] ?? null;
$role = $_SESSION['role'] ?? null;

$current_page = "index.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SAHARA – IIT M BS Welfare Society</title>
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

        /* Typography */
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

        

        /* Hero Section */
        .hero {
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            align-items: center;
            padding: 8rem 0 5rem;
            position: relative;
            overflow: hidden;
        }

        .hero-bg {
            position: absolute;
            inset: 0;
            z-index: -1;
        }

        .hero-blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
            animation: blobFloat 20s ease-in-out infinite;
        }

        .hero-blob-1 {
            width: min(600px, 80vw);
            height: min(600px, 80vw);
            background: linear-gradient(135deg, var(--peach), var(--sun-glow));
            top: -20%;
            right: -10%;
            animation-delay: 0s;
        }

        .hero-blob-2 {
            width: min(400px, 60vw);
            height: min(400px, 60vw);
            background: linear-gradient(135deg, var(--soft-orange), var(--peach));
            bottom: -10%;
            left: -10%;
            animation-delay: -7s;
        }

        .hero-blob-3 {
            width: min(300px, 50vw);
            height: min(300px, 50vw);
            background: linear-gradient(135deg, var(--sun-glow), var(--amber));
            top: 40%;
            left: 30%;
            animation-delay: -14s;
            opacity: 0.3;
        }

        @keyframes blobFloat {
            0%, 100% { transform: translate(0, 0) scale(1); }
            25% { transform: translate(30px, -40px) scale(1.05); }
            50% { transform: translate(-20px, 30px) scale(0.95); }
            75% { transform: translate(40px, 20px) scale(1.02); }
        }

        .hero-content {
            position: relative;
            z-index: 1;
        }

        .hero-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 4rem;
            align-items: center;
        }

        .hero-text {
            max-width: 720px;
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.625rem 1.25rem;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.12), rgba(245, 158, 11, 0.08));
            border: 1px solid rgba(249, 115, 22, 0.15);
            border-radius: 100px;
            font-size: 0.8rem;
            font-weight: 600;
            color: var(--sun);
            margin-bottom: 1.5rem;
            animation: fadeSlideUp 0.8s var(--ease-out) forwards;
            opacity: 0;
        }

        .hero-badge svg {
            width: 16px;
            height: 16px;
            animation: pulse 2s ease-in-out infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.15); opacity: 0.8; }
        }

        .hero h1 {
            font-size: clamp(2.5rem, 8vw, 5rem);
            color: var(--earth);
            margin-bottom: 1.5rem;
            animation: fadeSlideUp 0.8s var(--ease-out) 0.1s forwards;
            opacity: 0;
        }

        .hero h1 .highlight {
            position: relative;
            color: var(--sun);
            display: inline-block;
        }

        .hero h1 .highlight::before {
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

        .hero-desc {
            font-size: clamp(1.05rem, 2.5vw, 1.25rem);
            color: var(--stone);
            max-width: 540px;
            margin-bottom: 2.5rem;
            animation: fadeSlideUp 0.8s var(--ease-out) 0.2s forwards;
            opacity: 0;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            margin-bottom: 4rem;
            animation: fadeSlideUp 0.8s var(--ease-out) 0.3s forwards;
            opacity: 0;
        }

        /* NEW Hero Stats Design */
        .hero-stats {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            animation: fadeSlideUp 0.8s var(--ease-out) 0.4s forwards;
            opacity: 0;
        }

        .stat-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 1.25rem 1.75rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 4px 24px -4px rgba(0,0,0,0.06);
            border: 1px solid rgba(249, 115, 22, 0.08);
            transition: all 0.4s var(--ease-out);
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: linear-gradient(180deg, var(--sun), var(--amber));
            border-radius: 4px 0 0 4px;
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px -8px rgba(249, 115, 22, 0.15);
        }

        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: var(--radius-sm);
            background: linear-gradient(135deg, var(--soft-orange), var(--peach));
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .stat-content {
            display: flex;
            flex-direction: column;
        }

        .stat-value {
            font-family: 'Playfair Display', serif;
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--earth);
            line-height: 1.1;
        }

        .stat-label {
            font-size: 0.8rem;
            color: var(--stone-light);
            font-weight: 500;
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Section Styles */
        .section {
            padding: clamp(4rem, 10vw, 8rem) 0;
        }

        .section-header {
            text-align: center;
            max-width: 640px;
            margin: 0 auto 4rem;
        }

        .section-tag {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: var(--soft-orange);
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 700;
            color: var(--sun);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1.25rem;
        }

        .section-header h2 {
            font-size: clamp(2rem, 5vw, 3.25rem);
            color: var(--earth);
            margin-bottom: 1rem;
        }

        .section-header p {
            font-size: 1.1rem;
            color: var(--stone);
        }

        /* Campaigns Section */
        .campaigns {
            background: white;
            position: relative;
        }

        .campaigns::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 1px;
            background: linear-gradient(90deg, transparent, rgba(0,0,0,0.08), transparent);
        }

        .campaigns-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 340px), 1fr));
            gap: 1.5rem;
        }

        .campaign-card {
            background: var(--cream);
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

        .campaigns-more {
            text-align: center;
            margin-top: 3rem;
        }

        /* Workflow Section */
        .workflow {
            background: linear-gradient(180deg, var(--cream) 0%, var(--warm-white) 100%);
            position: relative;
            overflow: hidden;
        }

        .workflow-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            position: relative;
        }

        .workflow-line {
            position: absolute;
            top: 72px;
            left: 12.5%;
            right: 12.5%;
            height: 3px;
            background: linear-gradient(90deg, var(--peach), var(--sun), var(--peach));
            border-radius: 10px;
            z-index: 0;
        }

        .workflow-step {
            text-align: center;
            position: relative;
            z-index: 1;
        }

        .step-icon {
            width: 100px;
            height: 100px;
            margin: 0 auto 1.5rem;
            background: white;
            border-radius: var(--radius-md);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 16px 40px -12px rgba(0,0,0,0.1);
            position: relative;
            transition: all 0.4s var(--ease-out);
        }

        .workflow-step:hover .step-icon {
            transform: translateY(-8px) scale(1.05);
            box-shadow: 0 24px 48px -12px rgba(249, 115, 22, 0.2);
        }

        .step-icon svg {
            width: 40px;
            height: 40px;
            color: var(--sun);
        }

        .step-num {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, var(--sun), var(--amber));
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-weight: 700;
            font-size: 0.95rem;
            box-shadow: 0 4px 12px rgba(249, 115, 22, 0.4);
        }

        .workflow-step h3 {
            font-family: 'Outfit', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--earth);
            margin-bottom: 0.5rem;
        }

        .workflow-step p {
            font-size: 0.875rem;
            color: var(--stone);
            max-width: 200px;
            margin: 0 auto;
        }

        .workflow-cta {
            text-align: center;
            margin-top: 4rem;
            padding: 3rem;
            background: white;
            border-radius: var(--radius-xl);
            box-shadow: 0 24px 64px -16px rgba(0,0,0,0.08);
        }

        .workflow-cta h3 {
            font-size: clamp(1.5rem, 4vw, 2rem);
            color: var(--earth);
            margin-bottom: 0.75rem;
        }

        .workflow-cta p {
            font-size: 1rem;
            color: var(--stone);
            margin-bottom: 2rem;
            max-width: 480px;
            margin-left: auto;
            margin-right: auto;
        }

        .workflow-cta-btns {
            display: flex;
            justify-content: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        /* NEW Impact Section Design */
        .impact {
            background: var(--earth);
            position: relative;
            overflow: hidden;
            padding: clamp(5rem, 12vw, 9rem) 0;
        }

        .impact::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -25%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(249, 115, 22, 0.15) 0%, transparent 60%);
            pointer-events: none;
        }

        .impact-header {
            text-align: center;
            margin-bottom: 4rem;
            position: relative;
            z-index: 1;
        }

        .impact-header h2 {
            font-size: clamp(2rem, 5vw, 3rem);
            color: white;
            margin-bottom: 0.75rem;
        }

        .impact-header p {
            font-size: 1.1rem;
            color: rgba(255,255,255,0.6);
        }

        .impact-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .impact-card {
            background: rgba(255,255,255,0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: var(--radius-lg);
            padding: 2rem 1.5rem;
            text-align: center;
            transition: all 0.4s var(--ease-out);
            position: relative;
            overflow: hidden;
        }

        .impact-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(249, 115, 22, 0.1), transparent);
            opacity: 0;
            transition: opacity 0.4s;
        }

        .impact-card:hover {
            transform: translateY(-8px);
            border-color: rgba(249, 115, 22, 0.3);
            box-shadow: 0 24px 48px -12px rgba(0,0,0,0.3);
        }

        .impact-card:hover::before {
            opacity: 1;
        }

        .impact-emoji {
            font-size: 3rem;
            margin-bottom: 1rem;
            display: block;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.2));
            animation: float 3s ease-in-out infinite;
        }

        .impact-card:nth-child(1) .impact-emoji { animation-delay: 0s; }
        .impact-card:nth-child(2) .impact-emoji { animation-delay: 0.5s; }
        .impact-card:nth-child(3) .impact-emoji { animation-delay: 1s; }
        .impact-card:nth-child(4) .impact-emoji { animation-delay: 1.5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-8px); }
        }

        .impact-card h3 {
            font-size: clamp(2rem, 5vw, 3rem);
            color: var(--sun-glow);
            line-height: 1;
            margin-bottom: 0.5rem;
        }

        .impact-card p {
            font-size: 0.95rem;
            color: rgba(255,255,255,0.7);
            font-weight: 500;
        }

        /* Testimonials */
        .testimonials {
            background: white;
        }

        .testimonials-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(min(100%, 340px), 1fr));
            gap: 1.5rem;
        }

        .testimonial-card {
            padding: 2rem;
            background: var(--cream);
            border-radius: var(--radius-lg);
            border: 1px solid rgba(0,0,0,0.04);
            transition: all 0.4s var(--ease-out);
        }

        .testimonial-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 20px 40px -12px rgba(0,0,0,0.1);
        }

        .testimonial-quote {
            font-size: 1.05rem;
            color: var(--stone);
            margin-bottom: 1.5rem;
            position: relative;
            padding-left: 1.5rem;
            line-height: 1.7;
        }

        .testimonial-quote::before {
            content: '"';
            position: absolute;
            top: -0.5rem;
            left: -0.25rem;
            font-family: 'Playfair Display', serif;
            font-size: 4rem;
            color: var(--peach);
            line-height: 1;
        }

        .testimonial-author {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .author-avatar {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--peach), var(--sun-glow));
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--sun);
        }

        .author-info h4 {
            font-family: 'Outfit', sans-serif;
            font-size: 1rem;
            font-weight: 700;
            color: var(--earth);
        }

        .author-info p {
            font-size: 0.8rem;
            color: var(--stone-light);
        }

        /* Compact Footer Design */
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

        .footer-contact-col {
            position: relative;
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

        /* Responsive Design */
        @media (max-width: 1200px) {
            .workflow-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 3rem 2rem;
            }

            .workflow-line {
                display: none;
            }

            .impact-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .footer-main {
                grid-template-columns: repeat(2, 1fr);
            }
        }


        @media (max-width: 768px) {
            .hero {
                padding: 7rem 0 4rem;
            }

            .hero-stats {
                flex-direction: column;
                gap: 0.75rem;
            }

            .stat-card {
                width: 100%;
            }

            .workflow-grid {
                grid-template-columns: 1fr;
                gap: 2.5rem;
            }

            .workflow-cta {
                padding: 2rem 1.5rem;
            }

            .impact-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .impact-card {
                padding: 1.5rem 1rem;
            }

            .impact-emoji {
                font-size: 2.5rem;
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

            .hero {
                padding: 6rem 0 3rem;
            }

            .hero-actions {
                flex-direction: column;
            }

            .hero-actions .btn {
                width: 100%;
            }

            .impact-grid {
                grid-template-columns: 1fr 1fr;
            }

            .impact-card h3 {
                font-size: 1.75rem;
            }

            .testimonial-card {
                padding: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <?php include 'includes/header.php'; ?>

    <!-- Hero -->
    <section class="hero" id="home">
        <div class="hero-bg">
            <div class="hero-blob hero-blob-1"></div>
            <div class="hero-blob hero-blob-2"></div>
            <div class="hero-blob hero-blob-3"></div>
        </div>
        <div class="container">
            <div class="hero-content">
                <div class="hero-grid">
                    <div class="hero-text">
                        <div class="hero-badge">
                            <svg fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Trusted by 2,000+ students
                        </div>
                        <h1>Empowering <span class="highlight">Dreams</span> Through Community</h1>
                        <p class="hero-desc">Join SAHARA in creating lasting change. Support verified causes, help fellow students, and be part of a movement that transforms lives.</p>
                        <div class="hero-actions">
                            <a href="#campaigns" class="btn btn-sun">
                                Explore Campaigns
                                <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
                            </a>
                            <a href="#workflow" class="btn btn-ghost">Start a Fundraiser</a>
                        </div>
                        
                        <!-- NEW Hero Stats Design -->
                        <div class="hero-stats">
                            <div class="stat-card">
                                <div class="stat-icon">💰</div>
                                <div class="stat-content">
                                    <div class="stat-value">₹15L+</div>
                                    <div class="stat-label">Funds Raised</div>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">🎯</div>
                                <div class="stat-content">
                                    <div class="stat-value">50+</div>
                                    <div class="stat-label">Campaigns</div>
                                </div>
                            </div>
                            <div class="stat-card">
                                <div class="stat-icon">🏆</div>
                                <div class="stat-content">
                                    <div class="stat-value">98%</div>
                                    <div class="stat-label">Success Rate</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Campaigns -->
    <section class="section campaigns" id="campaigns">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clip-rule="evenodd"/></svg>
                    Active Campaigns
                </span>
                <h2>Support a Cause Today</h2>
                <p>Every contribution matters. Browse verified campaigns and help make someone's dream come true.</p>
            </div>
            <div class="campaigns-grid" id="campaignsGrid"></div>
            <div class="campaigns-more">
                <a href="campaigns.html" class="btn btn-ghost">View All Campaigns</a>
            </div>
        </div>
    </section>

    <!-- Workflow -->
    <section class="section workflow" id="workflow">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">
                    <svg width="14" height="14" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.707l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"/></svg>
                    Start a Fundraiser
                </span>
                <h2>How It Works</h2>
                <p>Launch your fundraising campaign in 4 simple steps</p>
            </div>
            <div class="workflow-grid">
                <div class="workflow-line"></div>
                <div class="workflow-step">
                    <div class="step-icon">
                        <span class="step-num">1</span>
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                    </div>
                    <h3>Create Account</h3>
                    <p>Sign up with your IIT M BS email and verify your identity</p>
                </div>
                <div class="workflow-step">
                    <div class="step-icon">
                        <span class="step-num">2</span>
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                    </div>
                    <h3>Submit Request</h3>
                    <p>Fill in campaign details, upload documents, and set your goal</p>
                </div>
                <div class="workflow-step">
                    <div class="step-icon">
                        <span class="step-num">3</span>
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                    </div>
                    <h3>Admin Review</h3>
                    <p>Our team verifies your request within 24-48 hours</p>
                </div>
                <div class="workflow-step">
                    <div class="step-icon">
                        <span class="step-num">4</span>
                        <svg fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12z"/></svg>
                    </div>
                    <h3>Get Support</h3>
                    <p>Campaign goes live and start receiving donations</p>
                </div>
            </div>
            <div class="workflow-cta">
                <h3>Ready to Make a Difference?</h3>
                <p>Start your fundraising journey today. Our team is here to support you every step of the way.</p>
                <div class="workflow-cta-btns">
                    <a href="#" class="btn btn-sun">
                        Create Free Account
                        <svg fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M17.25 8.25L21 12m0 0l-3.75 3.75M21 12H3"/></svg>
                    </a>
                    <a href="#" class="btn btn-ghost">Learn More</a>
                </div>
            </div>
        </div>
    </section>

    <!-- NEW Impact Section Design -->
    <section class="section impact" id="impact">
        <div class="container">
            <div class="impact-header">
                <h2>Our Impact in Numbers</h2>
                <p>Together, we're making a real difference in students' lives</p>
            </div>
            <div class="impact-grid">
                <div class="impact-card">
                    <span class="impact-emoji">💸</span>
                    <h3>₹15L+</h3>
                    <p>Total Funds Raised</p>
                </div>
                <div class="impact-card">
                    <span class="impact-emoji">🤝</span>
                    <h3>2,500+</h3>
                    <p>Generous Donors</p>
                </div>
                <div class="impact-card">
                    <span class="impact-emoji">🚀</span>
                    <h3>50+</h3>
                    <p>Campaigns Funded</p>
                </div>
                <div class="impact-card">
                    <span class="impact-emoji">✨</span>
                    <h3>100%</h3>
                    <p>Transparency</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="section testimonials">
        <div class="container">
            <div class="section-header">
                <span class="section-tag">Testimonials</span>
                <h2>Stories of Impact</h2>
                <p>Hear from students whose lives were transformed through SAHARA</p>
            </div>
            <div class="testimonials-grid">
                <div class="testimonial-card">
                    <p class="testimonial-quote">SAHARA helped me get a laptop when I couldn't afford one. Now I can attend all my online classes without worry. Forever grateful!</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">RS</div>
                        <div class="author-info">
                            <h4>Rahul Sharma</h4>
                            <p>BS Data Science, 2023</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-quote">When my father needed surgery, SAHARA raised funds within a week. The support from fellow students was overwhelming.</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">PK</div>
                        <div class="author-info">
                            <h4>Priya Krishnan</h4>
                            <p>BS Electronics, 2022</p>
                        </div>
                    </div>
                </div>
                <div class="testimonial-card">
                    <p class="testimonial-quote">I donated ₹500 to three campaigns. Seeing the impact reports makes me want to contribute more. Beautifully organized!</p>
                    <div class="testimonial-author">
                        <div class="author-avatar">AV</div>
                        <div class="author-info">
                            <h4>Amit Verma</h4>
                            <p>BS Programming, 2021</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script>
        // Campaign Data
        const campaigns = [
            { id: 1, title: "Education Support for Rural Students", desc: "Help underprivileged students access quality education materials and online resources.", category: "Education", raised: 125000, goal: 200000, donors: 189, urgent: false, img: "https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=500" },
            { id: 2, title: "Medical Emergency – Rahul's Surgery", desc: "Rahul needs urgent spinal surgery. Help us raise funds for his treatment and recovery.", category: "Medical", raised: 420000, goal: 500000, donors: 312, urgent: true, img: "https://images.unsplash.com/photo-1579684385127-1ef15d508118?w=500" },
            { id: 3, title: "Laptop Fund for Merit Students", desc: "Providing laptops to academically excellent students who cannot afford devices.", category: "Education", raised: 180000, goal: 300000, donors: 167, urgent: false, img: "https://images.unsplash.com/photo-1517694712202-14dd9538aa97?w=500" },
            { id: 4, title: "Mental Health Support Initiative", desc: "Funding counseling sessions and mental health resources for students.", category: "Health", raised: 78000, goal: 120000, donors: 98, urgent: false, img: "https://images.unsplash.com/photo-1544027993-37dbfe43562a?w=500" },
            { id: 5, title: "Flood Relief – Kerala Students", desc: "Emergency support for BS students affected by floods in Kerala.", category: "Disaster", raised: 340000, goal: 400000, donors: 423, urgent: true, img: "https://images.unsplash.com/photo-1547683905-f686c993aae5?w=500" },
            { id: 6, title: "Community Library Project", desc: "Building a community library with study spaces and internet access.", category: "Community", raised: 65000, goal: 250000, donors: 87, urgent: false, img: "https://images.unsplash.com/photo-1521587760476-6c12a4b040da?w=500" }
        ];

        // Render Campaigns
        function renderCampaigns() {
            const grid = document.getElementById('campaignsGrid');
            grid.innerHTML = campaigns.map(c => {
                const pct = Math.round((c.raised / c.goal) * 100);
                return `
                <article class="campaign-card">
                    <div class="campaign-img">
                        <img src="${c.img}" alt="${c.title}" loading="lazy">
                        <span class="campaign-tag">${c.category}</span>
                        ${c.urgent ? '<span class="campaign-urgent">Urgent</span>' : ''}
                    </div>
                    <div class="campaign-body">
                        <h3 class="campaign-title">${c.title}</h3>
                        <p class="campaign-desc">${c.desc}</p>
                        <div class="campaign-progress">
                            <div class="progress-track"><div class="progress-bar" style="width:${pct}%"></div></div>
                            <div class="progress-info">
                                <span class="progress-raised">₹${(c.raised/1000).toFixed(0)}K</span>
                                <span class="progress-goal">of ₹${(c.goal/1000).toFixed(0)}K</span>
                            </div>
                        </div>
                        <div class="campaign-footer">
                            <div class="donors">
                                <div class="donor-avatars">
                                    <div class="donor-avatar">A</div>
                                    <div class="donor-avatar">S</div>
                                    <div class="donor-avatar">P</div>
                                </div>
                                <span class="donor-count">${c.donors} donors</span>
                            </div>
                            <button class="btn btn-sun btn-donate" onclick="donate(${c.id})">Donate</button>
                        </div>
                    </div>
                </article>`;
            }).join('');
        }

        // Donate
        function donate(id) {
            alert(`Redirecting to secure payment for Campaign #${id}\n\n(Integrates with Razorpay/PayU in production)`);
        }

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
        window.addEventListener('scroll', () => {
            header.classList.toggle('scrolled', window.scrollY > 50);
        });

        // Smooth Scroll
        document.querySelectorAll('a[href^="#"]').forEach(a => {
            a.addEventListener('click', e => {
                e.preventDefault();
                const target = document.querySelector(a.getAttribute('href'));
                if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            });
        });

        // Init
        renderCampaigns();
    </script>
</body>
</html>