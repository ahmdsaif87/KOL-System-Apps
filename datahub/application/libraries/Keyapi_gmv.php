<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Keyapi_gmv Library — TikTok Shop GMV & Product Sync via KeyAPI (keyapi.ai)
 *
 * Replaces Kalodata & FastMoss.
 * Endpoints used:
 * 1. GET /v1/tiktok/influencer/detail/analytics  — Creator all-time GMV & sales
 * 2. GET /v1/tiktok/influencer/products/analytics — Top showcase products
 *
 * @return array ['status' => 'OK|EMPTY|FAILED|SKIP', 'message' => '...', 'count' => N, 'revenue' => float]
 */
class Keyapi_gmv
{
    protected $CI;
    protected $api_key         = ''; // kunci di-load dari cc_options/config keyapi.php
    protected $base_url        = 'https://api.keyapi.ai';
    protected $cache_days      = 7;
    protected $max_products    = 20;
    protected $usd_to_idr_rate = 16150;
    protected $last_error      = '';

    public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->database();
        $this->CI->config->load('keyapi', TRUE);
        $cfg = $this->CI->config->item('keyapi') ?: [];

        $this->api_key         = get_option('keyapi_api_key',         $cfg['keyapi_api_key']         ?? '');
        $this->base_url        = get_option('keyapi_base_url',        $cfg['keyapi_base_url']        ?? 'https://api.keyapi.ai');
        $this->cache_days      = (int) get_option('keyapi_cache_days',      $cfg['keyapi_cache_days']      ?? 7);
        $this->max_products    = (int) get_option('keyapi_max_products',    $cfg['keyapi_max_products']    ?? 20);
        $this->usd_to_idr_rate = (float) get_option('keyapi_usd_to_idr_rate', $cfg['keyapi_usd_to_idr_rate'] ?? 16150);
        if ($this->usd_to_idr_rate <= 0) {
            $this->usd_to_idr_rate = 16150;
        }
    }

    // ── PUBLIC ───────────────────────────────────────────────────────────────

    /**
     * Synchronize TikTok Shop GMV & product snapshots for a given KOL
     *
     * @param int  $kol_id
     * @param bool $force Bypass cache if true
     * @return array
     */
    public function sync_kol(int $kol_id, bool $force = false): array
    {
        $platform = $this->_get_tiktok_platform($kol_id);
        if (!$platform) {
            return $this->_skip($kol_id, 'Tidak ada platform TIKTOK aktif terdaftar untuk KOL ini.');
        }

        // Cache check (if fresh, no API call needed unless forced)
        if (!$force) {
            try {
                $kol = $this->CI->db->select('gmv_updated_at, gmv_sync_status')
                    ->where('id', $kol_id)->get('kols')->row();
                if ($kol && $kol->gmv_sync_status === 'OK' && !empty($kol->gmv_updated_at)) {
                    if ((time() - strtotime($kol->gmv_updated_at)) < ($this->cache_days * 86400)) {
                        return $this->_skip($kol_id, "Cache hit: data masih segar (< {$this->cache_days} hari).", true);
                    }
                }
            } catch (Throwable $e) {
                log_message('error', "[Keyapi_gmv] Cache check failed: " . $e->getMessage());
            }
        }

        if (empty($this->api_key)) {
            return $this->_fail($kol_id, 'KeyAPI API Key belum dikonfigurasi.');
        }

        $username = trim(ltrim($platform->username, '@'));
        if (empty($username)) {
            return $this->_fail($kol_id, 'Username TikTok kosong.');
        }

        $this->last_error = '';

        // 1. Fetch Creator Detail & GMV via KeyAPI
        // If numeric user_id is already cached in platform_uid, use it; otherwise use unique_ids
        $cached_uid = trim($platform->platform_uid ?? '');
        $creator = $this->_fetch_creator_analytics($username, $cached_uid);

        if (!$creator) {
            $err = !empty($this->last_error) ? $this->last_error : "Kreator @{$username} tidak ditemukan di database KeyAPI.";
            if (strpos($this->last_error, 'Unauthorized') !== false || strpos($this->last_error, 'Kredit habis') !== false) {
                return $this->_fail($kol_id, $err);
            }
            $this->_update_kol_status($kol_id, 'EMPTY', $err, 0.0);
            return [
                'status'  => 'EMPTY',
                'message' => $err,
                'count'   => 0,
                'revenue' => 0.0
            ];
        }

        // Save resolved user_id to platform_uid if present
        $user_id = (string)($creator['user_id'] ?? '');
        if (!empty($user_id) && $user_id !== $cached_uid) {
            $this->_save_creator_id($platform->id, $user_id);
        }

        $total_sale_gmv_amt_usd   = (float)($creator['total_sale_gmv_amt'] ?? 0);
        $total_sale_gmv_30d_usd   = (float)($creator['total_sale_gmv_30d_amt'] ?? 0);
        $total_sale_cnt           = (int)($creator['total_sale_cnt'] ?? 0);
        $total_video_sale_30d_cnt = (int)($creator['total_video_sale_30d_cnt'] ?? 0);
        $sales_flag               = isset($creator['sales_flag']) ? (int)$creator['sales_flag'] : 1;

        // Convert USD from KeyAPI to IDR for database & application display
        $rate = $this->usd_to_idr_rate;
        $total_sale_gmv_idr     = round($total_sale_gmv_amt_usd * $rate, 2);
        $total_sale_gmv_30d_idr = round($total_sale_gmv_30d_usd * $rate, 2);

        // Compute GMV Sync Status
        if ($sales_flag === 0) {
            $status  = 'SKIP';
            $msg     = "Kreator @{$username} tidak aktif jualan di TikTok Shop (sales_flag=0).";
        } elseif ($total_sale_gmv_amt_usd > 0) {
            $status  = 'OK';
            $idr_approx = ($total_sale_gmv_idr >= 1000000000) 
                ? (number_format($total_sale_gmv_idr / 1000000000, 1, ',', '.') . ' Miliar') 
                : (number_format($total_sale_gmv_idr / 1000000, 0, ',', '.') . ' Juta');
            $msg     = "Synced KeyAPI (All-Time): Rp {$idr_approx} ($" . number_format($total_sale_gmv_amt_usd, 2) . ") — Sold: " . number_format($total_sale_cnt, 0, ',', '.') . " pcs";
        } else {
            $status  = 'EMPTY';
            $msg     = "Kreator @{$username} belum memiliki riwayat GMV penjualan di TikTok Shop.";
        }

        // 2. Fetch Top Showcase Products (if user_id available and not SKIP)
        $snapshot_count = 0;
        $products = [];
        if (!empty($user_id) && $status !== 'SKIP') {
            $products = $this->_fetch_creator_products($user_id, $this->max_products);
        }

        // 3. Save Products to kol_shop_snapshots (converted to IDR)
        try {
            $table_exists = $this->CI->db->query("SHOW TABLES LIKE 'kol_shop_snapshots'")->num_rows();
            if ($table_exists) {
                $snapshot_at = date('Y-m-d');
                $this->CI->db->where('kol_id', $kol_id)->delete('kol_shop_snapshots');

                if (!empty($products) && is_array($products)) {
                    foreach ($products as $item) {
                        $pid = $item['product_id'] ?? '';
                        if (empty($pid)) continue;
                        $pid_str     = (string)$pid;
                        $title       = $item['product_name'] ?? ('Product #' . $pid_str);
                        $prod_url    = 'https://www.tiktok.com/view/product/' . $pid_str;
                        $price_usd        = !empty($item['spu_avg_price']) ? (float)$item['spu_avg_price'] : 0.0;
                        $creator_vid_gmv  = (float)($item['total_video_sale_gmv_amt'] ?? 0);
                        $creator_live_gmv = (float)($item['total_live_sale_gmv_amt'] ?? 0);
                        $creator_vid_sold = (int)($item['total_video_sale_cnt'] ?? 0);
                        $creator_live_sold= (int)($item['total_live_sale_cnt'] ?? 0);

                        $creator_gmv_usd  = $creator_vid_gmv + $creator_live_gmv;
                        $creator_sold     = $creator_vid_sold + $creator_live_sold;

                        $price_idr   = round($price_usd * $rate, 2);
                        $gmv_est_idr = round($creator_gmv_usd * $rate, 2);
                        $sold_count  = $creator_sold;

                        $this->CI->db->query(
                            "INSERT INTO kol_shop_snapshots
                             (kol_id, platform_username, product_id, title, product_url, shop_name,
                              price, sold_count, gmv_estimate, currency, rating, review_count, snapshot_at, raw_json)
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                             ON DUPLICATE KEY UPDATE
                              title = VALUES(title),
                              product_url = VALUES(product_url),
                              shop_name = VALUES(shop_name),
                              price = VALUES(price),
                              sold_count = VALUES(sold_count),
                              gmv_estimate = VALUES(gmv_estimate),
                              currency = VALUES(currency),
                              raw_json = VALUES(raw_json)",
                            [
                                $kol_id,
                                $username,
                                $pid_str,
                                substr($title, 0, 500),
                                substr($prod_url, 0, 1000),
                                '', // shop_name is not provided by KeyAPI endpoint
                                $price_idr,
                                $sold_count,
                                $gmv_est_idr,
                                'IDR',
                                null,
                                null,
                                $snapshot_at,
                                json_encode($item, JSON_UNESCAPED_UNICODE)
                            ]
                        );
                        $snapshot_count++;
                    }
                }
            }
        } catch (Throwable $e) {
            log_message('error', "[Keyapi_gmv] Snapshot save error: " . $e->getMessage());
        }

        if ($status === 'EMPTY' && $snapshot_count > 0) {
            $status = 'OK';
            $msg    = "Synced KeyAPI: GMV Rp 0 ({$snapshot_count} produk etalase terdaftar).";
        } elseif ($snapshot_count > 0 && $status === 'OK') {
            $msg .= " ({$snapshot_count} produk etalase)";
        }

        // 4. Update kols record (store IDR total_gmv_estimate, all-time units, and full JSON breakdown)
        $this->_update_kol_status($kol_id, $status, $msg, $total_sale_gmv_idr, $total_sale_cnt, [
            'scope'          => 'all_time',
            'currency'       => 'IDR',
            'usd_rate'       => $rate,
            'total_gmv_idr'  => $total_sale_gmv_idr,
            'total_gmv_usd'  => $total_sale_gmv_amt_usd,
            'gmv_30d_idr'    => $total_sale_gmv_30d_idr,
            'gmv_30d_usd'    => $total_sale_gmv_30d_usd,
            'total_units'    => $total_sale_cnt,
            'video_sale_30d' => $total_video_sale_30d_cnt
        ]);

        return [
            'status'  => $status,
            'message' => $msg,
            'count'   => $snapshot_count,
            'revenue' => $total_sale_gmv_idr
        ];
    }

    // ── PRIVATE ──────────────────────────────────────────────────────────────

    /**
     * Get active TIKTOK platform row for KOL
     */
    private function _get_tiktok_platform(int $kol_id): ?object
    {
        try {
            return $this->CI->db
                ->join('platforms', 'platforms.id = kol_platforms.platform_id', 'inner')
                ->where('kol_platforms.kol_id', $kol_id)
                ->where('platforms.code', 'TIKTOK')
                ->where('kol_platforms.is_active', 1)
                ->select('kol_platforms.id, kol_platforms.username, kol_platforms.platform_uid')
                ->get('kol_platforms')
                ->row();
        } catch (Throwable $e) {
            log_message('error', "[Keyapi_gmv] _get_tiktok_platform: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch influencer detail & GMV analytics
     */
    private function _fetch_creator_analytics(string $username, string $cached_uid = ''): ?array
    {
        // Try user_ids if numeric, or unique_ids by username
        $params = [];
        if (!empty($cached_uid) && is_numeric($cached_uid)) {
            $params['user_ids'] = $cached_uid;
        } else {
            $params['unique_ids'] = $username;
        }

        $res = $this->_get('/v1/tiktok/influencer/detail/analytics', $params);
        if ($res && isset($res['code']) && $res['code'] === 0 && !empty($res['data'])) {
            return $res['data'][0];
        }

        // If lookup by user_ids failed, fallback to unique_ids
        if (!empty($params['user_ids'])) {
            $res = $this->_get('/v1/tiktok/influencer/detail/analytics', ['unique_ids' => $username]);
            if ($res && isset($res['code']) && $res['code'] === 0 && !empty($res['data'])) {
                return $res['data'][0];
            }
        }

        return null;
    }

    /**
     * Fetch top products by Creator GMV (up to max_products, using pagination of 10 items)
     */
    private function _fetch_creator_products(string $user_id, int $limit = 20): array
    {
        $all_products = [];
        $pages = ($limit > 10) ? 2 : 1;

        // First attempt: Sort by video_sale_gmv (field 5) to get products with highest creator GMV
        for ($p = 1; $p <= $pages; $p++) {
            $params = [
                'user_id'                       => $user_id,
                'page_num'                      => $p,
                'page_size'                     => 10,
                'influencer_product_sort_field' => 5, // Sort by Video GMV (creator sales)
                'sort_type'                     => 1  // Descending
            ];

            $res = $this->_get('/v1/tiktok/influencer/products/analytics', $params);
            if ($res && isset($res['code']) && $res['code'] === 0 && !empty($res['data'])) {
                $all_products = array_merge($all_products, $res['data']);
                if (count($res['data']) < 10) {
                    break;
                }
            } else {
                break;
            }
        }

        // Fallback: If no products returned by video GMV sort, fetch by default sort
        if (empty($all_products)) {
            for ($p = 1; $p <= $pages; $p++) {
                $params = [
                    'user_id'                       => $user_id,
                    'page_num'                      => $p,
                    'page_size'                     => 10,
                    'influencer_product_sort_field' => 2,
                    'sort_type'                     => 1
                ];

                $res = $this->_get('/v1/tiktok/influencer/products/analytics', $params);
                if ($res && isset($res['code']) && $res['code'] === 0 && !empty($res['data'])) {
                    $all_products = array_merge($all_products, $res['data']);
                    if (count($res['data']) < 10) {
                        break;
                    }
                } else {
                    break;
                }
            }
        }

        return array_slice($all_products, 0, $limit);
    }

    /**
     * Save creator user_id into kol_platforms.platform_uid
     */
    private function _save_creator_id(int $platform_row_id, string $creator_id): void
    {
        try {
            $col = $this->CI->db->query("SHOW COLUMNS FROM kol_platforms LIKE 'platform_uid'")->num_rows();
            if ($col) {
                $this->CI->db->where('id', $platform_row_id)->update('kol_platforms', ['platform_uid' => $creator_id]);
            }
        } catch (Throwable $e) {
            log_message('error', "[Keyapi_gmv] _save_creator_id: " . $e->getMessage());
        }
    }

    /**
     * Perform HTTP GET request to KeyAPI with Bearer token authentication
     */
    private function _get(string $endpoint, array $query_params = []): ?array
    {
        $url = rtrim($this->base_url, '/') . $endpoint;
        if (!empty($query_params)) {
            $url .= '?' . http_build_query($query_params);
        }

        $attempts = 0;
        $max_attempts = 3;

        while ($attempts < $max_attempts) {
            $attempts++;
            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT        => 30,
                CURLOPT_HTTPGET        => true,
                CURLOPT_HTTPHEADER     => [
                    'Authorization: Bearer ' . $this->api_key,
                    'Accept: application/json',
                ],
                CURLOPT_SSL_VERIFYPEER => true,
            ]);

            $response  = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_err  = curl_error($ch);
            curl_close($ch);

            if ($curl_err) {
                $this->last_error = "cURL error ({$endpoint}): {$curl_err}";
                log_message('error', "[Keyapi_gmv] " . $this->last_error);
                return null;
            }

            // Handle HTTP error codes
            if ($http_code === 401) {
                $this->last_error = 'KeyAPI Unauthorized (401): API Key tidak valid.';
                log_message('error', "[Keyapi_gmv] " . $this->last_error);
                return null;
            }
            if ($http_code === 402) {
                $this->last_error = 'KeyAPI Payment Required (402): Kredit habis. Silakan top up.';
                log_message('error', "[Keyapi_gmv] " . $this->last_error);
                return null;
            }
            if ($http_code === 429) {
                $this->last_error = 'KeyAPI Rate limit (429): Menunggu retry...';
                log_message('error', "[Keyapi_gmv] " . $this->last_error);
                sleep(2);
                continue;
            }
            if ($http_code >= 500) {
                $this->last_error = "KeyAPI Server error ({$http_code})";
                log_message('error', "[Keyapi_gmv] " . $this->last_error);
                sleep(1 * $attempts);
                continue;
            }

            if ($http_code !== 200) {
                $this->last_error = "HTTP {$http_code} ({$endpoint}): " . substr($response, 0, 300);
                log_message('error', "[Keyapi_gmv] " . $this->last_error);
                return null;
            }

            $decoded = json_decode($response, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $this->last_error = "JSON decode error on {$endpoint}: " . json_last_error_msg();
                log_message('error', "[Keyapi_gmv] " . $this->last_error);
                return null;
            }

            if (isset($decoded['code']) && $decoded['code'] !== 0) {
                $api_msg = $decoded['message'] ?? 'Kesalahan API KeyAPI';
                $this->last_error = "KeyAPI API Error ({$decoded['code']}): {$api_msg}";
                log_message('error', "[Keyapi_gmv] " . $this->last_error);
                return null;
            }

            return $decoded;
        }

        return null;
    }

    /**
     * Update GMV metrics on kols record
     */
    private function _update_kol_status(int $kol_id, string $status, string $message, float $total_gmv = 0.0, int $total_units_sold = 0, array $breakdown = []): void
    {
        try {
            $data = [
                'gmv_sync_status'  => $status,
                'gmv_sync_message' => substr($message, 0, 255),
                'gmv_updated_at'   => date('Y-m-d H:i:s'),
            ];
            if ($status !== 'FAILED') {
                $data['total_gmv_estimate'] = $total_gmv;
            }

            // Check if total_units_sold exists in kols
            $has_units_sold = $this->CI->db->query("SHOW COLUMNS FROM kols LIKE 'total_units_sold'")->num_rows();
            if ($has_units_sold) {
                $data['total_units_sold'] = $total_units_sold;
            }

            // Check if gmv_breakdown exists in kols
            $has_breakdown = $this->CI->db->query("SHOW COLUMNS FROM kols LIKE 'gmv_breakdown'")->num_rows();
            if ($has_breakdown && !empty($breakdown)) {
                $data['gmv_breakdown'] = json_encode($breakdown);
            }

            $this->CI->db->where('id', $kol_id)->update('kols', $data);
        } catch (Throwable $e) {
            log_message('error', "[Keyapi_gmv] _update_kol_status: " . $e->getMessage());
        }
    }

    private function _fail(int $kol_id, string $message): array
    {
        $this->_update_kol_status($kol_id, 'FAILED', $message);
        log_message('error', "[Keyapi_gmv] FAILED kol_id={$kol_id}: {$message}");
        return ['status' => 'FAILED', 'message' => $message, 'count' => 0, 'revenue' => 0.0];
    }

    private function _skip(int $kol_id, string $message, bool $is_cache = false): array
    {
        if (!$is_cache) {
            $this->_update_kol_status($kol_id, 'SKIP', $message);
        }
        return ['status' => 'SKIP', 'message' => $message, 'count' => 0, 'revenue' => 0.0];
    }
}
