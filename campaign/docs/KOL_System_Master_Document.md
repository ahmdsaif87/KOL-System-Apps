# MASTER DOCUMENT: KOL Management System

**Dokumen Terpusat (Single Source of Truth) untuk Tim Capstone**

*Dokumen ini menggabungkan Product Requirements Document (PRD), Pembagian Tugas (Task Assignment) dengan detail Sprint yang lengkap, dan Arsitektur UI (Menu & Fungsi).*

---

## BAGIAN 1: PRODUCT REQUIREMENTS DOCUMENT (PRD)

### 1.1 Ringkasan Produk
Sistem ini adalah platform analitik dan manajemen Key Opinion Leader (KOL) yang dirancang untuk membantu perusahaan mengambil keputusan kolaborasi influencer secara objektif. Menggunakan **CodeIgniter 3** dan **Cicool Builder**, sistem ini mengelola lifecycle KOL mulai dari database profil, campaign, dealing, brief, hingga pelacakan performa otomatis (menggunakan Apify) dalam satu dashboard terintegrasi.

### 1.2 Target Pengguna (User Roles)
Sistem menggunakan *Role-Based Access Control* (RBAC) bawaan Cicool:
1. **Super Admin**: Memiliki akses penuh, manajemen user, dan konfigurasi sistem.
2. **Leader KOL (Mas Louis / Mas Owen)**: Approver dan konseptor. Bertugas menyetujui KOL baru, membuat Master Campaign (budget, SOW, varian), approval/revisi brief, dan memantau ROI dashboard.
3. **Staff KOL (KOL Specialist)**: Operasional. Bertugas mendaftarkan KOL baru, assign KOL ke campaign, input deal, status logistik, input brief, draf konten, dan link postingan terbit.

### 1.3 Alur Kerja Sistem (9 Langkah Utama)
1. **Master KOL**: Staff mendaftarkan KOL baru (kategori, rate card). Leader melakukan Approval (Approve/Tolak dengan catatan).
2. **Master Campaign**: Leader membuat campaign baru, menetapkan budget, tanggal, dan memilih varian parfum yang di-push.
3. **Assign KOL**: Staff memasukkan KOL ke dalam campaign, memilih varian parfum spesifik, dan input gambaran SOW.
4. **Reach Out & Dealing**: Staff menginput Rate Card Deal, menentukan Jumlah VT, Rate per VT, dan estimasi tanggal posting per VT.
5. **Kirim Barang**: Staff menandai status logistik / pengiriman sample.
6. **Buat Brief**: Staff membuat brief teks dan file panduan per postingan (VT).
7. **Approval Brief**: Leader meninjau brief (Approve / Tolak / Revisi + Catatan).
8. **Drafting Konten**: Staff menginput link draf video, caption, hashtag.
9. **Upload Link & Scraping**: Staff menginput URL Live postingan. Sistem men-scrape performa (Apify) untuk Engagement, Views, CPV.

---

## BAGIAN 2: PEMBAGIAN TUGAS & JOB DESK (TEAM OF 4)

Karena proyek ini menggunakan **Cicool Builder** (Code-Generator), pembagian tugas spesifik wajib dipatuhi agar file Git tidak konflik.

### 2.1 Spesifikasi Role

- **‍ Role 1: DBA & Cicool Architect**
 - Tugas: Penjaga gerbang database. Mendesain skema SQL, relasi tabel, dan *satu-satunya* yang boleh mengklik tombol "Generate Module" di UI Cicool.
- **Role 2: Backend & Integration Engineer**
 - Tugas: Membuat logika PHP di Controller. Menulis algoritma *KOL Scoring* (SPK), fungsi *Auto Generate Task*, dan skrip *Cron Job* untuk integrasi Apify API.
- **Role 3: Frontend & UI Customizer**
 - Tugas: Mengubah tampilan View HTML/PHP hasil generate Cicool. Merapikan form, memasang Javascript (*FullCalendar.js*, *Select2*), mendesain Modal Pop-up Approval.
