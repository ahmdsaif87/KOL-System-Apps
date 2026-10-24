# 📋 Task Assignment: Input & Sortir Rate Card KOL (4 Tipe Konten)

## 📌 Metadata & Distribusi Repository
* **Fitur:** Rate Card Numerik & Sorting Harga KOL
* **Assignee:** 1 Fullstack Developer
* **Stack:** PHP (CodeIgniter 3 HMVC), MySQL/MariaDB (Shared Database: `KOL`), jQuery / AdminLTE

### 🗺️ Pemetaan Aplikasi (Multi-App Environment):
1. **`KOL-System-card-generator-plugin`** ➔ **KOL Portal & Master Data Hub** (Tempat akun KOL login, menu "Rate Saya", my tasks, performance card).
2. **`KOL-System`** ➔ **Admin / Operational System** (Tempat admin operasional mengelola campaign, data KOL dealing, dan rekomendasi).
3. **Database** ➔ **Shared DB (`KOL`)**, kedua aplikasi membaca dan menulis ke database yang sama.

---

## 🎯 1. Latar Belakang & Tujuan
Sebelumnya, rate card KOL hanya berupa dokumen statis (upload file PDF/gambar). Hal ini menyulitkan tim operasional saat proses *dealing* dan *budgeting*, karena admin harus membuka file PDF satu per satu tanpa bisa menyortir KOL termurah secara langsung.

**Tujuan Fitur Ini:**
1. Memberikan akses bagi KOL untuk **menginput & mengupdate harga layanannya sendiri** melalui portal dalam bentuk angka numerik (~30 detik pengisian).
2. Memfasilitasi Admin/Operasional agar bisa **menyortir (termurah - termahal) dan memfilter** harga KOL langsung di tabel Data KOL & modul Rekomendasi saat mencocokkan *budget campaign*.

---

## 🔄 2. Workflow Alur Sistem

```
[KOL Portal: Halaman "Rate Saya"]
       │
       ├─► Menampilkan Arsip PDF Rate Card Lama (jika ada, tetap disimpan)
       ├─► Mengisi 4 Kotak Harga (VT, Live, Long Form, Unboxing) dalam format rupiah
       ▼ (Klik "Simpan Perubahan")
[Update Langsung ke Database (Tabel `kols`)]
       │
       ├─► Status: LANGSUNG AKTIF (Tanpa perlu approval admin)
       ├─► Keamanan: Hanya bisa mengedit data miliknya sendiri (berdasarkan session login)
       ▼
[Admin Panel: Data KOL & Rekomendasi]
       │
       ├─► Tabel menampilkan 4 kolom harga angka
       ├─► Admin klik header kolom untuk sortir ASC/DESC (termurah ➔ termahal)
       └─► Filter Dealing: Filter KOL berdasarkan budget & tipe konten tertentu
```

---

## 🛠️ 3. Step-by-Step Task Breakdown

### 🗄️ STEP 1: Database Schema & Migration Rule
* **Target Repo:** `KOL-System/kol.sql` dan `KOL-System-card-generator-plugin/kol.sql` (keduanya tersinkron ke shared DB `KOL`)
> ⚠️ **PERINGATAN KERAS REGULASI DB:**  
> - **JANGAN** membuat file migrasi baru di `application/migrations/`.  
> - **JANGAN** mengedit file `kol.sql` secara manual menggunakan text editor.  
> - Jalankan query SQL di MySQL lokal, kemudian lakukan `mysqldump` untuk menimpa file `kol.sql` di repo.

1. **Jalankan Query ALTER TABLE pada database lokal:**
   ```sql
   ALTER TABLE `kols`
     ADD COLUMN `price_vt` DECIMAL(15,2) NULL DEFAULT NULL AFTER `rate_card_file`,
     ADD COLUMN `price_live` DECIMAL(15,2) NULL DEFAULT NULL AFTER `price_vt`,
     ADD COLUMN `price_long_form` DECIMAL(15,2) NULL DEFAULT NULL AFTER `price_live`,
     ADD COLUMN `price_unboxing` DECIMAL(15,2) NULL DEFAULT NULL AFTER `price_long_form`;
   ```

2. **Eksekusi mysqldump ke file `kol.sql`:**
   ```bash
   mysqldump -u <db_user> -p <db_name> > kol.sql
   ```
   *Pastikan git diff pada `kol.sql` hanya mencakup penambahan 4 kolom tersebut.*

