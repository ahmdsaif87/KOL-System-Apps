<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_kols extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'kols';
    private $field_search   = ['name', 'tier_id', 'lifetime_score', 'phone', 'email', 'gender', 'rate_card_file', 'status', 'created_at', 'updated_at', 'created_by'];

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
            return "(kols.approval_status LIKE '%" . $q . "%')";
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
            case 'gmv_desc':
                $this->db->order_by('((kols.total_gmv_estimate IS NULL OR kols.total_gmv_estimate = 0)) ASC, kols.total_gmv_estimate DESC, kols.id DESC', '', FALSE);
                break;
            case 'gmv_asc':
                $this->db->order_by('((kols.total_gmv_estimate IS NULL OR kols.total_gmv_estimate = 0)) ASC, kols.total_gmv_estimate ASC, kols.id DESC', '', FALSE);
                break;
            case 'views_desc':
                $this->db->order_by('((SELECT MAX(kp.avg_views) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) IS NULL) ASC, (SELECT MAX(kp.avg_views) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) DESC, kols.id DESC', '', FALSE);
                break;
            case 'views_asc':
                $this->db->order_by('((SELECT MAX(kp.avg_views) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) IS NULL) ASC, (SELECT MAX(kp.avg_views) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) ASC, kols.id DESC', '', FALSE);
                break;
            case 'er_desc':
                $this->db->order_by('((SELECT MAX(kp.avg_er) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) IS NULL) ASC, (SELECT MAX(kp.avg_er) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) DESC, kols.id DESC', '', FALSE);
                break;
            case 'er_asc':
                $this->db->order_by('((SELECT MAX(kp.avg_er) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) IS NULL) ASC, (SELECT MAX(kp.avg_er) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) ASC, kols.id DESC', '', FALSE);
                break;
            case 'followers_desc':
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
            (SELECT MAX(kp.avg_views) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) as perf_avg_views,
            (SELECT MAX(kp.avg_er) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) as perf_avg_er,
            (SELECT SUM(kp.followers) FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1) as perf_followers
        ', FALSE);

        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_kols.php */
/* Location: ./application/models/Model_kols.php */