- **Role 4: Data Analyst & QA Tester**
 - Tugas: Membangun Dashboard (Chart.js), fitur Import/Export Excel, menyiapkan data dummy, dan men-testing jalannya sistem (Quality Assurance).

---

## BAGIAN 3: SPRINT BACKLOG

**Sprint Backlog: KOL & Campaign Tracking System**
**(Siap di-copy-paste ke Notion Tim)**

**Tech Stack:** CodeIgniter 3 + Cicool Builder + MySQL 8.x 
**Database Schema:** v1.0 (Final)

### SPRINT 1: Setup & Master Data Base (Hari 1-4) — [STATUS: SELESAI / COMPLETED]
**Goal:** Skema terpasang, RBAC beres, dan CRUD Master Data bisa diakses.

**‍ Role 1 (DBA / Cicool Architect)**
- Eksekusi script DDL Schema v1.0 di database MySQL lokal.
- Generate CRUD Module via Cicool untuk tabel referensi: tiers, categories, platforms, variants.
- Generate CRUD Module via Cicool untuk tabel utama: kols, campaigns.
- Push source code hasil generate Cicool ke branch main di Git.

**Role 2 (Backend / Logic)**
- Pull kode dari Git. Setup Role-Based Access Control (RBAC) via UI Cicool.
- Buat 3 Grup User utama: Super Admin, Leader KOL, Staff KOL.
- Kunci menu Master Data agar hanya bisa diakses oleh Super Admin dan Leader.
- Modifikasi Controller kols: Setup validasi approval_status (Pending/Approved/Rejected) khusus aksi Leader.

**Role 3 (Frontend / UI)**
- Buka file View kols_add.php dan kols_edit.php (hasil generate Role 1).
- Integrasikan plugin Javascript Select2 agar input Kategori bisa Multiple Selection (untuk menyuap data ke tabel pivot kol_categories).
- Ubah desain tabel list KOL: Tambahkan badge warna untuk kolom approval_status dan status.

**Role 4 (QA / Analytics)**
- Siapkan data dummy Kategori, Platform, dan Varian (input manual via UI Cicool yang sudah jadi).
- Testing form Add KOL (sebagai Staff) dan pastikan tombol Approve berfungsi saat login sebagai Leader.

### SPRINT 2: Campaign & Task Generator (Hari 5-10) — [STATUS: SELESAI / COMPLETED]
**Goal:** Fitur pembuatan Campaign, negosiasi KOL, dan auto-generate tugas/VT (Tabel tasks) selesai.

**‍ Role 1 (DBA / Cicool Architect)**
- Generate CRUD Module via Cicool untuk tabel: campaign_kols, tasks.

**Role 2 (Backend / Logic)**
- Modul Assign KOL: Buat logika simpan multi-select varian parfum ke tabel campaign_kol_variants.
- Logika Krusial (Auto-Generate Tasks): Di controller campaign_kols pada fungsi save, buat looping untuk melakukan INSERT INTO tasks sebanyak jumlah VT yang disepakati, dengan nilai awal status DRAFT.
- Tambahkan trigger kustom: Simpan histori perubahan rate ke tabel task_rate_histories.

**Role 3 (Frontend / UI)**
- Permak UI Form Master Campaign: Tambahkan Date Range Picker untuk start_date dan end_date dengan validasi JS (end_date >= start_date).
- Modifikasi Navigasi Tasks: Daripada menggunakan Expandable Row di tabel campaign_kols, buatkan menu khusus mandiri ("Tasks") di Sidebar agar Staff lebih mudah memantau seluruh daftar task yang ter-generate otomatis secara *helicopter view*.

**Role 4 (QA / Analytics)**
- QA Testing: Buat 1 Campaign, masukkan 1 KOL, tentukan 3 VT. Cek di database apakah tabel tasks benar-benar terisi 3 baris secara otomatis.

### SPRINT 3: Workflow Brief, Draft & Posting (Hari 11-16)
**Goal:** Memenuhi alur pembuatan brief, revisi, drafting konten, dan upload link postingan final.

