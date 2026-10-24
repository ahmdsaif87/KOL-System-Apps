<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Apify_service — CLI Cron Job Controller
 *
 * Sprint 4: Scrapes live posting URLs via Apify, stores performance metrics,
 * calculates engagement_rate / CPV, and computes KOL scores.
 *
 * Usage:
 *   php index.php apify_service run
 *
 * Windows Task Scheduler / cron:
 *   cd /d C:\laragon\www\kol-system && php index.php apify_service run
 *
 * Extends MY_Controller (MX_Controller) so Cicool's autoload chain
 * (Template, form_validation, helpers) bootstraps correctly even in CLI.
 */
class Apify_service extends MY_Controller
{
    /** @var int Maximum retry attempts per posting */
    private $max_retries;

    /** @var string Apify API token */
    private $api_token;

    /** @var string Apify base URL */
    private $base_url;

    /** @var int cURL timeout seconds */
    private $timeout;

    /** @var array Platform code => Actor ID mapping */
    private $actors;

    /** @var array Platform code => field mapping */
    private $field_map;

    /** @var int Counter for processed postings */
    private $processed = 0;

    /** @var int Counter for successful scrapes */
    private $success_count = 0;

    /** @var int Counter for failed scrapes */
    private $fail_count = 0;

    public function __construct()
    {
        parent::__construct();

        // ── CLI guard ─────────────────────────────────────────────
        if (!is_cli()) {
            show_error('This controller is CLI-only.', 403);
            exit;
        }

        // ── Load dependencies ─────────────────────────────────────
        // Database and app_helper are already autoloaded via MY_Controller.
        $this->config->load('apify', TRUE);

        // ── Read config ───────────────────────────────────────────
        $cfg = $this->config->item('apify') ?: [];

        // cc_options (Cicool settings table) takes precedence over config file
        $this->api_token   = $this->_get_option_safe('apify_api_token')
                             ?: ($cfg['apify_api_token'] ?? '');
        $this->base_url    = $cfg['apify_base_url']    ?? 'https://api.apify.com/v2';
        $this->max_retries = $cfg['apify_max_retries'] ?? 3;
        $this->timeout     = $cfg['apify_timeout']     ?? 120;
        $this->actors      = $cfg['apify_actors']      ?? [];
        $this->field_map   = $cfg['apify_field_map']   ?? [];
    }


    // ================================================================
    //  PUBLIC: ENTRY POINT
    // ================================================================

    /**
     * Main entry point — invoked by cron.
     *
     * Pipeline:
     *  1. Fetch unscraped postings
     *  2. For each: call Apify → store metrics → calculate scores
     *  3. Log summary
     */
    public function run()
    {
        $this->_log('=== Apify Service START ===');

        // ── Validate token ────────────────────────────────────────
        if (empty($this->api_token)) {
            $this->_log('ERROR: Apify API token is not configured. Set it in cc_options or config/apify.php.');
            $this->_log('=== Apify Service END (aborted) ===');
            return;
        }

        // ── 1. Get unscraped postings ─────────────────────────────
        $postings = $this->_get_unscraped_postings();

        if (empty($postings)) {
            $this->_log('No unscraped postings found. Nothing to do.');
            $this->_log('=== Apify Service END ===');
            return;
        }

        $this->_log(count($postings) . ' posting(s) to process.');

        // ── 2. Process each posting ───────────────────────────────
        // Track unique KOL and campaign_kol IDs for batch score recalculation
        $kol_ids          = [];
        $campaign_kol_ids = [];

        foreach ($postings as $posting) {
            $this->processed++;
            $this->_log("--- Processing posting #{$posting->id} (URL: {$posting->live_url}) ---");

            // Resolve actor
            $actor_id = $this->_resolve_actor_id($posting->platform_code);
            if (!$actor_id) {
                $this->_log("  SKIP: No Apify actor configured for platform '{$posting->platform_code}'.");
                continue;
            }

            // Call Apify
            $result = $this->_call_apify($posting, $actor_id);

            if ($result === false) {
                $this->fail_count++;
                continue;
            }

            // Store metrics
            $metric_id = $this->_store_metrics($posting, $result);

            if ($metric_id) {
                $this->success_count++;
                $kol_ids[$posting->kol_id]                     = true;
                $campaign_kol_ids[$posting->campaign_kol_id]   = true;
            } else {
                $this->fail_count++;
            }
        }

        // ── 3. Recalculate scores for affected KOLs ──────────────
        foreach ($campaign_kol_ids as $ck_id => $_) {
            $this->_calculate_collaboration_score($ck_id);
        }

        foreach ($kol_ids as $kol_id => $_) {
            $this->_calculate_kol_lifetime_score($kol_id);
        }

        // ── 4. Summary ───────────────────────────────────────────
        $this->_log("Summary: {$this->processed} processed, {$this->success_count} success, {$this->fail_count} failed.");
        $this->_log('=== Apify Service END ===');
    }

