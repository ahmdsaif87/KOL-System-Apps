<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_campaign_kols extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'campaign_kols';
    private $field_search   = ['campaign_id', 'kol_id', 'status', 'collaboration_score', 'jumlah_vt', 'assigned_at', 'created_at', 'updated_at', 'deal_date', 'rejection_reason', 'deal_amount', 'task_count', 'task_completed_count', 'currency_code'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    public function count_all($q = null, $field = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "campaign_kols.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "campaign_kols.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "campaign_kols.".$field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [])
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "campaign_kols.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "campaign_kols.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "campaign_kols.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('campaign_kols.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'LEFT');
        $this->db->join('kols', 'kols.id = campaign_kols.kol_id', 'LEFT');
        $this->db->join('aauth_users', 'aauth_users.id = campaign_kols.assigned_by', 'LEFT');
        
        $this->db->select('
            campaign_kols.*, 
            campaigns.name as campaigns_name, 
            kols.name as kols_name, 
            kols.status as kol_status, 
            COALESCE(NULLIF(aauth_users.full_name, ""), aauth_users.username) as aauth_users_full_name,
            (SELECT status FROM campaign_kol_contracts WHERE campaign_kol_contracts.campaign_kol_id = campaign_kols.id AND status != "CANCELLED" ORDER BY id DESC LIMIT 1) as contract_status,
            (SELECT status FROM invoices WHERE invoices.campaign_kol_id = campaign_kols.id ORDER BY id DESC LIMIT 1) as invoice_status
        ');
        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_campaign_kols.php */
/* Location: ./application/models/Model_campaign_kols.php */