<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Model_user extends MY_Model {

	private $primary_key 	= 'id';
	private $table_name 	= 'aauth_users';
	private $field_search 	= array('email', 'username', 'full_name');

	public function __construct()
	{
		$config = array(
			'primary_key' 	=> $this->primary_key,
		 	'table_name' 	=> $this->table_name,
		 	'field_search' 	=> $this->field_search,
		 );

		parent::__construct($config);
	}

	public function count_all($q = '', $field = '')
	{
		$iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
		$field = $this->scurity($field);

        if (empty($field)) {
	        foreach ($this->field_search as $field) {
	            if ($iterasi == 1) {
	                $where .= "(" . $this->table_name . "." . $field . " LIKE '%" . $q . "%' ";
	            } else if ($iterasi == $num) {
	                $where .= "OR " . $this->table_name . "." . $field . " LIKE '%" . $q . "%') ";
	            } else {
	                $where .= "OR " . $this->table_name . "." . $field . " LIKE '%" . $q . "%' ";
	            }
	            $iterasi++;
	        }
        } else {
        	$where .= "(" . $this->table_name . "." . $field . " LIKE '%" . $q . "%' )";
        }

        if ($where) {
            $this->db->where($where);
        }

        // Restrict to 'Kol' role only
        $this->db->select('COUNT(DISTINCT ' . $this->table_name . '.id) as total');
        $this->db->join('aauth_user_to_group', 'aauth_user_to_group.user_id = ' . $this->table_name . '.id');
        $this->db->join('aauth_groups', 'aauth_groups.id = aauth_user_to_group.group_id');
        $this->db->where('LOWER(aauth_groups.name)', 'kol');

		$query = $this->db->get($this->table_name);
		$row = $query->row();

		return $row ? (int)$row->total : 0;
	}

	public function get($q = '', $field = '', $limit = 0, $offset = 0)
	{
		$iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
		$field = $this->scurity($field);

        if (empty($field)) {
	        foreach ($this->field_search as $field) {
	            if ($iterasi == 1) {
	                $where .= "(" . $this->table_name . "." . $field . " LIKE '%" . $q . "%' ";
	            } else if ($iterasi == $num) {
	                $where .= "OR " . $this->table_name . "." . $field . " LIKE '%" . $q . "%') ";
	            } else {
	                $where .= "OR " . $this->table_name . "." . $field . " LIKE '%" . $q . "%' ";
	            }
	            $iterasi++;
	        }
        } else {
        	$where .= "(" . $this->table_name . "." . $field . " LIKE '%" . $q . "%' )";
        }

        if ($where) {
            $this->db->where($where);
        }

        // Restrict to 'Kol' role only
        $this->db->select($this->table_name . '.*, kols.id as linked_kol_id, kols.name as linked_kol_name');
        $this->db->join('aauth_user_to_group', 'aauth_user_to_group.user_id = ' . $this->table_name . '.id');
        $this->db->join('aauth_groups', 'aauth_groups.id = aauth_user_to_group.group_id');
        $this->db->where('LOWER(aauth_groups.name)', 'kol');

        // Join linked KOL profile (via user_variables or matching email)
        $this->db->join('aauth_user_variables', "aauth_user_variables.user_id = {$this->table_name}.id AND aauth_user_variables.data_key = 'kol_id'", 'left');
        $this->db->join('kols', "kols.id = aauth_user_variables.value OR (aauth_user_variables.value IS NULL AND kols.email = {$this->table_name}.email)", 'left');

        $this->db->group_by($this->table_name . '.id');

        if ($limit > 0) {
            $this->db->limit($limit, $offset);
        }
        $this->db->order_by($this->table_name . '.' . $this->primary_key, "DESC");
		$query = $this->db->get($this->table_name);

		return $query->result();
	}

	public function get_group_user($user_id = false)
	{
		if ($user_id === false) {
			$user_id = get_user_data('id');
		}
		$result_group_user = [];

		$query = $this->db->get_where('aauth_user_to_group', ['user_id' => $user_id]);
		foreach ($query->result() as $row) {
			$result_group_user[] = $row->group_id;
		}

		return $result_group_user;
	}


	public function get_user_oauth($email = null, $provider = null)
	{
		$this->db->where('email', $email);
		$this->db->where('oauth_provider', $provider);
		$query = $this->db->get($this->table_name);

		return $query->result();
	}

}


/* End of file Model_user.php */
/* Location: ./application/models/Model_user.php */