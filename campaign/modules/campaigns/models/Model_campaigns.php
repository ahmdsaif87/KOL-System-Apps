<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_campaigns extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'campaigns';
    private $field_search   = ['name', 'budget', 'start_date', 'end_date', 'status', 'campaign_type', 'brand_name', 'client_name', 'brand_category', 'target_views', 'target_er', 'target_cpv', 'target_cpe', 'created_by', 'created_at', 'updated_at', 'pic_user_id'];

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
                    $where .= "campaigns.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "campaigns.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            if ($field == 'products') {
                $where .= "(campaigns.id IN (SELECT campaign_id FROM campaign_products JOIN products ON campaign_products.product_id = products.id WHERE products.name LIKE '%" . $q . "%' OR products.sku LIKE '%" . $q . "%'))";
            } else {
                $where .= "(" . "campaigns.".$field . " LIKE '%" . $q . "%' )";
            }
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
                    $where .= "campaigns.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "campaigns.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            if ($field == 'products') {
                $where .= "(campaigns.id IN (SELECT campaign_id FROM campaign_products JOIN products ON campaign_products.product_id = products.id WHERE products.name LIKE '%" . $q . "%' OR products.sku LIKE '%" . $q . "%'))";
            } else {
                $where .= "(" . "campaigns.".$field . " LIKE '%" . $q . "%' )";
            }
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('campaigns.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('aauth_users', 'aauth_users.id = campaigns.created_by', 'LEFT');
        $this->db->join('aauth_users aauth_users1', 'aauth_users1.id = campaigns.pic_user_id', 'LEFT');
        
        $this->db->select('campaigns.*, COALESCE(NULLIF(aauth_users.full_name, ""), aauth_users.username) as created_by_name, COALESCE(NULLIF(aauth_users1.full_name, ""), aauth_users1.username) as pic_user_name');

        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_campaigns.php */
/* Location: ./application/models/Model_campaigns.php */