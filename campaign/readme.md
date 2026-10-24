# KOL and Campaign Management System

The KOL and Campaign Management System is a CodeIgniter 3 application built on the Cicool HMVC starter framework. It manages the complete end-to-end lifecycle of influencer marketing campaigns, covering planning, deal agreements, content production workflows, live posting verification, financial tracking, and internal performance evaluations.

## Tech Stack

- Framework: CodeIgniter 3.x (PHP 7.4+ / PHP 8.x compatible)
- Architecture: HMVC (Hierarchical Model-View-Controller)
- Template Engine: Cicool AdminLTE Theme with Bootstrap 3
- Authentication and RBAC: CodeIgniter-Aauth
- Database: MySQL 8.0 (InnoDB, utf8mb4)
- Multi-Tenant Menu System: Shared `KOL` database filtered by `app_id = 1`

## Architecture and Workflow

The system connects operational entities through a structured campaign pipeline:

```text
KOL Master -> Campaign -> Deals (Campaign KOL) -> Tasks / SOW -> Draft Review -> Live Posting -> Metrics -> Finance & Evaluation
```

1. Planning: KOL discovery, campaign creation, and deal agreement with rate cards and SOW.
2. Production: Task assignments, creative brief delivery, multi-round draft reviews, and live posting confirmation.
3. Content and Schedule: Content calendar timeline and live posting tracking.
4. Financials: Contract generation, vendor invoicing, payment disbursement, and financial dashboard.
5. Evaluation: Internal scoring matrix and influencer ranking.

## Quick Start

### 1. Database Setup

Create the database and import the latest schema:

```bash
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS KOL CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -u root -p KOL < kol.sql
```

Database connection settings are located in `application/config/database.php`.

### 2. Application Configuration

Ensure base URL and mod_rewrite are enabled:

- Application URL: Configure `$config['base_url']` in `application/config/config.php`.
- App ID Identifier: Set to `$config['app_id'] = 1` in `application/config/config.php` to load administrative campaign menus.

### 3. Default Credentials

| Field | Value |
| --- | --- |
| Login URL | `administrator/login` |
| Email | dibuat saat setup (di DB, bukan nilai default) |
| Password | set saat setup melalui panel admin / seed DB |

## Core Modules

- Planning (`Perencanaan`):
  - `kols`: Master database of creators with social accounts, engagement metrics, and tiering.
  - `campaigns`: Campaign setup with budgets, deliverable targets, dates, and target audiences.
  - `campaign_kols`: KOL assignment to campaigns, deals negotiation, rate lock, and task generation.
- Production (`Produksi`):
  - `tasks`: Individual deliverables per KOL with linear status tracking (Draft, Review, Live Posting).
  - `kalender`: Content schedule calendar for viewing estimated and confirmed posting dates.
- Logistics and Inventory (`Inventaris`):
  - `products`: Product database and variants.
  - `kol_shipments`: End-to-end sample shipment tracking (Order, In Transit, Delivered).
- Finance (`Keuangan`):
  - `finance_hub`: Financial analytics dashboard (Total Deals, Invoiced, Paid, Unpaid).
  - `campaign_kol_contracts`: Legal contracts and agreements per deal.
  - `invoices`: Vendor billing and invoice validation.
  - `payments`: Payment processing records.
- Evaluation (`Evaluasi`):
  - `dashboard/chart`: KOL performance rankings.
  - `internal_evaluation`: Multi-criteria internal performance evaluations.
- Integration:
  - `apify_config`: Configuration and task runner for social media scraping.

## Directory Structure

```text
KOL-System/
├── application/
│   ├── config/              # Database, routes, auth, and app configuration
│   ├── controllers/         # Root and utility controllers
│   ├── core/                # MY_Controller and MY_Model base classes
│   └── views/               # Standard layout templates and dashboard view
├── modules/                 # HMVC functional modules
│   ├── campaigns/
│   ├── campaign_kols/
│   ├── tasks/
│   ├── kols/
│   ├── finance_hub/
│   └── ...
├── kol.sql                  # Primary database schema and seed data
└── index.php                # Front controller
```

## User Roles and Permissions

User groups are configured in `aauth_groups` and permissions in `aauth_perms`:

- Admin: Super Admin with unrestricted access across all modules, configuration, and user management.
- Master KOL: Campaign director and team lead with access to approve KOLs, agreements, and budgets.
- Operational: Campaign execution team responsible for KOL outreach, task briefs, draft reviews, and live monitoring.
