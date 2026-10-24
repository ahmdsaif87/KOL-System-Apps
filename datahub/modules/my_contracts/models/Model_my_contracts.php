<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_my_contracts extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    public function get_contracts_by_kol($kol_id)
    {
        $this->db->select('
            ckc.*,
            ck.deal_amount,
            ck.campaign_id,
            c.name as campaign_name,
            c.brand_name,
            k.name as kol_name
        ');
        $this->db->from('campaign_kol_contracts ckc');
        $this->db->join('campaign_kols ck', 'ck.id = ckc.campaign_kol_id');
        $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'left');
        $this->db->join('kols k', 'k.id = ck.kol_id', 'left');
        $this->db->where('ck.kol_id', (int)$kol_id);
        $this->db->order_by('ckc.id', 'DESC');

        return $this->db->get()->result();
    }

    public function get_contract_detail($contract_id, $kol_id = null)
    {
        $this->db->select('
            ckc.*,
            ck.deal_amount,
            ck.kol_id,
            c.name as campaign_name,
            c.brand_name,
            k.name as kol_name,
            k.ktp_number,
            k.npwp_number,
            k.address,
            k.bank_name,
            k.bank_account_number,
            k.bank_account_name
        ');
        $this->db->from('campaign_kol_contracts ckc');
        $this->db->join('campaign_kols ck', 'ck.id = ckc.campaign_kol_id');
        $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'left');
        $this->db->join('kols k', 'k.id = ck.kol_id', 'left');
        $this->db->where('ckc.id', (int)$contract_id);
        if ($kol_id !== null) {
            $this->db->where('ck.kol_id', (int)$kol_id);
        }

        return $this->db->get()->row();
    }
}
