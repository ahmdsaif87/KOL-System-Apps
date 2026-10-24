# Project Brief: Modul Evaluasi & Visualisasi Performa Internal (KOL System)

**Dokumen:** Spesifikasi Kebutuhan & Desain Solusi (Feature Brief)  
**Target Pembaca:** Tim Developer / Partner Teknis / Management  
**Bahasa:** Bahasa Indonesia  
**Status:** Ready for Review / Discussion  

---

## 1. Latar Belakang & Tujuan

Saat ini sistem KOL sudah memiliki pencatatan kampanye, deal negosiasi, review draft, pengiriman sampel, dan invoice. Namun, belum ada modul terpusat untuk **mengevaluasi dan memvisualisasikan performa kerja tim internal**.

Client menginginkan adanya **Modul Evaluasi Internal** yang berfungsi sebagai dashboard KPI dan evaluasi kinerja untuk 3 peran utama:
1. **Leader KOL (Lead Sourcing & Strategi Kampanye)**
2. **Operational (Eksekusi Task, Review Konten, dan Logistik)**
3. **Admin (Tata Kelola Finansial, SLA Invoice, dan Manajemen Sistem)**

Secara khusus, client juga menekankan kebutuhan **visualisasi target biaya vs hasil nyata**, yaitu **CPV (Cost Per View)** dan **CPE (Cost Per Engagement)** per kampanye dan per PIC/Leader.

---

## 2. Pemetaan Peran & Metrik Evaluasi (KPI)

### A. Leader KOL (Sourcing Strategy & Cost Efficiency)
Leader bertanggung jawab atas pemilihan KOL, negosiasi harga deal, pencapaian target view/engagement, dan efisiensi budget kampanye.

* **Metrik Utama (KPI):**
  1. **CPV Target vs Actual**: Efisiensi biaya per view (Apakah hasil view tercapai di bawah atau di atas batas target biaya per view yang diminta client).
  2. **CPE Target vs Actual**: Efisiensi biaya per interaksi (Like, Comment, Share, Save).
  3. **Tingkat Keberhasilan Negosiasi (Deal Conversion Rate)**: Persentase KOL yang diajak kolaborasi dan berhasil deal (`ASSIGNED`/`ACTIVE` vs `NEGOTIATION`/`REJECTED`).
  4. **Pencapaian Target Kampanye (Target Attainment Rate)**: Persentase pencapaian total views dan ER yang dihasilkan kampanye dibanding target awal di brief client.
  5. **Efisiensi Budget**: Total pengeluaran aktual deal KOL dibandingkan alokasi total budget kampanye.

---

### B. Operational (Kecepatan Eksekusi, SLA Konten & Logistik)
Tim Operational memegang eksekusi harian: memastikan KOL menerima sampel produk tepat waktu, review draft konten cepat, dan konten tayang sesuai jadwal.

* **Metrik Utama (KPI):**
  1. **Kepatuhan Jadwal Tayang (On-Time Posting SLA)**: Persentase konten yang live tepat waktu pada atau sebelum estimasi jadwal posting (`tasks.estimated_posting_date`).
  2. **Kecepatan Review Draft (Turnaround Time)**: Rata-rata jam atau hari yang dibutuhkan tim operasional untuk memberikan feedback / approve sejak KOL mengunggah draft.
  3. **Kecepatan Kirim Sampel (Sample Dispatch Speed)**: Selisih hari dari deal disepakati hingga resi pengiriman dibuat (`kol_shipments.shipped_at`).
  4. **Rasio Putaran Revisi (Draft Revision Cycle)**: Rata-rata jumlah revisi draft per task (semakin sedikit revisi, semakin jelas briefing awal tim operasional).
  5. **Volume Task Selesai**: Jumlah task yang berhasil diselesaikan per staff per bulan (`status = 'DONE'`).

---

### C. Admin (SLA Keuangan, Kepatuhan Pajak & Kualitas Database)
Admin mengelola operasional makro, administrasi kontrak, persetujuan invoice kreator, dan pemeliharaan database.

* **Metrik Utama (KPI):**
  1. **Kecepatan Proses Invoice (Invoice Processing SLA)**: Rata-rata hari dari saat invoice di-submit KOL sampai di-approve/dibayarkan.
  2. **Ketepatan Jatuh Tempo Pembayaran**: Persentase invoice yang dibayar sebelum tanggal jatuh tempo (`invoices.due_date`).
  3. **Kepatuhan Pajak & Administrasi**: Jumlah invoice atau bukti potong pajak (PPh 21 / PPh 23) yang tuntas tanpa revisi/penolakan.
  4. **Pertumbuhan & Keamanan Database KOL**: Jumlah KOL baru yang diverifikasi dan diaudit kualitas followernya (menggunakan Fake Follower Score).

---

## 3. Fitur Khusus: Visualisasi Target CPV & CPE

### Mengapa CPV & CPE Penting?
* **CPV (Cost Per View)** = Total Biaya Dibayar $\div$ Total Views Video yang Didapat.
  * *Target CPV*: Biaya maksimal per view yang disetujui client (misal: target Rp 50 / view).
  * *Aktual CPV*: Biaya riil yang terjadi di lapangan. **Semakin rendah nilainya, semakin hemat dan efektif kampanye tersebut.**
* **CPE (Cost Per Engagement)** = Total Biaya Dibayar $\div$ Total Interaksi (Like + Komen + Share + Simpan).

### Desain Visualisasi yang Disarankan di Dashboard:

