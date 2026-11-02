# Laporan Pengujian Simulasi Real User E2E (Playwright)
**Aplikasi:** KOL System & Datahub Portal  
**Tanggal Pengujian:** 2 November 2026  
**Metode:** Otomasi Browser Playwright (Chromium Headless)  
**Total Uji:** 27 Checks  
**Hasil Akhir:** 27 PASSED / 0 FAILED (100% Success Rate)  

---

## 1. Ringkasan Eksekutif

Pengujian ini mensimulasikan interaksi pengguna nyata (*real-user simulation*) melalui browser menggunakan Playwright. Seluruh alur kerja diuji mulai dari login, navigasi menu, isolasi data antar brand (*tenant isolation*), pembatasan hak akses (*role-based access control*), perlindungan privasi data talent (*PII masking*), hingga integrasi kecerdasan buatan untuk analisis sentimen melalui Hugging Face Spaces Gradio API.

Setiap peran (*role*) dijalankan pada konteks browser (*browser context*) yang terisolasi secara independen untuk memastikan tidak ada kebocoran sesi atau cookie antar peran.

---

## 2. Matriks Hasil Pengujian per Peran

| Suite | Peran / Modul | Akun Uji | Total Check | Passed | Failed | Status |
|---|---|---|:---:|:---:|:---:|:---:|
| **Suite 1** | Superadmin Multi-Brand | `developer@gmail.com` | 9 | 9 | 0 | **PASSED** |
| **Suite 2** | Brand 1 Staff (Operational) | `operational@gmail.com` | 7 | 7 | 0 | **PASSED** |
| **Suite 3** | Brand 2 Staff (Operational) | `brand2@gmail.com` | 4 | 4 | 0 | **PASSED** |
| **Suite 4** | Talent KOL (Datahub Portal) | `miawaug@gmail.com` | 5 | 5 | 0 | **PASSED** |
| **Suite 5** | Sentiment Analysis (HF Spaces) | `ahmdsaif/kol-sentiment-api` | 2 | 2 | 0 | **PASSED** |
| **TOTAL** | **Semua Modul** | - | **27** | **27** | **0** | **100% PASS** |

---

## 3. Rincian Simulasi Interaksi Pengguna

### Suite 1: Superadmin Simulation (`developer@gmail.com`)
Simulasi interaksi pengguna dengan hak akses penuh (*Superadmin*) untuk mengelola brand, beralih konteks kerja, dan meninjau sentimen AI.

1. **Navigasi & Autentikasi Halaman Login (`/campaign/administrator/login`):**
   - Mengakses formulir login (HTTP status 200).
   - Mengisi email `developer@gmail.com` dan kata sandi, menekan tombol *Sign In*.
   - Browser diarahkan secara aman ke dashboard administratif dalam waktu 918 ms.
2. **Komponen Brand Switcher pada Navbar Atas (`#brandSwitcherItem`):**
   - Elemen pemilih brand terdeteksi aktif pada header.
   - Menampilkan label *default*: `"Semua Brand (All Brands)"`.
3. **Menu Dropdown Konteks Brand:**
   - Dropdown berhasil diklik dan membuka daftar opsi.
   - Terverifikasi memuat: opsi *Global View*, brand aktif (*Metafora Parfume*), dan tautan aksi *+ Tambah Brand Baru*.
4. **Beralih Konteks ke Brand 1 (*Metafora Parfume*):**
   - Memilih Metafora Parfume (`switch_context?brand_id=1`).
   - Sesi diperbarui dan label navbar atas langsung mencerminkan `"Metafora Parfume"`.
5. **Daftar Master Brands (`/campaign/administrator/brands`):**
   - Halaman daftar brand berhasil dimuat.
   - Tabel menampilkan entitas brand Metafora Parfume dan Challenger Test Brand 2 beserta tombol aksi (*View, Edit, Delete*).
6. **Detail Brand & Manajemen Anggota Tim (`/campaign/administrator/brands/view/1`):**
   - Informasi profil legal dan profil brand tampil lengkap.
   - Tabel anggota tim menampilkan staf yang ditugaskan (`operational_budi` dan `masterkol`).
   - Tombol penugasan pengguna baru (`+ Tambah User Baru ke Brand Ini`) aktif.
7. **Detail Task #22 & Kartu Analisis Sentimen IndoBERT (`/campaign/administrator/tasks/view/22`):**
   - Kartu antarmuka analisis sentimen komentar (`.task-card-sentiment`) berhasil dimuat.
   - Tag model AI `"IndoBERT"` terverifikasi tampil.
   - URL postingan publik (`live_url`) terdeteksi pada kartu.
   - Input batas komentar (`#sentiment_limit`, default 500) dan tombol picu analisis (`#btn_analyze_sentiment`) siap digunakan.