**‍ Role 1 (DBA / Cicool Architect)**
- Generate CRUD Module untuk tabel: briefs, drafts, postings. *(Status: Modul briefs, drafts, postings telah digenerate)*

**Role 2 (Backend / Logic) — [STATUS: SELESAI / COMPLETED]**
- Buat logika Transisi Status Task (`DRAFT ➔ APPROVED ➔ SCHEDULED ➔ POSTED`) yang otomatis mencatat log ke `task_status_histories`. *(Selesai di `Model_tasks::transition_status()` dan controller `Tasks::change_status()`)*
- Tambahkan proteksi penguncian rate (*Rate-Lock*): Task yang sudah `APPROVED` atau lebih lanjut tidak dapat diedit rate-nya via `Tasks::edit_save()`. *(Selesai)*
- Buat Controller khusus untuk Approval Brief: Saat Leader klik Reject/Revisi, tangkap kolom `note` dan simpan ke `brief_approval_histories`. *(Selesai di `Model_briefs::act()` dan controller `Briefs::act()`)*
- Buat logika validasi: Draft hanya bisa diakses/diubah jika status Brief sudah `APPROVED`. *(Selesai di `Model_briefs::is_approved_for_task()`)*
- Daftarkan permission RBAC: `tasks_change_status` dan `briefs_approve` untuk Admin & Master KOL (Leader). *(Selesai)*

**Role 3 (Frontend / UI) — [STATUS: IN PROGRESS]**
- Perombakan Menu Sidebar: Mengatur ulang navigasi menjadi kategori yang ringkas dan logis (Dashboard, Campaign, Database, Pengaturan). *(Selesai)*
- Minimalist UI & Tooltip: Menghapus teks pada seluruh tombol aksi tabel (View, Edit, Delete, PDF) dan menggantinya dengan ikon + *hover* tooltip. *(Selesai)*
- Buat Modal Pop-up Approval Brief (agar Leader bisa mengisi input tipe TEXT untuk note revisi tanpa pindah halaman). *(Belum dibuat)*
- Buat halaman Kalender Posting menggunakan library FullCalendar.js. *(Menu sudah ada, halaman belum)*
- Desain Form Live URL Posting di modul postings. *(Belum dikustomisasi)*

**Role 4 (QA / Analytics) — [STATUS: SELESAI / COMPLETED]**
- QA Testing Workflow: Lakukan simulasi pergantian status secara berurutan: Staff buat Brief ➔ Leader Reject (cek log) ➔ Staff Edit Brief ➔ Leader Approve ➔ Staff masuk ke form Draft. *(13/13 automated test suite dan live browser audit selesai)*

### SPRINT 4: Apify Scraper & Analytics Dashboard (Hari 17-25) — [STATUS: NOT STARTED]
**Goal:** Fitur penarikan data performa berjalan di background dan muncul di grafik Leaderboard.

**Role 2 (Backend / Logic)**
- Buat skrip CLI Cron Job (Apify_service.php): Ambil data live_url dari tabel postings yang belum discrape.
- Tembak API Apify, tangkap JSON-nya, masukkan status antrean ke tabel apify_jobs.
- Saat hasil scraping sukses, INSERT hasilnya (Views, Likes, Comments, Saves, Shares) ke tabel posting_metrics.
- Hitung rumus engagement_rate dan cpv di backend lalu simpan hasilnya.
- Hitung akumulasi Lifetime Score KOL dan simpan ke score_histories.

**Role 3 (Frontend / UI)**
- Tambahkan ikon indikator status scraping di tabel Posting (Contoh: Spinner Loading jika apify_jobs.status = RUNNING).

**Role 4 (QA / Analytics)**
- Buat Controller dan UI Custom Executive Dashboard.
- Query SQL rumit: Tarik data dari posting_metrics untuk menampilkan visualisasi Chart.js (Total Budget vs Aktual Views).
- Buat Tabel Leaderboard CPV (KOL paling murah per view-nya).
- Siapkan data Dummy skala besar (insert manual ke posting_metrics) untuk memvalidasi apakah grafik Chart.js merender data dengan benar.

