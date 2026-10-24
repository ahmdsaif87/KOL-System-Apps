<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Keyapi_service — CLI Cron Controller for KeyAPI TikTok Shop GMV Sync
 *
 * Usage (cron):
 *   php index.php cli/keyapi_service run_gmv_weekly
 *   php index.php cli/keyapi_service run_gmv_weekly 50
 *   php index.php cli/keyapi_service sync_one 14
 */
class Keyapi_service extends MX_Controller
{
    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            show_error('CLI access only', 403);
        }
        $this->load->helper(['cookie', 'url']);
        $this->load->database();
        $this->load->library('Keyapi_gmv');
    }

    /**
     * Run weekly GMV sync for all KOLs with active TIKTOK platform.
     * Respects cache_days (default 7 days) — KOLs synced recently are skipped.
     *
     * @param int $batch_size Max KOLs to process per run (default 100)
     */
    public function run_gmv_weekly(int $batch_size = 100): void
    {
        echo date('Y-m-d H:i:s') . " — Starting KeyAPI TikTok GMV weekly sync (batch={$batch_size})\n";

        try {
            $rows = $this->db
                ->distinct()
                ->select('kol_platforms.kol_id')
                ->join('platforms', 'platforms.id = kol_platforms.platform_id', 'inner')
                ->where('platforms.code', 'TIKTOK')
                ->where('kol_platforms.is_active', 1)
                ->join('kols', 'kols.id = kol_platforms.kol_id', 'inner')
                ->where('kols.status', 'ACTIVE')
                ->limit($batch_size)
                ->get('kol_platforms')
                ->result();
        } catch (Throwable $e) {
            echo "ERROR: Could not fetch KOL list: " . $e->getMessage() . "\n";
            return;
        }

        if (empty($rows)) {
            echo "No active TIKTOK KOLs found.\n";
            return;
        }

        $total = count($rows);
        echo "Found {$total} KOLs with active TIKTOK platform.\n\n";

        $stats = ['OK' => 0, 'SKIP' => 0, 'EMPTY' => 0, 'FAILED' => 0];

        foreach ($rows as $i => $row) {
            $kol_id = (int)$row->kol_id;
            $num    = $i + 1;
            echo "[{$num}/{$total}] KOL #{$kol_id}: ";

            try {
                $result = $this->keyapi_gmv->sync_kol($kol_id);
                $status = $result['status'] ?? 'FAILED';
                $stats[$status] = ($stats[$status] ?? 0) + 1;
                echo "{$status} — " . ($result['message'] ?? '') . "\n";
            } catch (Throwable $e) {
                $stats['FAILED']++;
                echo "FAILED — " . $e->getMessage() . "\n";
                log_message('error', "[Keyapi_service::run_gmv_weekly] kol_id={$kol_id}: " . $e->getMessage());
            }

            if ($num < $total) {
                sleep(1);
            }
        }

        echo "\n" . date('Y-m-d H:i:s') . " — KeyAPI GMV sync complete.\n";
        echo "Results: OK={$stats['OK']}  SKIP={$stats['SKIP']}  EMPTY={$stats['EMPTY']}  FAILED={$stats['FAILED']}\n";
    }

    /**
     * Force sync a single KOL by ID
     * Usage: php index.php cli/keyapi_service sync_one 14
     */
    public function sync_one(int $kol_id): void
    {
        echo date('Y-m-d H:i:s') . " — Starting KeyAPI TikTok GMV sync for KOL #{$kol_id} (forced)...\n";
        try {
            $result = $this->keyapi_gmv->sync_kol($kol_id, true);
            $status = $result['status'] ?? 'FAILED';
            echo "Result: [{$status}] " . ($result['message'] ?? '') . "\n";
            echo "Revenue: Rp " . number_format($result['revenue'] ?? 0, 2, ',', '.') . "\n";
            echo "Products Snapshotted: " . ($result['count'] ?? 0) . "\n";
        } catch (Throwable $e) {
            echo "ERROR: " . $e->getMessage() . "\n";
        }
    }

    /**
     * Force sync ALL active TikTok KOLs regardless of cache
     * Usage: php index.php cli/keyapi_service sync_all
     */
    public function sync_all(): void
    {
        echo date('Y-m-d H:i:s') . " — Starting KeyAPI TikTok GMV sync for ALL active TikTok KOLs (forced)...\n";

        try {
            $rows = $this->db
                ->distinct()
                ->select('kol_platforms.kol_id, kol_platforms.username, kols.name')
                ->join('platforms', 'platforms.id = kol_platforms.platform_id', 'inner')
                ->where('platforms.code', 'TIKTOK')
                ->where('kol_platforms.is_active', 1)
                ->join('kols', 'kols.id = kol_platforms.kol_id', 'inner')
                ->where('kols.status', 'ACTIVE')
                ->get('kol_platforms')
                ->result();
        } catch (Throwable $e) {
            echo "ERROR: Could not fetch KOL list: " . $e->getMessage() . "\n";
            return;
        }

        if (empty($rows)) {
            echo "No active TIKTOK KOLs found.\n";
            return;
        }

        $total = count($rows);
        echo "Found {$total} active TikTok KOLs to scrape.\n\n";

        $stats = ['OK' => 0, 'SKIP' => 0, 'EMPTY' => 0, 'FAILED' => 0];

        foreach ($rows as $i => $row) {
            $kol_id = (int)$row->kol_id;
            $num    = $i + 1;
            echo "[{$num}/{$total}] KOL #{$kol_id} ({$row->name} - @{$row->username}):\n";

            try {
                $result = $this->keyapi_gmv->sync_kol($kol_id, true);
                $status = $result['status'] ?? 'FAILED';
                $stats[$status] = ($stats[$status] ?? 0) + 1;
                echo "   Status : [{$status}] " . ($result['message'] ?? '') . "\n";
                echo "   Revenue: Rp " . number_format($result['revenue'] ?? 0, 2, ',', '.') . "\n";
                echo "   Produk : " . ($result['count'] ?? 0) . " produk\n\n";
            } catch (Throwable $e) {
                $stats['FAILED']++;
                echo "   FAILED : " . $e->getMessage() . "\n\n";
                log_message('error', "[Keyapi_service::sync_all] kol_id={$kol_id}: " . $e->getMessage());
            }

            if ($num < $total) {
                sleep(1);
            }
        }

        echo date('Y-m-d H:i:s') . " — All KeyAPI TikTok GMV sync completed!\n";
        echo "Summary: OK={$stats['OK']} | SKIP={$stats['SKIP']} | EMPTY={$stats['EMPTY']} | FAILED={$stats['FAILED']}\n";
    }
}
