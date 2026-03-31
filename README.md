# SAHARA — Fundraising Platform

**Live Demo**: [https://adityawork.live](https://adityawork.live)
Admin credentials
- Email: `admin@gmail.com`
- Password: `23456789`


**SAHARA** (IIT Madras BS Degree Social Welfare Society) is a web-based fundraising platform that enables students and community members to create, manage, and contribute to fundraising campaigns. The platform features a public-facing website for browsing and donating, a user dashboard for managing personal campaigns and donations, and a full-featured admin panel for reviewing and approving submissions.

---

## Table of Contents

- [Tech Stack](#tech-stack)
- [Features Overview](#features-overview)
- [Pages & Modules](#pages--modules)
- [Database Schema](#database-schema)
- [File Storage Strategy](#file-storage-strategy)
- [Project Structure](#project-structure)
- [Design System](#design-system)
- [Setup & Installation](#setup--installation)
- [Workflow](#workflow)
- [Responsive Breakpoints](#responsive-breakpoints)

---

## Tech Stack

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Icons**: Inline SVGs (no icon libraries)
- **Email**: PHPMailer (Gmail SMTP)

---

## Features Overview

### Public Pages
- Homepage with hero section, active campaigns grid, impact stats, and footer
- Campaign details page with progress bar, donation form, share buttons, and beneficiary info
- Campaign listing page with filters and search
- Fundraise request form with multi-step validation, file uploads, email verification, and review flow

### Auth Pages
- Login page with email and password
- Signup page for new user registration

### User Dashboard
- My Campaigns tab — view submitted campaigns and their status
- My Donations tab — track donation history
- Profile & Settings tab — upload profile photo, edit phone number, change password with strength meter

### Admin Panel
- Dashboard with stat cards, recent fundraise requests table, recent donations table, and activity timeline
- Fundraise Requests page — full table with S.No., timeline (submitted/approved/paused/rejected timestamps), status dropdown with confirmation popup, view + delete actions, search/filter/sort, pagination
- Donation Requests page — full table with S.No., date, donor, phone, email, campaign, amount, status dropdown with confirmation popup, trash action, search/sort, pagination
- Settings page — change password with live strength meter and match validation

---

## Pages & Modules

| File | Description |
|------|-------------|
| `index.php` | Homepage — hero, campaigns grid, impact section, footer |
| `all-campaigns.php` | Campaign listing with filters and search |
| `campaign.php` | Individual campaign page — progress, donate modal, share, beneficiary |
| `fundraise-request.php` | Fundraise request form — email verification gate, multi-section form, file upload with progress, review & submit flow |
| `dashboard.php` | User dashboard — My Campaigns, My Donations, Profile & Settings tabs |
| `login.html` | User login page |
| `signup.html` | User registration page |
| `admin/dashboard.php` | Admin overview — stats, recent fundraise/donation tables, activity timeline |
| `admin/fundraise-requests.php` | Admin fundraise management — S.No., timeline, status dropdown, view/trash, pagination |
| `admin/donation-requests.php` | Admin donation management — S.No., date, donor details, status dropdown, trash, pagination |
| `admin/setting.php` | Admin settings — change password |
| `sql/schema.sql` | MySQL database schema — users, campaigns, donations tables |

---

## Database Schema

Three tables with file storage handled via filesystem folders:

### `users`
| Column | Type | Notes |
|--------|------|-------|
| id | INT AUTO_INCREMENT | Primary key |
| name | VARCHAR(100) | Required |
| email | VARCHAR(150) | Unique, required |
| password | VARCHAR(255) | Hashed, required |
| phone | VARCHAR(20) | Optional |
| address | TEXT | Optional |
| role | ENUM('user','admin') | Default: 'user' |
| is_active | TINYINT(1) | Default: 1 |
| created_at | DATETIME | Auto |
| updated_at | DATETIME | Auto on update |

### `campaigns`
| Column | Type | Notes |
|--------|------|-------|
| id | INT AUTO_INCREMENT | Primary key |
| user_id | INT | FK → users.id (CASCADE) |
| title | VARCHAR(255) | Required |
| description | TEXT | Required |
| amount_needed | DECIMAL(12,2) | Default: 0 |
| category | VARCHAR(80) | Default: 'general' |
| beneficiary_name | VARCHAR(120) | Optional |
| beneficiary_phone | VARCHAR(20) | Optional |
| beneficiary_relation | VARCHAR(80) | Optional |
| beneficiary_city | VARCHAR(80) | Optional |
| urgency | ENUM('low','medium','high') | Default: 'medium' |
| status | ENUM('pending','approved','rejected','paused') | Default: 'pending' |
| admin_note | TEXT | Optional |
| views | INT | Default: 0 |
| created_at | DATETIME | Auto (submitted at) |
| updated_at | DATETIME | Auto on update |
| approved_at | DATETIME | Nullable |
| paused_at | DATETIME | Nullable |
| rejected_at | DATETIME | Nullable |
| delete_flag | TINYINT(1) | Soft delete, default: 0 |

### `donations`
| Column | Type | Notes |
|--------|------|-------|
| id | INT AUTO_INCREMENT | Primary key |
| campaign_id | INT | FK → campaigns.id (CASCADE) |
| user_id | INT | FK → users.id (CASCADE) |
| amount | DECIMAL(12,2) | Default: 0 |
| status | ENUM('pending','contacted','confirmed','cancelled') | Default: 'pending' |
| created_at | DATETIME | Auto |
| updated_at | DATETIME | Auto on update |

---

## File Storage Strategy

No media tables — files stored in filesystem folders:

```
/assets/
├── campaigns/
│   └── media/
│       ├── {campaign_id}/        # All images + video for a campaign
│       │   ├── img1.jpg
│       │   ├── img2.png
│       │   └── video.mp4
│       └── ...
└── logo.jpg                      # SAHARA logo
```

- **Campaign media**: Scan `/assets/campaigns/media/{campaign_id}/` to fetch all files

---

## Project Structure

```
sahara/
├── index.php                          # Homepage
├── all-campaigns.php                  # Campaign listing
├── campaign.php                       # Single campaign page
├── fundraise-request.php              # Fundraise request form
├── dashboard.php                      # User dashboard
├── login.html                         # Login page
├── signup.html                        # Signup page
├── admin/
│   ├── dashboard.php                  # Admin main dashboard
│   ├── fundraise-requests.php         # Admin fundraise management
│   ├── donation-requests.php          # Admin donation management
│   ├── setting.php                    # Admin settings (password)
│   └── logo.jpg                       # Admin logo
├── actions/
│   ├── auth/
│   │   ├── login_action.php           # Login handler
│   │   ├── signup_action.php          # Signup handler
│   │   ├── logout.php                 # Logout handler
│   │   └── update_user_profile.php    # Profile update handler
│   ├── admin/
│   │   ├── dashboard_action.php       # Admin dashboard actions
│   │   └── settings_action.php        # Admin settings actions
│   ├── campaigns/
│   │   ├── fetch_active.php           # Fetch active campaigns
│   │   ├── fetch_all.php              # Fetch all campaigns
│   │   └── request_action.php         # Campaign request handler
│   └── donate/
│       ├── check_donor.php            # Donor verification
│       └── process_donation.php       # Donation processing
├── includes/
│   ├── header.php                     # Public header component
│   ├── footer.php                     # Public footer component
│   ├── user_page_init.php             # User session initialization
│   └── admin/
│       └── sidebar.php                # Admin sidebar component
├── assets/
│   ├── logo.jpg                       # SAHARA logo
│   └── campaigns/
│       └── media/                     # Campaign uploads
├── sql/
│   └── schema.sql                     # Database schema
├── config/                            # Database & app config (gitignored)
├── composer.json                      # PHP dependencies (PHPMailer)
└── composer.lock
```

---

## Design System

### Brand Colors
| Token | Value | Usage |
|-------|-------|-------|
| `--sun` | `#F97316` | Primary accent, CTAs, links |
| `--amber` | `#F59E0B` | Gradients, secondary accent |
| `--cream` | `#FFFBF5` | Page background (public) |
| `--earth` | `#292524` | Primary text |
| `--stone` | `#57534E` | Secondary text |

### Admin Theme
| Token | Value | Usage |
|-------|-------|-------|
| `--sidebar-bg` | `#0F172A` | Sidebar + mobile header |
| `--page-bg` | `#F1F5F9` | Admin page background |
| `--card-bg` | `#FFFFFF` | Cards, tables |
| `--accent` | `#F97316` | Accent (shared with brand) |

### Status Colors
| Status | Background | Text |
|--------|-----------|------|
| Pending | `#FEF3C7` | `#92400E` |
| Approved | `#D1FAE5` | `#065F46` |
| Completed | `#F1F5F9` | `#94A3B8` |
| Paused | `#F3E8FF` | `#6B21A8` |
| Rejected | `#FEE2E2` | `#991B1B` |
| Contacted | `#DBEAFE` | `#1E40AF` |
| Confirmed | `#D1FAE5` | `#065F46` |
| Cancelled | `#FEE2E2` | `#991B1B` |

### Typography
- **Headings**: Playfair Display (500–800 weight)
- **Body**: Outfit (300–800 weight)
- **Fluid sizing**: `clamp()` for responsive text

### Spacing & Radius
- `--radius-sm`: 10–12px (inputs, pills)
- `--radius-md`: 14–20px (cards)
- `--radius-lg`: 20–32px (large cards, modals)
- Sidebar width: 260px
- Mobile header height: 72px

---

## Setup & Installation

### Prerequisites
- XAMPP or similar stack with PHP and MySQL
- PHP 7.4+ with `mysqli` extension
- MySQL 5.7+ or MariaDB 10.3+
- Composer (for PHPMailer)
- Modern browser (Chrome, Firefox, Safari, Edge)

### Steps

1. **Clone/download** the project files to your web server root (e.g. `htdocs/`)

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Create the database**:
   ```sql
   CREATE DATABASE sahara_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   USE sahara_db;
   SOURCE sql/schema.sql;
   ```

4. **Configure database connection** in `config/db.php` (this folder is gitignored)

5. **Create upload directories** (if not present):
   ```bash
   mkdir -p assets/campaigns/media
   ```

6. **Open** `index.php` in your browser to view the site

---

## Workflow

### User Flow
```
Register/Login → Browse Campaigns → Donate (name, phone, email, amount)
                                  → Start Fundraiser (email verify → fill form → review → submit)
              → Dashboard         → View My Campaigns, Donations, Edit Profile
```

### Fundraise Request Flow
```
User submits form → Status: PENDING
                  → Admin reviews in admin panel
                  → Admin changes status: APPROVED / REJECTED / PAUSED
                  → If APPROVED → Campaign goes live on public site
                  → Timeline tracks all status change timestamps
```

### Donation Flow
```
Visitor fills donate form on campaign page → Status: PENDING
                                           → Admin contacts donor → CONTACTED
                                           → Donation confirmed → CONFIRMED
                                           → Or cancelled → CANCELLED
```

### Admin Capabilities
- View dashboard stats (live campaigns, pending reviews, donations, total raised)
- Manage fundraise requests — change status (with confirmation popup), view details, delete entries
- Manage donations — change status, delete entries
- All status changes add timestamps to timeline
- Change admin password from settings

---

## Responsive Breakpoints

| Breakpoint | Target |
|-----------|--------|
| `1200px` | Large desktop — stat grid adjusts |
| `1024px` | Tablet — sidebar collapses, mobile header appears |
| `768px` | Small tablet — single column layouts, stacked filters |
| `480px` | Mobile — base font 15px, compact spacing |

### Mobile Features
- Hamburger menu → sidebar slides in from left
- X close button inside sidebar header
- Dark overlay covers content when sidebar open
- `body overflow: hidden` prevents scroll behind sidebar
- Touch-friendly: 48px minimum tap targets
- Tables scroll horizontally inside cards (not the page)

---


> Built with for social welfare
