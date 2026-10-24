# Implementation Plan: KOL Performance Card Generator (v4)

> **Project:** KOL-System-card-generator  
> **Framework:** CodeIgniter 3 (modified/Cicool Builder)  
> **Target Directory:** `C:\laragon\www\KOL-System-card-generator`  
> **Date:** 2026-08-28  
> **Last Updated:** 2026-08-31 (v4 — finalized DB strategy: both applications use the same physical `KOL` database as `$db['default']`; sidebar isolation remains application-level via `app_id`)

---

## 1. Context & Background

### What we're building
A new standalone CI3 application (the "KOL Data Hub") whose sole purpose is to:
1. **Manage KOL master data** — profiles, social platforms, niches/categories
2. **Scrape KOL profile-level performance** from Apify (last 20–30 posts per profile) to calculate baseline Average Views and Average Engagement Rate
3. **Generate a premium "Performance Card"** for each KOL that can be viewed, exported, or shared with stakeholders

### Why a separate system?
The existing `KOL-System` (Campaign Management Tool) handles budgets, tasks, briefs, shipments, etc. Mixing the KOL master data and scraping logic inside that system creates tight coupling. By separating:
- **This new system** owns the KOL identity and performance intelligence
- **The old system** stays focused on campaigns and simply *reads* KOL data
- **Future systems** (finance, KOL portal, etc.) can also consume KOL data without touching campaign logic

### How the two systems connect
Both systems connect to the **same shared physical MySQL database** (`KOL`) as their default database connection. They also share the `menu` table.
- `KOL-System-card-generator` (this system): `$db['default']` -> `KOL`. Configuration `$config['app_id'] = 2`.
- `KOL-System` (existing): `$db['default']` -> `KOL`. Configuration `$config['app_id'] = 1`.

To avoid Cicool sidebar menus colliding between the two apps, we use a Multi-Tenancy strategy on the `menu` table using an `app_id` column. Cicool's backend menu builder and frontend sidebar rendering are both filtered using this `app_id`.

---

## 2. Current State of `KOL-System-card-generator`

| Item | Status |
|------|--------|
| CI3 Framework | ✅ Installed (modified CI3 + Cicool) |
| HMVC Modules | ✅ 26 base modules (auth, dashboard, crud, api, etc.) |
| RBAC (auth, group, permission, access) | ✅ Already built-in from Cicool |
| Database Config | ✅ Merged into single shared `KOL` database with `app_id` tenancy |
| Apify Config | ❌ Not yet created — copy from KOL-System |
| CLI Controllers | ✅ Empty `application/controllers/cli/` directory ready |
| Core Classes | ✅ `My_Controller.php`, `My_Model.php` exist |

---

## 3. Design Decision: Approval Status & RBAC

### Do we need KOL approval in the new system?
**No.** In the old KOL-System, approval existed because staff submitted KOLs for a leader to review in the context of campaign onboarding. This new system is a pure **internal data management tool** — when you add a KOL here, you're simply registering them in a database.

**Decision:** All KOLs added through this system are set to `status = 'ACTIVE'` immediately. No approval workflow needed.
> If KOLs are imported from the old system, they keep whatever `approval_status` they already have. But the new system's Add KOL form will default to `ACTIVE`.

### Do we need RBAC?
**Yes, but it's already built-in.** The `KOL-System-card-generator` framework comes with Cicool's auth/group/permission modules. You can use the existing RBAC to control who can:
- View KOL data (read-only users)
- Add/Edit KOL data (editors)
- Trigger Apify scraping (admins)

No additional RBAC development needed — just configure the existing permission groups.

---

## 4. Database Strategy

### 4.1. Database connection strategy (Multi-Tenancy)
**File:** `application/config/database.php`

Both `KOL-System-card-generator` and `KOL-System` connect to the exact same database (`KOL`) as their default connection (`$db['default']`).

To prevent the Cicool sidebar menus from colliding, we are implementing a Multi-Tenancy strategy on the `menu` table:
1. Add an `app_id` column to the `menu` table in the database.
2. In `KOL-System/application/config/app.php`, set `$config['app_id'] = 1`.
3. In `KOL-System-card-generator/application/config/app.php`, set `$config['app_id'] = 2`.
4. In both applications' `application/helpers/app_helper.php`, the menu query will be patched to filter `WHERE (app_id = [current_app_id] OR app_id = 0)`.