    // ================================================================
    //  PRIVATE: DATA RETRIEVAL
    // ================================================================

    /**
     * Fetch postings that have a live_url but no successful Apify job yet,
     * and whose failed attempts haven't exceeded the retry limit.
     *
     * @return array of objects
     */
    private function _get_unscraped_postings()
    {
        $max = (int) $this->max_retries;

        $sql = "
            SELECT p.id,
                   p.live_url,
                   p.task_id,
                   pl.code   AS platform_code,
                   t.budget  AS task_budget,
                   t.campaign_kol_id,
                   ck.kol_id
            FROM   postings p
            JOIN   tasks t          ON t.id  = p.task_id
            JOIN   platforms pl     ON pl.id = p.platform_id
            JOIN   campaign_kols ck ON ck.id = t.campaign_kol_id
            WHERE  p.live_url IS NOT NULL
              AND  p.live_url != ''
              AND  p.status   = 'ACTIVE'
              -- Exclude postings that already have a SUCCESS or in-flight job
              AND  NOT EXISTS (
                  SELECT 1
                  FROM   apify_jobs aj
                  WHERE  aj.posting_id = p.id
                    AND  aj.status IN ('SUCCESS','QUEUED','RUNNING')
              )
              -- Exclude postings that have exhausted all retries
              AND  (
                  SELECT COALESCE(MAX(aj2.attempt_number), 0)
                  FROM   apify_jobs aj2
                  WHERE  aj2.posting_id = p.id
              ) < ?
            ORDER BY p.created_at ASC
        ";

        $query = $this->db->query($sql, [$max]);
        return $query->result();
    }

    // ================================================================
    //  PRIVATE: APIFY API
    // ================================================================

    /**
     * Map a platform code to the corresponding Apify actor ID.
     *
     * @param  string $platform_code  e.g. 'TIKTOK'
     * @return string|null            Actor ID or null if not mapped
     */
    private function _resolve_actor_id($platform_code)
    {
        $code = strtoupper(trim($platform_code));
        return $this->actors[$code] ?? null;
    }

    /**
     * Call the Apify synchronous endpoint for a single posting.
     *
     * Creates an apify_jobs record tracking the attempt. On success the raw
     * scraped data array is returned; on failure returns false.
     *
     * @param  object $posting  Row from _get_unscraped_postings()
     * @param  string $actor_id Apify actor identifier
     * @return array|false      Parsed first-item data, or false on failure
     */
    private function _call_apify($posting, $actor_id)
    {
        // ── Determine attempt number ──────────────────────────────
        $last_attempt = $this->db
            ->select_max('attempt_number')
            ->where('posting_id', $posting->id)
            ->get('apify_jobs')
            ->row();

        $attempt = ($last_attempt && $last_attempt->attempt_number)
            ? (int) $last_attempt->attempt_number + 1
            : 1;

        // ── Insert QUEUED job ─────────────────────────────────────
        $job_data = [
            'posting_id'     => $posting->id,
            'attempt_number' => $attempt,
            'status'         => 'QUEUED',
            'started_at'     => date('Y-m-d H:i:s'),
            'created_at'     => date('Y-m-d H:i:s'),
        ];
        $this->db->insert('apify_jobs', $job_data);
        $job_id = $this->db->insert_id();

        $this->_log("  apify_jobs #{$job_id} created (attempt {$attempt}).");

        // ── Build API request ─────────────────────────────────────
        $url = rtrim($this->base_url, '/')
             . '/actors/' . urlencode($actor_id)
             . '/run-sync-get-dataset-items'
             . '?token=' . urlencode($this->api_token);

        $payload = json_encode([
            'directUrls'     => [$posting->live_url],
            'startUrls'      => [['url' => $posting->live_url]],
            'resultsPerPage' => 1,
            'maxResults'     => 1,
        ]);

        // ── Update status to RUNNING ──────────────────────────────
        $this->db->update('apify_jobs', ['status' => 'RUNNING'], ['id' => $job_id]);

        // ── Execute cURL request ──────────────────────────────────
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $this->timeout,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);
        curl_close($ch);

