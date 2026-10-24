<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_campaign_kol_variants extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'campaign_kol_variants';
    private $field_search   = ['campaign_kol_id', 'variant_id', 'created_at'];

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
                    $where .= "campaign_kol_variants.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "campaign_kol_variants.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "campaign_kol_variants.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "campaign_kol_variants.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "campaign_kol_variants.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "campaign_kol_variants.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('campaign_kol_variants.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('campaign_kols', 'campaign_kols.id = campaign_kol_variants.campaign_kol_id', 'LEFT');
        $this->db->join('variants', 'variants.id = campaign_kol_variants.variant_id', 'LEFT');
        
        $this->db->select('campaign_kol_variants.*,campaign_kols.campaign_id as campaign_kols_campaign_id,variants.name as variants_name');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_campaign_kol_variants.php */
/* Location: ./application/models/Model_campaign_kol_variants.php */