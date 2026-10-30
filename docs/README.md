# Dokumentasi Pra-Build: Rewrite KOL System ke CodeIgniter 4

Dokumentasi ini adalah panduan komprehensif, terstruktur, dan teknis yang disiapkan sebelum memulai proses perancangan dan *rewrite* sistem **KOL Management, Datahub, & Landing Portal** dari CodeIgniter 3 (legacy Cicool builder) ke **CodeIgniter 4 (Enterprise Clean Architecture)**.

---

## 📚 Daftar Dokumen Spesifikasi

| No | File Dokumen | Deskripsi & Cakupan |
| :---: | :--- | :--- |
| **01** | [**01_ARCHITECTURE_AND_SYSTEM_DESIGN.md**](./01_ARCHITECTURE_AND_SYSTEM_DESIGN.md) | Arsitektur sistem monorepo/modular CI4, perbandingan CI3 vs CI4, tata kelola `public/` web root, file upload permanen, dan strategi *single-server deployment*. |
| **02** | [**02_DATABASE_AND_MODELS_SPECIFICATION.md**](./02_DATABASE_AND_MODELS_SPECIFICATION.md) | Analisis skema database MySQL, pembersihan *legacy overhead* Cicool, relasi antar tabel (50+ entitas), CI4 Model, Entities, dan strategi Migrations. |
| **03** | [**03_RBAC_AND_AUTHENTICATION.md**](./03_RBAC_AND_AUTHENTICATION.md) | Spesifikasi otorisasi lengkap (Superadmin, Leader KOL, Staff Operational, Talent KOL, Guest), CI4 Auth Filters, dan matriks hak akses. |
| **04** | [**04_CORE_BUSINESS_WORKFLOWS.md**](./04_CORE_BUSINESS_WORKFLOWS.md) | Finite State Machine (FSM) & pipeline bisnis: Rekrutmen KOL, Orkestrasi Kampanye, Auto-generate VT Tasks, Approval Brief/Draft, Logistik Resi, dan Finance Hub. |
| **05** | [**05_EXTERNAL_INTEGRATIONS_AND_API.md**](./05_EXTERNAL_INTEGRATIONS_AND_API.md) | Integrasi mesin scraping Apify (TikTok/IG/YouTube), KeyAPI TikTok Shop, WhatsApp Notification Engine, dan REST API internal (`/articles/feed`). |
| **06** | [**06_MIGRATION_ROADMAP_AND_DEVELOPER_GUIDE.md**](./06_MIGRATION_ROADMAP_AND_DEVELOPER_GUIDE.md) | Roadmap pengembangan 6 fase, panduan refactoring kode CI3 ke CI4 (Controllers, Requests, Views, Helpers), dan *checklist* verifikasi pra-rilis. |

---

## 🎯 Tujuan Utama Rewrite ke CI4

1. **Menghapus Ketergantungan Legacy Generator (Cicool Builder):**
   - Menghapus tabel-tabel generator internal (`menu`, `menu_type`, `crud`, `page`, dll).
   - Menghilangkan *code smells*, file scaffold yang redundant, dan query duplikatif.
2. **Menyatukan Tenancy & Database Isolation yang Rapih:**
   - Menghilangkan *hack* `app_id` pada tabel menu.
   - Menggunakan pemisahan modul yang jelas berbasis Namespace PSR-4 (`App\Modules\...`).
3. **Peningkatan Standar Keamanan & Performa:**
   - Menjalankan sistem di atas PHP 8.2+ dengan typed properties, strict types, dan null-safety.
   - Mengamankan Document Root di folder `public/`, mencegah kebocoran file `.env`, file log di `writable/`, dan core logic.
   - Perlindungan CSRF, XSS auto-escaping, dan Content Security Policy bawaan CI4.
4. **Self-Service Creator Portal & Finance Automation:**
   - Portal mandiri terisolasi untuk KOL (melamar campaign, submit draft, upload invoice).
   - Otomasi perhitungan pajak PPh 21/23 dan *invoice reconciliation* yang reliabel.
