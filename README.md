# KOL-System-Apps — Monorepo KOLAB.ID

Tiga aplikasi web KOLAB.ID dalam satu repo git, didesain untuk deploy via
**cPanel Git Version Control** (pull-based, tanpa CI server).

| App | Folder | Domain rencana | Fungsi |
| --- | --- | --- | --- |
| Landing | `landing/` | `kolab.id` | Landing page + blog (CI3, SSR, baca artikel lewat Campaign API — **tanpa akses DB**) |
| Campaign | `campaign/` | `app.kolab.id` | Backend orkestrasi campaign KOL (CI3 enterprise) — sumber REST API `/articles/feed` |
| Datahub | `datahub/` | `datahub.kolab.id` | Card generator/invoice hub (CI3) |
| Shared | `shared/uploads/` | — | Aset upload SEMUA app (`campaign/` & `datahub/` namespace). Runtime, tidak di-commit |

## Layout di server cPanel

```
~/repositories/KOL-System-Apps/     ← clone Git Version Control (BUKAN docroot)
~/KOL-System-Apps/
   ├── landing/                     ← docroot kolab.id
   ├── campaign/                    ← docroot app.kolab.id
   │       uploads -> ../shared/uploads/campaign   (symlink)
   ├── datahub/                     ← docroot datahub.kolab.id
   │       uploads -> ../shared/uploads/datahub    (symlink)
   └── shared/uploads/              ← file upload runtime (775, tidak disentuh deploy)
```

## Setup pertama kali (cPanel Terminal)

```bash
cd ~/repositories/KOL-System-Apps   # sesuai lokasi clone
bash setup-server.sh
```

Skrip membuat folder live, `shared/uploads`, symlink `uploads` per app, dan
permission cache/logs.

## Deploy harian

Deploy = cPanel → Git Version Control → **Update from Remote** → **Deploy HEAD
Commit**. Karena satu repo = satu `.cpanel.yml`, SATU kali deploy menyinkronkan
**semua app sekaligus** (rsync `--delete` per app; `uploads/` & `shared/`
dikecualikan sehingga file runtime aman).

> Jika host ternyata tidak punya `rsync`, fallback manual per app:
> ```bash
> cd ~/repositories/KOL-System-Apps
> for APP in landing campaign datahub; do
>     cp -r $APP/. ~/KOL-System-Apps/$APP/          # tanpa trailing-slash trick
>     # hapus file yang sudah tidak ada di repo secara manual bila perlu
> done
> ```
> (rsync hampir selalu tersedia di cPanel; gunakan `which rsync` untuk cek.)

Rollback: di repo lokal (laptop) `git revert <sha>` → push → deploy ulang.
History tidak pernah di-rewrite setelah baseline.

## Konfigurasi environment (di server, out-of-repo)

Landing membaca env var lewat Apache/LiteSpeed `SetEnv` (lihat `landing/.htaccess`)
atau cukup Sunting `landing/.htaccess` langsung di folder live jika perlu:

```
SetEnv CI_ENV production
SetEnv LANDING_API_BASE https://app.kolab.id   # sesuai hasil konfirmasi IT
```

Landing default bila `LANDING_API_BASE` kosong: `http://localhost:8000`
(API Campaign di-host sama → tetap bekerja, hanya lebih jauh dari HTTP-rewrite).

Campaign/datahub: kredensial DB di konfigurasikan pada `application/database.php`
di folder LIVE (tidak berada di clone-deploy yang di-rsync jika ditandai
?.env; jaga tetap out-of-repo). LANDING read-only, tidak punya DB.

## Keamanan repo

- `campaign/whatsapp-service/` di-exclude (tidak masuk repo/deploy),
  termasuk `session_auth/` berisi kredensial WA.
- `kol.sql` (dump DB asli) di-exclude; `ci3.sql` (template schema) ikut repo.
- `vendor/` COMMITTED (composer_autoload aktif; cPanel tidak punya composer).
- `shared/uploads` tidak di-commit kecuali `.gitkeep`; file runtime tetap aman
  di server karena rsync excludes.

## Development lokal

| Situs | Perintah |
| --- | --- |
| Campaign | `cd campaign && php -S localhost:8000` (API artikel hidup di sini) |
| Landing | `cd landing && php -S 127.0.0.1:8087 dev-router.php` dengan `LANDING_API_BASE=http://localhost:8000` |
| Datahub | sesuai port lokal Anda (standalone CI3) |

Tailwind landing: build lokal via Node; hasil `dist/output.css` COMMITTED
(karena server tidak punya Node). Setiap mengubah markup landing → rebuild +
commit output.css.
