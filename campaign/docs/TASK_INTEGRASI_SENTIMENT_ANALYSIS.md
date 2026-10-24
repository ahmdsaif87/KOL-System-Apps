# 📋 Task Assignment: Integrasi KOL Sentiment Analysis Microservice pada Task Details

## 🎯 Ringkasan Fitur
Menambahkan integrasi **KOL Sentiment Analysis Microservice** pada halaman **Task Details**. Fitur ini memungkinkan user/admin menekan tombol **"Analyze Sentiment"** untuk mengirim request ke microservice analisis sentimen (menggunakan model IndoBERT `Aardiiiiy/indoberttweet-base-indonesian-sentiment-analysis`). Microservice akan mendownload komentar dari TikTok/Instagram/YouTube via ScrapeCreators API, menganalisis sentimen, dan mengembalikan ringkasan ke sistem utama (CodeIgniter 3) untuk disimpan ke database dan ditampilkan pada halaman *Task Details*.

---

## 🔑 Konfigurasi & Detail API Microservice
* **Service Name:** KOL Sentiment Analysis Microservice (v1.0.0)
* **Base URL:** `https://precaudal-intraspinal-russel.ngrok-free.dev/`
* **Health Check Endpoint:** `GET https://precaudal-intraspinal-russel.ngrok-free.dev/health`
* **Main Analysis Endpoint:** `POST https://precaudal-intraspinal-russel.ngrok-free.dev/api/v1/analyze`
* **Authorization Header:** `Authorization: Bearer kol-system`
* **Header Tambahan:** `ngrok-skip-browser-warning: true` *(Wajib untuk cURL/Guzzle ke endpoint ngrok)*

---

## 🔄 Workflow Alur Sistem
```
[User di Task Details] 
       │
       ▼ (Klik tombol "Analyze Sentiment")
[AJAX Request] ──► [Backend CI3 Controller: Tasks/analyze_sentiment]
                         │
                         ├─► Fetch URL Postingan dari Task ID (TikTok / Instagram / YouTube)
                         ├─► Request POST /api/v1/analyze (Bearer Token: kol-system)
                         │
[Microservice IndoBERT] ◄┘ (Scrape via ScrapeCreators API -> IndoBERT Inference)
       │
[Response API] ──────────┐
       │                 ▼
       ├─► Simpan / Update Hasil ke DB (`task_sentiments`)
       │
       ▼ (Return JSON Success)
[Frontend Update UI] ──► Tampilkan Card Sentiment Summary, Highlight Komentar, Topik & Keywords
```

---

## 🛠️ Step-by-Step Task Breakdown

### 1. Database Migration & Schema Setup
* **Deskripsi:** Buat tabel baru `task_sentiments` untuk menyimpan cache/history hasil analisis sentimen per task.
* **Detail Kolom Tabel (`task_sentiments`):**
  * `id` (INT, Primary Key, Auto Increment)
  * `task_id` (INT, Foreign Key ke `tasks.id`, Unique index per task)
  * `post_url` (VARCHAR/TEXT, URL postingan yang di-scrape)
  * `total_comments` (INT, `total_comments_scraped`)
  * `positive_count` (INT, jumlah komentar positif)
  * `neutral_count` (INT, jumlah komentar netral)
  * `negative_count` (INT, jumlah komentar negatif)
  * `highlight_comments` (JSON/TEXT, array highlight positif, netral, negatif)
  * `frequent_keywords` (JSON/TEXT, array kata kunci populer)
  * `main_topics` (TEXT, `main_topics_discussed`)
  * `created_at` (DATETIME / TIMESTAMP)
  * `updated_at` (DATETIME / TIMESTAMP)

---

### 2. Service Integration & Configuration (Backend CodeIgniter 3)
* **Deskripsi:** Buat/tambahkan konfigurasi API dan helper / service class untuk memanggil Microservice API.
* **Spesifikasi Request API:**
  * **Method:** `POST`
  * **URL:** `https://precaudal-intraspinal-russel.ngrok-free.dev/api/v1/analyze`
  * **Headers:** 
    * `Content-Type: application/json`
    * `Authorization: Bearer kol-system`
    * `ngrok-skip-browser-warning: true`
  * **Body Payload:**
    ```json
    {
      "url": "https://www.tiktok.com/@user/video/xxx (atau link Instagram/YouTube)"
    }
    ```
