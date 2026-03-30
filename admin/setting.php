<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_id'] == '') {
    header('Location: ../actions/auth/logout.php');
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


$current_page = "setting.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings – Admin – SAHARA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg:#0F172A;--sidebar-hover:#1E293B;--sidebar-active:rgba(249,115,22,0.12);--sidebar-border:rgba(255,255,255,0.06);
            --page-bg:#F1F5F9;--card-bg:#FFFFFF;--card-border:#E2E8F0;
            --text-primary:#0F172A;--text-secondary:#475569;--text-muted:#94A3B8;
            --accent:#F97316;--accent-light:#FB923C;--accent-bg:#FFF7ED;
            --success:#10B981;--success-bg:#D1FAE5;--warning:#F59E0B;--warning-bg:#FEF3C7;
            --error:#EF4444;--error-bg:#FEE2E2;--info:#3B82F6;--info-bg:#DBEAFE;
            --sidebar-width:260px;--header-height:72px;
            --radius-sm:10px;--radius-md:14px;--radius-lg:20px;
            --ease-out:cubic-bezier(0.16,1,0.3,1);--ease-spring:cubic-bezier(0.34,1.56,0.64,1);
        }
        *{margin:0;padding:0;box-sizing:border-box}html{scroll-behavior:smooth;font-size:16px}
        body{font-family:'Outfit',sans-serif;background:var(--page-bg);color:var(--text-primary);line-height:1.6;-webkit-font-smoothing:antialiased;overflow-x:hidden}
        h1,h2,h3,h4{font-family:'Playfair Display',serif;font-weight:700;line-height:1.2}

        /* Sidebar */
        .sidebar{position:fixed;top:0;left:0;bottom:0;width:var(--sidebar-width);background:var(--sidebar-bg);display:flex;flex-direction:column;z-index:200;transition:transform .4s var(--ease-out);overflow-y:auto;overflow-x:hidden}
        .sidebar-header{padding:1.5rem 1.25rem;border-bottom:1px solid var(--sidebar-border);display:flex;align-items:center;gap:.75rem;flex-shrink:0;position:relative}
        .sidebar-close{display:none;position:absolute;top:1.25rem;right:1rem;width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);cursor:pointer;align-items:center;justify-content:center;transition:all .25s}.sidebar-close svg{width:18px;height:18px;color:rgba(255,255,255,.6)}.sidebar-close:hover{background:rgba(239,68,68,.2)}.sidebar-close:hover svg{color:var(--error)}
        .sidebar-logo{width:44px;height:44px;border-radius:12px;flex-shrink:0}
        .sidebar-brand{display:flex;flex-direction:column}.sidebar-brand strong{font-family:'Playfair Display',serif;font-size:1.2rem;font-weight:700;color:var(--accent);line-height:1.1}.sidebar-brand span{font-size:.6rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.12em;font-weight:500}
        .sidebar-admin-tag{display:inline-block;margin-top:.375rem;padding:.2rem .625rem;border-radius:100px;background:rgba(249,115,22,.15);color:var(--accent);font-size:.6rem;font-weight:700;text-transform:uppercase;letter-spacing:.05em;width:fit-content}
        .sidebar-nav{padding:1rem .75rem;flex:1}.sidebar-label{font-size:.65rem;font-weight:700;text-transform:uppercase;letter-spacing:.12em;color:var(--text-muted);padding:.75rem .75rem .5rem;margin-top:.5rem}.sidebar-label:first-child{margin-top:0}
        .sidebar-link{display:flex;align-items:center;gap:.75rem;padding:.75rem;border-radius:var(--radius-sm);font-size:.875rem;font-weight:500;color:rgba(255,255,255,.55);cursor:pointer;transition:all .25s var(--ease-out);border:none;background:none;width:100%;text-align:left;text-decoration:none}
        .sidebar-link:hover{background:var(--sidebar-hover);color:rgba(255,255,255,.9)}.sidebar-link.active{background:var(--sidebar-active);color:var(--accent);font-weight:600}
        .sidebar-link svg{width:20px;height:20px;flex-shrink:0;opacity:.6}.sidebar-link.active svg{opacity:1;color:var(--accent)}
        .sidebar-link-badge{margin-left:auto;padding:.15rem .5rem;border-radius:100px;font-size:.65rem;font-weight:700;background:var(--accent);color:white;min-width:20px;text-align:center}
        .sidebar-footer{padding:1rem 1.25rem;border-top:1px solid var(--sidebar-border);display:flex;align-items:center;gap:.75rem;flex-shrink:0}
        .sidebar-avatar{width:36px;height:36px;border-radius:50%;background:linear-gradient(135deg,var(--accent),#F59E0B);display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:.85rem;flex-shrink:0}
        .sidebar-user-name{font-size:.85rem;font-weight:600;color:rgba(255,255,255,.85)}.sidebar-user-role{font-size:.7rem;color:var(--text-muted)}
        .sidebar-logout{margin-left:auto;width:32px;height:32px;border-radius:8px;border:1px solid var(--sidebar-border);background:transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .25s}.sidebar-logout svg{width:16px;height:16px;color:var(--text-muted)}.sidebar-logout:hover{background:rgba(239,68,68,.15)}.sidebar-logout:hover svg{color:var(--error)}

        /* Mobile */
        .mobile-header{display:none;position:fixed;top:0;left:0;right:0;height:var(--header-height);background:var(--sidebar-bg);z-index:100;padding:0 1rem;align-items:center;justify-content:space-between}
        .mobile-header-brand{display:flex;align-items:center;gap:.625rem}.mobile-header-brand img{width:36px;height:36px;border-radius:10px}.mobile-header-brand strong{font-family:'Playfair Display',serif;font-size:1.1rem;color:var(--accent)}
        .sidebar-toggle{width:44px;height:44px;border-radius:var(--radius-sm);background:var(--sidebar-hover);border:1px solid var(--sidebar-border);cursor:pointer;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:5px}.sidebar-toggle span{display:block;width:20px;height:2px;background:rgba(255,255,255,.7);border-radius:2px}
        .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:150;opacity:0;transition:opacity .3s}.sidebar-overlay.show{display:block;opacity:1}

        /* Main */
        .main-content{margin-left:var(--sidebar-width);min-height:100vh;padding:2rem 2rem 4rem;overflow-x:hidden}
        .page-header{display:flex;align-items:center;justify-content:space-between;gap:1rem;margin-bottom:2rem;flex-wrap:wrap}
        .page-header h1{font-size:1.75rem}.page-header p{font-size:.9rem;color:var(--text-secondary);margin-top:.25rem}
        .btn-admin{display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.5rem;font-family:'Outfit',sans-serif;font-weight:600;font-size:.85rem;border:none;border-radius:var(--radius-sm);cursor:pointer;transition:all .3s var(--ease-out);text-decoration:none}.btn-admin svg{width:16px;height:16px}
        .btn-outline{background:white;color:var(--text-primary);border:1.5px solid var(--card-border)}.btn-outline:hover{border-color:var(--accent);color:var(--accent)}

        /* Settings Card */
        .settings-card{background:var(--card-bg);border-radius:var(--radius-lg);border:1px solid var(--card-border);overflow:hidden;max-width:560px}
        .settings-card-header{padding:1.5rem 2rem;border-bottom:1px solid var(--card-border);display:flex;align-items:center;gap:.75rem}
        .settings-card-icon{width:44px;height:44px;border-radius:var(--radius-sm);background:var(--accent-bg);display:flex;align-items:center;justify-content:center;font-size:1.4rem;flex-shrink:0}
        .settings-card-header h3{font-family:'Outfit',sans-serif;font-size:1.1rem;font-weight:700;color:var(--text-primary)}
        .settings-card-header p{font-size:.8rem;color:var(--text-muted);margin-top:.125rem}
        .settings-card-body{padding:2rem}

        /* Form */
        .form-group{margin-bottom:1.5rem}
        .form-group:last-of-type{margin-bottom:0}
        .form-label{display:block;font-size:.85rem;font-weight:600;color:var(--text-primary);margin-bottom:.5rem}
        .form-label .required{color:var(--error)}
        .input-wrapper{position:relative}
        .form-input{width:100%;padding:.875rem 1rem;font-family:'Outfit',sans-serif;font-size:.95rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);background:var(--page-bg);color:var(--text-primary);transition:all .3s var(--ease-out);padding-right:3rem}
        .form-input:focus{outline:none;border-color:var(--accent);background:white;box-shadow:0 0 0 3px rgba(249,115,22,.1)}
        .form-input::placeholder{color:var(--text-muted)}
        .form-input.error{border-color:var(--error);background:var(--error-bg)}
        .form-input.success{border-color:var(--success)}

        .toggle-password{position:absolute;right:.75rem;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;padding:.25rem;display:flex;align-items:center;justify-content:center;color:var(--text-muted);transition:color .2s}
        .toggle-password:hover{color:var(--text-primary)}
        .toggle-password svg{width:20px;height:20px}

        .error-text{font-size:.8rem;color:var(--error);margin-top:.375rem;display:none;align-items:center;gap:.375rem}
        .error-text.show{display:flex}

        /* Strength Meter */
        .strength-meter{margin-top:.625rem}
        .strength-bar-bg{width:100%;height:5px;background:rgba(0,0,0,.08);border-radius:3px;overflow:hidden}
        .strength-bar{height:100%;width:0;border-radius:3px;transition:all .4s var(--ease-out)}
        .strength-bar.weak{width:33%;background:var(--error)}
        .strength-bar.medium{width:66%;background:var(--warning)}
        .strength-bar.strong{width:100%;background:var(--success)}
        .strength-label{font-size:.75rem;font-weight:600;margin-top:.375rem;transition:color .3s}
        .strength-label.weak{color:var(--error)}
        .strength-label.medium{color:var(--warning)}
        .strength-label.strong{color:var(--success)}

        /* Match indicator */
        .match-indicator{font-size:.8rem;font-weight:600;margin-top:.375rem;display:none;align-items:center;gap:.375rem}
        .match-indicator.show{display:flex}
        .match-indicator.match{color:var(--success)}
        .match-indicator.no-match{color:var(--error)}
        .match-indicator svg{width:16px;height:16px}

        /* Submit */
        .form-actions{padding:1.5rem 2rem;border-top:1px solid var(--card-border);background:var(--page-bg)}
        .btn-save{padding:.875rem 2rem;font-family:'Outfit',sans-serif;font-weight:700;font-size:.9rem;border:none;border-radius:var(--radius-sm);cursor:pointer;transition:all .3s var(--ease-out);background:var(--accent);color:white;display:inline-flex;align-items:center;gap:.5rem}
        .btn-save:hover:not(:disabled){background:var(--accent-light);transform:translateY(-2px);box-shadow:0 8px 24px -4px rgba(249,115,22,.4)}
        .btn-save:disabled{opacity:.5;cursor:not-allowed;transform:none;box-shadow:none}
        .btn-save svg{width:18px;height:18px}
        .btn-save .spinner{width:18px;height:18px;border:2.5px solid rgba(255,255,255,.3);border-top-color:white;border-radius:50%;animation:spin .7s linear infinite}
        @keyframes spin{to{transform:rotate(360deg)}}

        /* Toast */
        .toast{position:fixed;bottom:2rem;right:2rem;padding:1rem 1.5rem;background:var(--text-primary);color:white;border-radius:var(--radius-sm);font-size:.875rem;font-weight:500;display:flex;align-items:center;gap:.5rem;box-shadow:0 16px 48px -12px rgba(0,0,0,.3);z-index:400;transform:translateY(120%);opacity:0;transition:all .4s var(--ease-spring)}.toast.show{transform:translateY(0);opacity:1}

        /* Responsive */
        @media(max-width:1024px){.sidebar{transform:translateX(-100%)}.sidebar.open{transform:translateX(0);box-shadow:8px 0 32px rgba(0,0,0,.3)}.mobile-header{display:flex}.main-content{margin-left:0;padding-top:calc(var(--header-height)+1.5rem)}.sidebar-close{display:flex}}
        @media(max-width:768px){.main-content{padding:calc(var(--header-height)+1rem) 1rem 3rem}.page-header{flex-direction:column;align-items:flex-start}.settings-card-body{padding:1.5rem}.settings-card-header{padding:1.25rem 1.5rem}.form-actions{padding:1.25rem 1.5rem}}
        @media(max-width:480px){html{font-size:15px}.page-header h1{font-size:1.35rem}}
    </style>