        // ── Handle response ───────────────────────────────────────
        if ($response === false || !empty($curl_err)) {
            $this->_mark_job_failed($job_id, "cURL error: {$curl_err}");
            return false;
        }

        if ($http_code < 200 || $http_code >= 300) {
            $snippet = mb_substr($response, 0, 500);
            $this->_mark_job_failed($job_id, "HTTP {$http_code}: {$snippet}");
            return false;
        }

        $data = json_decode($response, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->_mark_job_failed($job_id, 'JSON decode error: ' . json_last_error_msg());
            return false;
        }

        if (empty($data) || !is_array($data) || !isset($data[0])) {
            $this->_mark_job_failed($job_id, 'Empty or unexpected response structure.');
            return false;
        }

        // ── Extract external run ID if present ────────────────────
        $external_run_id = $data[0]['runId'] ?? $data[0]['id'] ?? null;

        // ── Mark SUCCESS ──────────────────────────────────────────
        $this->db->update('apify_jobs', [
            'status'          => 'SUCCESS',
            'external_run_id' => $external_run_id,
            'finished_at'     => date('Y-m-d H:i:s'),
        ], ['id' => $job_id]);

        $this->_log("  apify_jobs #{$job_id} => SUCCESS.");

        return $data[0];
    }

    /**
     * Mark an apify_jobs record as FAILED.
     *
     * @param int    $job_id
     * @param string $error_message
     */
    private function _mark_job_failed($job_id, $error_message)
    {
        $this->db->update('apify_jobs', [
            'status'        => 'FAILED',
            'error_message' => $error_message,
            'finished_at'   => date('Y-m-d H:i:s'),
        ], ['id' => $job_id]);

        $this->_log("  apify_jobs #{$job_id} => FAILED: {$error_message}");
    }

    // ================================================================
    //  PRIVATE: METRICS STORAGE
    // ================================================================

    /**
     * Parse scraped data, calculate engagement_rate & CPV, INSERT into
     * posting_metrics, and update postings.latest_metric_id.
     *
     * @param  object $posting     Row from _get_unscraped_postings()
     * @param  array  $scraped     First item from Apify response
     * @return int|false           Inserted metric ID, or false on failure
     */
    private function _store_metrics($posting, $scraped)
    {
        $platform_code = strtoupper(trim($posting->platform_code));
        $map           = $this->field_map[$platform_code] ?? [];

        // ── Extract raw metrics using field map ───────────────────
        $views    = (int) ($scraped[$map['views']    ?? 'playCount']    ?? 0);
        $likes    = (int) ($scraped[$map['likes']    ?? 'diggCount']    ?? 0);
        $comments = (int) ($scraped[$map['comments'] ?? 'commentCount'] ?? 0);
        $shares   = (int) ($scraped[$map['shares']   ?? 'shareCount']  ?? 0);
        $saves    = (int) ($scraped[$map['saves']    ?? 'collectCount'] ?? 0);

        // Bonus fields (may not exist in all scrapers)
        $followers   = (int) ($scraped['authorMeta']['fans']    ?? $scraped['followers'] ?? 0);
        $total_likes = (int) ($scraped['authorMeta']['heart']   ?? $scraped['totalLikes'] ?? 0);
        $impressions = (int) ($scraped['impressions'] ?? 0);
        $reach       = (int) ($scraped['reach']       ?? 0);

        // ── Calculate derived metrics ─────────────────────────────
        // ER = (likes + comments + shares + saves) / views
        $engagement_rate = ($views > 0)
            ? ($likes + $comments + $shares + $saves) / $views
            : 0;

        // CPV = task.budget / views (NULL when views = 0)
        $budget = (float) ($posting->task_budget ?? 0);
        $cpv    = ($views > 0) ? $budget / $views : null;

        // view_rate = views / followers (NULL when followers = 0)
        $view_rate = ($followers > 0) ? $views / $followers : null;

        $now = date('Y-m-d H:i:s');

        // ── INSERT posting_metrics ────────────────────────────────
        $metric_data = [
            'posting_id'      => $posting->id,
            'snapshot_at'     => $now,
            'views'           => $views,
            'likes'           => $likes,
            'comments'        => $comments,
            'shares'          => $shares,
            'saves'           => $saves,
            'followers'       => $followers,
            'total_likes'     => $total_likes,
            'impressions'     => $impressions,
            'reach'           => $reach,
            'engagement_rate' => round($engagement_rate, 6),
            'cpv'             => $cpv !== null ? round($cpv, 6) : null,
            'view_rate'       => $view_rate !== null ? round($view_rate, 6) : null,
            'created_at'      => $now,
        ];

        $this->db->insert('posting_metrics', $metric_data);
        $metric_id = $this->db->insert_id();

        if (!$metric_id) {
            $db_err = $this->db->error();
            $this->_log("  ERROR inserting posting_metrics: " . ($db_err['message'] ?? 'unknown'));
            return false;
        }

        // ── Update postings.latest_metric_id ──────────────────────
        $this->db->update('postings', [
            'latest_metric_id' => $metric_id,
        ], ['id' => $posting->id]);

        $this->_log("  posting_metrics #{$metric_id} stored. ER={$engagement_rate}, CPV=" . ($cpv ?? 'NULL') . ", views={$views}, likes={$likes}");

        return $metric_id;
    }

    // ================================================================
    //  PRIVATE: SCORE CALCULATION
    // ================================================================

    /**
     * Calculate collaboration score for a specific campaign_kol.
     *
     * Formula: AVG(engagement_rate) across all posting_metrics under this
     * campaign_kol's tasks.
     *
     * Inserts a score_histories row (type = COLLABORATION) and updates the
     * denormalized campaign_kols.collaboration_score.
     *
     * @param int $campaign_kol_id
     */
    private function _calculate_collaboration_score($campaign_kol_id)
    {
        // Get kol_id for score_histories FK
        $ck = $this->db->select('kol_id')->where('id', $campaign_kol_id)->get('campaign_kols')->row();
        if (!$ck) {
            $this->_log("  WARN: campaign_kol #{$campaign_kol_id} not found for collaboration score.");
            return;
        }

        // AVG(engagement_rate) across all metrics for this campaign_kol
        $sql = "
            SELECT AVG(pm.engagement_rate) AS avg_er
            FROM   posting_metrics pm
            JOIN   postings p ON p.id = pm.posting_id
            JOIN   tasks t    ON t.id = p.task_id
            WHERE  t.campaign_kol_id = ?
        ";
        $row = $this->db->query($sql, [$campaign_kol_id])->row();

        $score = ($row && $row->avg_er !== null) ? round((float) $row->avg_er, 2) : 0;

        // Insert score_histories
        $this->db->insert('score_histories', [
            'kol_id'          => $ck->kol_id,
            'campaign_kol_id' => $campaign_kol_id,
            'score_type'      => 'COLLABORATION',
            'score'           => $score,
            'calculated_at'   => date('Y-m-d H:i:s'),
        ]);

        // Denormalize to campaign_kols
        $this->db->update('campaign_kols', [
            'collaboration_score' => $score,
        ], ['id' => $campaign_kol_id]);

        $this->_log("  Collaboration score for campaign_kol #{$campaign_kol_id} = {$score}");
    }

    /**
     * Calculate lifetime score for a KOL.
     *
     * Weighted composite formula:
     *   lifetime_score = (0.40 × norm_er) + (0.30 × cpv_efficiency) + (0.30 × completion_rate)
     *
     * Where:
     *   - norm_er         = AVG(engagement_rate) × 100 (scaled to 0-100 range)
     *   - cpv_efficiency  = 100 − MIN(AVG(cpv) / 1000 × 100, 100)
     *                       → lower CPV = higher efficiency score (capped 0-100)
     *   - completion_rate = (completed tasks / total non-cancelled tasks) × 100
     *
     * Inserts a score_histories row (type = LIFETIME) and updates kols.lifetime_score.
     *
     * @param int $kol_id
     */
    private function _calculate_kol_lifetime_score($kol_id)
    {
        // ── 1. AVG engagement_rate across ALL metrics for this KOL ─
        $er_sql = "
            SELECT AVG(pm.engagement_rate) AS avg_er
            FROM   posting_metrics pm
            JOIN   postings p       ON p.id  = pm.posting_id
            JOIN   tasks t          ON t.id  = p.task_id
            JOIN   campaign_kols ck ON ck.id = t.campaign_kol_id
            WHERE  ck.kol_id = ?
        ";
        $er_row = $this->db->query($er_sql, [$kol_id])->row();
        $avg_er = ($er_row && $er_row->avg_er !== null) ? (float) $er_row->avg_er : 0;

        // Normalize: ER is typically 0.0–1.0 range; scale to 0–100
        // Cap at 100 for edge cases where ER > 1
        $norm_er = min($avg_er * 100, 100);

        // ── 2. CPV efficiency ─────────────────────────────────────
        $cpv_sql = "
            SELECT AVG(pm.cpv) AS avg_cpv
            FROM   posting_metrics pm
            JOIN   postings p       ON p.id  = pm.posting_id
            JOIN   tasks t          ON t.id  = p.task_id
            JOIN   campaign_kols ck ON ck.id = t.campaign_kol_id
            WHERE  ck.kol_id = ?
              AND  pm.cpv IS NOT NULL
        ";
        $cpv_row = $this->db->query($cpv_sql, [$kol_id])->row();
        $avg_cpv = ($cpv_row && $cpv_row->avg_cpv !== null) ? (float) $cpv_row->avg_cpv : 0;

        // Lower CPV = better. Scale: CPV of 0 = score 100, CPV of 1000+ = score 0.
        // Formula: 100 - MIN((avg_cpv / 1000) * 100, 100)
        $cpv_efficiency = max(100 - min(($avg_cpv / 1000) * 100, 100), 0);

        // ── 3. Task completion rate ───────────────────────────────
        $task_sql = "
            SELECT
                COUNT(*) AS total,
                SUM(CASE WHEN t.status = 'DONE' THEN 1 ELSE 0 END) AS completed
            FROM   tasks t
            JOIN   campaign_kols ck ON ck.id = t.campaign_kol_id
            WHERE  ck.kol_id  = ?
              AND  t.status  != 'CANCELLED'
        ";
        $task_row = $this->db->query($task_sql, [$kol_id])->row();
        $total     = ($task_row) ? (int) $task_row->total     : 0;
        $completed = ($task_row) ? (int) $task_row->completed : 0;

        $completion_rate = ($total > 0) ? ($completed / $total) * 100 : 0;

        // ── 4. Weighted composite ─────────────────────────────────
        $lifetime_score = round(
            (0.40 * $norm_er) + (0.30 * $cpv_efficiency) + (0.30 * $completion_rate),
            2
        );

        $this->_log("  Lifetime score for KOL #{$kol_id}: ER={$norm_er}, CPV_eff={$cpv_efficiency}, completion={$completion_rate} => {$lifetime_score}");

        // ── 5. Insert score_histories ─────────────────────────────
        $this->db->insert('score_histories', [
            'kol_id'          => $kol_id,
            'campaign_kol_id' => null,
            'score_type'      => 'LIFETIME',
            'score'           => $lifetime_score,
            'calculated_at'   => date('Y-m-d H:i:s'),
        ]);

        // ── 6. Denormalize to kols.lifetime_score ─────────────────
        $this->db->update('kols', [
            'lifetime_score' => $lifetime_score,
        ], ['id' => $kol_id]);
    }

    // ================================================================
    //  PRIVATE: UTILITIES
    // ================================================================

    /**
     * Safely read a value from cc_options (Cicool settings table).
     * Returns null if the function or table doesn't exist.
     *
     * @param  string $key
     * @return string|null
     */
    private function _get_option_safe($key)
    {
        if (function_exists('get_option')) {
            $val = get_option($key);
            return (!empty($val)) ? $val : null;
        }
        return null;
    }

    /**
     * Log a timestamped message to STDOUT (CLI output).
     *
     * @param string $message
     */
    private function _log($message)
    {
        $timestamp = date('Y-m-d H:i:s');
        echo "[{$timestamp}] {$message}" . PHP_EOL;
    }
}

/* End of file Apify_service.php */
/* Location: ./application/controllers/Apify_service.php */