---

### 👤 STEP 2: KOL Portal — Halaman "Rate Saya" (Input KOL)
* **Aplikasi Target:** 📂 **`KOL-System-card-generator-plugin`** (Portal KOL)
* **Target Modul:** Modul profil / portal KOL (atau buat modul/menu `my_rates` di bawah kelompok menu KOL Portal seperti `my_tasks`, `open_campaigns`, dll).
* **Sidebar Menu:** Tambahkan menu "Rate Saya" di sidebar portal KOL (`is_kol_user()`).

1. **Komponen Tampilan (UI):**
   * **Arsip PDF Lama:** Di bagian atas form, tetap sediakan kotak/banner arsip file rate card lama jika kolom `rate_card_file` terisi:
     ```html
     <!-- Contoh Tampilan Banner Arsip -->
     <div class="alert alert-info">
       <i class="fa fa-file-pdf-o"></i> <strong>Arsip Rate Card:</strong> 
       <a href="<?= base_url('uploads/kols/' . $kol->rate_card_file); ?>" target="_blank" class="btn btn-xs btn-default">Lihat Dokumen PDF</a>
       <small class="text-muted d-block mt-1">*Dokumen ini adalah arsip sebelumnya. Untuk pembaruan harga aktif, gunakan form angka di bawah.</small>
     </div>
     ```
   * **Form 4 Kotak Input Harga:**
     * `Harga VT` (Video TikTok / Reels / Shorts)
     * `Harga Live` (Live Streaming)
     * `Harga Long Form` (YouTube / Video Durasi Panjang)
     * `Harga Unboxing` (Review / Unboxing Produk)
   * **Masking Format Rupiah:** Gunakan plugin input mask atau JavaScript auto-format ribuan saat user mengetik (contoh input: `4500000` ➔ terformat: `4.500.000`).

2. **Aturan Bisnis & Validasi:**
   * **Universal / Flat per KOL:** Pengisian berlaku umum untuk profil KOL, tidak dipisah per platform (TikTok/IG/YT) agar proses input selesai dalam 30 detik.
   * **Opsional (Nullable):** Jika KOL tidak menyediakan layanan tertentu (misal: tidak melayani Live), input boleh dikosongkan dan disimpan sebagai `NULL`.
   * **Langsung Aktif (No Approval):** Ketika tombol **"Simpan Perubahan"** diklik, data langsung tersimpan ke database dan seketika terbaca oleh admin tanpa menunggu verifikasi/approval.

3. **Keamanan & Otorisasi (Security Guard):**
   * Ambil identitas KOL murni dari session login (`$this->aauth->get_user()->id` atau binding `kol_id`).
   * **Tolak akses manipulasi:** Pastikan query update memvalidasi kepemilikan (`WHERE id = $session_kol_id`). Dilarang menerima parameter `kol_id` bebas dari `$_POST` atau `$_GET` tanpa pengecekan otorisasi.

---

### 🖥️ STEP 3: Admin Data KOL — Tampilan & Sorting Tabel
* **Aplikasi Target:** 📂 **`KOL-System`** (Admin & Operational Utama)  
  *(Catatan: Karena `KOL-System-card-generator-plugin` juga memiliki modul `kols`, view di sana juga bisa disesuaikan agar seragam).*
* **Target File di `KOL-System`:**
  * `KOL-System/modules/kols/views/backend/standart/administrator/kols/kols_list.php`
  * `KOL-System/modules/kols/controllers/backend/Kols.php`
  * `KOL-System/modules/kols/models/Model_kols.php`

1. **Penyesuaian Kolom Tabel:**
   * Ubah kolom yang sebelumnya hanya menampilkan tombol icon PDF menjadi 4 kolom harga:
     * `Rate VT`
     * `Rate Live`
     * `Rate Long Form`
     * `Rate Unboxing`
   * *(Catatan: Icon PDF kecil tetap dapat disematkan di samping nama KOL jika file arsip PDF ada).*

2. **Format Tampilan Angka:**
   * Tampilkan angka dalam format ringkas dan mudah dibaca:
     * Contoh nilai `4500000` ➔ `4,5 jt` (atau `Rp 4.500.000` pada tooltip).
     * Jika bernilai `NULL` atau belum diisi ➔ tampilkan tanda `-` (abu-abu).