---

## BAGIAN 4: STRUKTUR MENU SIDEBAR, FUNGSI & ISINYA

Berikut adalah panduan lengkap arsitektur UI (*Sidebar AdminLTE*) beserta *field* data apa saja yang ada di dalamnya.

### 1. LABEL: DASHBOARD
Kelompok menu ini memuat visualisasi data untuk keputusan manajerial.

#### 1.1 Menu: Utama
* **Fungsi:** Halaman pertama yang dilihat saat login. Menampilkan *helicopter view* dari seluruh performa.
* **Isi Halaman (Konten):**
 - **Widget Cards:** Total Campaign Aktif, Total Budget Dikeluarkan, Total Views Keseluruhan, Rata-rata Engagement Rate (ER).
 - **Grafik (Chart.js):** Grafik batang/garis pengeluaran vs jumlah views per bulan.

#### 1.2 Menu: Leaderboard
* **Fungsi:** Tabel peringkat efisiensi KOL (Cost per View / CPV) dan alat banding (*Comparison*).
* **Isi Halaman (Konten):**
 - Tabel peringkat KOL (Nama KOL, Kategori, Total Views, Average ER, CPV Aktual).
 - Indikator "Above Average", "On Average", "Below Average" (*Auto-insight*).

---

### 2. LABEL: CAMPAIGN
Kelompok menu operasional harian tempat alur kerja utama (Step 2 - 9) terjadi.

#### 2.1 Menu: Master
* **Fungsi:** Tempat Leader merancang campaign baru secara garis besar (Alur Step 2).
* **Isi Form/Tabel:** Nama Campaign, Total Budget, Start Date, End Date, SOW General (Teks), dan **Multi-select Varian Parfum**.

#### 2.2 Menu: Assign KOL
* **Fungsi:** Tempat Staff mendaftarkan KOL spesifik ke sebuah campaign, memasukkan harga kesepakatan, dan resi logistik (Alur Step 3, 4, 5).
* **Isi Form/Tabel:**
 - Pilihan Campaign, Pilihan KOL (hanya yang sudah Approved).
 - *Multi-select* Varian Parfum khusus KOL tersebut.
 - SOW Outline (Teks / Upload File).
 - Deal Rate Total, **Jumlah VT** (Otomatis men-generate task di menu selanjutnya), Rate per VT.
 - Status Pengiriman Barang (Pending/Shipped/Delivered), Nomor Resi, Tanggal Kirim.

#### 2.3 Menu: Tasks (Daftar Keseluruhan VT)
* **Fungsi:** Menampilkan daftar keseluruhan task/VT dari seluruh campaign yang berjalan, yang di-generate otomatis pada langkah sebelumnya.
* **Isi Form/Tabel:**
 - Detail Campaign, Nama KOL, Nomor VT, Status (Draft/Approved/Scheduled/Posted).
 - Fitur ubah status atau update resi (tergantung *role* pengguna).

#### 2.4 Menu: Briefing
* **Fungsi:** Tempat Staff meng-input detail *brief* dan Leader melakukan persetujuan per-postingan (VT) (Alur Step 6, 7).
* **Isi Form/Tabel:**
 - Urutan VT (VT 1, VT 2, dst).
 - Estimasi Tanggal Posting.
 - Teks Brief, Upload File Brief Tambahan.
 - **Status Approval:** (Pending, Approved, Revision Needed, Rejected).
 - Tombol Action untuk Leader (Memunculkan *Modal Pop-up* untuk mengisi **Catatan Revisi**).

#### 2.5 Menu: Konten
* **Fungsi:** Tempat Staff mengumpulkan draf konten dari KOL, lalu memasukkan *link* terbit agar sistem men-scrape datanya (Alur Step 8, 9).
* **Isi Form/Tabel:**
 - Draft Content URL (Link Google Drive).
 - Draft Caption, Wajib Hashtag, Notes tambahan.
 - **Post URL** (Link asli TikTok/IG yang sudah tayang).
 - Status Scraping (Idle, Pending, Success, Failed) *Berjalan di background*.