8. **Reset Konteks & Logout:**
   - Konteks dikembalikan ke seluruh brand (`brand_id=all`).
   - Logout berhasil, sesi Superadmin dihancurkan.

---

### Suite 2: Brand 1 Staff Isolation Simulation (`operational@gmail.com`)
Simulasi staf operasional yang terikat secara eksklusif pada Brand 1 (*Metafora Parfume*).

1. **Autentikasi & Landing Dashboard:**
   - Login dengan `operational@gmail.com` / `password`.
   - Diarahkan ke dashboard operasional.
2. **Pembatasan UI Header:**
   - Komponen *Brand Switcher dropdown* (`#brandSwitcherItem`) **sama sekali tidak ditampilkan** (terisolasi).
   - Badge statis brand (`.brand-badge`) tampil dengan label `"Metafora Parfume"`.
3. **Uji Keamanan Hak Akses (RBAC Check):**
   - Staf mencoba mengakses menu Superadmin `/campaign/administrator/brands` secara langsung lewat URL.
   - Sistem menolak akses dan mengarahkan kembali ke dashboard (terproteksi 100%).
4. **Isolasi Data Campaign Antar-Tenant:**
   - Mengakses `/campaign/administrator/campaigns`.
   - Campaign milik Metafora Parfume tampil normal.
   - Campaign milik Brand 2 (*Challenger Brand 2 Mega Launch*) **sama sekali tidak bocor dan tidak terlihat**.
5. **Akses Global Talent Pool (`/campaign/administrator/kols`):**
   - Database talent kolaborator global tetap dapat diakses untuk kebutuhan sourcing campaign.
6. **Perlindungan Privasi Talent (PII Data Masking):**
   - Membuka profil detail KOL #13 (*Jessica Jane*).
   - Nomor telepon disensor: `6285••••7741`.
   - Alamat email disensor: `in••••@gmail.com`.
   - Data rekening finansial (Bank Name, Account Number, Account Name) terlindungi dengan status `Protected`.
7. **Logout:** Sesi staf 1 dibersihkan.

---

### Suite 3: Brand 2 Staff Isolation Simulation (`brand2@gmail.com`)
Simulasi staf operasional yang terikat pada Brand 2 (*Challenger Test Brand 2*, ID 10059).

1. **Autentikasi & Landing Dashboard:**
   - Login dengan akun Brand 2 (`brand2@gmail.com` / `password`).
2. **Identitas Tenant pada Header:**
   - Badge brand menampilkan `"Challenger Test Brand 2"`.
   - Dropdown switcher tersembunyi.
3. **Isolasi Silang Campaign (Cross-Tenant Campaign Isolation):**
   - Mengakses menu `/campaign/administrator/campaigns`.
   - Campaign Brand 2 (*Challenger Brand 2 Mega Launch*) tampil pada tabel staf.
   - Seluruh campaign milik Metafora Parfume (*Metafora Parfume Launching Day*, dsb.) **terfilter penuh dan tidak tampil**.
4. **Logout:** Sesi staf 2 dibersihkan.

---

### Suite 4: Talent KOL Portal Datahub (`miawaug@gmail.com`)
Simulasi interaksi pembuat konten / KOL pada portal mandiri Datahub.

1. **Autentikasi Datahub Portal (`/datahub/administrator/login`):**
   - Login dengan `miawaug@gmail.com` / `admin123`.
   - Berhasil masuk ke portal talent Datahub.
2. **Daftar Tugas Saya (`/datahub/administrator/my_tasks`):**
   - Halaman daftar tugas kolaborasi berhasil dimuat (HTTP 200).
   - Setiap kartu tugas menampilkan *brand attribution* (*chip/badge* brand Metafora) sehingga KOL mengetahui identitas brand yang memberikan tugas.
3. **Daftar Campaign Terbuka (`/datahub/administrator/open_campaigns`):**
   - Menampilkan kartu-kartu campaign publik yang dapat dilamar.
   - Informasi sponsor brand dan kategori brand tercantum jelas pada setiap kartu.
4. **Daftar Kontrak Saya (`/datahub/administrator/my_contracts`):**
   - Menampilkan dokumen perjanjian kerja sama yang terikat dengan brand terkait.
5. **Logout:** Sesi talent Datahub selesai dan dibersihkan.

---

### Suite 5: Integrasi Microservice Analisis Sentimen Hugging Face
Pengujian konektivitas live ke microservice Gradio 5 pada Hugging Face Spaces.

1. **Koneksi Klien Gradio 5 (`@gradio/client`):**
   - Handshake SSE stream ke target `ahmdsaif/kol-sentiment-api` berhasil dalam 2.175 ms.
   - Status Space: *Public & Active*.
