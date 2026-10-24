<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_kols extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'kols';
    private $field_search   = ['name', 'tier_id', 'lifetime_score', 'phone', 'email', 'gender', 'rate_card_file', 'status', 'created_at', 'updated_at', 'created_by', 'price_vt', 'price_live', 'price_long_form', 'price_unboxing'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    private function _build_search_condition($q = null, $field = null)
    {
        if ($q === null || $q === '') {
            return NULL;
        }

        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            $conditions = [];
            foreach ($this->field_search as $f) {
                $conditions[] = "kols." . $f . " LIKE '%" . $q . "%'";
            }
            // Search joined tiers name
            $conditions[] = "tiers.name LIKE '%" . $q . "%'";

            // Status mappings
            $q_lower = strtolower($q);
            if (strpos('aktif', $q_lower) !== false || strpos('active', $q_lower) !== false) {
                $conditions[] = "kols.status = 'ACTIVE'";
            }
            if (strpos('inaktif', $q_lower) !== false || strpos('inactive', $q_lower) !== false) {
                $conditions[] = "kols.status = 'INACTIVE'";
            }

            return '(' . implode(' OR ', $conditions) . ')';
        }

        // Specific field search handling
        if ($field === 'tier_id' || $field === 'tiers.name') {
            return "(tiers.name LIKE '%" . $q . "%' OR kols.tier_id LIKE '%" . $q . "%')";
        }

        if ($field === 'status') {
            $q_lower = strtolower($q);
            if (strpos('aktif', $q_lower) !== false || strpos('active', $q_lower) !== false) {
                return "(kols.status = 'ACTIVE')";
            }
            if (strpos('inaktif', $q_lower) !== false || strpos('inactive', $q_lower) !== false) {
                return "(kols.status = 'INACTIVE')";
            }
            return "(kols.status LIKE '%" . $q . "%')";
        }

        if ($field === 'lifetime_score') {
            if (is_numeric($q)) {
                return "(kols.lifetime_score >= " . (float)$q . ")";
            }
            return "(kols.lifetime_score LIKE '%" . $q . "%')";
        }

        if ($field === 'approval_status') {
            return "(kols.status LIKE '%" . $q . "%')";
        }

        return "(kols." . $field . " LIKE '%" . $q . "%')";
    }

    private function _build_perf_condition($perf_params = [])
    {
        if (empty($perf_params) || !is_array($perf_params)) {
            return NULL;
        }

        $conditions = [];

        // 1. Data status: synced vs unsynced vs has_gmv
        if (!empty($perf_params['perf_status'])) {
            if ($perf_params['perf_status'] === 'unsynced') {
                $conditions[] = "NOT EXISTS (SELECT 1 FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1 AND (kp.avg_views > 0 OR kp.avg_er > 0))";
            } elseif ($perf_params['perf_status'] === 'synced') {
                $conditions[] = "EXISTS (SELECT 1 FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1 AND (kp.avg_views > 0 OR kp.avg_er > 0))";
            } elseif ($perf_params['perf_status'] === 'has_gmv') {
                $conditions[] = "(kols.total_gmv_estimate > 0)";
            }
        }

        // Min GMV (Rp)
        if (isset($perf_params['perf_min_gmv']) && $perf_params['perf_min_gmv'] !== '') {
            $min_gmv = (float)$perf_params['perf_min_gmv'];
            $conditions[] = "kols.total_gmv_estimate >= {$min_gmv}";
        }

        // 2. Metrics filtering on kol_platforms (Platform, Avg Views, Avg ER, Followers)
        $kp_conds = ["kp.kol_id = kols.id", "kp.is_active = 1"];
        $has_platform = false;
        $has_metric   = false;

        if (!empty($perf_params['perf_platform'])) {
            $plat_id = (int)$perf_params['perf_platform'];
            if ($plat_id > 0) {
                $kp_conds[] = "kp.platform_id = {$plat_id}";
                $has_platform = true;
            }
        }

        if (isset($perf_params['perf_min_views']) && $perf_params['perf_min_views'] !== '') {
            $min_views = (float)$perf_params['perf_min_views'];
            $kp_conds[] = "kp.avg_views >= {$min_views}";
            $has_metric = true;
        }

        if (isset($perf_params['perf_max_views']) && $perf_params['perf_max_views'] !== '') {
            $max_views = (float)$perf_params['perf_max_views'];
            $kp_conds[] = "kp.avg_views <= {$max_views}";
            $has_metric = true;
        }

        if (isset($perf_params['perf_min_er']) && $perf_params['perf_min_er'] !== '') {
            $min_er_ratio = (float)$perf_params['perf_min_er'] / 100;
            $kp_conds[] = "kp.avg_er >= {$min_er_ratio}";
            $has_metric = true;
        }

        if (isset($perf_params['perf_max_er']) && $perf_params['perf_max_er'] !== '') {
            $max_er_ratio = (float)$perf_params['perf_max_er'] / 100;
            $kp_conds[] = "kp.avg_er <= {$max_er_ratio}";
            $has_metric = true;
        }

        if (isset($perf_params['perf_min_followers']) && $perf_params['perf_min_followers'] !== '') {
            $min_foll = (int)$perf_params['perf_min_followers'];
            $kp_conds[] = "kp.followers >= {$min_foll}";
            $has_metric = true;
        }

        if ($has_platform || $has_metric) {
            $conditions[] = "EXISTS (SELECT 1 FROM kol_platforms kp WHERE " . implode(' AND ', $kp_conds) . ")";
        }

        // 3. Category filtering (via junction table kol_categories)
        $cat_id = !empty($perf_params['category']) ? (int)$perf_params['category'] : (!empty($perf_params['category_id']) ? (int)$perf_params['category_id'] : 0);
        if ($cat_id > 0) {
            $conditions[] = "EXISTS (SELECT 1 FROM kol_categories kc WHERE kc.kol_id = kols.id AND kc.category_id = {$cat_id})";
        }

        // 4. Tier filtering (direct on kols.tier_id)
        $tier_id = !empty($perf_params['tier']) ? (int)$perf_params['tier'] : (!empty($perf_params['tier_id']) ? (int)$perf_params['tier_id'] : 0);
        if ($tier_id > 0) {
            $conditions[] = "kols.tier_id = {$tier_id}";
        }

        // 5. Gender filtering (enum: MALE, FEMALE)
        if (!empty($perf_params['gender'])) {
            $gender = strtoupper(trim($perf_params['gender']));
            if (in_array($gender, ['MALE', 'FEMALE'])) {
                $conditions[] = "kols.gender = '{$gender}'";
            }
        }

        if (empty($conditions)) {
            return NULL;
        }

        return '(' . implode(' AND ', $conditions) . ')';
    }

    public function count_all($q = null, $field = null, $perf_params = [])
    {
        $where_search = $this->_build_search_condition($q, $field);
        $where_perf   = $this->_build_perf_condition($perf_params);

        $this->join_avaiable()->filter_avaiable();
        if (!empty($where_search)) {
            $this->db->where($where_search);
        }
        if (!empty($where_perf)) {
            $this->db->where($where_perf);
        }
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [], $perf_params = [])
    {
        $where_search = $this->_build_search_condition($q, $field);
        $where_perf   = $this->_build_perf_condition($perf_params);

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        if (!empty($where_search)) {
            $this->db->where($where_search);
        }
        if (!empty($where_perf)) {
            $this->db->where($where_perf);
        }
        $this->db->limit($limit, $offset);

        // Sorting by Performance Card metrics (Avg Views, Avg ER, Followers) or Name/Default
        $sort = isset($perf_params['perf_sort']) ? $perf_params['perf_sort'] : '';
        switch ($sort) {
            case 'price_vt_asc':
                $this->db->order_by('ISNULL(kols.price_vt) ASC, kols.price_vt ASC, kols.id DESC', '', FALSE);
                break;
            case 'price_vt_desc':
                $this->db->order_by('ISNULL(kols.price_vt) ASC, kols.price_vt DESC, kols.id DESC', '', FALSE);
                break;
            case 'price_live_asc':
                $this->db->order_by('ISNULL(kols.price_live) ASC, kols.price_live ASC, kols.id DESC', '', FALSE);
                break;
            case 'price_live_desc':
                $this->db->order_by('ISNULL(kols.price_live) ASC, kols.price_live DESC, kols.id DESC', '', FALSE);
                break;
            case 'price_long_form_asc':
                $this->db->order_by('ISNULL(kols.price_long_form) ASC, kols.price_long_form ASC, kols.id DESC', '', FALSE);
                break;
            case 'price_long_form_desc':
                $this->db->order_by('ISNULL(kols.price_long_form) ASC, kols.price_long_form DESC, kols.id DESC', '', FALSE);
                break;
            case 'price_unboxing_asc':
                $this->db->order_by('ISNULL(kols.price_unboxing) ASC, kols.price_unboxing ASC, kols.id DESC', '', FALSE);
                break;
            case 'price_unboxing_desc':
                $this->db->order_by('ISNULL(kols.price_unboxing) ASC, kols.price_unboxing DESC, kols.id DESC', '', FALSE);
                break;
            case 'rate_min_asc':
                // Cheapest rate across all service types, KOLs without any rate last
                $this->db->order_by('((kols.price_vt IS NULL) AND (kols.price_live IS NULL) AND (kols.price_long_form IS NULL) AND (kols.price_unboxing IS NULL)) ASC, LEAST(COALESCE(kols.price_vt, 999999999999.99), COALESCE(kols.price_live, 999999999999.99), COALESCE(kols.price_long_form, 999999999999.99), COALESCE(kols.price_unboxing, 999999999999.99)) ASC, kols.id DESC', '', FALSE);
                break;
            case 'rate_min_desc':
                // Most expensive cheapest-rate first, KOLs without any rate last
                $this->db->order_by('((kols.price_vt IS NULL) AND (kols.price_live IS NULL) AND (kols.price_long_form IS NULL) AND (kols.price_unboxing IS NULL)) ASC, LEAST(COALESCE(kols.price_vt, 999999999999.99), COALESCE(kols.price_live, 999999999999.99), COALESCE(kols.price_long_form, 999999999999.99), COALESCE(kols.price_unboxing, 999999999999.99)) DESC, kols.id DESC', '', FALSE);
                break;
            case 'gmv_desc':
                // Highest total GMV first, 0/NULLs last
                $this->db->order_by('((kols.total_gmv_estimate IS NULL OR kols.total_gmv_estimate = 0)) ASC, kols.total_gmv_estimate DESC, kols.id DESC', '', FALSE);
                break;
            case 'gmv_asc':
                // Lowest positive GMV first, 0/NULLs last
                $this->db->order_by('((kols.total_gmv_estimate IS NULL OR kols.total_gmv_estimate = 0)) ASC, kols.total_gmv_estimate ASC, kols.id DESC', '', FALSE);
                break;
            case 'views_desc':
                // Highest avg views first, NULLs last
                $this->db->order_by('((SELECT AVG(kp.avg_views) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) IS NULL) ASC, (SELECT AVG(kp.avg_views) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) DESC, kols.id DESC', '', FALSE);
                break;
            case 'views_asc':
                $this->db->order_by('((SELECT AVG(kp.avg_views) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) IS NULL) ASC, (SELECT AVG(kp.avg_views) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) ASC, kols.id DESC', '', FALSE);
                break;
            case 'er_desc':
                // Highest avg ER first, NULLs last
                $this->db->order_by('((SELECT AVG(kp.avg_er) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) IS NULL) ASC, (SELECT AVG(kp.avg_er) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) DESC, kols.id DESC', '', FALSE);
                break;
            case 'er_asc':
                $this->db->order_by('((SELECT AVG(kp.avg_er) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) IS NULL) ASC, (SELECT AVG(kp.avg_er) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) ASC, kols.id DESC', '', FALSE);
                break;
            case 'followers_desc':
                // Most followers first, NULLs last
                $this->db->order_by('((SELECT SUM(kp.followers) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) IS NULL) ASC, (SELECT SUM(kp.followers) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) DESC, kols.id DESC', '', FALSE);
                break;
            case 'name_asc':
                $this->db->order_by('kols.name', 'ASC');
                break;
            default:
                $this->db->order_by('kols.'.$this->primary_key, "DESC");
                break;
        }

        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('tiers', 'tiers.id = kols.tier_id', 'LEFT');
        $this->db->join('aauth_users aauth_users1', 'aauth_users1.id = kols.created_by', 'LEFT');
        
        $this->db->select('
            kols.*, 
            tiers.name as tiers_name, 
            COALESCE(NULLIF(aauth_users1.full_name, ""), aauth_users1.username) as created_by_name,
            (SELECT AVG(kp.avg_views) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) as perf_avg_views,
            (SELECT AVG(kp.avg_er) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) as perf_avg_er,
            (SELECT SUM(kp.followers) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) as perf_followers
        ', FALSE);

        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

    /**
     * Get similar / lookalike KOLs for a picked reference KOL using the Hybrid Jaccard Engine
     *
     * @param int   $kol_id      Reference KOL ID
     * @param int   $limit       Number of similar KOLs to return (default 4)
     * @param array $exclude_ids Additional KOL IDs to exclude
     * @return array
     */
    public function get_similar_kols($kol_id, $limit = 4, $exclude_ids = [])
    {
        $kol_id = (int)$kol_id;
        if ($kol_id <= 0) {
            return [];
        }

        // 1. Fetch Target / Reference KOL Details
        $target = $this->db->select('kols.*, tiers.name as tier_name, tiers.min_followers, tiers.max_followers')
            ->from('kols')
            ->join('tiers', 'tiers.id = kols.tier_id', 'LEFT')
            ->where('kols.id', $kol_id)
            ->get()->row();

        if (!$target) {
            return [];
        }

        // Target categories
        $target_cats_raw = $this->db->select('category_id, categories.name as category_name')
            ->from('kol_categories')
            ->join('categories', 'categories.id = kol_categories.category_id', 'INNER')
            ->where('kol_id', $kol_id)
            ->get()->result();
        $target_cat_ids = array_map(function($c) { return (int)$c->category_id; }, $target_cats_raw);
        $target_cat_names = array_map(function($c) { return $c->category_name; }, $target_cats_raw);

        // Target platforms & aggregate metrics
        $target_plats_raw = $this->db->select('platform_id, followers, avg_views, avg_er, platforms.name as platform_name, platforms.code as platform_code')
            ->from('kol_platforms')
            ->join('platforms', 'platforms.id = kol_platforms.platform_id', 'LEFT')
            ->where('kol_id', $kol_id)
            ->where('kol_platforms.is_active', 1)
            ->get()->result();

        $target_plat_ids = [];
        $target_followers = 0;
        $target_views_sum = 0;
        $target_er_weighted = 0;

        foreach ($target_plats_raw as $tp) {
            $target_plat_ids[] = (int)$tp->platform_id;
            $f = (float)($tp->followers ?? 0);
            $target_followers += $f;
            $target_views_sum += (float)($tp->avg_views ?? 0);
            $target_er_weighted += ((float)($tp->avg_er ?? 0)) * $f;
        }

        $target_avg_er = ($target_followers > 0) ? ($target_er_weighted / $target_followers) : 0;
        $target_avg_views = count($target_plats_raw) > 0 ? ($target_views_sum / count($target_plats_raw)) : 0;

        // 2. Fetch Active, Non-Blacklisted Candidate KOLs (Excluding Target & Specified IDs)
        $not_in_ids = array_merge([$kol_id], array_map('intval', $exclude_ids));

        $candidates = $this->db->select('kols.*, tiers.name as tier_name, tiers.min_followers, tiers.max_followers')
            ->from('kols')
            ->join('tiers', 'tiers.id = kols.tier_id', 'LEFT')
            ->where_not_in('kols.id', $not_in_ids)
            ->where('kols.is_blacklisted', 0)
            ->where('kols.status', 'ACTIVE')
            ->get()->result();

        if (empty($candidates)) {
            return [];
        }

        $candidate_ids = array_map(function($c) { return (int)$c->id; }, $candidates);

        // Batch load categories for all candidates
        $all_cats = $this->db->select('kol_categories.kol_id, kol_categories.category_id, categories.name as category_name')
            ->from('kol_categories')
            ->join('categories', 'categories.id = kol_categories.category_id', 'INNER')
            ->where_in('kol_categories.kol_id', $candidate_ids)
            ->get()->result();

        $cats_by_kol = [];
        $cat_names_by_kol = [];
        foreach ($all_cats as $ac) {
            $cats_by_kol[$ac->kol_id][] = (int)$ac->category_id;
            $cat_names_by_kol[$ac->kol_id][] = $ac->category_name;
        }

        // Batch load platforms & metrics for all candidates
        $all_plats = $this->db->select('kol_platforms.*, platforms.name as platform_name, platforms.code as platform_code, platforms.icon_url')
            ->from('kol_platforms')
            ->join('platforms', 'platforms.id = kol_platforms.platform_id', 'LEFT')
            ->where_in('kol_platforms.kol_id', $candidate_ids)
            ->where('kol_platforms.is_active', 1)
            ->get()->result();

        $plats_by_kol = [];
        $plat_ids_by_kol = [];
        $avg_views_sum_by_kol = [];
        $avg_er_sum_by_kol = [];
        $plats_count_by_kol = [];

        foreach ($all_plats as $ap) {
            $kid = $ap->kol_id;
            $plats_by_kol[$kid][] = $ap;
            $plat_ids_by_kol[$kid][] = (int)$ap->platform_id;

            $f = (float)($ap->followers ?? 0);
            $followers_by_kol[$kid] = ($followers_by_kol[$kid] ?? 0) + $f;
            
            $avg_views_sum_by_kol[$kid] = ($avg_views_sum_by_kol[$kid] ?? 0) + (float)($ap->avg_views ?? 0);
            $avg_er_sum_by_kol[$kid] = ($avg_er_sum_by_kol[$kid] ?? 0) + (float)$ap->avg_er;
            $plats_count_by_kol[$kid] = ($plats_count_by_kol[$kid] ?? 0) + 1;
        }

        foreach ($plats_count_by_kol as $kid => $count) {
            $avg_views_by_kol[$kid] = $count > 0 ? ($avg_views_sum_by_kol[$kid] / $count) : 0;
            $avg_er_by_kol[$kid] = $count > 0 ? ($avg_er_sum_by_kol[$kid] / $count) : 0;
        }

        // Batch load internal review summary for candidates
        $reviews = $this->db->select('kol_id, AVG(overall_rating) as avg_rating, COUNT(id) as review_count')
            ->from('kol_reviews')
            ->where_in('kol_id', $candidate_ids)
            ->group_by('kol_id')
            ->get()->result();
        $reviews_by_kol = [];
        foreach ($reviews as $rev) {
            $reviews_by_kol[$rev->kol_id] = [
                'rating' => round((float)$rev->avg_rating, 1),
                'count'  => (int)$rev->review_count
            ];
        }

        // Batch load latest rate card pricing estimate
        $rates = $this->db->select('kp.kol_id, MIN(rc.rate) as min_rate, MAX(rc.rate) as max_rate')
            ->from('kol_rate_cards rc')
            ->join('kol_platforms kp', 'kp.id = rc.kol_platform_id', 'INNER')
            ->where_in('kp.kol_id', $candidate_ids)
            ->where('rc.is_active', 1)
            ->group_by('kp.kol_id')
            ->get()->result();
        $rates_by_kol = [];
        foreach ($rates as $r) {
            $rates_by_kol[$r->kol_id] = [
                'min_rate' => (float)$r->min_rate,
                'max_rate' => (float)$r->max_rate,
            ];
        }

        // 3. Compute Multi-Vector Similarity Scores
        $scored_candidates = [];

        foreach ($candidates as $cand) {
            $kid = (int)$cand->id;
            $cand_cats = $cats_by_kol[$kid] ?? [];
            $cand_cat_names = $cat_names_by_kol[$kid] ?? [];
            $cand_plats = $plat_ids_by_kol[$kid] ?? [];
            $cand_followers = $followers_by_kol[$kid] ?? 0;
            $cand_views = $avg_views_by_kol[$kid] ?? 0;
            $cand_er = $avg_er_by_kol[$kid] ?? 0;

            // Vector A: Category Jaccard Similarity (Weight: 30%)
            $shared_cats = array_intersect($target_cat_ids, $cand_cats);
            $union_cats = array_unique(array_merge($target_cat_ids, $cand_cats));
            $s_cat = count($union_cats) > 0 ? (count($shared_cats) / count($union_cats)) : 0.40;

            // Vector B: Channel & Platform Jaccard Similarity (Weight: 15%)
            $shared_plats = array_intersect($target_plat_ids, $cand_plats);
            $union_plats = array_unique(array_merge($target_plat_ids, $cand_plats));
            $s_plat = count($union_plats) > 0 ? (count($shared_plats) / count($union_plats)) : 0.40;

            // Vector C: Logarithmic Audience Scale & Tier Proximity (Weight: 25%)
            $log_target = log10(max(10, $target_followers));
            $log_cand = log10(max(10, $cand_followers));
            $log_diff = abs($log_target - $log_cand);
            // 2.0 log difference represents a 100x follower gap
            $s_aud = max(0, 1.0 - ($log_diff / 2.0));
            if ($target->tier_id && $target->tier_id == $cand->tier_id) {
                $s_aud = min(1.0, $s_aud + 0.10);
            }

            // Vector D: Performance & Engagement Rate Similarity (Weight: 20%)
            $er_denom = max($target_avg_er, $cand_er, 0.01);
            $er_sim = max(0, 1.0 - (abs($target_avg_er - $cand_er) / $er_denom));

            $views_log_target = log10(max(10, $target_avg_views));
            $views_log_cand = log10(max(10, $cand_views));
            $views_sim = max(0, 1.0 - (abs($views_log_target - $views_log_cand) / 2.0));

            $s_perf = ($er_sim * 0.50) + ($views_sim * 0.50);

            // Vector E: Quality, Trust & Review Multiplier (Weight: 10%)
            $rev_info = $reviews_by_kol[$kid] ?? null;
            $s_trust = 0.50; // neutral default
            if ($rev_info && $rev_info['count'] > 0) {
                // Scale 1-5 rating into 0-1
                $s_trust = max(0, min(1.0, ($rev_info['rating'] - 1.0) / 4.0));
            }

            // Anomaly penalty discount
            $anomaly_score = (int)($cand->anomaly_score ?? 0);
            $safety_factor = max(0.60, 1.0 - (($anomaly_score / 100.0) * 0.25));

            // Composite Match Index (0% - 100%)
            $raw_match = (
                ($s_cat * 0.30) +
                ($s_plat * 0.15) +
                ($s_aud * 0.25) +
                ($s_perf * 0.20) +
                ($s_trust * 0.10)
            ) * $safety_factor;

            // Convert to percentage
            $match_pct = (int)round(min(100, max(15, $raw_match * 100)));

            if ($match_pct < 50) continue;

            // Shared category labels for transparent badges
            $shared_cat_labels = [];
            foreach ($shared_cats as $scid) {
                $idx = array_search($scid, $target_cat_ids);
                if ($idx !== false && isset($target_cat_names[$idx])) {
                    $shared_cat_labels[] = $target_cat_names[$idx];
                }
            }

            $cand->match_percentage = $match_pct;
            $cand->breakdown = [
                'category_match_pct' => (int)round($s_cat * 100),
                'platform_match_pct' => (int)round($s_plat * 100),
                'audience_match_pct' => (int)round($s_aud * 100),
                'performance_match_pct' => (int)round($s_perf * 100),
                'shared_categories'  => $shared_cat_labels,
                'total_followers'    => $cand_followers,
                'avg_views'          => $cand_views,
                'avg_er'             => $cand_er,
            ];
            $cand->categories = $cand_cat_names;
            $cand->platforms = $plats_by_kol[$kid] ?? [];
            $cand->review_summary = $rev_info;
            $cand->rate_card = $rates_by_kol[$kid] ?? null;

            $scored_candidates[] = $cand;
        }

        // Sort descending by match percentage
        usort($scored_candidates, function($a, $b) {
            return $b->match_percentage <=> $a->match_percentage;
        });

        return array_slice($scored_candidates, 0, $limit);
    }

    /**
     * Generate 1 or many KOL recommendations based on user input criteria and optimization goals
     *
     * @param array $criteria User-selected filter options
     * @param int   $limit    Number of KOLs to generate (default 10)
     * @return array
     */
    public function generate_recommendations($criteria = [], $limit = 10)
    {
        $limit = max(1, min(50, (int)$limit));

        // 1. If baseline_kol_id is provided, execute Lookalike generator
        $baseline_kol_id = !empty($criteria['baseline_kol_id']) ? (int)$criteria['baseline_kol_id'] : 0;
        if ($baseline_kol_id > 0) {
            return $this->get_similar_kols($baseline_kol_id, $limit);
        }

        // 2. Fetch Active, Non-Blacklisted Candidate KOLs
        $builder = $this->db->select('kols.*, tiers.name as tier_name, tiers.min_followers, tiers.max_followers')
            ->from('kols')
            ->join('tiers', 'tiers.id = kols.tier_id', 'LEFT')
            ->where('kols.is_blacklisted', 0)
            ->where('kols.status', 'ACTIVE');

                // Service Price hard filter & sorting
        $max_budget = isset($criteria['max_budget']) && $criteria['max_budget'] !== '' ? (float)$criteria['max_budget'] : 0;
        $service_type = isset($criteria['service_type']) ? $criteria['service_type'] : '';
        
        if ($service_type && in_array($service_type, ['price_vt', 'price_live', 'price_long_form', 'price_unboxing'])) {
            $builder->where('kols.' . $service_type . ' >', 0);
            if ($max_budget > 0) {
                $builder->where('kols.' . $service_type . ' <=', $max_budget);
            }
            // Add custom flag to instruct later sorting logic, because the array map sorting is done down below
            // Actually, we can just let it fetch, and we'll sort the final $scored_candidates array differently.
        }

        // Demographics hard filters if explicitly requested
        if (!empty($criteria['gender']) && in_array(strtolower($criteria['gender']), ['male', 'female', 'pria', 'wanita'])) {
            $g = strtolower($criteria['gender']);
            $val = ($g === 'pria' || $g === 'male') ? 'Male' : 'Female';
            $builder->group_start()
                ->where('kols.gender', $val)
                ->or_where('kols.gender', strtolower($val))
                ->or_where('kols.gender', ($val === 'Male' ? 'L' : 'P'))
                ->group_end();
        }

        if (!empty($criteria['city'])) {
            $builder->like('kols.city', trim($criteria['city']));
        }

        $candidates = $builder->get()->result();

        if (empty($candidates)) {
            return [];
        }

        $candidate_ids = array_map(function($c) { return (int)$c->id; }, $candidates);

        // Preload categories
        $all_cats = $this->db->select('kol_categories.kol_id, kol_categories.category_id, categories.name as category_name')
            ->from('kol_categories')
            ->join('categories', 'categories.id = kol_categories.category_id', 'INNER')
            ->where_in('kol_categories.kol_id', $candidate_ids)
            ->get()->result();

        $cats_by_kol = [];
        $cat_names_by_kol = [];
        foreach ($all_cats as $ac) {
            $cats_by_kol[$ac->kol_id][] = (int)$ac->category_id;
            $cat_names_by_kol[$ac->kol_id][] = $ac->category_name;
        }

        // Preload platforms
        $all_plats = $this->db->select('kol_platforms.*, platforms.name as platform_name, platforms.code as platform_code, platforms.icon_url')
            ->from('kol_platforms')
            ->join('platforms', 'platforms.id = kol_platforms.platform_id', 'LEFT')
            ->where_in('kol_platforms.kol_id', $candidate_ids)
            ->where('kol_platforms.is_active', 1)
            ->get()->result();

        $plats_by_kol = [];
        $plat_ids_by_kol = [];
        $avg_views_sum_by_kol = [];
        $avg_er_sum_by_kol = [];
        $plats_count_by_kol = [];

        foreach ($all_plats as $ap) {
            $kid = $ap->kol_id;
            $plats_by_kol[$kid][] = $ap;
            $plat_ids_by_kol[$kid][] = (int)$ap->platform_id;

            $f = (float)($ap->followers ?? 0);
            $followers_by_kol[$kid] = ($followers_by_kol[$kid] ?? 0) + $f;
            
            $avg_views_sum_by_kol[$kid] = ($avg_views_sum_by_kol[$kid] ?? 0) + (float)($ap->avg_views ?? 0);
            $avg_er_sum_by_kol[$kid] = ($avg_er_sum_by_kol[$kid] ?? 0) + (float)$ap->avg_er;
            $plats_count_by_kol[$kid] = ($plats_count_by_kol[$kid] ?? 0) + 1;
        }

        foreach ($plats_count_by_kol as $kid => $count) {
            $avg_views_by_kol[$kid] = $count > 0 ? ($avg_views_sum_by_kol[$kid] / $count) : 0;
            $avg_er_by_kol[$kid] = $count > 0 ? ($avg_er_sum_by_kol[$kid] / $count) : 0;
        }

        // Preload reviews
        $reviews = $this->db->select('kol_id, AVG(overall_rating) as avg_rating, COUNT(id) as review_count')
            ->from('kol_reviews')
            ->where_in('kol_id', $candidate_ids)
            ->group_by('kol_id')
            ->get()->result();
        $reviews_by_kol = [];
        foreach ($reviews as $rev) {
            $reviews_by_kol[$rev->kol_id] = [
                'rating' => round((float)$rev->avg_rating, 1),
                'count'  => (int)$rev->review_count
            ];
        }

        // Preload rate cards
        $rates = $this->db->select('kp.kol_id, MIN(rc.rate) as min_rate, MAX(rc.rate) as max_rate')
            ->from('kol_rate_cards rc')
            ->join('kol_platforms kp', 'kp.id = rc.kol_platform_id', 'INNER')
            ->where_in('kp.kol_id', $candidate_ids)
            ->where('rc.is_active', 1)
            ->group_by('kp.kol_id')
            ->get()->result();
        $rates_by_kol = [];
        foreach ($rates as $r) {
            $rates_by_kol[$r->kol_id] = [
                'min_rate' => (float)$r->min_rate,
                'max_rate' => (float)$r->max_rate,
            ];
        }

        // User target criteria
        $req_cats = !empty($criteria['categories']) ? array_map('intval', (array)$criteria['categories']) : [];
        $req_plats = !empty($criteria['platforms']) ? array_map('intval', (array)$criteria['platforms']) : [];
        $req_tiers = !empty($criteria['tiers']) ? array_map('intval', (array)$criteria['tiers']) : [];
        $min_f = isset($criteria['min_followers']) && $criteria['min_followers'] !== '' ? (float)$criteria['min_followers'] : 0;
        $max_f = isset($criteria['max_followers']) && $criteria['max_followers'] !== '' ? (float)$criteria['max_followers'] : 0;
        $min_v = isset($criteria['min_views']) && $criteria['min_views'] !== '' ? (float)$criteria['min_views'] : 0;
        $min_er = isset($criteria['min_er']) && $criteria['min_er'] !== '' ? (float)$criteria['min_er'] : 0;
        // Normalize min_er if input as percentage e.g. 2.5 -> 0.025
        if ($min_er > 1.0) {
            $min_er = $min_er / 100.0;
        }
        $max_budget = isset($criteria['max_budget']) && $criteria['max_budget'] !== '' ? (float)$criteria['max_budget'] : 0;
        $priority = $criteria['priority'] ?? 'match';

        $scored_candidates = [];

        foreach ($candidates as $cand) {
            $kid = (int)$cand->id;
            $cand_cats = $cats_by_kol[$kid] ?? [];
            $cand_cat_names = $cat_names_by_kol[$kid] ?? [];
            $cand_plats = $plat_ids_by_kol[$kid] ?? [];
            $cand_followers = $followers_by_kol[$kid] ?? 0;
            $cand_views = $avg_views_by_kol[$kid] ?? 0;
            $cand_er = $avg_er_by_kol[$kid] ?? 0;
            $cand_rate = $rates_by_kol[$kid] ?? null;

            // 1. Category Match (Jaccard)
            if (!empty($req_cats)) {
                $shared_c = array_intersect($req_cats, $cand_cats);
                $union_c = array_unique(array_merge($req_cats, $cand_cats));
                $s_cat = count($union_c) > 0 ? (count($shared_c) / count($union_c)) : 0;
            } else {
                $s_cat = 0.85; // neutral full score if no category requested
            }

            // 2. Platform Match (Jaccard)
            if (!empty($req_plats)) {
                $shared_p = array_intersect($req_plats, $cand_plats);
                $union_p = array_unique(array_merge($req_plats, $cand_plats));
                $s_plat = count($union_p) > 0 ? (count($shared_p) / count($union_p)) : 0;
            } else {
                $s_plat = 0.85;
            }

            // 3. Tier & Follower Range Match
            $s_tier = 1.0;
            if (!empty($req_tiers) && !in_array((int)$cand->tier_id, $req_tiers)) {
                $s_tier = 0.40;
            }

            $s_fol = 1.0;
            if ($min_f > 0 && $cand_followers < $min_f) {
                $s_fol = max(0.20, $cand_followers / $min_f);
            }
            if ($max_f > 0 && $cand_followers > $max_f) {
                $s_fol = max(0.20, $max_f / $cand_followers);
            }

            // 4. Performance (Views & ER) Match
            $s_views = 1.0;
            if ($min_v > 0) {
                $s_views = ($cand_views >= $min_v) ? 1.0 : max(0.15, $cand_views / $min_v);
            }

            $s_er = 1.0;
            if ($min_er > 0) {
                $s_er = ($cand_er >= $min_er) ? 1.0 : max(0.15, $cand_er / $min_er);
            }

            // 5. Budget Match
            $s_budget = 1.0;
            if ($max_budget > 0 && $cand_rate) {
                $min_price = $cand_rate['min_rate'];
                if ($min_price > $max_budget) {
                    $s_budget = max(0.20, $max_budget / $min_price);
                }
            }

            // 6. Quality & Trust
            $rev_info = $reviews_by_kol[$kid] ?? null;
            $s_trust = 0.50;
            if ($rev_info && $rev_info['count'] > 0) {
                $s_trust = max(0, min(1.0, ($rev_info['rating'] - 1.0) / 4.0));
            }

            // Weighted aggregation according to Priority goal
            switch ($priority) {
                case 'er':
                    $final_score = ($s_er * 0.45) + ($s_cat * 0.25) + ($s_plat * 0.15) + ($s_fol * 0.15);
                    break;
                case 'views':
                    $final_score = ($s_views * 0.45) + ($s_cat * 0.25) + ($s_plat * 0.15) + ($s_fol * 0.15);
                    break;
                case 'budget':
                    $final_score = ($s_budget * 0.40) + ($s_cat * 0.25) + ($s_views * 0.20) + ($s_er * 0.15);
                    break;
                case 'rating':
                    $final_score = ($s_trust * 0.45) + ($s_cat * 0.25) + ($s_er * 0.15) + ($s_fol * 0.15);
                    break;
                case 'match':
                default:
                    $final_score = (
                        ($s_cat * 0.30) +
                        ($s_plat * 0.15) +
                        (($s_tier * 0.5 + $s_fol * 0.5) * 0.20) +
                        (($s_views * 0.5 + $s_er * 0.5) * 0.20) +
                        ($s_trust * 0.15)
                    );
                    break;
            }

            $match_pct = (int)round(min(100, max(20, $final_score * 100)));

            if ($match_pct < 50) continue;

            $cand->match_percentage = $match_pct;
            $cand->breakdown = [
                'category_match_pct'    => (int)round($s_cat * 100),
                'platform_match_pct'    => (int)round($s_plat * 100),
                'audience_match_pct'    => (int)round($s_fol * 100),
                'performance_match_pct' => (int)round((($s_views + $s_er) / 2) * 100),
                'total_followers'       => $cand_followers,
                'avg_views'             => $cand_views,
                'avg_er'                => $cand_er,
            ];
            $cand->categories = $cand_cat_names;
            $cand->platforms = $plats_by_kol[$kid] ?? [];
            $cand->review_summary = $rev_info;
            $cand->rate_card = $cand_rate;

            $scored_candidates[] = $cand;
        }

        // Sort descending by computed score
        usort($scored_candidates, function($a, $b) {
            return $b->match_percentage <=> $a->match_percentage;
        });

        return array_slice($scored_candidates, 0, $limit);
    }

    /**
     * Get metadata for recommendation generator filters
     *
     * @return array
     */
    public function get_recommendation_meta()
    {
        return [
            'categories' => $this->db->where('is_active', 1)->order_by('name', 'ASC')->get('categories')->result(),
            'platforms'  => $this->db->where('is_active', 1)->order_by('name', 'ASC')->get('platforms')->result(),
            'tiers'      => $this->db->where('is_active', 1)->order_by('min_followers', 'ASC')->get('tiers')->result(),
            'campaigns'  => $this->db->select('id, name, status, sourcing_model')
                                ->where_in('status', ['DRAFT', 'ACTIVE', 'OPEN_REGISTRATION'])
                                ->order_by('id', 'DESC')
                                ->get('campaigns')->result(),
            'kols_list'  => $this->db->select('kols.id, kols.name, kols.avatar_url, tiers.name as tier_name')
                                ->join('tiers', 'tiers.id = kols.tier_id', 'LEFT')
                                ->where('kols.is_blacklisted', 0)
                                ->where('kols.status', 'ACTIVE')
                                ->order_by('kols.name', 'ASC')
                                ->get('kols')->result(),
        ];
    }

}

/* End of file Model_kols.php */
/* Location: ./application/models/Model_kols.php */



