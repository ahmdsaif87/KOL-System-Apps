# Future Plan: KOL-System Transition to Consumer Mode

> **System:** KOL-System (Campaign Management Tool)  
> **Status:** 🕐 FUTURE — Do not implement yet  
> **Prerequisite:** The new `ci3_metafora` (KOL Data Hub) must be fully operational first  
> **Date Created:** 2026-08-28

---

## 1. Goal

Transition the existing KOL-System from being the **owner** of KOL master data to being a **consumer** of KOL data managed by the new KOL Data Hub (`ci3_metafora`). After this transition:

- **ci3_metafora** = the single source of truth for KOL profiles, platform accounts, Apify scraping, and performance cards
- **KOL-System** = purely a Campaign Management tool that *reads* KOL data but no longer creates or edits it

---

## 2. Current State: Modules That Touch KOL Data

These are the modules in KOL-System that currently **write** to KOL-related tables:

| Module | Table(s) Affected | Current Action | Future Action |
|--------|-------------------|----------------|---------------|
| `kols` | `kols` | Full CRUD (add, edit, delete, approve, reject) | **Read-only** — remove Add/Edit, keep View |
| `kol_platforms` | `kol_platforms` | Full CRUD | **Read-only** — remove Add/Edit |
| `kol_categories` | `kol_categories` | Full CRUD | **Read-only** — remove Add/Edit |
| `kol_rate_cards` | `kol_rate_cards` | Full CRUD | **Read-only** — remove Add/Edit |
| `kol_approval_histories` | `kol_approval_histories` | Write (approve/reject flow) | **Remove** — approval moves to ci3_metafora |
| `kol_shipments` | `kol_shipments` | Full CRUD | **Keep** — shipments are campaign-specific |
| `kol_shipment_items` | `kol_shipment_items` | Full CRUD | **Keep** — shipments are campaign-specific |
| `platforms` | `platforms` | Full CRUD (master data) | **Read-only** — master data managed in ci3_metafora |
| `tiers` | `tiers` | Full CRUD (master data) | **Read-only** — master data managed in ci3_metafora |
| `categories` | `categories` | Full CRUD (master data) | **Read-only** — master data managed in ci3_metafora |
| `apify_jobs` | `apify_jobs` | Write (scraping jobs) | **Remove** — scraping moves to ci3_metafora |
| `posting_metrics` | `posting_metrics` | Write (Apify results) | **Read-only** — metrics written by ci3_metafora |
| `score_histories` | `score_histories` | Write (score calc) | **Read-only** — scoring moves to ci3_metafora |

### Modules that stay untouched (campaign-specific)
These modules are **not affected** by the transition:
`campaigns`, `campaign_kols`, `campaign_kol_variants`, `campaign_variants`, `tasks`, `task_budget_histories`, `task_status_histories`, `briefs`, `brief_approval_histories`, `brief_content_histories`, `drafts`, `draft_review_histories`, `postings`, `products`, `stock_history`, `kalender`

---

## 3. Transition Steps

### Phase 1: Make KOL Module Read-Only

**Goal:** Users can still *see* KOL data in KOL-System, but all creation/editing is done in ci3_metafora.

#### 3.1. Controller Changes: `modules/kols/controllers/backend/Kols.php`

| Method | Line | Action |
|--------|------|--------|
| `index()` | 28 | **Keep** — list view stays |
| `view()` | 781 | **Keep** — detail view stays |
| `add()` | 81 | **Remove or disable** — redirect to ci3_metafora |
| `add_save()` | 97 | **Remove** |
| `edit()` | 469 | **Remove or disable** — redirect to ci3_metafora |
| `edit_save()` | 489 | **Remove** |
| `delete()` | 718 | **Remove** |
| `approve()` | 906 | **Remove** — approval moves to ci3_metafora |
| `reject()` | 980 | **Remove** — approval moves to ci3_metafora |
| `sync_platform_ajax()` | 319 | **Remove** — syncing moves to ci3_metafora |
| `sync_apify_intelligence()` | 370 | **Remove** — Apify moves to ci3_metafora |
| `upload_profile_url_file()` | 1119 | **Remove** |
| `upload_rate_card_file_file()` | 1194 | **Remove** |
| `import_excel()` | 1336 | **Remove** |
| `export()` | 1269 | **Keep** — exporting still useful |
| `export_pdf()` | 1281 | **Keep** |
| `single_pdf()` | 1289 | **Keep** |

