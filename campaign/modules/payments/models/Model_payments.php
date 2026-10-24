<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_payments extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'payments';
    private $field_search   = ['invoice_id', 'payment_date', 'amount', 'payment_method', 'payment_proof_file', 'notes', 'created_by', 'created_at', 'updated_at'];

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
                    $where .= "payments.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "payments.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "payments.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "payments.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "payments.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "payments.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('payments.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('invoices', 'invoices.id = payments.invoice_id', 'LEFT');
        $this->db->join('campaign_kols', 'campaign_kols.id = invoices.campaign_kol_id', 'LEFT');
        $this->db->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'LEFT');
        $this->db->join('kols', 'kols.id = campaign_kols.kol_id', 'LEFT');
        $this->db->join('aauth_users', 'aauth_users.id = payments.created_by', 'LEFT');
        
        $this->db->select('payments.*, invoices.invoice_no as invoices_invoice_no, campaigns.name as campaign_name, kols.name as kol_name, aauth_users.full_name as aauth_users_full_name');

        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_payments.php */
/* Location: ./application/models/Model_payments.php */