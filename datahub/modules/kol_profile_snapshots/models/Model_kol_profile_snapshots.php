<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_kol_profile_snapshots extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'kol_profile_snapshots';
    private $field_search   = ['kol_id', 'platform_id', 'followers_count', 'average_views_last_30', 'average_er_last_30', 'total_posts', 'snapshot_date', 'created_at'];

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
                    $where .= "kol_profile_snapshots.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "kol_profile_snapshots.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "kol_profile_snapshots.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "kol_profile_snapshots.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "kol_profile_snapshots.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "kol_profile_snapshots.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('kol_profile_snapshots.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        
        $this->db->select('kol_profile_snapshots.*, kols.name as kol_name, platforms.name as platform_name');
        $this->db->join('kols', 'kols.id = kol_profile_snapshots.kol_id', 'LEFT');
        $this->db->join('platforms', 'platforms.id = kol_profile_snapshots.platform_id', 'LEFT');

        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_kol_profile_snapshots.php */
/* Location: ./application/models/Model_kol_profile_snapshots.php */