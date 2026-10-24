<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Internal Evaluation — Operational staff scorecard.
 *
 * NOTE (cleanup 2026-09-28): unused Leader/Admin/Executive/Scatter/Drilldown
 * methods were removed from the hot path. Exact copy archived at:
 *   modules/internal_evaluation/models/Model_internal_evaluation_legacy_backup_20260928.php
 * Re-wire from backup if Tabs 1/2/4 are approved.
 */
class Model_internal_evaluation extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Tunable brackets from application/config/internal_evaluation.php
     * with safe hardcoded fallback.
     */
    private function _thresholds() {
        $defaults = [
            'on_time_brackets' => [
                ['min' => 90, 'points' => 40],
                ['min' => 75, 'points' => 30],
                ['min' => 50, 'points' => 20],
                ['min' => 0,  'points' => 10],
            ],
            'tat_brackets' => [
                ['max' => 2,  'points' => 30],
                ['max' => 8,  'points' => 25],
                ['max' => 24, 'points' => 15],
                ['max' => PHP_FLOAT_MAX, 'points' => 5],
            ],
            'dispatch_brackets' => [
                ['max' => 1,  'points' => 30],
                ['max' => 3,  'points' => 25],
                ['max' => 5,  'points' => 15],
                ['max' => PHP_FLOAT_MAX, 'points' => 5],
            ],
            'grade_a_min' => 85,
            'grade_b_min' => 65,
            'tasks_per_staff_limit' => 50,
            'staff_page_size' => 200,
            // Provisional-grade guards: below these samples (or with a missing
            // dimension) the grade is shown as "Sementara" (provisional).
            'min_decided_postings' => 5,
            'min_reviewed_drafts' => 3,
            'provisional_suffix' => 'Sementara',
            // Grace period after campaign end_date before a COMPLETED campaign
            // grade becomes final (waiting for Apify scraping to mature).
            'evaluation_grace_days' => 7,

            // Leader evaluation fallback defaults
            'leader_target_user_ids' => [25],
            'leader_view_brackets' => [
                ['min' => 100, 'points' => 50],
                ['min' => 80,  'points' => 40],
                ['min' => 50,  'points' => 20],
                ['min' => 0,   'points' => 10],
            ],
            'leader_cpv_brackets' => [
                ['max' => 50,  'points' => 20],
                ['max' => 100, 'points' => 15],
                ['max' => 200, 'points' => 5],
                ['max' => PHP_FLOAT_MAX, 'points' => 0],
            ],
            'leader_deal_brackets' => [
                ['min' => 50, 'points' => 15],
                ['min' => 30, 'points' => 10],
                ['min' => 10, 'points' => 5],
                ['min' => 0,  'points' => 0],
            ],
            'leader_approval_brackets' => [
                ['max' => 2,  'points' => 15],
                ['max' => 8,  'points' => 10],
                ['max' => 24, 'points' => 5],
                ['max' => PHP_FLOAT_MAX, 'points' => 0],
            ],
            'leader_grade_a_min' => 85,
            'leader_grade_b_min' => 65,
            'min_leader_campaigns' => 3,
        ];
        $this->load->config('internal_evaluation', TRUE);
        $cfg = $this->config->item('internal_evaluation');
        
        // Fix double nesting (if file contains $config['internal_evaluation'] instead of raw keys)
        if (is_array($cfg) && isset($cfg['internal_evaluation'])) {
            $cfg = $cfg['internal_evaluation'];
        }

        if (is_array($cfg)) {
            foreach ($cfg as $k => $v) {
                $defaults[$k] = $v;
            }
        }
        return $defaults;
    }

    private function _points_from_brackets($value, $brackets, $mode = 'min') {
        foreach ($brackets as $b) {
            if ($mode === 'min') {
                if ($value >= $b['min']) return (int)$b['points'];
            } else {
                if ($value <= $b['max']) return (int)$b['points'];
            }
        }
        return 0;
    }

    /**
     * Per-campaign maturity for provisional ("Sementara") status.
     * Returns null when no single-campaign filter is active (caller keeps
     * the aggregate sample-size guards). Otherwise returns
     * ['provisional' => bool, 'reason' => string|null].
     *
     * Rule: a filtered campaign is final only when status is
     * COMPLETED/CANCELLED AND end_date + evaluation_grace_days has passed
     * (waiting for Apify scraping H+3/H+7 to mature).
     */
    private function _single_campaign_maturity($campaign_id, $cfg) {
        $cid = (int)$campaign_id;
        if ($cid <= 0) {
            return null;
        }
        $row = $this->db->select('id, status, end_date')
            ->from('campaigns')
            ->where('id', $cid)
            ->get()->row();
        $this->db->reset_query();
        if (!$row) {
            return null;
        }
        $grace = (int)($cfg['evaluation_grace_days'] ?? 7);
        $status = strtoupper(trim((string)($row->status ?? '')));
        if (!in_array($status, ['COMPLETED', 'CANCELLED'], true)) {
            return [
                'provisional' => true,
                'reason' => 'campaign belum selesai (status: ' . ($row->status ?: '-') . ')',
            ];
        }
        if (empty($row->end_date)) {
            return [
                'provisional' => true,
                'reason' => 'tanggal selesai belum diisi, menunggu scraping',
            ];
        }
        $end_ts = strtotime($row->end_date . ' 23:59:59');
        if ($end_ts === false) {
            return [
                'provisional' => true,
                'reason' => 'tanggal selesai belum valid, menunggu scraping',
            ];
        }
        $final_ts = $end_ts + max(0, $grace) * 86400;
        if (time() < $final_ts) {
            return [
                'provisional' => true,
                'reason' => 'menunggu scraping hingga H+' . $grace . ' (final ' . date('d M Y', $final_ts) . ')',
            ];
        }
        return ['provisional' => false, 'reason' => null];
    }

    public function get_campaigns_list() {
        return $this->db->select('id, name, status, start_date, end_date')
            ->from('campaigns')
            ->order_by("CASE WHEN status = 'ACTIVE' THEN 1 WHEN status = 'OPEN_REGISTRATION' THEN 2 WHEN status = 'COMPLETED' THEN 3 ELSE 4 END, id DESC")
            ->get()->result();
    }

    /**
     * Base staff query builder (no limit) — shared by list + count.
     */
    private function _staff_base_query($campaign_id = null) {
        $this->db->select('u.id, u.username, u.full_name, u.avatar, u.email');
        $this->db->from('aauth_users u');
        $this->db->join('aauth_user_to_group ug', 'ug.user_id = u.id', 'inner');
        $this->db->join('aauth_groups g', 'g.id = ug.group_id', 'inner');
        // Single source of truth: group name. (Legacy also accepted id 5.)
        $this->db->where('g.name', 'Operational');

        $cid = (int)$campaign_id;
        if ($cid > 0) {
            $this->db->where("u.id IN (
                SELECT DISTINCT ck.assigned_by FROM campaign_kols ck
                WHERE ck.campaign_id = " . $cid . " AND ck.assigned_by IS NOT NULL
                UNION
                SELECT DISTINCT t.created_by FROM tasks t
                JOIN campaign_kols ck ON ck.id = t.campaign_kol_id 
                WHERE ck.campaign_id = " . $cid . " AND t.created_by IS NOT NULL
            )", NULL, FALSE);
        } else {
            $this->db->where("u.id IN (
                SELECT DISTINCT ck.assigned_by FROM campaign_kols ck WHERE ck.assigned_by IS NOT NULL
                UNION
                SELECT DISTINCT t.created_by FROM tasks t WHERE t.created_by IS NOT NULL
            )", NULL, FALSE);
        }
        $this->db->group_by('u.id');
        return $this;
    }

    public function get_staff_operational_count($campaign_id = null) {
        $this->_staff_base_query($campaign_id);
        // Count grouped rows without fetching them
        $sql = $this->db->get_compiled_select();
        $row = $this->db->query("SELECT COUNT(*) AS c FROM (" . $sql . ") x")->row();
        $this->db->reset_query();
        return (int)($row->c ?? 0);
    }

    /**
     * Batched KPI fetch: 1 staff query + 4 aggregate queries + 1 tasks query.
     * No per-staff / per-task queries (fixes N+1).
     */
    public function get_staff_operational_kpis($campaign_id = null, $limit = 200, $offset = 0) {
        $cfg = $this->_thresholds();
        $cid = (int)$campaign_id;
        $camp_where = ($cid > 0) ? " AND ck.campaign_id = " . $cid : "";
        // Per-campaign maturity (COMPLETED + H+grace). Null in "Semua" mode.
        $maturity = $this->_single_campaign_maturity($cid, $cfg);

        // 1. Staff page
        $this->_staff_base_query($cid > 0 ? $cid : null);
        $this->db->order_by('u.full_name', 'ASC');
        $this->db->limit((int)$limit, (int)$offset);
        $staff_list = $this->db->get()->result();
        $this->db->reset_query();

        if (empty($staff_list)) {
            return [];
        }

        $staff_ids = array_map(function($s) { return (int)$s->id; }, $staff_list);
        $id_list = implode(',', $staff_ids);

        // 2. KOL counts per staff (batched)
        $kol_rows = $this->db->query("
            SELECT ck.assigned_by AS staff_id,
                   COUNT(DISTINCT ck.id) AS kol_count,
                   COALESCE(SUM(ck.deal_amount), 0) AS total_deal_amount
            FROM campaign_kols ck
            WHERE ck.assigned_by IN (" . $id_list . ")" . $camp_where . "
            GROUP BY ck.assigned_by
        ")->result();
        $kol_map = [];
        foreach ($kol_rows as $r) { $kol_map[(int)$r->staff_id] = $r; }

        // 3. Tasks + postings aggregates per staff (batched)
        // on_time_rate denominator = decided postings (ON_TIME + LATE) only;
        // PENDING validation stays visible via pending_postings but does not drag the rate.
        $task_rows = $this->db->query("
            SELECT ck.assigned_by AS staff_id,
                   COUNT(DISTINCT t.id) AS total_tasks,
                   SUM(CASE WHEN t.status = 'DONE' THEN 1 ELSE 0 END) AS completed_tasks,
                   SUM(CASE WHEN p.id IS NOT NULL THEN 1 ELSE 0 END) AS total_postings,
                   SUM(CASE WHEN p.date_validation_status = 'ON_TIME' THEN 1 ELSE 0 END) AS on_time_postings,
                   SUM(CASE WHEN p.date_validation_status = 'LATE' THEN 1 ELSE 0 END) AS late_postings
            FROM campaign_kols ck
            JOIN tasks t ON t.campaign_kol_id = ck.id
            LEFT JOIN postings p ON p.task_id = t.id
            WHERE ck.assigned_by IN (" . $id_list . ")" . $camp_where . "
            GROUP BY ck.assigned_by
        ")->result();
        $task_map = [];
        foreach ($task_rows as $r) { $task_map[(int)$r->staff_id] = $r; }

        // 3b. Actual Views, Engagements & Spent per staff (batched)
        $perf_rows = $this->db->query("
            SELECT ck.assigned_by AS staff_id,
                   COALESCE(SUM(t.budget), 0) AS actual_spent,
                   COALESCE(SUM(pm.views), 0) AS actual_views,
                   COALESCE(SUM(pm.likes + pm.comments + pm.shares + pm.saves), 0) AS actual_engagements
            FROM campaign_kols ck
            JOIN tasks t ON t.campaign_kol_id = ck.id
            LEFT JOIN postings p ON p.task_id = t.id
            LEFT JOIN posting_metrics pm ON pm.id = COALESCE(p.latest_metric_id, (SELECT MAX(pm2.id) FROM posting_metrics pm2 WHERE pm2.posting_id = p.id))
            WHERE ck.assigned_by IN (" . $id_list . ")" . $camp_where . "
            GROUP BY ck.assigned_by
        ")->result();
        $perf_map = [];
        foreach ($perf_rows as $r) { $perf_map[(int)$r->staff_id] = $r; }

        // 4. Drafts: revision depth + TAT from draft_review_histories (SUBMITTED -> first REVISION/APPROVED).
        // Falls back to drafts.created->updated only when no history row exists (counted as tat_fallback).
        $draft_rows = $this->db->query("
            SELECT staff_id,
                   COUNT(*) AS total_drafts,
                   AVG(version) AS avg_version,
                   AVG(tat_hours) AS avg_tat,
                   SUM(is_fallback) AS tat_fallback,
                   SUM(CASE WHEN is_fallback = 0 THEN 1 ELSE 0 END) AS reviewed_drafts
            FROM (
                SELECT ck.assigned_by AS staff_id, d.id, d.version,
                       CASE WHEN MIN(h.created_at) IS NOT NULL
                            THEN TIMESTAMPDIFF(MINUTE, d.created_at, MIN(h.created_at)) / 60
                            ELSE GREATEST(TIMESTAMPDIFF(MINUTE, d.created_at, d.updated_at) / 60, 0.1)
                       END AS tat_hours,
                       CASE WHEN MIN(h.created_at) IS NOT NULL THEN 0 ELSE 1 END AS is_fallback
                FROM drafts d
                JOIN tasks t ON t.id = d.task_id
                JOIN campaign_kols ck ON ck.id = t.campaign_kol_id
                LEFT JOIN draft_review_histories h
                  ON h.draft_id = d.id AND h.action IN ('REVISION', 'APPROVED')
                WHERE ck.assigned_by IN (" . $id_list . ")" . $camp_where . "
                  AND d.status IN ('APPROVED', 'REVISION')
                GROUP BY d.id
            ) x
            GROUP BY staff_id
        ")->result();
        $draft_map = [];
        foreach ($draft_rows as $r) { $draft_map[(int)$r->staff_id] = $r; }

        // Draft totals (all statuses) for revision context per staff
        $draft_total_rows = $this->db->query("
            SELECT ck.assigned_by AS staff_id, COUNT(*) AS all_drafts
            FROM drafts d
            JOIN tasks t ON t.id = d.task_id
            JOIN campaign_kols ck ON ck.id = t.campaign_kol_id
            WHERE ck.assigned_by IN (" . $id_list . ")" . $camp_where . "
            GROUP BY ck.assigned_by
        ")->result();
        $draft_total_map = [];
        foreach ($draft_total_rows as $r) { $draft_total_map[(int)$r->staff_id] = (int)$r->all_drafts; }

        // 5. Shipments per staff: valid dispatch avg + pending (not yet shipped) count.
        $ship_rows = $this->db->query("
            SELECT ck.assigned_by AS staff_id,
                   SUM(CASE WHEN s.shipped_at IS NOT NULL
                             AND DATEDIFF(s.shipped_at, ck.deal_date) >= 0 THEN 1 ELSE 0 END) AS valid_shipments,
                   AVG(CASE WHEN s.shipped_at IS NOT NULL
                             AND DATEDIFF(s.shipped_at, ck.deal_date) >= 0
                            THEN DATEDIFF(s.shipped_at, ck.deal_date) ELSE NULL END) AS avg_dispatch,
                   SUM(CASE WHEN s.shipped_at IS NULL THEN 1 ELSE 0 END) AS pending_shipments,
                   COUNT(*) AS all_shipments
            FROM kol_shipments s
            JOIN campaign_kols ck ON ck.id = s.campaign_kol_id
            WHERE ck.assigned_by IN (" . $id_list . ")" . $camp_where . "
            GROUP BY ck.assigned_by
        ")->result();
        $ship_map = [];
        foreach ($ship_rows as $r) { $ship_map[(int)$r->staff_id] = $r; }

        // 6. Task drill-down rows for this staff page (single query, capped per staff in PHP)
        $per_staff_cap = (int)($cfg['tasks_per_staff_limit'] ?? 50);
        $detail_rows = $this->db->query("
            SELECT ck.assigned_by AS staff_id,
                   t.id AS task_id, t.title AS task_title, t.status AS task_status,
                   t.budget AS task_budget, t.estimated_posting_date,
                   k.id AS kol_id, k.name AS kol_name, k.avatar_url AS kol_avatar, k.email AS kol_username,
                   c.id AS campaign_id, c.name AS campaign_name,
                   p.id AS posting_id, p.date_validation_status, p.actual_posted_at, p.live_url,
                   COALESCE(pm.views, 0) AS views,
                   COALESCE(pm.likes + pm.comments + pm.shares + pm.saves, 0) AS engagements
            FROM campaign_kols ck
            JOIN tasks t ON t.campaign_kol_id = ck.id
            JOIN kols k ON k.id = ck.kol_id
            JOIN campaigns c ON c.id = ck.campaign_id
            LEFT JOIN postings p ON p.task_id = t.id
            LEFT JOIN posting_metrics pm ON pm.id = COALESCE(p.latest_metric_id, (SELECT MAX(pm2.id) FROM posting_metrics pm2 WHERE pm2.posting_id = p.id))
            WHERE ck.assigned_by IN (" . $id_list . ")" . $camp_where . "
            ORDER BY t.id DESC
            LIMIT 2000
        ")->result();
        $tasks_map = [];
        foreach ($detail_rows as $t) {
            $sid = (int)$t->staff_id;
            if (!isset($tasks_map[$sid])) $tasks_map[$sid] = [];
            $t->cpv = ($t->views > 0 && (float)$t->task_budget > 0) ? round((float)$t->task_budget / $t->views, 2) : 0;
            $t->cpe = ($t->engagements > 0 && (float)$t->task_budget > 0) ? round((float)$t->task_budget / $t->engagements, 2) : 0;
            if (count($tasks_map[$sid]) < $per_staff_cap) $tasks_map[$sid][] = $t;
        }

        // 7. Hydrate
        $results = [];
        foreach ($staff_list as $staff) {
            $sid = (int)$staff->id;
            $item = new stdClass();
            $item->id = $staff->id;
            $item->username = $staff->username;
            $item->full_name = $staff->full_name ?: $staff->username;
            $item->avatar = $staff->avatar;
            $item->email = $staff->email;

            $k = $kol_map[$sid] ?? null;
            $item->kol_count = (int)($k->kol_count ?? 0);
            $item->total_deal_amount = (float)($k->total_deal_amount ?? 0);

            $pf = $perf_map[$sid] ?? null;
            $item->actual_spent = (float)($pf->actual_spent ?? 0);
            $item->actual_views = (int)($pf->actual_views ?? 0);
            $item->actual_engagements = (int)($pf->actual_engagements ?? 0);
            $item->actual_cpv = ($item->actual_views > 0) ? round($item->actual_spent / $item->actual_views, 2) : 0;
            $item->actual_cpe = ($item->actual_engagements > 0) ? round($item->actual_spent / $item->actual_engagements, 2) : 0;

            $t = $task_map[$sid] ?? null;
            $item->total_tasks = (int)($t->total_tasks ?? 0);
            $item->completed_tasks = (int)($t->completed_tasks ?? 0);
            $item->total_postings = (int)($t->total_postings ?? 0);
            $item->on_time_postings = (int)($t->on_time_postings ?? 0);
            $item->late_postings = (int)($t->late_postings ?? 0);
            $item->pending_postings = max(0, $item->total_postings - $item->on_time_postings - $item->late_postings);

            $decided = $item->on_time_postings + $item->late_postings;
            $item->decided_postings = $decided;
            $item->on_time_rate = ($decided > 0) ? round(($item->on_time_postings / $decided) * 100, 1) : null;
            $item->task_completion_rate = ($item->total_tasks > 0) ? round(($item->completed_tasks / $item->total_tasks) * 100, 1) : 0;

            $d = $draft_map[$sid] ?? null;
            $item->total_drafts = (int)($draft_total_map[$sid] ?? 0);
            $item->avg_revision_cycles = ($item->total_drafts > 0 && $d && $d->avg_version !== null) ? round((float)$d->avg_version, 1) : null;
            $item->avg_turnaround_hours = ($d && $d->avg_tat !== null) ? round((float)$d->avg_tat, 1) : null;
            $item->tat_fallback_count = (int)($d->tat_fallback ?? 0);
            // Drafts with a real review-history trail (decisive REVISION/APPROVED row).
            $item->reviewed_drafts = (int)($d->reviewed_drafts ?? 0);

            $s = $ship_map[$sid] ?? null;
            $item->total_shipments = (int)($s->all_shipments ?? 0);
            $item->pending_shipments = (int)($s->pending_shipments ?? 0);
            $item->avg_dispatch_days = ($s && $s->avg_dispatch !== null) ? round((float)$s->avg_dispatch, 1) : null;

            // Strict PENDING: no KOLs and no tasks => excluded from team avgs, no free Grade B.
            if ($item->kol_count == 0 && $item->total_tasks == 0) {
                $item->excluded = true;
                $item->grade = 'PENDING';
                $item->grade_label = 'Belum Bertugas';
                $item->grade_badge = 'badge-soft-slate';
                $item->score = null;
                $item->max_earned = 0;
                $item->is_provisional = false;
                $item->provisional_reasons = [];
                $item->reviewed_drafts = 0;
                $item->decided_postings = 0;
                $item->missing_dimensions = ['on_time', 'tat', 'dispatch'];
                $item->tasks = [];
                $results[] = $item;
                continue;
            }
            $item->excluded = false;

            // Raw scoring: missing dimension contributes 0 (not a neutral baseline).
            // Score keeps its 0-100 meaning; max_earned gives context ("65 dari maks 70").
            $missing = [];
            $score = 0;
            $max_earned = 0;
            if ($item->on_time_rate !== null) {
                $score += $this->_points_from_brackets($item->on_time_rate, $cfg['on_time_brackets'], 'min');
                $max_earned += 40;
            } else {
                $missing[] = 'on_time';
            }
            if ($item->avg_turnaround_hours !== null) {
                $score += $this->_points_from_brackets($item->avg_turnaround_hours, $cfg['tat_brackets'], 'max');
                $max_earned += 30;
            } else {
                $missing[] = 'tat';
            }
            if ($item->avg_dispatch_days !== null) {
                $score += $this->_points_from_brackets($item->avg_dispatch_days, $cfg['dispatch_brackets'], 'max');
                $max_earned += 30;
            } else {
                $missing[] = 'dispatch';
            }
            $item->missing_dimensions = $missing;
            $item->score = $score;
            $item->max_earned = $max_earned;

            // Provisional guard ("Sementara").
            // Per-campaign mode: final only when campaign COMPLETED/CANCELLED
            // and end_date + grace days has passed (scraping matured).
            // Aggregate ("Semua") mode keeps the sample-size guards.
            $prov_reasons = [];
            if ($maturity !== null) {
                if (!empty($maturity['provisional']) && !empty($maturity['reason'])) {
                    $prov_reasons[] = $maturity['reason'];
                }
            } else {
                $min_posts = (int)($cfg['min_decided_postings'] ?? 5);
                $min_drafts = (int)($cfg['min_reviewed_drafts'] ?? 3);
                if ($item->decided_postings < $min_posts) {
                    $prov_reasons[] = 'baru ' . $item->decided_postings . ' dari ' . $min_posts . ' konten dinilai';
                }
                if ($item->reviewed_drafts < $min_drafts) {
                    $prov_reasons[] = 'baru ' . $item->reviewed_drafts . ' dari ' . $min_drafts . ' draf diulas';
                }
            }
            if (!empty($missing)) {
                $dim_names = ['on_time' => 'ketepatan tayang', 'tat' => 'balasan draf', 'dispatch' => 'kirim sampel'];
                $readable = array_map(function($m) use ($dim_names) { return $dim_names[$m] ?? $m; }, $missing);
                $prov_reasons[] = 'belum ada data: ' . implode(', ', $readable);
            }
            $item->is_provisional = !empty($prov_reasons);
            $item->provisional_reasons = $prov_reasons;

            if ($score >= $cfg['grade_a_min']) {
                $item->grade = 'A';
                $item->grade_label = 'Grade A (Sangat Baik)';
                $item->grade_badge = 'badge-soft-emerald';
            } elseif ($score >= $cfg['grade_b_min']) {
                $item->grade = 'B';
                $item->grade_label = 'Grade B (Baik)';
                $item->grade_badge = 'badge-soft-blue';
            } else {
                $item->grade = 'C';
                $item->grade_label = 'Grade C (Perlu Evaluasi)';
                $item->grade_badge = 'badge-soft-rose';
            }
            if ($item->is_provisional) {
                $suffix = $cfg['provisional_suffix'] ?? 'Sementara';
                $item->grade_label .= ' (' . $suffix . ')';
            }

            $item->tasks = $tasks_map[$sid] ?? [];
            $results[] = $item;
        }

        return $results;
    }

    /**
     * Legend data for the view / export, built from the SAME config array as
     * scoring so displayed brackets can never drift from the actual logic.
     * Returns ['grades' => [...], 'groups' => [...], 'minimums' => [...]].
     */
    public function get_legend() {
        $cfg = $this->_thresholds();
        // Kalimat non-teknis, tetap dibentuk dari config yang sama dengan skoring.
        $fmt_rate = function($brackets) {
            $parts = [];
            $prev_min = null;
            foreach ($brackets as $b) {
                $min = (int)$b['min'];
                $pts = (int)$b['points'];
                if ($prev_min === null) {
                    $parts[] = $min . '% ke atas = ' . $pts . ' poin';
                } elseif ($min <= 0) {
                    $parts[] = 'di bawah ' . $prev_min . '% = ' . $pts . ' poin';
                } else {
                    $parts[] = $min . '% sampai di bawah ' . $prev_min . '% = ' . $pts . ' poin';
                }
                $prev_min = $min;
            }
            return implode('; ', $parts);
        };
        $fmt_speed = function($brackets, $unit) {
            $parts = [];
            $prev_max = null;
            foreach ($brackets as $b) {
                $pts = (int)$b['points'];
                $is_last = ($b['max'] == PHP_FLOAT_MAX);
                if ($prev_max === null && !$is_last) {
                    $parts[] = 'sampai ' . $b['max'] . ' ' . $unit . ' = ' . $pts . ' poin';
                } elseif ($is_last) {
                    $parts[] = 'lebih dari ' . $prev_max . ' ' . $unit . ' = ' . $pts . ' poin';
                } else {
                    $parts[] = 'lebih dari ' . $prev_max . ' sampai ' . $b['max'] . ' ' . $unit . ' = ' . $pts . ' poin';
                }
                if (!$is_last) {
                    $prev_max = $b['max'];
                }
            }
            return implode('; ', $parts);
        };
        $grade_a_min = (int)$cfg['grade_a_min'];
        $grade_b_min = (int)$cfg['grade_b_min'];
        $min_decided = (int)$cfg['min_decided_postings'];
        $min_drafts = (int)$cfg['min_reviewed_drafts'];
        $grace_days = (int)($cfg['evaluation_grace_days'] ?? 7);
        return [
            'grades' => [
                ['label' => 'Grade A (Sangat Baik)', 'badge' => 'badge-soft-emerald', 'rule' => 'Skor ' . $grade_a_min . ' ke atas'],
                ['label' => 'Grade B (Baik)', 'badge' => 'badge-soft-blue', 'rule' => 'Skor ' . $grade_b_min . ' sampai di bawah ' . $grade_a_min],
                ['label' => 'Grade C (Perlu Evaluasi)', 'badge' => 'badge-soft-rose', 'rule' => 'Skor di bawah ' . $grade_b_min],
                ['label' => 'Belum Bertugas', 'badge' => 'badge-soft-slate', 'rule' => 'Belum menangani KOL dan belum ada task, tidak ikut rata-rata tim'],
                ['label' => 'Nilai Sementara*', 'badge' => 'badge-soft-amber', 'rule' => 'Campaign belum selesai / masih dalam masa scraping H+' . $grace_days . ' setelah end date; di mode Semua Kampanye juga jika data masih sedikit (kurang dari ' . $min_decided . ' konten dinilai / kurang dari ' . $min_drafts . ' draf diulas) atau ada bagian yang belum ada datanya'],
            ],
            'groups' => [
                [
                    'title' => 'Ketepatan Waktu Tayang (bobot 40 poin) - Staff',
                    'desc' => 'Berapa banyak konten yang tayang tepat waktu dari yang sudah dinilai. Yang masih menunggu tidak mengurangi nilai. ' . $fmt_rate($cfg['on_time_brackets']) . '.',
                ],
                [
                    'title' => 'Kecepatan Membalas Draf (bobot 30 poin) - Staff',
                    'desc' => 'Rata-rata waktu staf membalas draf dalam jam. Makin cepat makin tinggi. ' . $fmt_speed($cfg['tat_brackets'], 'jam') . '.',
                ],
                [
                    'title' => 'Kecepatan Kirim Sampel (bobot 30 poin) - Staff',
                    'desc' => 'Rata-rata hari dari kesepakatan sampai paket dikirim. Paket yang belum dikirim dihitung terpisah. ' . $fmt_speed($cfg['dispatch_brackets'], 'hari') . '.',
                ],
                [
                    'title' => 'Views Attainment (bobot 50 poin) - Leader',
                    'desc' => 'Pencapaian jumlah tayangan dibandingkan target campaign. ' . $fmt_rate($cfg['leader_view_brackets'] ?? []) . '.',
                ],
                [
                    'title' => 'CPV Efficiency (bobot 20 poin) - Leader',
                    'desc' => 'Efisiensi Cost Per View. Makin rendah CPV makin baik. ' . $fmt_speed($cfg['leader_cpv_brackets'] ?? [], 'Rupiah/View') . '.',
                ],
                [
                    'title' => 'Deal Conversion (bobot 15 poin) - Leader',
                    'desc' => 'Persentase KOL yang berhasil mencapai deal dari total yang dinegosiasikan. ' . $fmt_rate($cfg['leader_deal_brackets'] ?? []) . '.',
                ],
                [
                    'title' => 'Approval TAT Leader (bobot 15 poin) - Leader',
                    'desc' => 'Kecepatan leader menyetujui draf konten dalam jam. ' . $fmt_speed($cfg['leader_approval_brackets'] ?? [], 'jam') . '.',
                ],
            ],
            'minimums' => [
                'decided' => $min_decided,
                'drafts' => $min_drafts,
                'grace_days' => $grace_days,
            ],
        ];
    }

    public function get_operational_team_summary($staff_kpis = []) {
        $summary = new stdClass();
        $summary->total_staff = count($staff_kpis);
        $summary->active_staff = 0;
        $summary->pending_staff = 0;
        $summary->provisional_staff = 0;
        $summary->total_kols = 0;
        $summary->total_tasks = 0;
        $summary->completed_tasks = 0;
        $summary->total_postings = 0;
        $summary->on_time_postings = 0;
        $summary->late_postings = 0;
        $summary->decided_postings = 0;

        $turnaround_sum = 0;
        $turnaround_count = 0;
        $dispatch_sum = 0;
        $dispatch_count = 0;
        $total_spent = 0;
        $total_views = 0;
        $total_engagements = 0;

        foreach ($staff_kpis as $s) {
            // PENDING staff: counted for roster size only, excluded from all averages.
            if (!empty($s->excluded)) {
                $summary->pending_staff++;
                continue;
            }
            if ($s->kol_count > 0 || $s->total_tasks > 0) {
                $summary->active_staff++;
            }
            if (!empty($s->is_provisional)) {
                $summary->provisional_staff++;
            }
            $summary->total_kols += $s->kol_count;
            $summary->total_tasks += $s->total_tasks;
            $summary->completed_tasks += $s->completed_tasks;
            $summary->total_postings += $s->total_postings;
            $summary->on_time_postings += $s->on_time_postings;
            $summary->late_postings += $s->late_postings;
            $summary->decided_postings += (int)($s->decided_postings ?? ($s->on_time_postings + $s->late_postings));

            $total_spent += (float)($s->actual_spent ?? 0);
            $total_views += (int)($s->actual_views ?? 0);
            $total_engagements += (int)($s->actual_engagements ?? 0);

            if ($s->avg_turnaround_hours !== null) {
                $turnaround_sum += $s->avg_turnaround_hours;
                $turnaround_count++;
            }
            if ($s->avg_dispatch_days !== null) {
                $dispatch_sum += $s->avg_dispatch_days;
                $dispatch_count++;
            }
        }

        // Decided-only rate: pending validations don't drag the team rate.
        $summary->team_on_time_rate = ($summary->decided_postings > 0) ? round(($summary->on_time_postings / $summary->decided_postings) * 100, 1) : 0;
        $summary->team_avg_turnaround_hours = ($turnaround_count > 0) ? round($turnaround_sum / $turnaround_count, 1) : 0;
        $summary->team_avg_dispatch_days = ($dispatch_count > 0) ? round($dispatch_sum / $dispatch_count, 1) : 0;
        $summary->task_completion_rate = ($summary->total_tasks > 0) ? round(($summary->completed_tasks / $summary->total_tasks) * 100, 1) : 0;

        $summary->total_spent = $total_spent;
        $summary->total_views = $total_views;
        $summary->total_engagements = $total_engagements;
        $summary->team_avg_cpv = ($total_views > 0) ? round($total_spent / $total_views, 2) : 0;
        $summary->team_avg_cpe = ($total_engagements > 0) ? round($total_spent / $total_engagements, 2) : 0;

        return $summary;
    }
    // ==========================================
    // LEADER EVALUATION (T5)
    // ==========================================

    public function _leader_base_query($campaign_id = null) {
        $cfg = $this->_thresholds();
        $target_ids = $cfg['leader_target_user_ids'] ?? [25];
        
        $this->db->select('u.id, u.username, u.full_name, u.avatar, u.email');
        $this->db->from('aauth_users u');
        $this->db->where_in('u.id', $target_ids);
        
        $cid = (int)$campaign_id;
        if ($cid > 0) {
            $this->db->where("u.id IN (SELECT pic_user_id FROM campaigns WHERE id = {$cid} AND pic_user_id IS NOT NULL)", NULL, FALSE);
        } else {
            $this->db->where("u.id IN (SELECT pic_user_id FROM campaigns WHERE pic_user_id IS NOT NULL)", NULL, FALSE);
        }
        $this->db->group_by('u.id');
        return $this;
    }

    public function get_leader_count($campaign_id = null) {
        $this->_leader_base_query($campaign_id);
        $sql = $this->db->get_compiled_select();
        $row = $this->db->query("SELECT COUNT(*) AS c FROM (" . $sql . ") x")->row();
        $this->db->reset_query();
        return (int)($row->c ?? 0);
    }

    public function get_leader_kpis($campaign_id = null, $limit = 200, $offset = 0) {
        $cfg = $this->_thresholds();
        $cid = (int)$campaign_id;
        $camp_where = ($cid > 0) ? " AND c.id = " . $cid : "";
        // Per-campaign maturity (COMPLETED + H+grace). Null in "Semua" mode.
        $maturity = $this->_single_campaign_maturity($cid, $cfg);

        // 1. Leader Page
        $this->_leader_base_query($cid);
        $this->db->order_by('u.full_name', 'ASC');
        $this->db->limit((int)$limit, (int)$offset);
        $leader_list = $this->db->get()->result();
        $this->db->reset_query();

        if (empty($leader_list)) return [];

        $leader_ids = array_map(function($s) { return (int)$s->id; }, $leader_list);
        $id_list = implode(',', $leader_ids);

        // 2. Campaign Aggregates (Target Views, Budget) per Leader
        $camp_rows = $this->db->query("
            SELECT c.pic_user_id AS leader_id,
                   COUNT(DISTINCT c.id) AS total_campaigns,
                   COALESCE(SUM(c.budget), 0) AS total_budget,
                   COALESCE(SUM(c.target_views), 0) AS total_target_views
            FROM campaigns c
            WHERE c.pic_user_id IN ({$id_list}) {$camp_where}
            GROUP BY c.pic_user_id
        ")->result();
        $camp_map = [];
        foreach ($camp_rows as $r) { $camp_map[(int)$r->leader_id] = $r; }

        // 3. Actual Spent & Views (from tasks and posting_metrics)
        $actual_rows = $this->db->query("
            SELECT c.pic_user_id AS leader_id,
                   COALESCE(SUM(t.budget), 0) AS actual_spent,
                   COALESCE(SUM(pm.views), 0) AS actual_views,
                   COALESCE(SUM(pm.likes + pm.comments + pm.shares + pm.saves), 0) AS actual_engagements,
                   SUM(CASE WHEN pm.id IS NOT NULL THEN 1 ELSE 0 END) AS tracked_postings
            FROM campaigns c
            JOIN campaign_kols ck ON ck.campaign_id = c.id
            JOIN tasks t ON t.campaign_kol_id = ck.id
            LEFT JOIN postings p ON p.task_id = t.id
            LEFT JOIN posting_metrics pm ON pm.id = COALESCE(p.latest_metric_id, (SELECT MAX(pm2.id) FROM posting_metrics pm2 WHERE pm2.posting_id = p.id))
            WHERE c.pic_user_id IN ({$id_list}) {$camp_where}
            GROUP BY c.pic_user_id
        ")->result();
        $actual_map = [];
        foreach ($actual_rows as $r) { $actual_map[(int)$r->leader_id] = $r; }

        // 4. Deal Conversion (campaign_kols.status ACTIVE/COMPLETED vs total)
        $deal_rows = $this->db->query("
            SELECT c.pic_user_id AS leader_id,
                   COUNT(ck.id) AS total_negotiated,
                   SUM(CASE WHEN ck.status IN ('ACTIVE', 'COMPLETED') THEN 1 ELSE 0 END) AS deal_success
            FROM campaigns c
            JOIN campaign_kols ck ON ck.campaign_id = c.id
            WHERE c.pic_user_id IN ({$id_list}) {$camp_where}
            GROUP BY c.pic_user_id
        ")->result();
        $deal_map = [];
        foreach ($deal_rows as $r) { $deal_map[(int)$r->leader_id] = $r; }

        // 5. Approval TAT (Draft review history)
        $tat_rows = $this->db->query("
            SELECT leader_id,
                   AVG(tat_hours) AS avg_tat,
                   SUM(is_fallback) AS tat_fallback,
                   COUNT(*) AS reviewed_items
            FROM (
                SELECT c.pic_user_id AS leader_id, d.id,
                       CASE WHEN MIN(h.created_at) IS NOT NULL
                            THEN TIMESTAMPDIFF(MINUTE, d.created_at, MIN(h.created_at)) / 60
                            ELSE GREATEST(TIMESTAMPDIFF(MINUTE, d.created_at, d.updated_at) / 60, 0.1)
                       END AS tat_hours,
                       CASE WHEN MIN(h.created_at) IS NOT NULL THEN 0 ELSE 1 END AS is_fallback
                FROM drafts d
                JOIN tasks t ON t.id = d.task_id
                JOIN campaign_kols ck ON ck.id = t.campaign_kol_id
                JOIN campaigns c ON c.id = ck.campaign_id
                LEFT JOIN draft_review_histories h
                  ON h.draft_id = d.id AND h.action IN ('REVISION', 'APPROVED')
                WHERE c.pic_user_id IN ({$id_list}) {$camp_where}
                  AND d.status IN ('APPROVED', 'REVISION')
                GROUP BY d.id
            ) x
            GROUP BY leader_id
        ")->result();
        $tat_map = [];
        foreach ($tat_rows as $r) { $tat_map[(int)$r->leader_id] = $r; }

        // 6. Drill-down Campaigns per Leader (Khusus untuk Modal Detail)
        $camp_detail_rows = $this->db->query("
            SELECT c.pic_user_id AS leader_id,
                   c.id AS campaign_id, c.name AS campaign_name, c.status,
                   c.target_views, c.budget, c.target_cpv, c.target_cpe, c.target_er,
                   COALESCE(SUM(pm.views), 0) AS actual_views,
                   COALESCE(SUM(pm.likes + pm.comments + pm.shares + pm.saves), 0) AS actual_engagements,
                   COALESCE(SUM(t.budget), 0) AS actual_spent
            FROM campaigns c
            LEFT JOIN campaign_kols ck ON ck.campaign_id = c.id
            LEFT JOIN tasks t ON t.campaign_kol_id = ck.id
            LEFT JOIN postings p ON p.task_id = t.id
            LEFT JOIN posting_metrics pm ON pm.id = COALESCE(p.latest_metric_id, (SELECT MAX(pm2.id) FROM posting_metrics pm2 WHERE pm2.posting_id = p.id))
            WHERE c.pic_user_id IN ({$id_list}) {$camp_where}
            GROUP BY c.id
            ORDER BY c.id DESC
            LIMIT 500
        ")->result();
        $camp_detail_map = [];
        foreach ($camp_detail_rows as $row) {
            $lid = (int)$row->leader_id;
            if (!isset($camp_detail_map[$lid])) $camp_detail_map[$lid] = [];

            $target_cpv = (float)($row->target_cpv ?? 0);
            if ($target_cpv <= 0 && (float)$row->target_views > 0 && (float)$row->budget > 0) {
                $target_cpv = round((float)$row->budget / (float)$row->target_views, 2);
            }
            $row->target_cpv = $target_cpv;

            $target_cpe = (float)($row->target_cpe ?? 0);
            if ($target_cpe <= 0 && !empty($row->target_er) && (float)$row->target_er > 0 && (float)$row->target_views > 0 && (float)$row->budget > 0) {
                $est_eng = (float)$row->target_views * ((float)$row->target_er / 100);
                if ($est_eng > 0) {
                    $target_cpe = round((float)$row->budget / $est_eng, 2);
                }
            }
            $row->target_cpe = $target_cpe;

            $row->actual_cpv = ($row->actual_views > 0) ? round($row->actual_spent / $row->actual_views, 2) : 0;
            $row->actual_cpe = ($row->actual_engagements > 0) ? round($row->actual_spent / $row->actual_engagements, 2) : 0;
            $camp_detail_map[$lid][] = $row;
        }

        // Hydrate
        $results = [];
        foreach ($leader_list as $ldr) {
            $lid = (int)$ldr->id;
            $item = clone $ldr;
            
            $item->campaigns = $camp_detail_map[$lid] ?? [];

            $c = $camp_map[$lid] ?? null;
            $item->total_campaigns = (int)($c->total_campaigns ?? 0);
            $item->total_budget = (float)($c->total_budget ?? 0);
            $item->total_target_views = (int)($c->total_target_views ?? 0);

            $a = $actual_map[$lid] ?? null;
            $item->actual_spent = (float)($a->actual_spent ?? 0);
            $item->actual_views = (int)($a->actual_views ?? 0);
            $item->actual_engagements = (int)($a->actual_engagements ?? 0);
            $item->tracked_postings = (int)($a->tracked_postings ?? 0);

            $d = $deal_map[$lid] ?? null;
            $item->total_negotiated = (int)($d->total_negotiated ?? 0);
            $item->deal_success = (int)($d->deal_success ?? 0);

            $t = $tat_map[$lid] ?? null;
            $item->avg_turnaround_hours = ($t && $t->avg_tat !== null) ? round((float)$t->avg_tat, 1) : null;
            $item->tat_fallback_count = (int)($t->tat_fallback ?? 0);
            $item->reviewed_items = (int)($t->reviewed_items ?? 0);

            // Calculations
            $item->actual_cpv = ($item->actual_views > 0) ? ($item->actual_spent / $item->actual_views) : 0;
            $item->actual_cpe = ($item->actual_engagements > 0) ? ($item->actual_spent / $item->actual_engagements) : 0;
            $item->total_target_cpv = ($item->total_target_views > 0 && $item->total_budget > 0) ? round($item->total_budget / $item->total_target_views, 2) : 0;
            $item->view_attainment_pct = ($item->total_target_views > 0) ? round(($item->actual_views / $item->total_target_views) * 100, 1) : 0;
            $item->budget_efficiency_pct = ($item->total_budget > 0) ? round(($item->actual_spent / $item->total_budget) * 100, 1) : 0;
            $item->deal_conversion_pct = ($item->total_negotiated > 0) ? round(($item->deal_success / $item->total_negotiated) * 100, 1) : 0;

            if ($item->total_campaigns == 0) {
                $item->excluded = true;
                $item->grade = 'PENDING';
                $item->grade_label = 'Belum Ada Campaign';
                $item->grade_badge = 'badge-soft-slate';
                $item->score = null;
                $item->max_earned = 0;
                $item->is_provisional = false;
                $results[] = $item;
                continue;
            }
            $item->excluded = false;

            // Scoring
            $missing = [];
            $score = 0;
            $max_earned = 0;

            // View Attainment
            if ($item->view_attainment_pct > 0 || $item->total_target_views > 0) {
                $score += $this->_points_from_brackets($item->view_attainment_pct, $cfg['leader_view_brackets'] ?? [], 'min');
                $max_earned += 50;
            } else {
                $missing[] = 'views';
            }

            // CPV Efficiency
            if ($item->actual_cpv > 0 || $item->actual_spent > 0) {
                $score += $this->_points_from_brackets($item->actual_cpv, $cfg['leader_cpv_brackets'] ?? [], 'max');
                $max_earned += 20;
            } else {
                $missing[] = 'cpv';
            }

            // Deal Conversion
            if ($item->total_negotiated > 0) {
                $score += $this->_points_from_brackets($item->deal_conversion_pct, $cfg['leader_deal_brackets'] ?? [], 'min');
                $max_earned += 15;
            } else {
                $missing[] = 'deal';
            }

            // Approval TAT
            if ($item->avg_turnaround_hours !== null) {
                $score += $this->_points_from_brackets($item->avg_turnaround_hours, $cfg['leader_approval_brackets'] ?? [], 'max');
                $max_earned += 15;
            } else {
                $missing[] = 'tat';
            }

            $item->missing_dimensions = $missing;
            $item->score = $score;
            $item->max_earned = $max_earned;

            // Provisional guard ("Sementara").
            // Per-campaign mode: final only when campaign COMPLETED/CANCELLED
            // and end_date + grace days has passed (scraping matured).
            // Aggregate ("Semua") mode keeps the min-campaigns guard.
            $prov_reasons = [];
            if ($maturity !== null) {
                if (!empty($maturity['provisional']) && !empty($maturity['reason'])) {
                    $prov_reasons[] = $maturity['reason'];
                }
            } else {
                $min_camp = (int)($cfg['min_leader_campaigns'] ?? 3);
                if ($item->total_campaigns < $min_camp) {
                    $prov_reasons[] = 'baru ' . $item->total_campaigns . ' dari ' . $min_camp . ' campaign minimum';
                }
            }
            if (!empty($missing)) {
                $dim_names = ['views' => 'Views Attainment', 'cpv' => 'CPV Efficiency', 'deal' => 'Deal Conversion', 'tat' => 'Approval TAT'];
                $readable = array_map(function($m) use ($dim_names) { return $dim_names[$m] ?? $m; }, $missing);
                $prov_reasons[] = 'belum ada data: ' . implode(', ', $readable);
            }
            $item->is_provisional = !empty($prov_reasons);
            
            $grade_a = $cfg['leader_grade_a_min'] ?? 85;
            $grade_b = $cfg['leader_grade_b_min'] ?? 65;
            if ($score >= $grade_a) {
                $item->grade = 'A';
                $item->grade_label = 'Grade A (Sangat Baik)';
                $item->grade_badge = 'badge-soft-emerald';
            } elseif ($score >= $grade_b) {
                $item->grade = 'B';
                $item->grade_label = 'Grade B (Baik)';
                $item->grade_badge = 'badge-soft-blue';
            } else {
                $item->grade = 'C';
                $item->grade_label = 'Grade C (Perlu Evaluasi)';
                $item->grade_badge = 'badge-soft-rose';
            }

            if ($item->is_provisional) {
                $item->grade_label .= ' (' . ($cfg['provisional_suffix'] ?? 'Sementara') . ')';
            }

            $results[] = $item;
        }

        return $results;
    }

    public function get_leader_team_summary($leader_kpis = []) {
        $summary = new stdClass();
        $summary->total_leaders = count($leader_kpis);
        $summary->active_leaders = 0;
        $summary->provisional_leaders = 0;
        
        $total_views = 0;
        $target_views = 0;
        $total_spent = 0;
        $total_budget = 0;
        $total_engagements = 0;
        
        $deal_success = 0;
        $deal_total = 0;
        
        $tat_sum = 0;
        $tat_count = 0;

        foreach ($leader_kpis as $l) {
            if (!empty($l->excluded)) continue;
            
            $summary->active_leaders++;
            if (!empty($l->is_provisional)) $summary->provisional_leaders++;
            
            $total_views += $l->actual_views;
            $target_views += $l->total_target_views;
            $total_spent += $l->actual_spent;
            $total_budget += $l->total_budget;
            $total_engagements += (int)($l->actual_engagements ?? 0);
            
            $deal_success += $l->deal_success;
            $deal_total += $l->total_negotiated;
            
            if ($l->avg_turnaround_hours !== null) {
                $tat_sum += $l->avg_turnaround_hours;
                $tat_count++;
            }
        }
        
        $summary->team_view_attainment = ($target_views > 0) ? round(($total_views / $target_views) * 100, 1) : 0;
        $summary->team_budget_efficiency = ($total_budget > 0) ? round(($total_spent / $total_budget) * 100, 1) : 0;
        $summary->team_avg_cpv = ($total_views > 0) ? round($total_spent / $total_views, 2) : 0;
        $summary->team_avg_cpe = ($total_engagements > 0) ? round($total_spent / $total_engagements, 2) : 0;
        $summary->team_target_cpv = ($target_views > 0 && $total_budget > 0) ? round($total_budget / $target_views, 2) : 0;
        $summary->team_deal_conversion = ($deal_total > 0) ? round(($deal_success / $deal_total) * 100, 1) : 0;
        $summary->team_avg_approval_tat = ($tat_count > 0) ? round($tat_sum / $tat_count, 1) : 0;

        // Attach raw sums for widgets
        $summary->total_views = $total_views;
        $summary->target_views = $target_views;
        $summary->total_spent = $total_spent;
        $summary->total_budget = $total_budget;
        $summary->total_engagements = $total_engagements;
        $summary->deal_success = $deal_success;
        $summary->deal_total = $deal_total;

        return $summary;
    }
}
