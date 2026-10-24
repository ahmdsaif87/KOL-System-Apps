<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_kols extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'kols';
    private $field_search   = ['name', 'city', 'province', 'tier_id'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
        );

        parent::__construct($config);
    }

    public function get_kols()
    {
        $this->db->select('kols.*, tiers.name as tier_name');
        $this->db->from($this->table_name);
        $this->db->join('tiers', 'tiers.id = kols.tier_id', 'left');
        return $this->db->get()->result();
    }

    public function get_kol($id)
    {
        $this->db->select('kols.*, tiers.name as tier_name');
        $this->db->from($this->table_name);
        $this->db->join('tiers', 'tiers.id = kols.tier_id', 'left');
        $this->db->where('kols.id', $id);
        return $this->db->get()->row();
    }

    public function get_kol_platforms($kol_id)
    {
        $this->db->select('kol_platforms.*, platforms.code, platforms.name as platform_name, platforms.icon');
        $this->db->from('kol_platforms');
        $this->db->join('platforms', 'platforms.id = kol_platforms.platform_id');
        $this->db->where('kol_platforms.kol_id', $kol_id);
        $this->db->where('kol_platforms.is_active', 1);
        return $this->db->get()->result();
    }

    public function get_historical_snapshots($kol_id)
    {
        $this->db->select('kol_profile_snapshots.*, platforms.code');
        $this->db->from('kol_profile_snapshots');
        $this->db->join('platforms', 'platforms.id = kol_profile_snapshots.platform_id');
        $this->db->where('kol_profile_snapshots.kol_id', $kol_id);
        $this->db->order_by('kol_profile_snapshots.snapshot_date', 'ASC');
        return $this->db->get()->result();
    }
}