3. **Fitur Sorting (Urutkan Termurah - Termahal):**
   * Jadikan judul kolom di header tabel dapat diklik untuk sorting `ASC` dan `DESC`.
   * Daftarkan kolom baru (`price_vt`, `price_live`, `price_long_form`, `price_unboxing`) ke dalam `$field_search` atau konfigurasi orderable pada DataTables/Model.
   * **Handling Nilai NULL:** Pastikan saat disortir `ASC` (termurah), baris dengan nilai `NULL` tidak muncul di urutan paling atas, melainkan ditaruh di urutan paling akhir:
     ```sql
     ORDER BY ISNULL(price_unboxing) ASC, price_unboxing ASC
     ```

---

### 🎯 STEP 4: Admin Rekomendasi KOL — Filter & Dealing Cepat
* **Aplikasi Target:** 📂 **`KOL-System`** (Eksklusif di `KOL-System` karena modul Rekomendasi berada di sini)
* **Target File:**
  * `KOL-System/modules/kols/views/backend/standart/administrator/kols/kols_rekomendasi.php`
  * `KOL-System/modules/kols/controllers/backend/Kols.php` (method `rekomendasi()` dan `generate_recommendations_ajax()`)

1. **Tampilan Harga pada Hasil Rekomendasi:**
   * Pada kartu (*card*) atau baris rekomendasi KOL, tampilkan rincian 4 jenis rate:
     `VT: Rp 4,5jt | Live: Rp 3jt | Long Form: Rp 10jt | Unboxing: Rp 4,5jt`.

2. **Filter Berdasarkan Tipe Konten & Budget:**
   * Sediakan filter pencarian:
     * Dropdown: **Tipe Layanan** (`VT`, `Live`, `Long Form`, `Unboxing`).
     * Input Angka: **Maksimal Budget (IDR)** (contoh: `7.000.000`).
   * Saat filter dijalankan, sistem menyaring KOL yang harga layanannya `<= Maksimal Budget` dan otomatis mengurutkan dari harga terendah.

3. **Simulasi Skenario Penggunaan:**
   * **Kebutuhan Campaign:** Membutuhkan 2 talent untuk konten *Unboxing* dengan plafon budget per talent Rp 7.000.000.
   * **Aksi Admin:** Pilih filter *Layanan: Unboxing* & *Max Budget: 7.000.000* ➔ Klik Sortir Termurah.
   * **Hasil:** Sistem menampilkan urutan termurah, misalnya:
     1. *Mamank Kuliner* — Rp 4.500.000
     2. *Jessica Jane* — Rp 6.000.000
     3. *(KOL lain di atas 7jt otomatis tersaring keluar)*.
   * Tim operasional dapat langsung mengeksekusi dealing tanpa membuka PDF satu per satu.

---

## ✅ 4. Kriteria Pengujian & Definition of Done (DoD)

| No | Komponen Pengujian | Skenario Uji | Hasil yang Diharapkan | Status |
|---|---|---|---|---|
| 1 | **Database** | Cek struktur tabel `kols` dan file `kol.sql`. | Kolom `price_vt`, `price_live`, `price_long_form`, `price_unboxing` ada; `kol.sql` terupdate via dump. | [ ] |
| 2 | **KOL Input** | Login sebagai akun KOL ➔ buka halaman *Rate Saya* ➔ isi 4 kotak harga ➔ klik *Simpan*. | Data tersimpan, notifikasi sukses muncul, data langsung aktif tanpa status pending/approval. | [ ] |
| 3 | **Arsip PDF** | Akun KOL yang memiliki file PDF lama membuka form. | Tautan/preview PDF lama tetap terlihat dan bisa diunduh/dibuka. | [ ] |
| 4 | **Keamanan Data** | Coba kirim request POST update dengan ID milik KOL lain. | Request ditolak (`403 Forbidden` / error validasi kepemilikan). | [ ] |
| 5 | **Data KOL Table** | Admin membuka halaman Data KOL (`/administrator/kols`). | 4 kolom harga tampil dengan rapi (format rupiah / jt). Data kosong tampil sebagai `-`. | [ ] |
| 6 | **Admin Sorting** | Klik header kolom `Rate Unboxing` (ASC). | Data terurut dari harga terendah ke tertinggi; data NULL berada di paling bawah. | [ ] |
| 7 | **Rekomendasi** | Filter tipe *Unboxing* dengan batas budget Rp 7.000.000. | Menampilkan KOL dengan rate <= 7jt dan terurut mulai dari yang termurah. | [ ] |
