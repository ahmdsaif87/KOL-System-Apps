# KOL Performance Card Generator and Intelligence Hub

The KOL Performance Card Generator is a standalone CodeIgniter 3 application built on the Cicool HMVC framework. It functions as the creator intelligence hub and self-service portal within the KOL System ecosystem. Its primary capabilities include creator profile management, automated social media performance scraping, generating shareable KOL Performance Cards, and providing a dedicated portal for creators.

## Tech Stack

- Framework: CodeIgniter 3.x (PHP 7.4+ / PHP 8.x compatible)
- Architecture: HMVC (Hierarchical Model-View-Controller)
- Template Engine: Cicool AdminLTE Theme with Bootstrap 3
- Authentication and RBAC: CodeIgniter-Aauth
- Database: MySQL 8.0 (InnoDB, utf8mb4)
- Multi-Tenant Menu System: Shared `KOL` database filtered by `app_id = 2`

## Relationship with Main System

Both `KOL-System` (Campaign Operations) and `KOL-System-card-generator-plugin` (Data and Portal Hub) connect to the same underlying database (`KOL`), sharing records such as users, KOL profiles, campaigns, contracts, and invoices.

Sidebar navigation and access control are isolated via the `app_id` column in the `menu` table:
- `KOL-System`: Loads operational campaign menus (`app_id = 1`).
- `KOL-System-card-generator-plugin`: Loads profile intelligence, performance card generator, and KOL portal menus (`app_id = 2`).

## Quick Start

### 1. Database Configuration

The application shares the database with the main KOL System:

```bash
# Verify connection to the shared database
mysql -u root -p KOL
```

Configure database credentials in `application/config/database.php`.

### 2. Application Setup

- Base URL: Configure `$config['base_url']` in `application/config/config.php`.
- App ID Identifier: Set `$config['app_id'] = 2` in `application/config/config.php` to activate creator hub and portal navigation.
- Image Proxy: `avatar_proxy.php` is available at the root to proxy remote profile pictures and prevent CORS issues during card generation and export.

### 3. Default Credentials

| Field | Value |
| --- | --- |
| Login URL | `administrator/login` |
| Email | dibuat saat setup (di DB, bukan nilai default) |
| Password | set saat setup melalui panel admin / seed DB |

## Core Modules

### 1. Performance Card and Analytics

- `performance_card`: Generates standardized, visually refined performance cards for creators. Displays follower metrics, average views, engagement rates (ER), and platform distributions suitable for client pitches and internal reporting.
- `kol_profile_snapshots`: Historical snapshots of creator statistics over time to analyze growth trends.

### 2. Data Intelligence and Scraping

- `apify_config`: Manages actor runs and API tokens for automated Instagram, TikTok, and YouTube profile metric extraction.
- `keyapi_config`: Integration settings for KeyAPI (keyapi.ai) TikTok Shop GMV and creator commerce intelligence.

### 3. KOL Master Data

- `kols`: Creator profiles, contact details, niche categories, and bio information.
- `kol_platforms`: Platform-specific accounts, handles, and follower counts.
- `kol_rate_cards`: Commercial rate structures per content type and social channel.

### 4. Creator Self-Service Portal

- `open_campaigns`: Open opportunities where registered creators can explore requirements and apply.
- `open_campaigns/my_applications`: Tracks submission status and feedback for campaign applications.
- `my_contracts`: Digital agreement viewing for confirmed campaigns.
- `my_invoices`: Payment request and invoice management for completed work.

## Directory Structure

```text
KOL-System-card-generator-plugin/
├── application/
│   ├── config/              # Application, database, and auth settings
│   ├── controllers/         # Base and utility controllers
│   ├── core/                # MY_Controller and MY_Model base classes
│   └── views/               # Shared layout views and card templates
├── modules/                 # HMVC functional modules
│   ├── performance_card/
│   ├── open_campaigns/
│   ├── my_contracts/
│   ├── my_invoices/
│   ├── apify_config/
│   └── ...
├── avatar_proxy.php         # CORS proxy script for external avatar assets
├── kol.sql                  # Database schema dump
└── index.php                # Front controller
```
