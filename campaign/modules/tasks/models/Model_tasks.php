<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_tasks extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'tasks';
    private $field_search   = ['title', 'campaign_kol_id', 'variant_id', 'platform_id', 'task_number', 'budget', 'cpv', 'cpe', 'content_type', 'notes', 'currency_code', 'estimated_posting_date', 'status', 'created_at', 'updated_at', 'created_by'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    /**
     * Peta nilai dropdown filter -> kolom asli (termasuk tabel join).
     * Nilai legacy (campaign_kol_id/variant_id/platform_id = ID angka) dipetakan ke NAMA
     * agar sesuai ekspektasi pengguna ("Cari judul / KOL / campaign...").
     */
    private $field_map = [
        'title'                 => 'tasks.title',
        'task_number'           => 'tasks.task_number',
        'status'                => 'tasks.status',
        'estimated_posting_date'=> 'tasks.estimated_posting_date',
        'campaign'              => 'campaigns.name',
        'kol'                   => 'kols.name',
        'platform'              => 'platforms.name',
        'variant'               => 'variants.name',
        // legacy: dulu ID angka, sekarang arahkan ke nama
        'campaign_kol_id'       => 'kols.name',
        'variant_id'            => 'variants.name',
        'platform_id'           => 'platforms.name',
    ];

    private $all_search_columns = [
        'tasks.title', 'tasks.task_number', 'tasks.status',
        'campaigns.name', 'kols.name', 'platforms.name', 'variants.name',
    ];

    protected function _build_search_where($q, $field)
    {
        if ($field === 'cpv' || $field === 'cpe') {
            return $this->_build_metric_where($field, $q);
        }
        if (!empty($field) && isset($this->field_map[$field])) {
            return "(" . $this->field_map[$field] . " LIKE '%" . $q . "%' )";
        }
        if (!empty($field)) {
            return "(tasks." . $field . " LIKE '%" . $q . "%' )";
        }
        $parts = [];
        foreach ($this->all_search_columns as $c) {
            $parts[] = $c . " LIKE '%" . $q . "%'";
        }
        return '(' . implode(' OR ', $parts) . ')';
    }

    protected function _build_metric_where($field, $q)
    {
        $q_clean = trim(strtolower($q));
        if ($field === 'cpv') {
            $act_cpv = "COALESCE(NULLIF(tasks.cpv, 0), CASE WHEN posting_metrics.views > 0 THEN (CAST(tasks.budget AS DECIMAL(15,2)) / posting_metrics.views) ELSE 0 END)";
            $t_cpv = "COALESCE(NULLIF(campaigns.target_cpv, 0), CASE WHEN campaigns.target_views > 0 THEN (CAST(campaigns.budget AS DECIMAL(15,2)) / campaigns.target_views) ELSE 0 END)";
            
            if (strpos($q_clean, 'on') !== false || strpos($q_clean, 'target') !== false) {
                return "({$act_cpv} > 0 AND {$t_cpv} > 0 AND {$act_cpv} <= {$t_cpv})";
            } elseif (strpos($q_clean, 'over') !== false) {
                return "({$act_cpv} > 0 AND {$t_cpv} > 0 AND {$act_cpv} > {$t_cpv})";
            } else {
                return "(tasks.cpv LIKE '%" . $q . "%' OR ({$act_cpv}) LIKE '%" . $q . "%')";
            }
        } elseif ($field === 'cpe') {
            $eng_expr = '(COALESCE(posting_metrics.likes, 0) + COALESCE(posting_metrics.comments, 0) + COALESCE(posting_metrics.shares, 0) + COALESCE(posting_metrics.saves, 0))';
            $act_cpe = "COALESCE(NULLIF(tasks.cpe, 0), CASE WHEN {$eng_expr} > 0 THEN (CAST(tasks.budget AS DECIMAL(15,2)) / {$eng_expr}) ELSE 0 END)";
            $t_cpe = "COALESCE(NULLIF(campaigns.target_cpe, 0), CASE WHEN campaigns.target_views > 0 AND campaigns.target_er > 0 THEN (CAST(campaigns.budget AS DECIMAL(15,2)) / (campaigns.target_views * campaigns.target_er)) ELSE 0 END)";
            
            if (strpos($q_clean, 'on') !== false || strpos($q_clean, 'target') !== false) {
                return "({$act_cpe} > 0 AND {$t_cpe} > 0 AND {$act_cpe} <= {$t_cpe})";
            } elseif (strpos($q_clean, 'over') !== false) {
                return "({$act_cpe} > 0 AND {$t_cpe} > 0 AND {$act_cpe} > {$t_cpe})";
            } else {
                return "(tasks.cpe LIKE '%" . $q . "%' OR ({$act_cpe}) LIKE '%" . $q . "%')";
            }
        }
        return "tasks.{$field} LIKE '%{$q}%'";
    }

    public function count_all($q = null, $field = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        $where = $this->_build_search_where($q, $field);

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [], $sort_by = null, $sort_dir = 'DESC')
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        $where = $this->_build_search_where($q, $field);

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);

        $sort_dir_clean = strtoupper($sort_dir) === 'ASC' ? 'ASC' : 'DESC';
        if ($sort_by === 'cpv') {
            if ($sort_dir_clean === 'ASC') {
                $this->db->order_by('CASE WHEN tasks.cpv > 0 THEN tasks.cpv WHEN COALESCE(posting_metrics.views, 0) > 0 THEN (CAST(tasks.budget AS DECIMAL(15,2)) / posting_metrics.views) ELSE 9999999999 END', 'ASC', false);
            } else {
                $this->db->order_by('CASE WHEN tasks.cpv > 0 THEN tasks.cpv WHEN COALESCE(posting_metrics.views, 0) > 0 THEN (CAST(tasks.budget AS DECIMAL(15,2)) / posting_metrics.views) ELSE -1 END', 'DESC', false);
            }
        } elseif ($sort_by === 'cpe') {
            $eng_expr = '(COALESCE(posting_metrics.likes, 0) + COALESCE(posting_metrics.comments, 0) + COALESCE(posting_metrics.shares, 0) + COALESCE(posting_metrics.saves, 0))';
            if ($sort_dir_clean === 'ASC') {
                $this->db->order_by("CASE WHEN tasks.cpe > 0 THEN tasks.cpe WHEN {$eng_expr} > 0 THEN (CAST(tasks.budget AS DECIMAL(15,2)) / {$eng_expr}) ELSE 9999999999 END", 'ASC', false);
            } else {
                $this->db->order_by("CASE WHEN tasks.cpe > 0 THEN tasks.cpe WHEN {$eng_expr} > 0 THEN (CAST(tasks.budget AS DECIMAL(15,2)) / {$eng_expr}) ELSE -1 END", 'DESC', false);
            }
        } else {
            $this->db->order_by('tasks.'.$this->primary_key, "DESC");
        }

        $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('campaign_kols', 'campaign_kols.id = tasks.campaign_kol_id', 'LEFT');
        $this->db->join('kols', 'kols.id = campaign_kols.kol_id', 'LEFT');
        $this->db->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'LEFT');
        $this->db->join('variants', 'variants.id = tasks.variant_id', 'LEFT');
        $this->db->join('platforms', 'platforms.id = tasks.platform_id', 'LEFT');
        $this->db->join('aauth_users', 'aauth_users.id = tasks.created_by', 'LEFT');
        // Metrics via latest posting only (1-to-1, anti fan-out)
        $this->db->join('posting_metrics', 'posting_metrics.id = (SELECT latest_metric_id FROM postings WHERE postings.task_id = tasks.id ORDER BY postings.id DESC LIMIT 1)', 'LEFT');

        // Latest brief/draft/posting via correlated subquery (1 row per task, anti duplikat).
        // live_url HANYA dari postings.live_url (legacy fallback tasks.notes dihapus).
        $this->db->select('tasks.*, campaign_kols.deal_scope as master_sow, kols.name as kol_name, kols.name as campaign_kols_kol_id, campaigns.name as campaign_title, campaigns.id as campaign_id, campaigns.status as campaign_status, campaigns.budget as campaign_budget, campaigns.target_views as campaign_target_views, campaigns.target_er as campaign_target_er, campaigns.target_cpv as campaign_target_cpv, campaigns.target_cpe as campaign_target_cpe, variants.name as variants_name, platforms.name as platforms_name, platforms.code as platform_code, COALESCE(NULLIF(aauth_users.full_name, ""), aauth_users.username) as aauth_users_full_name, (SELECT file_path FROM briefs WHERE briefs.task_id = tasks.id ORDER BY briefs.id DESC LIMIT 1) as brief_file, COALESCE((SELECT content FROM briefs WHERE briefs.task_id = tasks.id ORDER BY briefs.id DESC LIMIT 1), tasks.brief_note) as brief_note, COALESCE((SELECT draft_url FROM drafts WHERE drafts.task_id = tasks.id ORDER BY drafts.version DESC, drafts.id DESC LIMIT 1), tasks.draft_url) as draft_url, COALESCE((SELECT notes FROM drafts WHERE drafts.task_id = tasks.id ORDER BY drafts.version DESC, drafts.id DESC LIMIT 1), tasks.draft_note) as draft_note, (SELECT live_url FROM postings WHERE postings.task_id = tasks.id ORDER BY postings.id DESC LIMIT 1) as posting_live_url_joined, (SELECT live_url FROM postings WHERE postings.task_id = tasks.id ORDER BY postings.id DESC LIMIT 1) as live_url, (SELECT id FROM postings WHERE postings.task_id = tasks.id ORDER BY postings.id DESC LIMIT 1) as posting_id, (SELECT COUNT(id) FROM kol_shipments WHERE kol_shipments.campaign_kol_id = tasks.campaign_kol_id AND kol_shipments.status = "DELIVERED") as delivered_shipments_count, (SELECT status FROM kol_shipments WHERE kol_shipments.campaign_kol_id = tasks.campaign_kol_id ORDER BY id DESC LIMIT 1) as shipment_status, posting_metrics.views as metric_views, posting_metrics.likes as metric_likes, posting_metrics.comments as metric_comments, posting_metrics.shares as metric_shares, posting_metrics.saves as metric_saves, posting_metrics.engagement_rate as metric_er, posting_metrics.cpv as metric_cpv', false);
        $this->db->group_by('tasks.id');

        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_tasks.php */
/* Location: ./application/models/Model_tasks.php */