This approach ensures perfect isolation for the frontend sidebars while allowing native sharing of backend tables and KOL domain data without any connection switching. Both applications intentionally use the same physical database; `app_id` is used to isolate application-specific menus, not to isolate the database itself.

### 4.2. Shared Database Tables This System Will READ & WRITE

| Table | Purpose | Key Columns |
|-------|---------|-------------|
| `kols` | KOL master profiles | `id`, `name`, `phone`, `email`, `city`, `province`, `tier_id`, `lifetime_score`, `approval_status`, `avatar_url` |
| `kol_platforms` | Social media accounts per KOL | `kol_id`, `platform_id`, `username`, `profile_url`, `followers`, `avg_views`, `avg_er`, `last_synced_at` |
| `kol_categories` | KOL niche/category tags | `kol_id`, `category_id` |
| `kol_rate_cards` | Pricing per platform/content type | `kol_platform_id`, `content_type`, `rate` |
| `platforms` | Platform master (TikTok, IG, YT) | `id`, `name`, `code` |
| `tiers` | Tier master (Nano, Micro, Macro) | `id`, `name`, `min_followers`, `max_followers` |
| `categories` | Category master (Beauty, F&B, etc.) | `id`, `name` |

### 4.3. New table (need to create via migration)

#### [NEW] `kol_profile_snapshots`
Stores a historical record each time we scrape a KOL's profile. **One row per KOL per platform per sync** — so if a KOL has TikTok + Instagram, each sync produces 2 rows.

