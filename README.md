# 🌞 SAHARA — Fundraising Platform

**SAHARA** (IIT Madras BS Degree Social Welfare Society) is a web-based fundraising platform that enables students and community members to create, manage, and contribute to fundraising campaigns. The platform features a public-facing website for browsing and donating, a user dashboard for managing personal campaigns and donations, and a full-featured admin panel for reviewing and approving submissions.

---

## 📋 Table of Contents

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
- [Browser Support](#browser-support)
- [License](#license) 

---

## Tech Stack

- **Frontend**: HTML, CSS, JavaScript
- **Backend**: PHP
- **Database**: MySQL
- **Icons**: Inline SVGs (no icon libraries)
- **Email**: Gmail SMTP

---

## Features Overview

### Public Pages
- Homepage with hero section, active campaigns grid, impact stats, and footer
- Campaign details page with progress bar, donation form, share buttons, and beneficiary info
- Campaign listing page with filters and search
- Fundraise request form with multi-step validation, file uploads, email verification, and review flow

### User Dashboard
- My Campaigns tab — view submitted campaigns and their status
- My Donations tab — track donation history
- Profile & Settings tab — upload profile photo, edit phone number, change password with strength meter

### Admin Panel
- Dashboard with 5 stat cards, recent fundraise requests table, recent donations table, and activity timeline
- Fundraise Requests page — full table with S.No., timeline (submitted/approved/paused/rejected timestamps), status dropdown with confirmation popup, view + delete actions, search/filter/sort, pagination (8 per page)
- Donation Requests page — full table with S.No., date, donor, phone, email, campaign, amount, status dropdown with confirmation popup, trash action, search/sort, pagination (8 per page)
- Settings page — change password with live strength meter and match validation

---

## Pages & Modules

| File | Description |
|------|-------------|
| `index.html` | Homepage — hero, campaigns grid, impact section, footer |
| `campaigns.html` | Campaign listing with filters (referenced as navigation target) |
| `campaign-details.html` | Individual campaign page — progress, donate modal, share, beneficiary |
| `fundraise.html` | Fundraise request form — email verification gate, multi-section form, file upload with progress, review & submit flow |
| `dashboard.html` | User dashboard — My Campaigns, My Donations, Profile & Settings tabs |
| `admin-dashboard.html` | Admin overview — stats, recent fundraise/donation tables with full controls, activity timeline |
| `admin-fundraise-requests.html` | Admin fundraise management — S.No., timeline, status dropdown, view/trash, pagination |
| `admin-donation-requests.html` | Admin donation management — S.No., date, donor details in separate columns, status dropdown, trash, pagination |
| `admin-settings.html` | Admin settings — change password |
| `sahara_schema.sql` | MySQL database schema — users, campaigns, donations tables |

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
| urgency | ENUM('low','medium','high','critical') | Default: 'medium' |
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
| campaign_id | INT | References campaigns.id |
| user_id | INT | References users.id |
| amount | DECIMAL(12,2) | Default: 0 |
| created_at | DATETIME | Auto |
| updated_at | DATETIME | Auto on update |

---

## File Storage Strategy

No media tables — files stored in filesystem folders:

```
/uploads/
├── campaigns/
│   ├── {campaign_id}/        # All images + video for a campaign
│   │   ├── img1.jpg
│   │   ├── img2.png
│   │   └── video.mp4
│   └── ...
└── profiles/
    ├── {user_id}.jpg          # Single profile photo per user
    └── ...
```

- **Campaign media**: Scan `/uploads/campaigns/{campaign_id}/` to fetch all files
- **Profile photos**: Direct path `/uploads/profiles/{user_id}.jpg`

---

## Project Structure

```
sahara/
├── index.html                      # Homepage
├── campaigns.html                  # Campaign listing
├── campaign-details.html           # Single campaign page
├── fundraise.html                  # Fundraise request form
├── dashboard.html                  # User dashboard
├── admin-dashboard.html            # Admin main dashboard
├── admin-fundraise-requests.html   # Admin fundraise management
├── admin-donation-requests.html    # Admin donation management
├── admin-settings.html             # Admin settings (password)
├── sahara_schema.sql               # Database schema
├── logo.jpg                        # SAHARA logo
├── uploads/                        # File storage (see above)
│   ├── campaigns/
│   └── profiles/
└── includes/                       # PHP includes (planned)
    ├── header.php
    ├── footer.php
    └── db.php
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
- Web server with PHP support (Apache/Nginx)
- MySQL 5.7+ or MariaDB 10.3+
- Modern browser (Chrome, Firefox, Safari, Edge)

### Steps

1. **Clone/download** the project files to your web server root

2. **Create the database**:
   ```sql
   CREATE DATABASE sahara_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   USE sahara_db;
   SOURCE sahara_schema.sql;
   ```

3. **Create upload directories**:
   ```bash
   mkdir -p uploads/campaigns uploads/profiles
   chmod 755 uploads/campaigns uploads/profiles
   ```

4. **Place the logo** file (`logo.jpg`) in the project root

5. **Open** `index.html` in your browser to view the frontend

> **Note**: All pages currently use sample JavaScript data. Backend PHP integration is planned as the next phase.

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

## Browser Support

- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+
- Mobile Safari (iOS 14+)
- Chrome Mobile (Android 10+)

---

## License

This project is built for **SAHARA — IIT Madras BS Degree Social Welfare Society**. All rights reserved.

---

## Contributors

- **Tabish Shaikh** — Project Lead, Design & Development

---

> Built with ❤️ for social welfare