#### 2.6 Menu: Kalender Posting
* **Fungsi:** Kalender visual raksasa (*FullCalendar.js*) sebagai pengingat jadwal posting.
* **Isi Halaman (Konten):**
 - Tampilan Kalender Bulanan/Mingguan.
 - Menampilkan blok warna berdasarkan *Estimasi Tanggal Posting* dari setiap VT.
 - Filter berdasarkan Campaign, KOL, atau Platform.

---

### 3. LABEL: DATABASE
Kelompok menu untuk pencatatan profil kandidat KOL.

#### 3.1 Menu: Data KOL
* **Fungsi:** Katalog lengkap semua profil KOL (Alur Step 1).
* **Isi Form/Tabel:**
 - Nama Lengkap, Username, Platform (TikTok, IG, YT).
 - Followers Count, Tier (Otomatis dari followers: Nano/Micro/Mid/Macro/Mega).
 - Rate Card Asking, Kontak Person (No. HP, Email, Alamat, Info Bank).
 - **Status KOL Approval:** (Pending, Approved, Rejected) beserta Catatan Penolakan.

#### 3.2 Menu: Rate Card
* **Fungsi:** Log dan direktori sentral pencatatan harga aktual KOL di luar harga *asking rate*.
* **Isi Form/Tabel:**
 - KOL, Harga Final yang disepakati, Tanggal Deal, dan Catatan Negosiasi.

---

### 4. LABEL: PENGATURAN (Dropdown Treeview)
Master data referensi dan pengaturan (Hanya untuk Super Admin). Semua menu bawaan Cicool dan data pendukung yang tidak masuk ke alur utama disimpan di sini secara terstruktur.

#### 4.1 Menu Dropdown: Referensi
* **↳ Sub-menu: Kategori KOL**
 * *Isi:* Nama Kategori (Beauty, Lifestyle, Perfume), Slug.
* **↳ Sub-menu: Varian Parfum**
 * *Isi:* Nama Varian, SKU, Deskripsi, Upload Foto Botol Parfum.
* **↳ Sub-menu: Tier KOL**
 * *Isi:* Data referensi klasifikasi tier follower KOL (Nano, Micro, dll).
* **↳ Sub-menu: Platform KOL**
 * *Isi:* Data referensi platform media sosial utama (TikTok, Instagram, dll).

#### 4.2 Menu Dropdown: Hak Akses
* **↳ Sub-menu: User** (Daftar akun tim internal).
* **↳ Sub-menu: Group** (Membuat grup role: Super Admin, Leader, Staff).
* **↳ Sub-menu: Akses** (Mengatur hak akses dan role system).
* **↳ Sub-menu: Permission** (Mengatur akses menu tertentu).
* **↳ Sub-menu: API Key** (Manajemen kredensial API).

#### 4.3 Menu Dropdown: Sistem
* **↳ Sub-menu: Pengaturan Umum** (Mengatur Logo, Nama Aplikasi, API Key, dll).

#### 4.4 Menu Dropdown: Developer
* **↳ Sub-menu: CRUD, API, Page, Form Builder, Menu, Ekstensi**
 * *Fungsi:* Kumpulan generator bawaan Cicool untuk manajemen backend dan UI.

#### 4.5 Menu Dropdown: Dokumentasi
* **↳ Sub-menu: Doc Web & Doc API**
 * *Fungsi:* Panduan teknis otomatis yang disediakan oleh sistem.

#### 4.6 Menu Dropdown: Data Relasi
* **↳ Sub-menu: Pivot Kategori, Pivot Platform, Pivot Varian**
 * *Fungsi:* Tabel relasi/pivot yang berjalan di latar belakang (Biasanya diakses langsung dari form input utama, namun disiapkan menunya untuk keperluan *maintenance* admin).

---

*Dokumen ini adalah Hak Milik Tim Capstone Universitas Harkat Negeri.*