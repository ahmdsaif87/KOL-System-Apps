#!/bin/bash
# ============================================================
# setup-server.sh — dijalankan SEKALI via cPanel Terminal
# setelah repo monorepo selesai di-clone (Git Version Control).
#
# Yang dilakukannya:
#   1. Membuat folder live docroot: ~/KOL-System-Apps/{landing,campaign,datahub}
#      (Git Version Control hanya akan me-rsync ke sini, lihat .cpanel.yml)
#   2. Membuat shared/uploads + symlink uploads per app
#   3. Membuat cache/logs dengan permission 775
# ============================================================
set -e

ROOT="$HOME/KOL-System-Apps"

echo "== Struktur live: $ROOT =="
for APP in landing campaign datahub; do
    mkdir -p "$ROOT/$APP/application/cache" "$ROOT/$APP/application/logs"
    chmod 775 "$ROOT/$APP/application/cache" "$ROOT/$APP/application/logs" 2>/dev/null || true
done

mkdir -p "$ROOT/shared/uploads/campaign" "$ROOT/shared/uploads/datahub"
chmod -R 775 "$ROOT/shared/uploads" 2>/dev/null || true

# Symlink uploads (idempotent — hanya dibuat bila belum ada)
[ -e "$ROOT/campaign/uploads" ] || ln -s ../shared/uploads/campaign "$ROOT/campaign/uploads"
[ -e "$ROOT/datahub/uploads" ]  || ln -s ../shared/uploads/datahub  "$ROOT/datahub/uploads"

# database.php produksi: dipertahankan antar-deploy (di-exclude .cpanel.yml).
# Sebelum deploy pertama, pastikan ada template salinannya (credential diisi
# manual via File Manager: nama DB cPanel-prefixed, user, password).
CLONE="${1:-$HOME/repositories/KOL-System-Apps}"
for APP in campaign datahub; do
    [ -f "$ROOT/$APP/application/config/database.php" ] && continue
    if [ -f "$CLONE/$APP/application/config/database.php" ]; then
        cp "$CLONE/$APP/application/config/database.php" "$ROOT/$APP/application/config/database.php"
        echo "catatan: $APP/application/config/database.php ditaruh kosong-credential — isi di server"
    fi
done

echo "Selesai. Verifikasi:"
ls -l "$ROOT/campaign/uploads" "$ROOT/datahub/uploads"
echo ""
echo "Langkah berikutnya:"
echo "  1. Pastikan docroot subdomain mengarah ke folder di atas"
echo "  2. cPanel → Git Version Control → 'Update from Remote'"
echo "  3. 'Deploy HEAD Commit' (jalankan .cpanel.yml)"
