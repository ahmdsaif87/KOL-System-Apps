<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_campaign_kol_contracts extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'campaign_kol_contracts';
    private $field_search   = ['campaign_kol_id', 'contract_no', 'contract_file', 'status', 'signed_at', 'created_by', 'created_at', 'updated_at'];

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
                    $where .= "campaign_kol_contracts.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "campaign_kol_contracts.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "campaign_kol_contracts.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "campaign_kol_contracts.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "campaign_kol_contracts.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "campaign_kol_contracts.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('campaign_kol_contracts.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('campaign_kols', 'campaign_kols.id = campaign_kol_contracts.campaign_kol_id', 'LEFT');
        $this->db->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'LEFT');
        $this->db->join('kols', 'kols.id = campaign_kols.kol_id', 'LEFT');
        $this->db->join('aauth_users', 'aauth_users.id = campaign_kol_contracts.created_by', 'LEFT');
        $this->db->join('invoices', 'invoices.campaign_kol_id = campaign_kol_contracts.campaign_kol_id', 'LEFT');
        
        $this->db->select('campaign_kol_contracts.*, campaign_kols.kol_id as campaign_kols_kol_id, campaigns.name as campaign_name, kols.name as kol_name, aauth_users.full_name as aauth_users_full_name, invoices.id as invoice_id, invoices.invoice_no, invoices.status as invoice_status, invoices.grand_total');

        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_campaign_kol_contracts.php */
/* Location: ./application/models/Model_campaign_kol_contracts.php */