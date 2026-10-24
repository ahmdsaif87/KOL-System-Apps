<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_postings extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'postings';
    private $field_search   = ['task_id', 'platform_id', 'live_url', 'actual_posted_at', 'posted_at_source', 'platform_validation_status', 'date_validation_status', 'thumbnail_url', 'validation_note', 'created_at', 'updated_at', 'latest_metric_id', 'status'];

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
                    $where .= "postings.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "postings.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "postings.".$field . " LIKE '%" . $q . "%' )";
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
                    $where .= "postings.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "postings.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "postings.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('postings.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('tasks', 'tasks.id = postings.task_id', 'LEFT');
        $this->db->join('platforms', 'platforms.id = postings.platform_id', 'LEFT');
        $this->db->join('posting_metrics', 'posting_metrics.id = postings.latest_metric_id', 'LEFT');
        $this->db->join('aauth_users', 'aauth_users.id = postings.submitted_by', 'LEFT');
        
        $this->db->select('postings.*,tasks.title as tasks_title,platforms.name as platforms_name,posting_metrics.snapshot_at as posting_metrics_snapshot_at,aauth_users.full_name as aauth_users_full_name');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

}

/* End of file Model_postings.php */
/* Location: ./application/models/Model_postings.php */