```sql
CREATE TABLE IF NOT EXISTS `kol_profile_snapshots` (
    `id`                     BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `kol_id`                 BIGINT UNSIGNED NOT NULL,
    `platform_id`            INT UNSIGNED    NOT NULL,       -- Per-platform! Not combined
    `followers_count`        BIGINT UNSIGNED NOT NULL DEFAULT 0,
    `average_views_last_30`  DECIMAL(15,2)   NOT NULL DEFAULT 0.00,
    `average_er_last_30`     DECIMAL(10,6)   NOT NULL DEFAULT 0.000000,
    `total_posts`            BIGINT UNSIGNED NOT NULL DEFAULT 0,
    `snapshot_date`          DATETIME        NOT NULL,
    `created_at`             DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_snapshots_kol` (`kol_id`),
    KEY `idx_snapshots_platform` (`platform_id`),
    KEY `idx_snapshots_date` (`snapshot_date`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

**Why per-platform?**  
A KOL might have 500K followers on TikTok with 50K avg views but only 10K followers on IG with 2K avg views. Combining them would produce a meaningless average. The Performance Card displays each platform separately so stakeholders can see exactly where the KOL performs best.

---

## 5. Database Safety & Non-Destructive Migration Rules

This section is a **hard implementation constraint** for all future work.

### 5.1. Existing database must be preserved
The existing `KOL` database is production/shared application state for both systems. The new system must integrate into it without destroying or resetting existing data.

**STRICTLY FORBIDDEN:**
- `DROP DATABASE`
- `DROP TABLE` on any existing table
- `TRUNCATE TABLE` on any existing table
- Deleting existing records as part of setup/migration
- Recreating existing tables from scratch
- Resetting the database/schema
- Replacing existing schema with an assumed schema
- Destructive renames/removals without explicit approval

### 5.2. Reuse existing tables
Before creating or modifying any table, inspect the actual existing schema in the `KOL` database. If an entity already exists, reuse it. Do not create duplicate tables merely because the new application has a different module structure.
The existing KOL-domain tables listed in §4.2 are shared data and must remain the source of truth unless inspection proves otherwise.

### 5.3. Safe schema changes
Allowed changes are limited to non-destructive integration changes such as:
- Creating genuinely new tables (for example `kol_profile_snapshots`)
- Adding required columns when they do not already exist
- Adding indexes/constraints after checking compatibility
- Adding `app_id` to `menu` if it does not already exist

### 5.4. Database verification
After migrations or schema changes, verify that:
- Existing tables still exist
- Existing records remain intact
- Existing KOL-System functionality still works
- New tables/columns are available to `KOL-System-card-generator`
- Both applications can connect to the same `KOL` database
- Sidebar/menu filtering by `app_id` still works

---

## 6. Apify Configuration

### 6.1. Copy config from existing system
**Source:** `KOL-System/application/config/apify.php`  
**Destination:** `KOL-System-card-generator/application/config/apify.php`

This config already contains everything we need:
- `apify_api_token` — API authentication
- `apify_base_url` — `https://api.apify.com/v2`
- `apify_profile_actors` — the actors for profile scraping:
  - TikTok: `clockworks~tiktok-profile-scraper`
  - Instagram: `apify~instagram-profile-scraper`  
  - YouTube: `streamers~youtube-channel-scraper`
- `apify_field_map` — maps Apify JSON response keys to our database columns

### 6.2. Key note about actors
The existing system uses `apify_actors` (for scraping individual video URLs). **This new system uses `apify_profile_actors`** (for scraping an entire KOL profile's last N posts). Different actors, different payloads, different responses.

---

## 7. Backend: Profile Scraper

### 7.1. CLI Cron Job (Automatic)
**File:** `application/controllers/cli/Apify_profile_service.php`

#### Logic flow
```
1. Load apify config
2. Query kol_platforms WHERE:
   - platform code IN ('TIKTOK', 'INSTAGRAM', 'YOUTUBE')
   - last_synced_at IS NULL OR last_synced_at < NOW() - 7 DAYS
   - LIMIT 10 (batch size per cron run)
3. For each KOL platform:
   a. Determine actor_id from $config['apify_profile_actors'][platform_code]
   b. POST to Apify: /actors/{actor_id}/run-sync-get-dataset-items
      Payload: { profiles: [username], resultsPerPage: 30 }
   c. Parse the response (array of 20–30 post objects)
   d. Calculate:
      - followers = extract from authorMeta.fans (or equivalent)
      - avg_views = SUM(playCount|viewCount) / post_count
      - avg_er = SUM(likes+comments+shares) / SUM(views)
   e. INSERT into kol_profile_snapshots
   f. UPDATE kol_platforms SET last_synced_at = NOW()
```

#### Cron schedule
**Recommendation: Every 7 days.** KOL profile stats don't change dramatically day-to-day, and each Apify call costs API credits. The scraper's WHERE clause already enforces `last_synced_at < NOW() - 7 DAYS`, so even if the cron fires daily, it only re-scrapes KOLs that are actually due.

```bash
# Option A: Run daily at 3 AM (safe — only scrapes KOLs due for refresh)
0 3 * * * cd /path/to/KOL-System-card-generator && php index.php cli/apify_profile_service run

# Option B: Run every 3 days at 3 AM (lighter on server)
0 3 */3 * * cd /path/to/KOL-System-card-generator && php index.php cli/apify_profile_service run
```

### 7.2. Bulk Scrape (Manual from UI)
**File:** `application/controllers/Scraping.php` (or add method to existing controller)

Users should be able to select multiple KOLs from the KOL List page and click "Bulk Scrape" to trigger immediate scraping without waiting for the cron job.

#### Logic flow
```
1. Receive POST request with array of kol_ids from the frontend
2. For each kol_id:
   a. Get all active kol_platforms for that KOL
   b. Queue them for scraping (reuse the same logic as CLI scraper)
   c. Ignore the 7-day check — force immediate scrape
3. Return JSON response with results summary
```

#### UI Trigger
- Checkbox column on the KOL List table
- "Bulk Scrape Selected" button in the toolbar
- AJAX POST to `/scraping/bulk` with selected IDs
- Show a progress toast/notification as each KOL completes

---

## 8. Frontend Layout & Sidebar

### 8.1. Sidebar Menu Structure
```
┌──────────────────────────────┐
│  🏠  KOL Data Hub            │  ← App brand
├──────────────────────────────┤
│  📊  Dashboard               │  ← Overview stats
│                              │
│  👤  KOL Management          │  ← Expandable group
│    ├─ KOL List               │  ← Main CRUD (built via CRUD Builder)
│    └─ Add New KOL            │  ← Quick link to add form
│                              │
│  🔄  Scraping                │  ← Expandable group
│    ├─ Scraping Monitor       │  ← View job history + trigger bulk
│    └─ Scraping Settings      │  ← Apify token, interval config
│                              │
│  📋  Master Data             │  ← Expandable group
│    ├─ Tiers                  │  ← Nano, Micro, Macro, etc.
│    ├─ Categories             │  ← Beauty, F&B, Tech, etc.
│    └─ Platforms              │  ← TikTok, IG, YouTube, etc.
│                              │
│  ⚙️  Settings               │  ← General app settings
│  👥  Users & Permissions     │  ← RBAC (from Cicool)
└──────────────────────────────┘
```

### 8.2. Pages & What They Show

#### 📊 Dashboard
| Widget | Data Source | Description |
|--------|------------|-------------|
| Total KOLs | `COUNT(*) FROM kols WHERE status='ACTIVE'` | Big number card |
| KOLs by Tier | `GROUP BY tier_id` | Donut chart (Nano/Micro/Macro/Mega) |
| Avg ER Across All KOLs | `AVG(average_er_last_30) FROM kol_profile_snapshots` | Gauge or number |
| Recently Scraped | Latest 5 `kol_profile_snapshots` rows | Table with KOL name, platform, date |
| Stale KOLs | KOLs where `last_synced_at > 14 days` or NULL | Warning list |

#### 👤 KOL List (CRUD — built via CRUD Builder)
- **Table columns:** Name, Tier, City, Platforms (icons), Last Synced, Avg Views, Actions
- **Filters:** Tier, Category, Platform, City/Province, Sync Status (Fresh/Stale)
- **Actions per row:** View Detail, Edit, Performance Card, Scrape Now
- **Bulk actions:** Bulk Scrape Selected (checkbox + toolbar button)

#### 👤 KOL Detail / Performance Card
When clicking "View" or "Performance Card" on a KOL:
- **Header:** Avatar, Name, Location, Tier badge, Category tags
- **Platform Grid:** One card per platform (TikTok, IG, YouTube), each showing:
  - Followers count
  - Avg Views (last 30 posts) — highlighted value
  - Avg ER (last 30 posts) — green percentage
  - Last Sync date
- **Growth Trend:** Chart.js line chart showing `average_views_last_30` over the last 6 months from historical `kol_profile_snapshots`
- **Actions:** "Scrape Now" button, "Export as PDF" button

#### 🔄 Scraping Monitor
| Column | Description |
|--------|-------------|
| KOL Name | Who was scraped |
| Platform | Which platform |
| Avg Views | Result |
| Avg ER | Result |
| Snapshot Date | When it was scraped |
| Status | Success / Failed / Pending |
- **Toolbar button:** "Bulk Scrape All Stale KOLs" — triggers scraping for all KOLs where `last_synced_at` is older than 7 days
- **Filter:** By date range, platform, status

#### 📋 Master Data (Tiers, Categories, Platforms)
Standard CRUD pages (built via CRUD Builder). Simple table + Add/Edit/Delete.

---

## 9. Design Guidelines

### Typography & Colors
- **Font:** Google Fonts — Inter (weight 400–800)
- **Primary:** Indigo `#4F46E5`
- **Text Main:** Slate `#0F172A`
- **Text Muted:** Slate `#64748B`
- **Success/ER:** Green `#059669`
- **Background:** Slate `#F8FAFC`
- **Card BG:** White `#FFFFFF`
- **Border:** Slate `#E2E8F0`

### Sidebar Styling
- Dark sidebar background (`#1E293B` — Slate 800) with white text
- Active menu item highlighted with primary color (`#4F46E5`)
- Icons for each menu group (use Font Awesome 6)
- Smooth expand/collapse animation for sub-menus
- App logo/brand at the top

### Performance Card Styling
- Glassmorphism header with subtle gradient
- Rounded cards (16–24px border radius)
- Hover micro-animation on platform cards (translateY -4px + box-shadow)
- Icons: `fa-tiktok`, `fa-instagram`, `fa-youtube` with brand colors

---

## 10. File Checklist

### Files to CREATE
| # | File | Type | Description |
|---|------|------|-------------|
| 1 | `application/config/apify.php` | Config | Copy from KOL-System |
| 2 | `application/controllers/cli/Apify_profile_service.php` | CLI Controller | Cron-based profile scraper |
| 3 | `application/controllers/Performance_card.php` | Web Controller | Renders the Performance Card |
| 4 | `application/controllers/Scraping.php` | Web Controller | Scraping monitor + bulk scrape endpoint |
| 5 | `application/views/performance_card_view.php` | View | Performance Card HTML/CSS/JS |
| 6 | `application/views/scraping_monitor.php` | View | Scraping job history + bulk actions |
| 7 | `application/views/dashboard_widgets.php` | View | Dashboard stats widgets |

### CRUD Modules to generate (via CRUD Builder)
| # | Module | Table | Notes |
|---|--------|-------|-------|
| 1 | `kols` | `kols` | Main KOL CRUD — default `status = 'ACTIVE'`, no approval |
| 2 | `kol_platforms` | `kol_platforms` | Inline or sub-form inside KOL detail |
| 3 | `kol_categories` | `kol_categories` | Checkbox multi-select inside KOL form |
| 4 | `kol_rate_cards` | `kol_rate_cards` | Sub-table inside KOL detail |
| 5 | `tiers` | `tiers` | Simple master data CRUD |
| 6 | `categories` | `categories` | Simple master data CRUD |
| 7 | `platforms` | `platforms` | Simple master data CRUD |

### Files to MODIFY
| # | File | Change |
|---|------|--------|
| 1 | `application/config/database.php` | Ensure `$db['default']` -> `KOL`; remove/avoid any separate `work_molindo` default DB strategy |
| 2 | `application/config/routes.php` | Add card route + scraping routes |

### SQL to RUN
| # | Description |
|---|-------------|
| 1 | CREATE TABLE `kol_profile_snapshots` (see §4.3 above) |

---

## 11. Development Order (Recommended)

```text
Step 1: Database Setup & Multi-Tenancy (✅ DONE)
  ├── ✅ Connect both systems to `KOL` as default database
  ├── ✅ Run CREATE TABLE kol_profile_snapshots
  ├── ✅ Implement `app_id` column in `menu` table
  └── ✅ Filter Cicool sidebar (`app_helper.php`) for strict multi-tenancy

Step 2: Apify Config
  └── Copy apify.php from KOL-System

Step 3: CRUD Modules (via CRUD Builder)
  ├── Generate KOL CRUD module
  ├── Generate master data modules (tiers, categories, platforms)
  └── Configure sidebar menu and permissions

Step 4: Sidebar & Navigation (✅ DONE)
  ├── ✅ Set up the sidebar menu structure (see §8.1)
  ├── ✅ Style the sidebar (dark theme, icons, collapsible groups)
  └── ✅ Wire up all menu links

Step 5: Backend Scraper
  ├── Build Apify_profile_service.php (CLI cron)
  ├── Build Scraping.php controller (bulk scrape + monitor)
  ├── Test with 1 KOL via CLI
  └── Verify snapshot appears in kol_profile_snapshots

Step 6: Performance Card
  ├── Build Performance_card.php controller
  ├── Build performance_card_view.php (HTML/CSS)
  ├── Add "View Card" button to KOL detail page
  └── Test by opening /card/{kol_id}

Step 7: Dashboard (✅ DONE)
  ├── ✅ Build dashboard widgets (stats, charts, stale KOL alerts)
  └── ✅ Set Dashboard as the landing page after login

Step 8: Bulk Scrape UI
  ├── Add checkboxes to KOL List table
  ├── Add "Bulk Scrape Selected" button
  ├── Wire AJAX to Scraping controller
  └── Add progress notification

Step 9: UI Adjustments & Theming (Main System Alignment)
  ├── Adjust UI of new system to seamlessly match the Main System's styling
  ├── Update Cicool Builder templates (view scaffolds) to output matching UI
  └── Ensure consistent typography, colors, buttons, and layout across both apps

Step 10: Polish & Stretch Goals
  ├── Chart.js growth trend on Performance Card
  ├── Export Performance Card as PDF/image
  ├── Scraping Settings page (configure interval, API token via UI)
  └── Style refinements
```

---

## 12. Key Reference Files in KOL-System

These files from the existing system should be studied before coding:

| File | Why |
|------|-----|
| `application/config/apify.php` | Contains all actor IDs, field maps, and API token |
| `application/controllers/Apify_service.php` | Reference for cURL calls, payload structure, response parsing |
| `kol_system_schema_v1.6.sql` | Full database schema — all table structures |
| `modules/kols/views/backend/.../kols_view.php` | Existing Performance Card UI for design inspiration |
| `modules/kols/controllers/backend/Kols.php` | Reference for KOL CRUD logic (add_save, edit_save, sync flows) |