</head>
<body>
    <?php include '../includes/admin/sidebar.php'; ?>

    <div class="mobile-header"><div class="mobile-header-brand"><img src="logo.jpg" alt="SAHARA"><div><strong>SAHARA</strong><span style="display:block;font-size:.55rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.1em;font-weight:500">IIT M BS Welfare</span></div></div><button class="sidebar-toggle" id="sidebarToggle"><span></span><span></span><span></span></button></div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="toast" id="toast"><span id="toastText"></span></div>

    <div class="main-content">
        <div class="page-header">
            <div><h1>Settings</h1><p>Manage your admin account preferences</p></div>
            <a href="dashboard.php" class="btn-admin btn-outline"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Dashboard</a>
        </div>

        <!-- Change Password -->
        <div class="settings-card">
            <div class="settings-card-header">
                <div class="settings-card-icon">🔒</div>
                <div>
                    <h3>Change Password</h3>
                    <p>Update your admin account password</p>
                </div>
            </div>
            <form id="passwordForm" class="settings-card-body" novalidate>
                <div class="form-group">
                    <label class="form-label" for="newPassword">New Password <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="password" id="newPassword" class="form-input" placeholder="Enter new password" required>
                        <button type="button" class="toggle-password" data-target="newPassword" aria-label="Toggle visibility">
                            <svg class="eye-open" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="eye-closed" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        </button>
                    </div>
                    <div class="strength-meter" id="strengthMeter" style="display:none">
                        <div class="strength-bar-bg"><div class="strength-bar" id="strengthBar"></div></div>
                        <div class="strength-label" id="strengthLabel"></div>
                    </div>
                    <div class="error-text" id="newPwError">Password must be at least 8 characters</div>
                </div>

                <div class="form-group">
                    <label class="form-label" for="confirmPassword">Confirm New Password <span class="required">*</span></label>
                    <div class="input-wrapper">
                        <input type="password" id="confirmPassword" class="form-input" placeholder="Re-enter new password" required>
                        <button type="button" class="toggle-password" data-target="confirmPassword" aria-label="Toggle visibility">
                            <svg class="eye-open" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            <svg class="eye-closed" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L6.59 6.59m7.532 7.532l3.29 3.29M3 3l18 18"/></svg>
                        </button>
                    </div>
                    <div class="match-indicator" id="matchIndicator">
                        <svg class="match-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                        <svg class="no-match-icon" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24" style="display:none"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        <span id="matchText"></span>
                    </div>
                    <div class="error-text" id="confirmPwError">Passwords do not match</div>
                </div>
            </form>
            <div class="form-actions">
                <button type="button" class="btn-save" id="saveBtn" disabled>
                    <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    <span>Update Password</span>
                </button>
            </div>
        </div>
    </div>

    <script>
        // --- UI HELPERS: Sidebar & Mobile ---
        const sidebar = document.getElementById('sidebar'),
            sidebarToggle = document.getElementById('sidebarToggle'),
            sidebarOverlay = document.getElementById('sidebarOverlay');

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

        if(sidebarToggle) sidebarToggle.addEventListener('click', () => {
            sidebar.classList.contains('open') ? closeSidebar() : openSidebar();
        });
        if(sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

        // --- PASSWORD VISIBILITY TOGGLE ---
        document.querySelectorAll('.toggle-password').forEach(btn => {
            btn.addEventListener('click', () => {
                const input = document.getElementById(btn.dataset.target);
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
                btn.querySelector('.eye-open').style.display = isPassword ? 'none' : 'block';
                btn.querySelector('.eye-closed').style.display = isPassword ? 'block' : 'none';
            });
        });

        // --- FORM LOGIC ---
        const newPw = document.getElementById('newPassword');
        const confirmPw = document.getElementById('confirmPassword');
        const saveBtn = document.getElementById('saveBtn');
        const matchIndicator = document.getElementById('matchIndicator');
        const matchText = document.getElementById('matchText');

        // Validation & Button State
        function validateAndMatch() {
            const val = newPw.value;
            const cf = confirmPw.value;
            
            // Error Visibility Reset
            document.getElementById('newPwError').classList.remove('show');
            document.getElementById('confirmPwError').classList.remove('show');

            // Check Matching UI
            if (cf.length > 0) {
                matchIndicator.classList.add('show');
                if (val === cf) {
                    matchIndicator.className = 'match-indicator show match';
                    matchIndicator.querySelector('.match-icon').style.display = 'block';
                    matchIndicator.querySelector('.no-match-icon').style.display = 'none';
                    matchText.textContent = 'Passwords match';
                } else {
                    matchIndicator.className = 'match-indicator show no-match';
                    matchIndicator.querySelector('.match-icon').style.display = 'none';
                    matchIndicator.querySelector('.no-match-icon').style.display = 'block';
                    matchText.textContent = 'Passwords do not match';
                }
            } else {
                matchIndicator.classList.remove('show');
            }

            // Enable button only if length >= 6 and they match
            saveBtn.disabled = !(val.length >= 6 && val === cf);
        }

        newPw.addEventListener('input', validateAndMatch);
        confirmPw.addEventListener('input', validateAndMatch);

        // --- SUBMIT ACTION ---
        saveBtn.addEventListener('click', async () => {
            // Final sanity check
            if (newPw.value.length < 6) {
                showToast('⚠️', 'Minimum 6 characters required');
                return;
            }

            saveBtn.disabled = true;
            saveBtn.innerHTML = '<div class="spinner"></div><span>Updating...</span>';

            const formData = new FormData();
            formData.append('action', 'change_password');
            formData.append('password', newPw.value);

            try {
                // Using the relative path to your admin actions folder
                const response = await fetch('../actions/admin/settings_action.php', {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                if (result.status === 'success') {
                    showToast('✅', 'Password updated successfully');
                    // Reset Form
                    newPw.value = '';
                    confirmPw.value = '';
                    matchIndicator.classList.remove('show');
                    saveBtn.disabled = true;
                } else {
                    showToast('❌', result.message || 'Update failed');
                    saveBtn.disabled = false;
                }
            } catch (error) {
                console.error("Error:", error);
                showToast('❌ Server error. Try again.');
                saveBtn.disabled = false;
            } finally {
                saveBtn.innerHTML = '<svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 13l4 4L19 7"/></svg><span>Update Password</span>';
            }
        });

        // Toast Function
        function showToast(icon, text) {
            const toast = document.getElementById('toast');
            const toastText = document.getElementById('toastText');
            toastText.innerHTML = `${icon} ${text}`;
            toast.classList.add('show');
            setTimeout(() => toast.classList.remove('show'), 3000);
        }
    </script>
</body>
</html>