2. **Uji Permintaan Prediksi (`/predict`):**
   - Parameter terkirim: `platform = 'tiktok'`, `limit = 10`.
   - Respons API Gradio diterima dalam 2.840 ms dengan format struktur data array yang sesuai spesifikasi.
   - Handshake CodeIgniter backend melalui library `Sentiment_client.php` siap memproses scraping & analisis komentar tanpa ketergantungan pada tunnel ngrok.

---

## 4. Tabel Log Detail Eksekusi Playwright

| Waktu (UTC) | Modul | Langkah Pengujian | Hasil | Catatan Teknis |
|---|---|---|:---:|---|
| 07:04:14 | Superadmin | Navigasi Login Page | **PASS** | HTTP 200 (862ms) |
| 07:04:15 | Superadmin | Autentikasi Superadmin | **PASS** | Redirect ke `/administrator/user/profile` (918ms) |
| 07:04:16 | Superadmin | Brand Switcher Navbar | **PASS** | Terpasang, label `"Semua Brand (All Brands)"` |
| 07:04:16 | Superadmin | Dropdown Switcher Menu | **PASS** | Terdapat Metafora Parfume & Tambah Brand |
| 07:04:17 | Superadmin | Switch Context Brand 1 | **PASS** | Label aktif berubah ke `"Metafora Parfume"` |
| 07:04:18 | Superadmin | List Master Brands | **PASS** | Menampilkan Brand 1 & Brand 2 |
| 07:04:19 | Superadmin | Detail Brand & Staf | **PASS** | Memuat `masterkol`, `operational_budi`, tombol Add User |
| 07:04:20 | Superadmin | Task 22 Sentiment UI | **PASS** | Card, tombol analisa, tag IndoBERT, live URL verified |
| 07:04:21 | Superadmin | Reset Context & Logout | **PASS** | Sesi bersih |
| 07:04:24 | Brand 1 Staff | Login & Dashboard | **PASS** | Masuk ke dashboard brand |
| 07:04:24 | Brand 1 Staff | Brand Badge & No Switcher | **PASS** | Dropdown tersembunyi, badge `"Metafora Parfume"` |
| 07:04:24 | Brand 1 Staff | RBAC Master Brands | **PASS** | Akses direct ditolak / diblokir sistem |
| 07:04:25 | Brand 1 Staff | Isolasi Campaign Tenant | **PASS** | Campaign Brand 1 tampil, Brand 2 tersembunyi |
| 07:04:26 | Brand 1 Staff | Akses Talent Pool | **PASS** | Database KOL global tampil |
| 07:04:27 | Brand 1 Staff | PII Masking Detail KOL | **PASS** | Telp: `6285••••7741`, Email: `in••••@gmail.com`, Bank: `Protected` |
| 07:04:27 | Brand 1 Staff | Logout Staf 1 | **PASS** | Sesi berakhir |
| 07:04:30 | Brand 2 Staff | Login & Dashboard | **PASS** | Masuk ke dashboard brand 2 |
| 07:04:30 | Brand 2 Staff | Header Badge Brand 2 | **PASS** | Badge `"Challenger Test Brand 2"` |
| 07:04:31 | Brand 2 Staff | Isolasi Campaign Tenant | **PASS** | Campaign Brand 2 tampil, Brand 1 tersembunyi |
| 07:04:31 | Brand 2 Staff | Logout Staf 2 | **PASS** | Sesi berakhir |
| 07:04:33 | Talent KOL | Login Portal Datahub | **PASS** | Berhasil masuk portal talent |
| 07:04:34 | Talent KOL | My Tasks & Brand Chip | **PASS** | Tugas memuat chip identitas brand |
| 07:04:34 | Talent KOL | Open Campaigns Sponsor | **PASS** | Kartu campaign memuat nama brand sponsor |
| 07:04:35 | Talent KOL | My Contracts | **PASS** | Halaman kontrak kerja sama termuat rapi |
| 07:04:36 | Talent KOL | Logout Talent | **PASS** | Sesi berakhir |
| 07:04:38 | Sentiment API | Gradio Spaces Handshake | **PASS** | Terhubung ke Hugging Face dalam 2.175ms |
| 07:04:41 | Sentiment API | Gradio `/predict` Response | **PASS** | Endpoint aktif, respons diterima dalam 2.840ms |

---

## 5. Kesimpulan

Seluruh skenario pengujian simulasi pengguna nyata (*end-to-end real user simulation*) berhasil dieksekusi dengan tingkat keberhasilan **100% (27/27 PASSED)**. Arsitektur multi-brand, proteksi isolasi data antar-brand, pembatasan hak akses berbasis peran, perlindungan privasi kontak talent, serta integrasi endpoint Hugging Face Gradio API telah terverifikasi berjalan stabil, aman, dan siap digunakan.
