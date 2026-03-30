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


$current_page = "dashboard.php";

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
            --sidebar-bg:#0F172A;--sidebar-hover:#1E293B;--sidebar-active:rgba(249,115,22,0.12);--sidebar-border:rgba(255,255,255,0.06);
            --page-bg:#F1F5F9;--card-bg:#FFFFFF;--card-border:#E2E8F0;
            --text-primary:#0F172A;--text-secondary:#475569;--text-muted:#94A3B8;
            --accent:#F97316;--accent-light:#FB923C;--accent-glow:#FDBA74;--accent-bg:#FFF7ED;--accent-border:#FFEDD5;
            --success:#10B981;--success-bg:#D1FAE5;--warning:#F59E0B;--warning-bg:#FEF3C7;
            --error:#EF4444;--error-bg:#FEE2E2;--info:#3B82F6;--info-bg:#DBEAFE;
            --sidebar-width:260px;--header-height:72px;
            --radius-sm:10px;--radius-md:14px;--radius-lg:20px;
            --ease-out:cubic-bezier(0.16,1,0.3,1);--ease-spring:cubic-bezier(0.34,1.56,0.64,1);
        }
        *{margin:0;padding:0;box-sizing:border-box}html{scroll-behavior:smooth;font-size:16px}
        body{font-family:'Outfit',sans-serif;background:var(--page-bg);color:var(--text-primary);line-height:1.6;-webkit-font-smoothing:antialiased;overflow-x:hidden}
        h1,h2,h3,h4{font-family:'Playfair Display',serif;font-weight:700;line-height:1.2}

        /* ===== SIDEBAR ===== */
        .sidebar{position:fixed;top:0;left:0;bottom:0;width:var(--sidebar-width);background:var(--sidebar-bg);display:flex;flex-direction:column;z-index:200;transition:transform .4s var(--ease-out);overflow-y:auto;overflow-x:hidden}
        .sidebar-header{padding:1.5rem 1.25rem;border-bottom:1px solid var(--sidebar-border);display:flex;align-items:center;gap:.75rem;flex-shrink:0;position:relative}
        .sidebar-close{display:none;position:absolute;top:1.25rem;right:1rem;width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);cursor:pointer;align-items:center;justify-content:center;transition:all .25s var(--ease-out)}.sidebar-close svg{width:18px;height:18px;color:rgba(255,255,255,.6)}.sidebar-close:hover{background:rgba(239,68,68,.2)}.sidebar-close:hover svg{color:var(--error)}
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
        .header-actions{display:flex;gap:.75rem;align-items:center}
        .btn-admin{display:inline-flex;align-items:center;gap:.5rem;padding:.75rem 1.5rem;font-family:'Outfit',sans-serif;font-weight:600;font-size:.85rem;border:none;border-radius:var(--radius-sm);cursor:pointer;transition:all .3s var(--ease-out);text-decoration:none}.btn-admin svg{width:16px;height:16px}
        .btn-outline{background:white;color:var(--text-primary);border:1.5px solid var(--card-border)}.btn-outline:hover{border-color:var(--accent);color:var(--accent)}
        .date-display{font-size:.85rem;color:var(--text-muted);display:flex;align-items:center;gap:.5rem}.date-display svg{width:16px;height:16px}

        /* Stats */
        .stats-grid{display:grid;grid-template-columns:repeat(5,1fr);gap:1.25rem;margin-bottom:2rem}
        .stat-card{background:var(--card-bg);border-radius:var(--radius-lg);padding:1.5rem;border:1px solid var(--card-border);position:relative;overflow:hidden;transition:all .3s var(--ease-out)}
        .stat-card:hover{transform:translateY(-4px);box-shadow:0 12px 32px -12px rgba(0,0,0,.1)}
        .stat-card-accent{position:absolute;top:0;left:0;right:0;height:3px}
        .stat-card-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:1rem}
        .stat-icon-box{width:48px;height:48px;border-radius:var(--radius-sm);display:flex;align-items:center;justify-content:center;font-size:1.5rem}
        .stat-change{display:inline-flex;align-items:center;gap:.25rem;padding:.25rem .5rem;border-radius:100px;font-size:.7rem;font-weight:700}
        .stat-change.up{background:var(--success-bg);color:#047857}
        .stat-value{font-family:'Playfair Display',serif;font-size:2rem;font-weight:700;margin-bottom:.25rem}
        .stat-label{font-size:.85rem;color:var(--text-muted);font-weight:500}

        /* Content */
        .content-grid{display:grid;grid-template-columns:1fr;gap:1.5rem}
        .table-card{background:var(--card-bg);border-radius:var(--radius-lg);border:1px solid var(--card-border);overflow:hidden}
        .table-card-header{display:flex;align-items:center;justify-content:space-between;gap:1rem;padding:1.25rem 1.5rem;border-bottom:1px solid var(--card-border)}
        .table-card-header h3{font-family:'Outfit',sans-serif;font-size:1rem;font-weight:700;display:flex;align-items:center;gap:.5rem}
        .view-all-link{font-size:.8rem;font-weight:600;color:var(--accent);cursor:pointer;display:flex;align-items:center;gap:.375rem;transition:gap .3s;background:none;border:none;text-decoration:none}.view-all-link:hover{gap:.625rem}.view-all-link svg{width:14px;height:14px}

        /* Table */
        .table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}.table-wrap::-webkit-scrollbar{height:6px}.table-wrap::-webkit-scrollbar-track{background:var(--page-bg)}.table-wrap::-webkit-scrollbar-thumb{background:var(--card-border);border-radius:3px}
        .admin-table{width:100%;border-collapse:collapse}
        .admin-table.fr-table{min-width:950px}
        .admin-table.dn-table{min-width:900px}
        .admin-table th{padding:.75rem 1.25rem;text-align:left;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);background:var(--page-bg);border-bottom:1px solid var(--card-border);white-space:nowrap}
        .admin-table td{padding:.75rem 1.25rem;border-bottom:1px solid rgba(0,0,0,.04);font-size:.85rem;vertical-align:middle;white-space:nowrap}
        .admin-table tbody tr{transition:background .2s}.admin-table tbody tr:hover{background:rgba(249,115,22,.03)}.admin-table tbody tr:last-child td{border-bottom:none}
        .sno{font-weight:600;color:var(--text-muted);font-size:.8rem}
        .table-campaign-title{font-weight:600;color:var(--text-primary);margin-bottom:.125rem;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .table-campaign-id{font-size:.68rem;color:var(--text-muted);font-weight:500}
        .table-organizer{font-size:.85rem;color:var(--text-secondary)}
        .table-category{display:inline-flex;align-items:center;gap:.375rem;font-size:.78rem;color:var(--text-secondary);background:var(--page-bg);padding:.2rem .5rem;border-radius:100px}
        .table-amount{font-weight:700;color:var(--text-primary)}
        .table-date{font-size:.8rem;color:var(--text-muted)}
        .table-donor-name{font-weight:600;color:var(--text-primary)}
        .table-phone{font-size:.8rem;color:var(--text-secondary)}
        .table-email{font-size:.8rem;color:var(--text-secondary);max-width:160px;overflow:hidden;text-overflow:ellipsis}
        .table-campaign{font-weight:500;color:var(--text-primary);max-width:180px;overflow:hidden;text-overflow:ellipsis}
        .table-don-amount{font-weight:700;color:var(--accent);font-size:.95rem}

        /* Timeline */
        .timeline-cell{display:flex;flex-direction:column;gap:.2rem;white-space:normal}
        .timeline-entry{display:flex;align-items:baseline;gap:.375rem;font-size:.68rem;line-height:1.4}
        .timeline-label{font-weight:700;color:var(--text-secondary);min-width:62px;flex-shrink:0}
        .timeline-date{color:var(--text-muted)}

        /* Status Dropdown */
        .status-select{appearance:none;padding:.3rem 1.6rem .3rem .5rem;border-radius:100px;font-family:'Outfit',sans-serif;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.03em;cursor:pointer;border:1.5px solid transparent;transition:all .25s;background-repeat:no-repeat;background-position:right .4rem center;background-size:9px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748B'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E")}
        .status-select:focus{outline:none}
        .status-select.pending{background-color:var(--warning-bg);color:#92400E;border-color:#FDE68A}
        .status-select.approved{background-color:var(--success-bg);color:#065F46;border-color:#6EE7B7}
        .status-select.completed{background-color:#F1F5F9;color:var(--text-muted);border-color:var(--card-border)}
        .status-select.rejected{background-color:var(--error-bg);color:#991B1B;border-color:#FCA5A5}
        .status-select.paused{background-color:#F3E8FF;color:#6B21A8;border-color:#D8B4FE}
        .status-select.contacted{background-color:var(--info-bg);color:#1E40AF;border-color:#93C5FD}
        .status-select.confirmed{background-color:var(--success-bg);color:#065F46;border-color:#6EE7B7}
        .status-select.cancelled{background-color:var(--error-bg);color:#991B1B;border-color:#FCA5A5}

        /* Action */
        .action-group{display:flex;align-items:center;gap:.4rem}
        .btn-view{padding:.35rem .75rem;font-size:.72rem;font-weight:600;font-family:'Outfit',sans-serif;border-radius:8px;cursor:pointer;transition:all .25s var(--ease-out);border:1.5px solid var(--card-border);background:white;color:var(--text-primary);display:inline-flex;align-items:center;gap:.3rem;text-decoration:none}.btn-view svg{width:13px;height:13px}.btn-view:hover{border-color:var(--accent);color:var(--accent);background:var(--accent-bg)}
        .btn-trash{width:30px;height:30px;border-radius:8px;border:1.5px solid var(--card-border);background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .25s var(--ease-out)}.btn-trash svg{width:14px;height:14px;color:var(--text-muted)}.btn-trash:hover{border-color:var(--error);background:var(--error-bg)}.btn-trash:hover svg{color:var(--error)}

        /* Activity */
        .activity-section{margin-top:1.5rem}
        .activity-card{background:var(--card-bg);border-radius:var(--radius-lg);border:1px solid var(--card-border);overflow:hidden}
        .activity-row{display:flex;align-items:center;gap:1rem;padding:1rem 1.5rem;border-bottom:1px solid rgba(0,0,0,.04);transition:background .2s}.activity-row:last-child{border-bottom:none}.activity-row:hover{background:rgba(0,0,0,.01)}
        .activity-icon-box{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;flex-shrink:0}
        .activity-icon-box.new-request{background:var(--warning-bg)}.activity-icon-box.approved{background:var(--success-bg)}.activity-icon-box.donation{background:var(--accent-bg)}.activity-icon-box.rejected{background:var(--error-bg)}
        .activity-text{flex:1;font-size:.85rem;color:var(--text-secondary)}.activity-text strong{color:var(--text-primary)}
        .activity-time{font-size:.75rem;color:var(--text-muted);white-space:nowrap;flex-shrink:0}

        /* Modal */
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:300;opacity:0;visibility:hidden;transition:all .3s var(--ease-out);padding:1rem}.modal-overlay.show{opacity:1;visibility:visible}
        .modal-box{background:white;border-radius:var(--radius-lg);padding:2rem;max-width:380px;width:100%;text-align:center;transform:scale(.9);transition:transform .3s var(--ease-spring)}.modal-overlay.show .modal-box{transform:scale(1)}
        .modal-icon{font-size:2.5rem;margin-bottom:1rem}.modal-box h3{font-family:'Outfit',sans-serif;font-size:1.1rem;font-weight:700;margin-bottom:.5rem}.modal-box p{font-size:.9rem;color:var(--text-secondary);margin-bottom:1.5rem}.modal-box p strong{color:var(--text-primary)}
        .modal-actions{display:flex;gap:.75rem;justify-content:center}
        .modal-btn{padding:.7rem 1.5rem;border-radius:var(--radius-sm);font-family:'Outfit',sans-serif;font-weight:600;font-size:.85rem;cursor:pointer;transition:all .25s;border:none}
        .modal-btn.cancel{background:var(--page-bg);color:var(--text-secondary);border:1.5px solid var(--card-border)}.modal-btn.cancel:hover{border-color:var(--text-muted)}
        .modal-btn.confirm{background:var(--accent);color:white}.modal-btn.confirm:hover{background:var(--accent-light)}
        .modal-btn.danger{background:var(--error);color:white}.modal-btn.danger:hover{background:#DC2626}

        /* Toast */
        .toast{position:fixed;bottom:2rem;right:2rem;padding:1rem 1.5rem;background:var(--text-primary);color:white;border-radius:var(--radius-sm);font-size:.875rem;font-weight:500;display:flex;align-items:center;gap:.5rem;box-shadow:0 16px 48px -12px rgba(0,0,0,.3);z-index:400;transform:translateY(120%);opacity:0;transition:all .4s var(--ease-spring)}.toast.show{transform:translateY(0);opacity:1}

        /* Responsive */
        @media(max-width:1200px){.stats-grid{grid-template-columns:repeat(3,1fr)}}
        @media(max-width:1024px){.sidebar{transform:translateX(-100%)}.sidebar.open{transform:translateX(0);box-shadow:8px 0 32px rgba(0,0,0,.3)}.mobile-header{display:flex}.main-content{margin-left:0;padding-top:calc(var(--header-height)+1.5rem)}.sidebar-close{display:flex}}
        @media(max-width:768px){.main-content{padding:calc(var(--header-height)+1rem) 1rem 3rem}.page-header{flex-direction:column;align-items:flex-start}.stats-grid{gap:.75rem}.stat-card{padding:1.25rem}.stat-value{font-size:1.5rem}.admin-table th,.admin-table td{padding:.65rem .75rem}.table-card-header{padding:1rem}}
        @media(max-width:480px){html{font-size:15px}.stats-grid{grid-template-columns:1fr 1fr}.stat-icon-box{width:40px;height:40px;font-size:1.25rem}.stat-value{font-size:1.35rem}.page-header h1{font-size:1.35rem}.header-actions{width:100%}}
    </style>
</head>
<body>
    
    <!-- Sidebar -->
    <?php include '../includes/admin/sidebar.php'; ?>
    <div class="mobile-header"><div class="mobile-header-brand"><img src="logo.jpg" alt="SAHARA"><div><strong>SAHARA</strong><span style="display:block;font-size:.55rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.1em;font-weight:500">IIT M BS Welfare</span></div></div><button class="sidebar-toggle" id="sidebarToggle"><span></span><span></span><span></span></button></div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="modal-overlay" id="confirmModal"><div class="modal-box"><div class="modal-icon" id="modalIcon">⚠️</div><h3 id="modalTitle">Confirm</h3><p id="modalMessage">Are you sure?</p><div class="modal-actions"><button class="modal-btn cancel" onclick="closeModal()">Cancel</button><button class="modal-btn confirm" id="modalConfirmBtn" onclick="confirmAction()">Confirm</button></div></div></div>
    <div class="toast" id="toast"><span id="toastText"></span></div>

    <div class="main-content">
        <div class="page-header"><div><h1>Dashboard</h1><p>Overview of campaigns, donations, and platform activity</p></div><div class="header-actions"><div class="date-display"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><span id="currentDate"></span></div><a href="index.html" class="btn-admin btn-outline"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>View Site</a></div></div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card"><div class="stat-card-accent" style="background:linear-gradient(90deg,#10B981,#34D399)"></div><div class="stat-card-top"><div class="stat-icon-box" style="background:var(--success-bg)">🟢</div><span class="stat-change up">↑ 8%</span></div><div class="stat-value">8</div><div class="stat-label">Live Campaigns</div></div>
            <div class="stat-card"><div class="stat-card-accent" style="background:linear-gradient(90deg,#F59E0B,#FBBF24)"></div><div class="stat-card-top"><div class="stat-icon-box" style="background:var(--warning-bg)">⏳</div><span class="stat-change up">+3 new</span></div><div class="stat-value">3</div><div class="stat-label">Pending for Review</div></div>
            <div class="stat-card"><div class="stat-card-accent" style="background:linear-gradient(90deg,#EF4444,#F87171)"></div><div class="stat-card-top"><div class="stat-icon-box" style="background:var(--error-bg)">💝</div><span class="stat-change up">+5 new</span></div><div class="stat-value">5</div><div class="stat-label">Donations Pending</div></div>
            <div class="stat-card"><div class="stat-card-accent" style="background:linear-gradient(90deg,#3B82F6,#60A5FA)"></div><div class="stat-card-top"><div class="stat-icon-box" style="background:var(--info-bg)">📊</div><span class="stat-change up">↑ 18%</span></div><div class="stat-value">156</div><div class="stat-label">Total Donations Done</div></div>
            <div class="stat-card"><div class="stat-card-accent" style="background:linear-gradient(90deg,#F97316,#FB923C)"></div><div class="stat-card-top"><div class="stat-icon-box" style="background:var(--accent-bg)">💰</div><span class="stat-change up">↑ 24%</span></div><div class="stat-value">₹18.5L</div><div class="stat-label">Total Raised Amount</div></div>
        </div>

        <div class="content-grid">
            <!-- Recent Fundraise Requests -->
            <div class="table-card">
                <div class="table-card-header"><h3><span>📋</span> Recent Fundraise Requests</h3><a href="admin-fundraise-requests.html" class="view-all-link">View All <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></a></div>
                <div class="table-wrap"><table class="admin-table fr-table"><thead><tr><th>S.No.</th><th>Campaign</th><th>Organizer</th><th>Category</th><th>Goal</th><th>Timeline</th><th>Status</th><th>Action</th></tr></thead><tbody id="frBody"></tbody></table></div>
            </div>

            <!-- Recent Donations -->
            <div class="table-card">
                <div class="table-card-header"><h3><span>💝</span> Recent Donations</h3><a href="admin-donation-requests.html" class="view-all-link">View All <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></a></div>
                <div class="table-wrap"><table class="admin-table dn-table"><thead><tr><th>S.No.</th><th>Date</th><th>Donor</th><th>Phone</th><th>Email</th><th>Campaign</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead><tbody id="dnBody"></tbody></table></div>
            </div>
        </div>

        <!-- Activity -->
        <div class="activity-section"><div class="activity-card"><div class="table-card-header"><h3><span>🔔</span> Recent Activity</h3><a href="#" class="view-all-link">View All <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></a></div><div id="activityTimeline"></div></div></div>
    </div>

    <script>
    // --- Global State ---
    let frData = [];
    let dnData = [];

    // --- Sidebar & UI Helpers ---
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');

    function openSidebar() { 
        sidebar.classList.add('open'); 
        sidebarOverlay.classList.add('show'); 
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() { 
        sidebar.classList.remove('open'); 
        sidebarOverlay.classList.remove('show'); 
        document.body.style.overflow = '';
    }

    if(sidebarToggle) sidebarToggle.addEventListener('click', openSidebar);
    if(sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

    // Set Current Date
    document.getElementById('currentDate').textContent = new Date().toLocaleDateString('en-IN', {
        weekday: 'long', day: 'numeric', month: 'long', year: 'numeric'
    });

    // --- 1. DATA INITIALIZATION ---
    document.addEventListener('DOMContentLoaded', () => {
        loadDashboardData();
    });

    async function loadDashboardData() {
        const formData = new FormData();
        formData.append('action', 'get_data');

        try {
            const res = await fetch('../actions/admin/dashboard_action.php', { method: 'POST', body: formData });
            const data = await res.json();

            if (data.status === 'error') {
                showToast('❌ Session expired. Please login.');
                return;
            }

            // Update Stats Cards
            const statCards = document.querySelectorAll('.stat-value');
            statCards[0].textContent = data.stats.live;
            statCards[1].textContent = data.stats.pending;
            statCards[2].textContent = data.stats.pending; // Reusing pending count for "Donations Pending" if logic matches
            statCards[3].textContent = data.stats.donations;
            
            // Format Currency for Total Raised
            const raised = parseFloat(data.stats.raised);
            statCards[4].textContent = raised >= 100000 
                ? '₹' + (raised / 100000).toFixed(1) + 'L' 
                : '₹' + raised.toLocaleString('en-IN');

            frData = data.campaigns || [];
            dnData = data.donations || [];

            renderFR();
            renderDN();
        } catch (e) {
            console.error("Fetch Error:", e);
            showToast('❌ Error connecting to backend');
        }
    }

    // --- 2. RENDERING LOGIC ---
    const catIcons = { Education: '📚', Medical: '🏥', Health: '💚', Disaster: '🆘', Community: '🤝' };
    const frStatusOpts = ['pending', 'approved', 'completed', 'paused', 'rejected'];

    function renderFR() {
        const body = document.getElementById('frBody');
        if (frData.length === 0) {
            body.innerHTML = '<tr><td colspan="8" style="text-align:center; padding:2rem; color:#94A3B8;">No recent requests found.</td></tr>';
            return;
        }

        body.innerHTML = frData.map((r, i) => {
            const opts = frStatusOpts.map(s => `<option value="${s}" ${r.status === s ? 'selected' : ''}>${s.toUpperCase()}</option>`).join('');
            return `
            <tr>
                <td class="sno">${i + 1}</td>
                <td>
                    <div class="table-campaign-title">${r.title}</div>
                    <div class="table-campaign-id">#${r.id}</div>
                </td>
                <td class="table-organizer">${r.organizer}</td>
                <td><span class="table-category">${catIcons[r.category] || '📋'} ${r.category}</span></td>
                <td class="table-amount">₹${parseFloat(r.amount_needed).toLocaleString('en-IN')}</td>
                <td><div class="table-date">${new Date(r.created_at).toLocaleDateString('en-IN')}</div></td>
                <td>
                    <select class="status-select ${r.status}" onchange="updateStatus(${r.id}, 'campaign', this)">
                        ${opts}
                    </select>
                </td>
                <td>
                    <div class="action-group">
                        <button class="btn-trash" onclick="deleteItem(${r.id}, 'campaign')" title="Delete">
                            <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </div>
                </td>
            </tr>`;
        }).join('');
    }

    function renderDN() {
        const body = document.getElementById('dnBody');
        if (dnData.length === 0) {
            body.innerHTML = '<tr><td colspan="9" style="text-align:center; padding:2rem; color:#94A3B8;">No recent donations found.</td></tr>';
            return;
        }

        body.innerHTML = dnData.map((d, i) => `
            <tr>
                <td class="sno">${i + 1}</td>
                <td class="table-date">${new Date(d.created_at).toLocaleDateString('en-IN')}</td>
                <td class="table-donor-name">${d.donor}</td>
                <td class="table-phone">${d.phone || '—'}</td>
                <td class="table-email">${d.email}</td>
                <td><div class="table-campaign">${d.campaign_title}</div></td>
                <td class="table-don-amount">₹${parseFloat(d.amount).toLocaleString('en-IN')}</td>
                <td><span class="status-badge live"><span class="status-dot"></span>Confirmed</span></td>
                <td>
                    <button class="btn-trash" onclick="deleteItem(${d.id}, 'donation')" title="Remove Entry">
                        <svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </td>
            </tr>`).join('');
    }

    // --- 3. ACTIONS ---

    async function updateStatus(id, type, el) {
        const status = el.value;
        const formData = new FormData();
        formData.append('action', 'update_status');
        formData.append('id', id);
        formData.append('type', type);
        formData.append('status', status);

        el.disabled = true;

        try {
            const res = await fetch('../actions/admin/dashboard_action.php', { method: 'POST', body: formData });
            const data = await res.json();
            
            if (data.status === 'success') {
                el.className = `status-select ${status}`;
                showToast('✅ Status updated successfully');
            } else {
                showToast('❌ Update failed');
            }
        } catch (e) {
            showToast('❌ Connection error');
        } finally {
            el.disabled = false;
        }
    }

    async function deleteItem(id, type) {
        if (!confirm(`Are you sure you want to permanently delete this ${type}?`)) return;

        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('id', id);
        formData.append('type', type);

        try {
            const res = await fetch('../actions/admin/dashboard_action.php', { method: 'POST', body: formData });
            const data = await res.json();
            
            if (data.status === 'success') {
                showToast(`🗑️ ${type.charAt(0).toUpperCase() + type.slice(1)} removed`);
                loadDashboardData(); // Refresh list and stats
            } else {
                showToast('❌ Deletion failed');
            }
        } catch (e) {
            showToast('❌ Connection error');
        }
    }

    // --- 4. UTILITIES ---
    function showToast(message) {
        const toast = document.getElementById('toast');
        const toastText = document.getElementById('toastText');
        toastText.textContent = message;
        toast.classList.add('show');
        setTimeout(() => toast.classList.remove('show'), 3000);
    }
</script>
</body>
</html>