<style>
    /* Header */
    .header {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        padding: 1rem 0;
        background: var(--cream);
        transition: all 0.4s var(--ease-out);
    }

    .header.scrolled {
        background: rgba(255, 251, 245, 0.85);
        backdrop-filter: blur(20px) saturate(180%);
        -webkit-backdrop-filter: blur(20px) saturate(180%);
        box-shadow: 0 1px 0 rgba(0,0,0,0.05);
    }

    .header-inner {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 2rem;
    }

    .logo {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        z-index: 1001;
    }

    .logo img {
        height: 48px;
        width: auto;
        border-radius: var(--radius-sm);
    }

    .logo-text {
        display: flex;
        flex-direction: column;
    }

    .logo-text strong {
        font-family: 'Playfair Display', serif;
        font-size: 1.35rem;
        font-weight: 700;
        color: var(--sun);
        line-height: 1.1;
    }

    .logo-text span {
        font-size: 0.65rem;
        font-weight: 500;
        color: var(--stone-light);
        text-transform: uppercase;
        letter-spacing: 0.1em;
    }

    .nav {
        display: flex;
        align-items: center;
        gap: 2.5rem;
    }

    .nav-links {
        display: flex;
        align-items: center;
        gap: 2rem;
        list-style: none;
    }

    .nav-links a {
        font-weight: 500;
        font-size: 0.9rem;
        color: var(--stone);
        position: relative;
        padding: 0.5rem 0;
        transition: color 0.3s;
    }

    .nav-links a::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0;
        height: 2px;
        background: var(--sun);
        border-radius: 2px;
        transition: width 0.4s var(--ease-out);
    }

    .nav-links a:hover,
    .nav-links a.active {
        color: var(--sun);
    }

    .nav-links a:hover::after,
    .nav-links a.active::after {
        width: 100%;
    }

    .nav-cta {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .nav-cta .btn {
        padding: 0.75rem 1.5rem;
        font-size: 0.875rem;
    }

    /* Mobile Menu */
    .menu-toggle {
        display: none;
        width: 48px;
        height: 48px;
        border-radius: var(--radius-sm);
        background: var(--warm-white);
        border: 1px solid rgba(0,0,0,0.06);
        cursor: pointer;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 6px;
        z-index: 1001;
    }

    .menu-toggle span {
        display: block;
        width: 22px;
        height: 2px;
        background: var(--earth);
        border-radius: 2px;
        transition: all 0.3s var(--ease-out);
        transform-origin: center;
    }

    .menu-toggle.active span:nth-child(1) {
        transform: rotate(45deg) translate(5px, 6px);
    }

    .menu-toggle.active span:nth-child(2) {
        opacity: 0;
        transform: scaleX(0);
    }

    .menu-toggle.active span:nth-child(3) {
        transform: rotate(-45deg) translate(5px, -6px);
    }

    .mobile-nav {
        display: none;
        position: fixed;
        inset: 0;
        height: 100vh;
        height: 100dvh;
        background: var(--cream);
        z-index: 999;
        padding: 120px 2rem 2rem;
        opacity: 0;
        visibility: hidden;
        transition: all 0.4s var(--ease-out);
        overflow-y: auto;
    }

    .mobile-nav.active {
        display: block;
        opacity: 1;
        visibility: visible;
    }

    .mobile-nav-links {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .mobile-nav-links a {
        display: block;
        padding: 1rem 0;
        font-size: 1.75rem;
        font-family: 'Playfair Display', serif;
        font-weight: 600;
        color: var(--earth);
        border-bottom: 1px solid rgba(0,0,0,0.06);
        transition: all 0.3s;
    }

    .mobile-nav-links a:hover,
    .mobile-nav-links a.active {
        color: var(--sun);
        padding-left: 1rem;
    }

    .mobile-nav-cta {
        margin-top: 2rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .mobile-nav-cta .btn {
        width: 100%;
        justify-content: center;
    }

    @media (max-width: 1024px) {
        .nav {
            display: none;
        }

        .menu-toggle {
            display: flex;
        }

        .mobile-nav {
            display: block;
        }
    }

</style>
<!-- Header -->
    <header class="header" id="header">
        <div class="container">
            <div class="header-inner">
                <a href="index.html" class="logo">
                    <img src="assets/logo.jpg" alt="SAHARA">
                    <div class="logo-text">
                        <strong>SAHARA</strong>
                        <span>IIT M BS Welfare</span>
                    </div>
                </a>

                <nav class="nav">
                    <ul class="nav-links">
                        <li><a href="index.php" class="<?php if ($current_page == "index.php") { echo "active"; } ?>">Home</a></li>
                        <li><a href="all-campaigns.php" class="<?php if ($current_page == "all-campaigns.php") { echo "active"; } ?>">Campaigns</a></li>
                        <li><a href="fundraise-request.php" class="<?php if ($current_page == "fundraise-request.php") { echo "active"; } ?>">Start Fundraiser</a></li>
                        <li><a href="index.php#impact">Impact</a></li>
                        <li><a href="index.php#footer">Contact</a></li>
                    </ul>
                    <div class="nav-cta">
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != '') { ?>
                            <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "user") { ?>
                                <a href="dashboard.php" class="btn btn-ghost" style="border-color: var(--sun); color: var(--sun);">Dashboard</a>
                            <?php } else if (isset($_SESSION['role']) && $_SESSION['role'] == "admin") { ?>
                                <a href="admin/dashboard.php" class="btn btn-ghost" style="border-color: var(--sun); color: var(--sun);">Admin Panel</a>
                        <?php } ?>
                        <a href="actions/auth/logout.php" class="btn btn-sun" onclick="alert('Logging out...')">Logout</a>
                        <?php } else { ?>
                        <a href="login.html" class="btn btn-ghost">Log in</a>
                        <a href="signup.html" class="btn btn-sun">Get Started</a>
                        <?php } ?>
                    </div>
                </nav>

                <button class="menu-toggle" id="menuToggle" aria-label="Toggle menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </div>
        </div>

        <!-- Mobile Nav -->
        <nav class="mobile-nav" id="mobileNav">
            <ul class="mobile-nav-links">
                <li><a href="index.php" class="<?php if ($current_page == "index.php") { echo "active"; } ?>" data-close>Home</a></li>
                <li><a href="all-campaigns.php" class="<?php if ($current_page == "all-campaigns.php") { echo "active"; } ?>" data-close>Campaigns</a></li>
                <li><a href="fundraise-request.php" class="<?php if ($current_page == "fundraise-request.php") { echo "active"; } ?>" data-close>Start Fundraiser</a></li>
                <li><a href="index.php#impact" data-close>Impact</a></li>
                <li><a href="index.php#footer" data-close>Contact</a></li>
            </ul>
            <div class="mobile-nav-cta">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] != '') { ?>
                <?php if (isset($_SESSION['role']) && $_SESSION['role'] == "user") { ?>
                <a href="dashboard.php" class="btn btn-ghost">Dashboard</a>
                <?php } else if (isset($_SESSION['role']) && $_SESSION['role'] == "admin") { ?>
                <a href="admin/dashboard.php" class="btn btn-ghost">Admin Panel</a>
                <?php } ?>
                <a href="actions/auth/logout.php" class="btn btn-sun">Logout</a>
                <?php } else { ?>
                <a href="login.html" class="btn btn-ghost">Log in</a>
                <a href="signup.html" class="btn btn-sun">Get Started</a>
                <?php } ?>
            </div>
        </nav>
    </header>