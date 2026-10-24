<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_invoices extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'invoices';
    private $field_search   = ['campaign_kol_id', 'invoice_no', 'subtotal', 'tax_amount', 'grand_total', 'invoice_file', 'due_date', 'status', 'created_by', 'created_at', 'updated_at'];

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
                    $where .= "invoices.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "invoices.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "invoices.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "invoices.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "invoices.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "invoices.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('invoices.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('campaign_kols', 'campaign_kols.id = invoices.campaign_kol_id', 'LEFT');
        $this->db->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'LEFT');
        $this->db->join('kols', 'kols.id = campaign_kols.kol_id', 'LEFT');
        $this->db->join('aauth_users', 'aauth_users.id = invoices.created_by', 'LEFT');
        
        $this->db->select('invoices.*, campaign_kols.kol_id as campaign_kols_kol_id, campaigns.name as campaign_name, campaigns.brand_name, kols.name as kol_name, kols.address as kol_address, kols.bank_name, kols.bank_account_number, kols.bank_account_name, kols.npwp_number, kols.ktp_number, COALESCE(NULLIF(aauth_users.full_name, ""), aauth_users.username) as aauth_users_full_name');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_invoices.php */
/* Location: ./application/models/Model_invoices.php */