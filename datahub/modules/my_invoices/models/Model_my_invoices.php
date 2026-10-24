<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_my_invoices extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all invoices belonging to a specific KOL
     */
    public function get_invoices_by_kol($kol_id)
    {
        $this->db->select('
            i.*,
            ck.campaign_id,
            ck.deal_amount,
            c.name as campaign_name,
            c.brand_name,
            k.name as kol_name,
            k.npwp_number,
            k.bank_name,
            k.bank_account_number,
            k.bank_account_name,
            (SELECT COALESCE(SUM(amount), 0) FROM payments p WHERE p.invoice_id = i.id) as amount_paid
        ');
        $this->db->from('invoices i');
        $this->db->join('campaign_kols ck', 'ck.id = i.campaign_kol_id');
        $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'left');
        $this->db->join('kols k', 'k.id = ck.kol_id', 'left');
        $this->db->where('ck.kol_id', (int)$kol_id);
        $this->db->order_by('i.id', 'DESC');

        return $this->db->get()->result();
    }

    /**
     * Get active/completed deals eligible for new invoice submission
     */
    public function get_billable_deals_by_kol($kol_id)
    {
        $sql = "
            SELECT 
                ck.id as campaign_kol_id,
                ck.deal_amount,
                ck.status as deal_status,
                ck.jumlah_vt,
                c.name as campaign_name,
                c.brand_name,
                k.name as kol_name,
                k.npwp_number,
                k.bank_name,
                k.bank_account_number,
                k.bank_account_name
            FROM campaign_kols ck
            JOIN campaigns c ON c.id = ck.campaign_id
            JOIN kols k ON k.id = ck.kol_id
            WHERE ck.kol_id = ?
              AND ck.deal_amount > 0
              AND ck.status IN ('ASSIGNED', 'ACTIVE', 'COMPLETED')
              AND NOT EXISTS (
                  SELECT 1 FROM invoices i 
                  WHERE i.campaign_kol_id = ck.id 
                    AND i.status IN ('SUBMITTED', 'APPROVED', 'PARTIAL', 'PAID')
              )
            ORDER BY ck.id DESC
        ";

        return $this->db->query($sql, [(int)$kol_id])->result();
    }

    /**
     * Get single invoice detail with ownership verification
     */
    public function get_invoice_detail($invoice_id, $kol_id = null)
    {
        $this->db->select('
            i.*,
            ck.campaign_id,
            ck.kol_id,
            ck.deal_amount,
            c.name as campaign_name,
            c.brand_name,
            k.name as kol_name,
            k.address as kol_address,
            k.phone as kol_phone,
            k.email as kol_email,
            k.npwp_number,
            k.ktp_number,
            k.bank_name,
            k.bank_account_number,
            k.bank_account_name,
            (SELECT COALESCE(SUM(amount), 0) FROM payments p WHERE p.invoice_id = i.id) as amount_paid
        ');
        $this->db->from('invoices i');
        $this->db->join('campaign_kols ck', 'ck.id = i.campaign_kol_id');
        $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'left');
        $this->db->join('kols k', 'k.id = ck.kol_id', 'left');
        $this->db->where('i.id', (int)$invoice_id);
        if ($kol_id !== null) {
            $this->db->where('ck.kol_id', (int)$kol_id);
        }

        return $this->db->get()->row();
    }

    /**
     * Get audit trail history of an invoice
     */
    public function get_invoice_histories($invoice_id)
    {
        return $this->db->select('ih.*, COALESCE(u.full_name, u.username) as actor_name')
            ->from('invoice_histories ih')
            ->join('aauth_users u', 'u.id = ih.actor_user_id', 'left')
            ->where('ih.invoice_id', (int)$invoice_id)
            ->order_by('ih.id', 'ASC')
            ->get()->result();
    }

    /**
     * Generate next atomic monotonic invoice number
     */
    public function generate_invoice_no()
    {
        $ym = date('Ym');
        $this->db->query("
            INSERT INTO invoice_sequences (`year_month`, `current_seq`) 
            VALUES (?, 1) 
            ON DUPLICATE KEY UPDATE `current_seq` = LAST_INSERT_ID(`current_seq` + 1)
        ", [$ym]);
        
        $seq_row = $this->db->query("SELECT LAST_INSERT_ID() as seq")->row();
        $seq = $seq_row ? (int)$seq_row->seq : 1;

        return 'INV/' . $ym . '/' . sprintf('%04d', $seq);
    }
}
