# 🚀 Checklist Deploy cPanel — KOL-System-Apps

Server layout: clone git di `~/repositories/`, folder live di `~/KOL-System-Apps/`.
DB tunggal `KOL` dipakai campaign + datahub (data artikel, `cc_options`, menu bersama).

---

## FASE 1 — Deploy key (SSH, read-only)

```bash
# cPanel Terminal
ssh-keygen -t ed25519 -f ~/.ssh/kol_deploy -N "" -C "deploy kol-system-apps"
cat ~/.ssh/kol_deploy.pub           # copy output

cat >> ~/.ssh/config <<'EOF'
Host github-kolapps
  HostName github.com
  User git
  IdentityFile ~/.ssh/kol_deploy
EOF
chmod 600 ~/.ssh/config
ssh -T git@github-kolapps           # ✅ "You've successfully authenticated"
```

Tambahkan pub key di GitHub: **Settings → Deploy keys** (read-only, jangan centang write).

## FASE 2 — Clone via Git Version Control

- cPanel → Git Version Control → Create
- Clone URL: `git@github-kolapps:ahmdsaif87/KOL-System-Apps.git`
- Repo path: `~/repositories/KOL-System-Apps`

## FASE 3 — Siapkan struktur live

```bash
cd ~/repositories/KOL-System-Apps && bash setup-server.sh
```

Hasil: `~/KOL-System-Apps/{landing,campaign,datahub}`, `shared/uploads/`, symlink
`uploads`, cache/logs 775, template `database.php` ditaruh.

## FASE 4 — Database (sekali)

1. MySQL Database Wizard → DB `kol` → jadi `userprefix_kol` + user (ALL PRIVILEGES).
2. phpMyAdmin → Import → `kol.sql` (dump campaign, 90 tabel). ✅ 90 tabel.
3. phpMyAdmin → SQL → paste `config-private/cc_options-seeds-KOL.sql` → Go.
   ✅ verifikasi akhir: semua `terisi`, kecuali sentiment opsional.

## FASE 5 — Kredensial DB di dua app (File Manager)

Edit (isi SAMA untuk keduanya — satu DB):

- `~/KOL-System-Apps/campaign/application/config/database.php`
- `~/KOL-System-Apps/datahub/application/config/database.php`

```php
'username' => 'userprefix_koluser',
'password' => 'PASSWORD',
'database' => 'userprefix_kol',
```

File ini **di-exclude** dari deploy — diset sekali, deploy berikutnya tidak menimpa.

## FASE 6 — Upload file lama (sekali)

- File Manager → `~/KOL-System-Apps/` (root live, bukan di dalam app)
- Upload `kol-shared-uploads-to-upload.zip` → Extract
- ✅ `find ~/KOL-System-Apps/shared/uploads -type f | wc -l` → `153`

## FASE 7 — Deploy pertama

- cPanel → Git Version Control → Manage
- **Update from Remote History** → cek commit terbaru
- **Deploy HEAD Commit** → `.cpanel.yml` rsync semua app
- ✅ `campaign/index.php` & `landing/index.php` ada; uploads tetap `153`

## FASE 8 — Domain & SSL (koordinasi IT)

- Docroot: `kolab.id → landing/`, `app.kolab.id → campaign/`, `datahub.kolab.id → datahub/`
- Tunggu AutoSSL hijau.
- (Opsional, setelah sitemap hidup) Submit `https://kolab.id/sitemap.xml` ke
  Google Search Console.
- Kedua app memakai login admin biasa — tidak ada OAuth yang perlu diset.

## FASE 9 — Uji End-to-End

| Uji | Harus |
| --- | --- |
| `https://kolab.id` | landing tampil; kartu artikel dari API |
| `/artikel` + detail | cover image tampil (bukti symlink uploads) |
| `kolab.id/sitemap.xml` | XML valid |
| `app.kolab.id` login → menu | admin masuk; menu tampil |
| `app.kolab.id/articles/feed?limit=1` | JSON `success: true` |
| Menu Keyapi Config → Save (tanpa ubah nilai) | tersimpan, tidak membuat baris duplikat |
| `datahub.kolab.id` login + card generator | normal |
| (Nantinya, saat domain fix) | commit `SetEnv LANDING_API_BASE https://app.kolab.id` di `landing/.htaccess` → deploy ulang |

## Deploy harian

Perubahan → `git push` → cPanel **Deploy HEAD Commit**. Rollback = `git revert` + push + deploy ulang.

---

## 🔧 Troubleshooting

| Gejala | Penyebab | Solusi |
| --- | --- | --- |
| Deploy error | cPanel tanpa rsync | fallback `cp -r` manual per README |
| Blank/HTTP 500 | versi PHP | paksa PHP 7.2–7.4 via PHP Selector |
| Tampilan tanpa CSS | `dist/output.css` tidak ternaik | pastikan deploy sukses (file committed) |
| Login DB error | FASE 5 belum diisi | isi kredensial di kedua database.php |
| Cover image 404/403 | zip belum extract / symlink rusak | ulangi FASE 6 / cek symlink FASE 3 |

---

## 🔐 Catatan arsitektur (ringkas)

- Semua runtime API keys dibaca dari DB `cc_options` (KeyAPI/Apify/Sentiment),
  repo menyimpan fallback `''`.
- Edit key via menu admin → langsung berlaku di campaign & datahub.
- `shared/uploads` fisik di luar docroot rsync; file upload runtime tidak pernah
  tersapu oleh `--delete`.