1. **Gauge Dial "Cost Efficiency Meter" (Per Kampanye / Per PIC)**
   * Menampilkan jarum penunjuk posisi Aktual CPV terhadap Target CPV:
     * **Zona Hijau (Hemat / Beat Target)**: Jika Aktual CPV lebih murah daripada Target (contoh: Target Rp 50, Aktual Rp 38 $\to$ **Hemat 24%**).
     * **Zona Merah (Over-Budget / Inefisien)**: Jika Aktual CPV lebih mahal daripada Target (contoh: Target Rp 50, Aktual Rp 75 $\to$ **Boncos 50%**).
2. **Leaderboard Efisiensi Biaya per PIC / Leader**
   * Menampilkan ranking Lea1dder KOL / PIC Kampanye berdasarkan seberapa hemat dan tepat sasaran mereka mengelola dana client:
     * Kolom: *Nama PIC*, *Jumlah Kampanye*, *Total Views Didapat*, *Target CPV Rata-rata*, *Aktual CPV Rata-rata*, *Status Skor Nilai (% Efisiensi)*.
3. **Scatter Matrix "Value vs Reach" (Grafik Kuadran 4 Kotak)**
   * Sumbu X: Biaya CPV (kiri = murah, kanan = mahal).
   * Sumbu Y: Total Views yang dihasilkan (atas = tinggi, bawah = rendah).
   * **Kuadran Kiri-Atas (Bintang)**: Views besar dengan biaya per view murah $\to$ Kampanye paling sukses.
   * **Kuadran Kanan-Bawah**: Views kecil dengan biaya mahal $\to$ Perlu evaluasi pemilihan KOL.
4. **Drill-down Tabel Per-KOL dalam Kampanye**
   * Tabel detail per kreator dalam kampanye untuk melihat siapa KOL penyumbang CPV termurah vs siapa yang overprice:
     * *Contoh KOL A*: Bayar Rp 2.000.000, hasil 120.000 Views $\to$ **CPV Rp 16,6 (Sangat Efisien)**.
     * *Contoh KOL B*: Bayar Rp 5.000.000, hasil 25.000 Views $\to$ **CPV Rp 200,0 (Tidak Efisien)**.

---

## 4. Struktur Antarmuka & Navigasi Modul

Modul ini dapat diakses pada menu sidebar: **Evaluasi Internal** (dengan izin akses khusus Management/Superadmin), terbagi menjadi 4 tab:

```
[ Menu: Evaluasi Kinerja Internal ]
 ├── [Tab 1] Ringkasan Eksekutif (Ringkasan KPI Perusahaan & Leaderboard Bulanan)
 ├── [Tab 2] Performa Leader KOL (Target CPV/CPE, Budget Variance & Delivery Rate)
 ├── [Tab 3] Performa Operasional (SLA Jadwal Tayang, Kecepatan Review & Logistik)
 └── [Tab 4] Performa Admin & Finansial (SLA Pembayaran Invoice & Tata Kelola)
```

### Komponen Filter di Tiap Tab:
* **Filter Periode**: Bulan Ini, Bulan Lalu, Kuartal Ini, atau Custom Tanggal.
* **Filter Brand / Kampanye**: Melihat performa tim untuk client/brand tertentu.
* **Filter Individu PIC**: Memilih profil staff tertentu untuk melihat kartu rapor kinerjanya.

---

## 5. Ketersediaan Data di Database (Sudah Siap Pakai)

Kelebihan utama arsitektur sistem ini adalah **sebagian besar kolom dan tabel pendukung sudah tersedia di database**, sehingga pengembangan modul ini fokus pada query analitik dan antarmuka visual (UI/Chart), tanpa perlu membongkar database:

| Kebutuhan Visualisasi | Tabel & Kolom di Database Saat Ini |
| :--- | :--- |
| **Target CPV & CPE Kampanye** | `campaigns.target_cpv`, `campaigns.target_cpe`, `campaigns.budget`, `campaigns.target_views`, `campaigns.target_er` |
| **PIC / Pemilik Kampanye** | `campaigns.pic_user_id`, `campaigns.created_by` |
| **Aktual Views, Engagement & CPV** | `posting_metrics.views`, `likes`, `comments`, `shares`, `saves`, `cpv`, `engagement_rate` |
| **Realisasi Jadwal Tayang Konten** | `tasks.estimated_posting_date`, `postings.actual_posted_at`, `postings.date_validation_status` ('ON_TIME', 'LATE') |
| **Kecepatan Review Draft** | `drafts.created_at`, `drafts.status`, `draft_review_histories.created_at` |
| **Kecepatan Pengiriman Sampel** | `campaign_kols.deal_date` vs `kol_shipments.shipped_at`, `delivered_at` |
| **SLA Pembayaran Invoice** | `invoices.created_at`, `invoices.approved_at`, `invoices.due_date`, `invoices.status` |

---

## 6. Rekomendasi Tahapan Pengerjaan (Roadmap)

1. **Fase 1: Modul Analitik Target CPV / CPE & Performa Leader KOL (High Priority Client)**
   * Pembuatan kalkulator agregat Target vs Realisasi CPV & CPE per kampanye dan per PIC Leader.
   * Visualisasi gauge efisiensi biaya dan leaderboard PIC.
2. **Fase 2: Scorecard Operasional (SLA Jadwal Tayang & Review Draft)**
   * Pembuatan indikator persentase konten on-time (`date_validation_status`).
   * Visualisasi waktu tunggu review draft dan status pengiriman produk sampel.
3. **Fase 3: Scorecard Admin & Finansial**
   * Monitoring SLA approval invoice dan ketepatan jatuh tempo pembayaran.
4. **Fase 4: Export Laporan Bulanan (PDF / Excel)**
   * Tombol satu-klik untuk mencetak rapor evaluasi performa bulanan tim internal untuk rapat manajemen atau laporan ke client.
