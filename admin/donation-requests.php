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

$current_page = "donation-requests.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Requests – Admin – SAHARA</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=Playfair+Display:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --sidebar-bg:#0F172A;--sidebar-hover:#1E293B;--sidebar-active:rgba(249,115,22,0.12);--sidebar-border:rgba(255,255,255,0.06);
            --page-bg:#F1F5F9;--card-bg:#FFFFFF;--card-border:#E2E8F0;
            --text-primary:#0F172A;--text-secondary:#475569;--text-muted:#94A3B8;
            --accent:#F97316;--accent-light:#FB923C;--accent-bg:#FFF7ED;--accent-border:#FFEDD5;
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
        .sidebar-close{display:none;position:absolute;top:1.25rem;right:1rem;width:36px;height:36px;border-radius:50%;background:rgba(255,255,255,.08);border:1px solid rgba(255,255,255,.1);cursor:pointer;align-items:center;justify-content:center;transition:all .25s var(--ease-out)}
        .sidebar-close svg{width:18px;height:18px;color:rgba(255,255,255,.6)}.sidebar-close:hover{background:rgba(239,68,68,.2)}.sidebar-close:hover svg{color:var(--error)}
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
        .sidebar-logout{margin-left:auto;width:32px;height:32px;border-radius:8px;border:1px solid var(--sidebar-border);background:transparent;display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all .25s var(--ease-out)}.sidebar-logout svg{width:16px;height:16px;color:var(--text-muted)}.sidebar-logout:hover{background:rgba(239,68,68,.15)}.sidebar-logout:hover svg{color:var(--error)}

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

        /* Summary */
        .summary-pills{display:flex;gap:.75rem;flex-wrap:wrap;margin-bottom:1.5rem}
        .summary-pill{display:flex;align-items:center;gap:.5rem;padding:.625rem 1rem;background:var(--card-bg);border-radius:var(--radius-sm);border:1px solid var(--card-border);transition:all .3s var(--ease-out);cursor:pointer}
        .summary-pill:hover{border-color:var(--accent);transform:translateY(-2px);box-shadow:0 4px 12px rgba(0,0,0,.06)}.summary-pill.active{border-color:var(--accent);background:var(--accent-bg)}
        .summary-pill-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0}.summary-pill-value{font-weight:700;font-size:1rem;color:var(--text-primary)}.summary-pill-label{font-size:.8rem;color:var(--text-muted)}

        /* Filter */
        .filter-bar{display:flex;align-items:center;gap:.75rem;flex-wrap:wrap;background:var(--card-bg);border-radius:var(--radius-lg);padding:1rem 1.25rem;border:1px solid var(--card-border);margin-bottom:1.5rem}
        .filter-search{flex:1;min-width:200px;position:relative}.filter-search input{width:100%;padding:.7rem .75rem .7rem 2.5rem;font-family:'Outfit',sans-serif;font-size:.875rem;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);background:var(--page-bg);color:var(--text-primary);transition:all .3s}.filter-search input:focus{outline:none;border-color:var(--accent);background:white;box-shadow:0 0 0 3px rgba(249,115,22,.1)}.filter-search input::placeholder{color:var(--text-muted)}.filter-search svg{position:absolute;left:.75rem;top:50%;transform:translateY(-50%);width:18px;height:18px;color:var(--text-muted)}
        .filter-select{position:relative}.filter-select select{appearance:none;padding:.7rem 2.25rem .7rem 1rem;font-family:'Outfit',sans-serif;font-size:.85rem;font-weight:500;border:1.5px solid var(--card-border);border-radius:var(--radius-sm);background:var(--page-bg);color:var(--text-primary);cursor:pointer}.filter-select select:focus{outline:none;border-color:var(--accent)}.filter-select::after{content:'';position:absolute;right:.875rem;top:50%;transform:translateY(-50%);border-left:4px solid transparent;border-right:4px solid transparent;border-top:5px solid var(--text-muted);pointer-events:none}
        .filter-count{font-size:.85rem;color:var(--text-muted);font-weight:500;white-space:nowrap;margin-left:auto}.filter-count strong{color:var(--text-primary)}

        /* Table */
        .table-card{background:var(--card-bg);border-radius:var(--radius-lg);border:1px solid var(--card-border);overflow:hidden}
        .table-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch}.table-wrap::-webkit-scrollbar{height:6px}.table-wrap::-webkit-scrollbar-track{background:var(--page-bg)}.table-wrap::-webkit-scrollbar-thumb{background:var(--card-border);border-radius:3px}
        .admin-table{width:100%;min-width:1000px;border-collapse:collapse}
        .admin-table th{padding:.875rem 1.25rem;text-align:left;font-size:.68rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);background:var(--page-bg);border-bottom:1px solid var(--card-border);white-space:nowrap}
        .admin-table td{padding:.875rem 1.25rem;border-bottom:1px solid rgba(0,0,0,.04);font-size:.85rem;vertical-align:middle;white-space:nowrap}
        .admin-table td:nth-child(7){white-space:normal}
        .admin-table tbody tr{transition:background .2s}.admin-table tbody tr:hover{background:rgba(249,115,22,.03)}.admin-table tbody tr:last-child td{border-bottom:none}
        .sno{font-weight:600;color:var(--text-muted);font-size:.8rem}
        .table-donor-name{font-weight:600;color:var(--text-primary)}
        .table-phone{font-size:.8rem;color:var(--text-secondary)}
        .table-email{font-size:.8rem;color:var(--text-secondary);max-width:180px;overflow:hidden;text-overflow:ellipsis}
        .table-campaign{font-weight:500;color:var(--text-primary);max-width:200px;overflow:hidden;text-overflow:ellipsis}
        .table-campaign-id{font-size:.68rem;color:var(--text-muted)}
        .table-amount{font-weight:700;color:var(--accent);font-size:.95rem}
        .table-date{font-size:.8rem;color:var(--text-muted)}

        /* Status Dropdown */
        .status-select{appearance:none;padding:.35rem 1.75rem .35rem .625rem;border-radius:100px;font-family:'Outfit',sans-serif;font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.03em;cursor:pointer;border:1.5px solid transparent;transition:all .25s;background-repeat:no-repeat;background-position:right .5rem center;background-size:10px;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2364748B'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E")}
        .status-select:focus{outline:none}
        .status-select.pending{background-color:var(--warning-bg);color:#92400E;border-color:#FDE68A}
        .status-select.contacted{background-color:var(--info-bg);color:#1E40AF;border-color:#93C5FD}
        .status-select.confirmed{background-color:var(--success-bg);color:#065F46;border-color:#6EE7B7}
        .status-select.cancelled{background-color:var(--error-bg);color:#991B1B;border-color:#FCA5A5}

        /* Trash */
        .btn-trash{width:34px;height:34px;border-radius:8px;border:1.5px solid var(--card-border);background:white;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .25s var(--ease-out)}.btn-trash svg{width:16px;height:16px;color:var(--text-muted)}.btn-trash:hover{border-color:var(--error);background:var(--error-bg)}.btn-trash:hover svg{color:var(--error)}

        /* Empty */
        .empty-state{text-align:center;padding:4rem 2rem}.empty-state-icon{font-size:3rem;margin-bottom:1rem}.empty-state h3{font-family:'Outfit',sans-serif;font-size:1.15rem;margin-bottom:.5rem}.empty-state p{font-size:.9rem;color:var(--text-muted)}

        /* Pagination */
        .pagination{display:flex;justify-content:center;align-items:center;gap:.375rem;padding:1.25rem;border-top:1px solid var(--card-border)}
        .page-btn{min-width:36px;height:36px;border-radius:8px;border:1.5px solid var(--card-border);background:white;font-family:'Outfit',sans-serif;font-weight:600;font-size:.8rem;color:var(--text-secondary);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .25s var(--ease-out);padding:0 .5rem}
        .page-btn:hover{border-color:var(--accent);color:var(--accent)}.page-btn.active{background:var(--accent);border-color:var(--accent);color:white}.page-btn:disabled{opacity:.4;cursor:not-allowed}.page-btn svg{width:16px;height:16px}

        /* Modal */
        .modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.5);backdrop-filter:blur(4px);display:flex;align-items:center;justify-content:center;z-index:300;opacity:0;visibility:hidden;transition:all .3s var(--ease-out);padding:1rem}
        .modal-overlay.show{opacity:1;visibility:visible}
        .modal-box{background:white;border-radius:var(--radius-lg);padding:2rem;max-width:380px;width:100%;text-align:center;transform:scale(.9);transition:transform .3s var(--ease-spring)}
        .modal-overlay.show .modal-box{transform:scale(1)}
        .modal-icon{font-size:2.5rem;margin-bottom:1rem}.modal-box h3{font-family:'Outfit',sans-serif;font-size:1.1rem;font-weight:700;margin-bottom:.5rem}.modal-box p{font-size:.9rem;color:var(--text-secondary);margin-bottom:1.5rem}.modal-box p strong{color:var(--text-primary)}
        .modal-actions{display:flex;gap:.75rem;justify-content:center}
        .modal-btn{padding:.7rem 1.5rem;border-radius:var(--radius-sm);font-family:'Outfit',sans-serif;font-weight:600;font-size:.85rem;cursor:pointer;transition:all .25s;border:none}
        .modal-btn.cancel{background:var(--page-bg);color:var(--text-secondary);border:1.5px solid var(--card-border)}.modal-btn.cancel:hover{border-color:var(--text-muted)}
        .modal-btn.confirm{background:var(--accent);color:white}.modal-btn.confirm:hover{background:var(--accent-light)}
        .modal-btn.danger{background:var(--error);color:white}.modal-btn.danger:hover{background:#DC2626}

        /* Toast */
        .toast{position:fixed;bottom:2rem;right:2rem;padding:1rem 1.5rem;background:var(--text-primary);color:white;border-radius:var(--radius-sm);font-size:.875rem;font-weight:500;display:flex;align-items:center;gap:.5rem;box-shadow:0 16px 48px -12px rgba(0,0,0,.3);z-index:400;transform:translateY(120%);opacity:0;transition:all .4s var(--ease-spring)}.toast.show{transform:translateY(0);opacity:1}

        /* Responsive */
        @media(max-width:1024px){.sidebar{transform:translateX(-100%)}.sidebar.open{transform:translateX(0);box-shadow:8px 0 32px rgba(0,0,0,.3)}.mobile-header{display:flex}.main-content{margin-left:0;padding-top:calc(var(--header-height)+1.5rem)}.sidebar-close{display:flex}}
        @media(max-width:768px){.main-content{padding:calc(var(--header-height)+1rem) 1rem 3rem}.page-header{flex-direction:column;align-items:flex-start}.filter-bar{flex-direction:column}.filter-search{min-width:100%}.filter-count{margin-left:0}.summary-pills{gap:.5rem}.summary-pill{padding:.5rem .75rem}.admin-table th,.admin-table td{padding:.75rem .875rem}}
        @media(max-width:480px){html{font-size:15px}.page-header h1{font-size:1.35rem}}
    </style>
</head>
<body>
    <?php include "../includes/admin/sidebar.php"; ?>

    <div class="mobile-header"><div class="mobile-header-brand"><img src="logo.jpg" alt="SAHARA"><div><strong>SAHARA</strong><span style="display:block;font-size:.55rem;color:var(--text-muted);text-transform:uppercase;letter-spacing:.1em;font-weight:500">IIT M BS Welfare</span></div></div><button class="sidebar-toggle" id="sidebarToggle"><span></span><span></span><span></span></button></div>
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="modal-overlay" id="confirmModal"><div class="modal-box"><div class="modal-icon" id="modalIcon">⚠️</div><h3 id="modalTitle">Confirm</h3><p id="modalMessage">Are you sure?</p><div class="modal-actions"><button class="modal-btn cancel" onclick="closeModal()">Cancel</button><button class="modal-btn confirm" id="modalConfirmBtn" onclick="confirmAction()">Confirm</button></div></div></div>
    <div class="toast" id="toast"><span id="toastText"></span></div>

    <div class="main-content">
        <div class="page-header"><div><h1>Donation Requests</h1><p>Track and manage all donation submissions</p></div><a href="admin-dashboard.html" class="btn-admin btn-outline"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>Back to Dashboard</a></div>

        <div class="summary-pills">
            <div class="summary-pill active" data-filter="all" onclick="filterByStatus('all')"><span class="summary-pill-dot" style="background:var(--accent)"></span><span class="summary-pill-value" id="countAll">0</span><span class="summary-pill-label">All</span></div>
            <div class="summary-pill" data-filter="pending" onclick="filterByStatus('pending')"><span class="summary-pill-dot" style="background:#92400E"></span><span class="summary-pill-value" id="countPending">0</span><span class="summary-pill-label">Pending</span></div>
            <div class="summary-pill" data-filter="contacted" onclick="filterByStatus('contacted')"><span class="summary-pill-dot" style="background:#1E40AF"></span><span class="summary-pill-value" id="countContacted">0</span><span class="summary-pill-label">Contacted</span></div>
            <div class="summary-pill" data-filter="confirmed" onclick="filterByStatus('confirmed')"><span class="summary-pill-dot" style="background:#065F46"></span><span class="summary-pill-value" id="countConfirmed">0</span><span class="summary-pill-label">Confirmed</span></div>
            <div class="summary-pill" data-filter="cancelled" onclick="filterByStatus('cancelled')"><span class="summary-pill-dot" style="background:#991B1B"></span><span class="summary-pill-value" id="countCancelled">0</span><span class="summary-pill-label">Cancelled</span></div>
        </div>

        <div class="filter-bar">
            <div class="filter-search"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg><input type="text" id="searchInput" placeholder="Search by donor, campaign, or ID..."></div>
            <div class="filter-select"><select id="sortFilter"><option value="newest">Newest First</option><option value="oldest">Oldest First</option><option value="amount-high">Amount: High → Low</option><option value="amount-low">Amount: Low → High</option></select></div>
            <div class="filter-count"><strong id="resultCount">0</strong> results</div>
        </div>

        <div class="table-card">
            <div class="table-wrap">
                <table class="admin-table">
                    <thead><tr><th>S.No.</th><th>Date</th><th>Donor</th><th>Phone</th><th>Email</th><th>Campaign</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
                    <tbody id="tableBody"></tbody>
                </table>
            </div>
            <div class="empty-state" id="emptyState" style="display:none"><div class="empty-state-icon">🔍</div><h3>No donations found</h3><p>Try adjusting your search or filters</p></div>
            <div class="pagination" id="pagination"></div>
        </div>
    </div>

    <script>
        const sidebar=document.getElementById('sidebar'),sidebarToggle=document.getElementById('sidebarToggle'),sidebarClose=document.getElementById('sidebarClose'),sidebarOverlay=document.getElementById('sidebarOverlay');
        function closeSidebar(){sidebar.classList.remove('open');sidebarOverlay.classList.remove('show');document.body.style.overflow=''}
        function openSidebar(){sidebar.classList.add('open');sidebarOverlay.classList.add('show');document.body.style.overflow='hidden'}
        sidebarToggle.addEventListener('click',()=>{sidebar.classList.contains('open')?closeSidebar():openSidebar()});
        sidebarClose.addEventListener('click',closeSidebar);sidebarOverlay.addEventListener('click',closeSidebar);

        const donations=[
            {id:"DON-001",donor:"Priya Mehta",phone:"+91 98765 12345",email:"priya@example.com",campaign:"Medical Emergency – Rahul's Surgery",campaignId:"SAH-2026-015",amount:10000,status:"pending",date:"2026-03-29"},
            {id:"DON-002",donor:"Anonymous",phone:"—",email:"—",campaign:"Laptop Fund for Merit Students",campaignId:"SAH-2026-014",amount:25000,status:"pending",date:"2026-03-29"},
            {id:"DON-003",donor:"Rahul Verma",phone:"+91 87654 32109",email:"rahul.v@example.com",campaign:"Medical Emergency – Rahul's Surgery",campaignId:"SAH-2026-015",amount:5000,status:"pending",date:"2026-03-28"},
            {id:"DON-004",donor:"Amit Singh",phone:"+91 99887 76655",email:"amit.s@example.com",campaign:"Women Safety Workshop",campaignId:"SAH-2026-013",amount:2000,status:"pending",date:"2026-03-28"},
            {id:"DON-005",donor:"Sneha Gupta",phone:"+91 77665 54433",email:"sneha@example.com",campaign:"Laptop Fund for Merit Students",campaignId:"SAH-2026-014",amount:1000,status:"pending",date:"2026-03-27"},
            {id:"DON-006",donor:"Vikram Patel",phone:"+91 88776 65544",email:"vikram@example.com",campaign:"Medical Emergency – Rahul's Surgery",campaignId:"SAH-2026-015",amount:15000,status:"contacted",date:"2026-03-26"},
            {id:"DON-007",donor:"Neha Sharma",phone:"+91 99112 23344",email:"neha.s@example.com",campaign:"Women Safety Workshop",campaignId:"SAH-2026-013",amount:3000,status:"contacted",date:"2026-03-25"},
            {id:"DON-008",donor:"Karthik R",phone:"+91 88990 01122",email:"karthik@example.com",campaign:"Laptop Fund for Merit Students",campaignId:"SAH-2026-014",amount:7500,status:"contacted",date:"2026-03-24"},
            {id:"DON-009",donor:"Tabish Shaikh",phone:"+91 98765 43210",email:"tabish@study.iitm.ac.in",campaign:"Medical Emergency – Rahul's Surgery",campaignId:"SAH-2026-015",amount:2000,status:"confirmed",date:"2026-03-22"},
            {id:"DON-010",donor:"Divya S",phone:"+91 77889 90011",email:"divya@example.com",campaign:"Laptop Fund for Merit Students",campaignId:"SAH-2026-014",amount:5000,status:"confirmed",date:"2026-03-20"},
            {id:"DON-011",donor:"Manish K",phone:"+91 66778 89900",email:"manish@example.com",campaign:"Women Safety Workshop",campaignId:"SAH-2026-013",amount:1500,status:"confirmed",date:"2026-03-18"},
            {id:"DON-012",donor:"Riya M",phone:"+91 55667 78899",email:"riya@example.com",campaign:"Medical Emergency – Rahul's Surgery",campaignId:"SAH-2026-015",amount:2000,status:"confirmed",date:"2026-03-15"},
            {id:"DON-013",donor:"Anonymous",phone:"—",email:"—",campaign:"Laptop Fund for Merit Students",campaignId:"SAH-2026-014",amount:50000,status:"confirmed",date:"2026-03-12"},
            {id:"DON-014",donor:"Suresh P",phone:"+91 44556 67788",email:"suresh@example.com",campaign:"Medical Emergency – Rahul's Surgery",campaignId:"SAH-2026-015",amount:1000,status:"cancelled",date:"2026-03-10"},
            {id:"DON-015",donor:"Anjali D",phone:"+91 33445 56677",email:"anjali@example.com",campaign:"Women Safety Workshop",campaignId:"SAH-2026-013",amount:500,status:"cancelled",date:"2026-03-08"}
        ];

        let currentStatus='all',currentSort='newest',searchQuery='',currentPage=1;
        const perPage=8;
        const statusLabels={pending:'Pending',contacted:'Contacted',confirmed:'Confirmed',cancelled:'Cancelled'};
        const statusOptions=['pending','contacted','confirmed','cancelled'];

        function getFiltered(){
            let f=[...donations];
            if(currentStatus!=='all')f=f.filter(d=>d.status===currentStatus);
            if(searchQuery){const q=searchQuery.toLowerCase();f=f.filter(d=>d.donor.toLowerCase().includes(q)||d.campaign.toLowerCase().includes(q)||d.campaignId.toLowerCase().includes(q)||d.id.toLowerCase().includes(q));}
            switch(currentSort){case 'newest':f.sort((a,b)=>new Date(b.date)-new Date(a.date));break;case 'oldest':f.sort((a,b)=>new Date(a.date)-new Date(b.date));break;case 'amount-high':f.sort((a,b)=>b.amount-a.amount);break;case 'amount-low':f.sort((a,b)=>a.amount-b.amount);break;}
            return f;
        }

        function render(){
            const filtered=getFiltered();const totalPages=Math.ceil(filtered.length/perPage);
            if(currentPage>totalPages)currentPage=Math.max(1,totalPages);
            const start=(currentPage-1)*perPage;const page=filtered.slice(start,start+perPage);
            const tbody=document.getElementById('tableBody'),empty=document.getElementById('emptyState');
            document.getElementById('resultCount').textContent=filtered.length;
            if(filtered.length===0){tbody.innerHTML='';empty.style.display='block';document.getElementById('pagination').innerHTML='';return;}
            empty.style.display='none';
            tbody.innerHTML=page.map((d,i)=>{
                const sno=start+i+1;
                const opts=statusOptions.map(s=>`<option value="${s}"${d.status===s?' selected':''}>${statusLabels[s]}</option>`).join('');
                return`<tr>
                    <td class="sno">${sno}</td>
                    <td class="table-date">${formatDate(d.date)}</td>
                    <td class="table-donor-name">${d.donor}</td>
                    <td class="table-phone">${d.phone}</td>
                    <td class="table-email">${d.email}</td>
                    <td><div class="table-campaign">${d.campaign}</div><div class="table-campaign-id">${d.campaignId}</div></td>
                    <td class="table-amount">₹${d.amount.toLocaleString('en-IN')}</td>
                    <td><select class="status-select ${d.status}" onchange="onStatusChange('${d.id}',this)" data-prev="${d.status}">${opts}</select></td>
                    <td><button class="btn-trash" title="Delete" onclick="onDelete('${d.id}')"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg></button></td>
                </tr>`;
            }).join('');
            renderPagination(totalPages);
        }

        function renderPagination(tp){
            if(tp<=1){document.getElementById('pagination').innerHTML='';return;}
            let h=`<button class="page-btn"${currentPage===1?' disabled':''} onclick="goPage(${currentPage-1})"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/></svg></button>`;
            for(let i=1;i<=tp;i++){if(i===1||i===tp||(i>=currentPage-1&&i<=currentPage+1))h+=`<button class="page-btn${i===currentPage?' active':''}" onclick="goPage(${i})">${i}</button>`;else if(i===currentPage-2||i===currentPage+2)h+=`<button class="page-btn" disabled>…</button>`;}
            h+=`<button class="page-btn"${currentPage===tp?' disabled':''} onclick="goPage(${currentPage+1})"><svg fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/></svg></button>`;
            document.getElementById('pagination').innerHTML=h;
        }

        function goPage(p){currentPage=p;render();document.querySelector('.table-card').scrollIntoView({behavior:'smooth',block:'start'});}
        function updateCounts(){document.getElementById('countAll').textContent=donations.length;document.getElementById('countPending').textContent=donations.filter(d=>d.status==='pending').length;document.getElementById('countContacted').textContent=donations.filter(d=>d.status==='contacted').length;document.getElementById('countConfirmed').textContent=donations.filter(d=>d.status==='confirmed').length;document.getElementById('countCancelled').textContent=donations.filter(d=>d.status==='cancelled').length;}
        function filterByStatus(s){currentStatus=s;currentPage=1;document.querySelectorAll('.summary-pill').forEach(p=>p.classList.toggle('active',p.dataset.filter===s));render();}
        document.getElementById('searchInput').addEventListener('input',e=>{searchQuery=e.target.value;currentPage=1;render();});
        document.getElementById('sortFilter').addEventListener('change',e=>{currentSort=e.target.value;currentPage=1;render();});

        let pendingAction=null;
        function onStatusChange(id,el){const nw=el.value,prev=el.dataset.prev;if(nw===prev)return;const d=donations.find(x=>x.id===id);pendingAction={type:'status',id,newStatus:nw,prev,el};showModal('⚠️','Change Status?',`Change <strong>${d.donor}</strong>'s status from <strong>${statusLabels[prev]}</strong> to <strong>${statusLabels[nw]}</strong>?`,'confirm');}
        function onDelete(id){const d=donations.find(x=>x.id===id);pendingAction={type:'delete',id};showModal('🗑️','Delete Donation?',`Permanently delete <strong>${d.donor}</strong>'s donation of <strong>₹${d.amount.toLocaleString('en-IN')}</strong>?`,'danger');}
        function showModal(icon,title,msg,cls){document.getElementById('modalIcon').textContent=icon;document.getElementById('modalTitle').textContent=title;document.getElementById('modalMessage').innerHTML=msg;const b=document.getElementById('modalConfirmBtn');b.className='modal-btn '+cls;b.textContent=cls==='danger'?'Delete':'Confirm';document.getElementById('confirmModal').classList.add('show');}
        function closeModal(){document.getElementById('confirmModal').classList.remove('show');if(pendingAction&&pendingAction.type==='status'){pendingAction.el.value=pendingAction.prev;pendingAction.el.className='status-select '+pendingAction.prev;}pendingAction=null;}
        function confirmAction(){if(!pendingAction)return;if(pendingAction.type==='status'){const d=donations.find(x=>x.id===pendingAction.id);d.status=pendingAction.newStatus;pendingAction.el.dataset.prev=pendingAction.newStatus;pendingAction.el.className='status-select '+pendingAction.newStatus;showToast('✅ Status updated to '+statusLabels[pendingAction.newStatus]);}else if(pendingAction.type==='delete'){const idx=donations.findIndex(x=>x.id===pendingAction.id);if(idx>-1)donations.splice(idx,1);showToast('🗑️ Donation entry deleted');}document.getElementById('confirmModal').classList.remove('show');pendingAction=null;updateCounts();render();}
        function showToast(t){const el=document.getElementById('toast');document.getElementById('toastText').textContent=t;el.classList.add('show');setTimeout(()=>el.classList.remove('show'),3000);}
        function formatDate(d){return new Date(d).toLocaleDateString('en-IN',{day:'numeric',month:'short',year:'numeric'});}
        updateCounts();render();
    </script>
</body>
</html>