* **Tugas Developer:**
  1. Simpan `BASE_URL` (`https://precaudal-intraspinal-russel.ngrok-free.dev/`) dan `BEARER_TOKEN` (`kol-system`) di file config (`application/config/sentiment.php` atau `config.php`).
  2. Buat method HTTP client menggunakan `cURL` PHP native / `GuzzleHTTP` lengkap dengan error handling (timeout, HTTP status 400, 401, 404, 422, 500).
  3. *(Opsional)* Buat pengecekan kesehatan service melalui endpoint `GET /health` sebelum mengirim request analisis.

---

### 3. Backend Controller & Model Implementation
* **Deskripsi:** Tambahkan method AJAX endpoint di controller `Tasks` untuk memproses request trigger dari frontend.
* **Tugas Developer:**
  1. **Model:** Buat `Model_task_sentiments` untuk relasi tabel `task_sentiments` dengan fungsi `get_by_task_id()` dan `save_or_update()`.
  2. **Controller Endpoint:** `POST tasks/analyze_sentiment/$task_id`
     * Ambil data task & URL postingan berdasarkan `$task_id`.
     * Panggil Sentiment Analysis Microservice (`POST /api/v1/analyze`).
     * Jika sukses (`status == 'success'`), parse data response lalu simpan/update ke database `task_sentiments`.
     * Kembalikan response JSON ke frontend:
       ```json
       {
         "status": true,
         "message": "Analisis sentimen berhasil diperbarui!",
         "data": { ... }
       }
       ```

---

### 4. UI & Visualisasi di View (`tasks_view.php`)
* **Deskripsi:** Tambahkan tombol pemicu dan widget visualisasi data di halaman Task Details (`modules/tasks/views/backend/standart/administrator/tasks/tasks_view.php`).
* **Komponen Frontend yang Dibutuhkan:**
  1. **Action Button:**
     * Tombol *"Analyze Sentiment"* (dilengkapi spinner loading saat diproses & status disabled).
  2. **Sentiment Summary Widget:**
     * Badge/Stat-card angka: **Total Comment**, **Positif**, **Netral**, **Negatif** (dilengkapi progress bar/persentase visual).
  3. **Main Topics & Keywords:**
     * Tag/Badge pills untuk `frequent_keywords`.
     * Teks ringkasan untuk `main_topics_discussed`.
  4. **Highlight Comments Section:**
     * Tabbed container / grup list komentar highlight yang terbagi menjadi 3 tab: **Positif (Hijau)**, **Netral (Abu-abu/Biru)**, dan **Negatif (Merah)**.

---

### 5. Error Handling & Edge Cases
* **Tugas Developer:**
  * **URL Kosong:** Validasi dan berikan alert jika task belum memiliki link postingan yang valid.
  * **API Fail / Error Response:** Tampilkan toast notification jika API eksternal merespons error (HTTP 401/404/422/500 atau timeout).
  * **State Indicator:** Tampilkan indikator loading / spinner selama proses pemanggilan AJAX berjalan.

---

## 📄 Lampiran: Contract Response Microservice API

```json
{
  "status": "success",
  "data": {
    "url": "string",
    "total_comments_scraped": 0,
    "sentiment_summary": {
      "positive": 0,
      "neutral": 0,
      "negative": 0
    },
    "highlight_comments": {
      "positive": [
        "string"
      ],
      "negative": [
        "string"
      ],
      "neutral": [
        "string"
      ]
    },
    "frequent_keywords": [
      "string"
    ],
    "main_topics_discussed": "string"
  }
}
```

---

## ✅ Kriteria Selesai (Definition of Done)
- [ ] Table Migration `task_sentiments` berhasil dibuat di database.
- [ ] Tombol *"Analyze Sentiment"* muncul dan berfungsi di halaman Task Details.
- [ ] Request POST ke Microservice Endpoint `https://precaudal-intraspinal-russel.ngrok-free.dev/api/v1/analyze` dengan Header `Authorization: Bearer kol-system` dan `ngrok-skip-browser-warning: true` berhasil terkoneksi.
- [ ] Response sukses tersimpan ke DB & otomatis merender visualisasi sentimen di Task Details.
- [ ] Pengujian error handling (URL invalid, HTTP Error 401/404/422) berfungsi dengan pesan pemberitahuan yang informatif bagi user.