#### 3.2. View Changes
- **`kols_list.php`**: Remove "Add New KOL" button, remove "Edit" and "Delete" action buttons, add a link/button "Manage in KOL Hub" that opens ci3_metafora
- **`kols_view.php`**: Remove "Edit" button, remove "Sync Apify" buttons, replace with embedded Performance Card from ci3_metafora (via iframe or direct DB read)
- **`kols_add.php`**: Can be deleted entirely
- **`kols_edit.php`**: Can be deleted entirely

#### 3.3. Sidebar Menu
- Remove or hide the "Add KOL" menu item
- Optionally add a new menu item: "KOL Hub (External)" linking to ci3_metafora

### Phase 2: Remove Apify Scraping from KOL-System

**Goal:** All scraping logic moves to ci3_metafora. KOL-System only reads the results.

| File | Action |
|------|--------|
| `application/controllers/Apify_service.php` | **Remove** or mark as deprecated |
| `application/config/apify.php` | **Keep** (may still be needed for reading config) or **Remove** |
| `modules/apify_jobs/` | **Make read-only** (remove add/edit, keep list view for monitoring) |
| `modules/posting_metrics/` | **Keep as read-only** (useful for campaign reporting) |
| `modules/score_histories/` | **Keep as read-only** |

### Phase 3: Remove Master Data CRUD from KOL-System

**Goal:** Master data (tiers, categories, platforms) is now exclusively managed in ci3_metafora.

| Module | Action |
|--------|--------|
| `modules/platforms/` | Remove Add/Edit/Delete, keep list view |
| `modules/tiers/` | Remove Add/Edit/Delete, keep list view |
| `modules/categories/` | Remove Add/Edit/Delete, keep list view |

### Phase 4: Integration — Embed Performance Card

**Goal:** When a user views a KOL in KOL-System, they see the Performance Card generated by ci3_metafora.

**Option A: Direct DB Read (Simplest)**
Since both systems share the same database, the `kols_view.php` in KOL-System can directly query `kol_profile_snapshots` and render the card inline. No API needed.

**Option B: Iframe Embed**
Embed the Performance Card URL from ci3_metafora:
```html
<iframe src="http://localhost/ci3_metafora/card/<?= $kol->id ?>" 
        style="width:100%; height:600px; border:none;">
</iframe>
```

**Recommendation:** Start with **Option A** (direct DB read) for speed and simplicity. Move to Option B only if you want the card design to be fully controlled by ci3_metafora without duplicating HTML.

---

## 4. Campaign KOL Selection Flow (Important)

When creating a campaign in KOL-System, users currently pick KOLs from the `kols` table. After transition, this flow **stays the same** since both systems share the same database. The `campaign_kols` table still references `kols.id`.

**No change needed** for:
- `modules/campaign_kols/` — still writes `campaign_kols` rows linking `campaign_id` to `kol_id`
- `modules/tasks/` — still creates tasks for `campaign_kol_id`

---

## 5. Risk & Mitigation

| Risk | Mitigation |
|------|------------|
| Users accidentally edit KOL in old system while new system is active | Remove CRUD buttons in Phase 1 immediately |
| Inconsistent scoring if both systems run Apify | Disable `Apify_service.php` cron in old system once ci3_metafora scraper is confirmed working |
| Database schema drift between two systems | Both point to same DB — schema changes go through migration files in one place only |
| Performance (two apps hitting same DB) | MySQL InnoDB row-level locking handles concurrent reads/writes fine at this scale |

---

## 6. Timeline Suggestion

| Phase | Description | When |
|-------|-------------|------|
| Phase 1 | KOL Module → Read-Only | After ci3_metafora KOL CRUD is fully operational |
| Phase 2 | Remove Apify from old system | After ci3_metafora scraper has run successfully for 2+ weeks |
| Phase 3 | Remove Master Data CRUD | Can be done together with Phase 1 |
| Phase 4 | Embed Performance Card | After ci3_metafora Performance Card UI is polished |
