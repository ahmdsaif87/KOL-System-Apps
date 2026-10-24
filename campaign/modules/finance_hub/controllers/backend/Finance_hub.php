<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Finance_hub extends Admin {
    public function __construct() {
        parent::__construct();
        $this->load->model('campaign_kols/model_campaign_kols');
        $this->load->model('campaign_kol_contracts/model_campaign_kol_contracts');
        $this->load->model('invoices/model_invoices');
        $this->load->model('payments/model_payments');
    }

    public function index($offset = 0) {
        if (!$this->aauth->is_allowed('campaign_kols_list')) {
            redirect('administrator/dashboard');
        }

        // Disable ONLY_FULL_GROUP_BY for this session if enabled by MySQL server
        $this->db->query("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))");

        $this->db->select('
            campaign_kols.id as campaign_kol_id,
            campaign_kols.deal_amount,
            campaign_kols.status as deal_status,
            MAX(kols.id) as kol_id,
            MAX(kols.name) as kol_name,
            MAX(kols.avatar_url) as kol_avatar,
            MAX(kols.bank_name) as kol_bank_name,
            MAX(kols.bank_account_number) as kol_bank_account_number,
            MAX(kols.bank_account_name) as kol_bank_account_name,
            MAX(kols.npwp_number) as kol_npwp_number,
            MAX(kols.ktp_number) as kol_ktp_number,
            MAX(campaigns.name) as campaign_name,
            MAX(campaigns.brand_name) as brand_name,
            MAX(campaign_kol_contracts.id) as contract_id,
            MAX(campaign_kol_contracts.contract_no) as contract_no,
            MAX(campaign_kol_contracts.contract_file) as contract_file,
            MAX(campaign_kol_contracts.status) as contract_status,
            MAX(invoices.id) as invoice_id,
            MAX(invoices.invoice_no) as invoice_no,
            MAX(invoices.due_date) as due_date,
            MAX(invoices.status) as invoice_status,
            MAX(invoices.grand_total) as grand_total,
            MAX(invoices.subtotal) as subtotal,
            MAX(invoices.tax_amount) as tax_amount,
            MAX(invoices.tax_type) as tax_type,
            MAX(invoices.tax_rate_percent) as tax_rate_percent,
            MAX(invoices.bupot_no) as bupot_no,
            MAX(invoices.bupot_file) as bupot_file,
            MAX(invoices.bupot_uploaded_at) as bupot_uploaded_at,
            MAX(invoices.rejection_reason) as rejection_reason,
            MAX(invoices.invoice_file) as invoice_file,
            MAX(invoices.created_at) as invoice_created_at
        ');
        
        // subquery for total_paid to avoid multiple rows on joins
        $this->db->select('(SELECT COALESCE(SUM(amount), 0) FROM payments WHERE payments.invoice_id = MAX(invoices.id)) as total_paid');
        
        $this->db->from('campaign_kols');
        $this->db->join('kols', 'kols.id = campaign_kols.kol_id', 'left');
        $this->db->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'left');
        $this->db->join('campaign_kol_contracts', 'campaign_kol_contracts.campaign_kol_id = campaign_kols.id AND campaign_kol_contracts.status != "CANCELLED"', 'left');
        $this->db->join('invoices', 'invoices.campaign_kol_id = campaign_kols.id', 'left');
        
        if (!empty($filter)) {
            $this->db->group_start();
            $this->db->like('kols.name', $filter);
            $this->db->or_like('campaigns.name', $filter);
            $this->db->or_like('campaign_kol_contracts.contract_no', $filter);
            $this->db->or_like('invoices.invoice_no', $filter);
            $this->db->group_end();
        }
        
        // Show deals that are ASSIGNED, ACTIVE, or COMPLETED
        $this->db->where_in('campaign_kols.status', ['ASSIGNED', 'ACTIVE', 'COMPLETED']);
        $this->db->group_by('campaign_kols.id');
        $this->db->order_by('campaign_kols.updated_at', 'DESC');
        
        $deals = $this->db->get()->result();
        $this->data['deals'] = $deals;

        // Compute summary metrics
        $total_deal_amount = 0;
        $total_invoiced = 0;
        $total_paid = 0;
        $count_deals = count($deals);
        $count_submitted = 0;
        $count_approved = 0;
        $count_paid = 0;

        foreach ($deals as $d) {
            $total_deal_amount += floatval($d->deal_amount);
            if ($d->invoice_id) {
                $total_invoiced += floatval($d->grand_total);
                if ($d->invoice_status === 'SUBMITTED') $count_submitted++;
                elseif (in_array($d->invoice_status, ['APPROVED', 'PARTIAL'])) $count_approved++;
                elseif ($d->invoice_status === 'PAID') $count_paid++;
            }
            $total_paid += floatval($d->total_paid);
        }

        $this->data['stats'] = [
            'total_deals'       => $count_deals,
            'total_deal_amount' => $total_deal_amount,
            'total_invoiced'    => $total_invoiced,
            'total_paid'        => $total_paid,
            'total_unpaid'      => max(0, $total_invoiced - $total_paid),
            'count_submitted'   => $count_submitted,
            'count_approved'    => $count_approved,
            'count_paid'        => $count_paid
        ];

        $this->template->title('Finance Hub');
        $this->render('backend/standart/administrator/finance_hub/finance_hub_list', $this->data);
    }

    /**
     * Approve incoming invoice from KOL, with final tax withholding verification
     */
    public function approve_invoice() {
        header('Content-Type: application/json');
        
        if (!$this->aauth->is_allowed('invoices_update') && !$this->aauth->is_allowed('campaign_kols_list')) {
            echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk memverifikasi invoice.']);
            return;
        }

        $invoice_id = (int)$this->input->post('invoice_id');
        $tax_type = $this->input->post('tax_type') ?: 'NONE';
        $tax_rate_percent = floatval($this->input->post('tax_rate_percent'));

        if (!$invoice_id) {
            echo json_encode(['success' => false, 'message' => 'ID Invoice tidak valid.']);
            return;
        }

        $invoice = $this->db->get_where('invoices', ['id' => $invoice_id])->row();
        if (!$invoice) {
            echo json_encode(['success' => false, 'message' => 'Invoice tidak ditemukan.']);
            return;
        }

        if ($invoice->status !== 'SUBMITTED') {
            echo json_encode(['success' => false, 'message' => 'Tagihan sudah diproses atau status telah berubah (status: ' . $invoice->status . ').']);
            return;
        }

        $subtotal = floatval($invoice->subtotal);
        $tax_amount = round($subtotal * ($tax_rate_percent / 100), 2);
        $grand_total = max(0, $subtotal - $tax_amount);
        $admin_id = $this->aauth->get_user_id();

        // Conditional atomic update
        $this->db->query("
            UPDATE invoices 
            SET status = 'APPROVED',
                approved_by = ?,
                approved_at = NOW(),
                tax_type = ?,
                tax_rate_percent = ?,
                tax_amount = ?,
                grand_total = ?
            WHERE id = ? AND status = 'SUBMITTED'
        ", [$admin_id, $tax_type, $tax_rate_percent, $tax_amount, $grand_total, $invoice_id]);

        if ($this->db->affected_rows() === 0) {
            echo json_encode(['success' => false, 'message' => 'Tagihan sudah diproses oleh pengguna lain. Silakan muat ulang halaman.']);
            return;
        }

        // Log audit history
        $notes = 'Invoice disetujui oleh Finance. Verifikasi Pajak: ' . $tax_type . ' ' . $tax_rate_percent . '%, Potongan Pajak: Rp ' . number_format($tax_amount, 0, ',', '.') . ', Net Transfer: Rp ' . number_format($grand_total, 0, ',', '.');
        $this->db->insert('invoice_histories', [
            'invoice_id'    => $invoice_id,
            'action'        => 'APPROVED',
            'notes'         => $notes,
            'actor_user_id' => $admin_id,
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Invoice ' . $invoice->invoice_no . ' berhasil disetujui! Status tagihan kini APPROVED (Siap Dibayar).'
        ]);
    }

    /**
     * Reject incoming invoice with mandatory reason
     */
    public function reject_invoice() {
        header('Content-Type: application/json');
        
        if (!$this->aauth->is_allowed('invoices_update') && !$this->aauth->is_allowed('campaign_kols_list')) {
            echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk menolak invoice.']);
            return;
        }

        $invoice_id = (int)$this->input->post('invoice_id');
        $rejection_reason = trim($this->input->post('rejection_reason') ?? '');

        if (!$invoice_id || empty($rejection_reason)) {
            echo json_encode(['success' => false, 'message' => 'Alasan penolakan invoice wajib diisi!']);
            return;
        }

        $admin_id = $this->aauth->get_user_id();

        // Conditional atomic update
        $this->db->query("
            UPDATE invoices 
            SET status = 'REJECTED',
                rejected_by = ?,
                rejected_at = NOW(),
                rejection_reason = ?
            WHERE id = ? AND status = 'SUBMITTED'
        ", [$admin_id, $rejection_reason, $invoice_id]);

        if ($this->db->affected_rows() === 0) {
            echo json_encode(['success' => false, 'message' => 'Tagihan sudah diproses atau status telah berubah.']);
            return;
        }

        // Log audit history
        $this->db->insert('invoice_histories', [
            'invoice_id'    => $invoice_id,
            'action'        => 'REJECTED',
            'notes'         => 'Invoice ditolak oleh Finance. Alasan: ' . $rejection_reason,
            'actor_user_id' => $admin_id,
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Invoice berhasil ditolak. Notifikasi dan alasan penolakan dapat dilihat oleh KOL untuk revisi.'
        ]);
    }

    /**
     * Upload official Bukti Potong Pajak (PPh 21 / PPh 23)
     */
    public function upload_bupot() {
        header('Content-Type: application/json');

        if (!$this->aauth->is_allowed('invoices_update') && !$this->aauth->is_allowed('campaign_kols_list')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $invoice_id = (int)$this->input->post('invoice_id');
        $bupot_no = trim($this->input->post('bupot_no') ?? '');

        if (!$invoice_id || empty($bupot_no)) {
            echo json_encode(['success' => false, 'message' => 'Nomor Bukti Potong wajib diisi!']);
            return;
        }

        if (empty($_FILES['bupot_file']['name'])) {
            echo json_encode(['success' => false, 'message' => 'Berkas bukti potong (PDF/JPG/PNG) wajib diunggah!']);
            return;
        }

        $upload_dir = FCPATH . 'uploads/bukti_potong/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['bupot_file']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, ['pdf', 'jpg', 'jpeg', 'png'])) {
            echo json_encode(['success' => false, 'message' => 'Format berkas tidak didukung. Harap unggah PDF, JPG, atau PNG.']);
            return;
        }

        $filename = 'bupot_' . $invoice_id . '_' . date('YmdHis') . '.' . $ext;
        $target = $upload_dir . $filename;

        if (!move_uploaded_file($_FILES['bupot_file']['tmp_name'], $target)) {
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan berkas ke server.']);
            return;
        }

        // Sync to sibling kol-system-card-plugin uploads directory if it exists
        $sibling_dir = dirname(FCPATH) . DIRECTORY_SEPARATOR . 'kol-system-card-plugin' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'bukti_potong' . DIRECTORY_SEPARATOR;
        if (is_dir($sibling_dir)) {
            @copy($target, $sibling_dir . $filename);
        }

        $bupot_file_path = 'uploads/bukti_potong/' . $filename;

        $this->db->where('id', $invoice_id)->update('invoices', [
            'bupot_no'          => $bupot_no,
            'bupot_file'        => $bupot_file_path,
            'bupot_uploaded_at' => date('Y-m-d H:i:s')
        ]);

        $admin_id = $this->aauth->get_user_id();
        $this->db->insert('invoice_histories', [
            'invoice_id'    => $invoice_id,
            'action'        => 'BUPOT_UPLOADED',
            'notes'         => 'Bukti Potong resmi diunggah dengan No: ' . $bupot_no,
            'actor_user_id' => $admin_id,
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        echo json_encode([
            'success' => true,
            'message' => 'Bukti Potong ' . $bupot_no . ' berhasil diunggah! KOL kini dapat mengunduh bukti potong dari portal mereka.'
        ]);
    }

    /**
     * Concurrency-safe payment recording with row locks and transaction
     */
    public function save_payment() {
        header('Content-Type: application/json');
        
        if (!$this->aauth->is_allowed('payments_add') && !$this->aauth->is_allowed('campaign_kols_list')) {
            echo json_encode([
                'success' => false, 
                'message' => 'Anda tidak memiliki hak akses untuk mencatat pembayaran.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $invoice_id = (int)$this->input->post('invoice_id');
        $raw_amount = $this->input->post('amount');
        if (is_string($raw_amount)) {
            $clean_amount = str_replace('.', '', $raw_amount);
            $clean_amount = str_replace(',', '.', $clean_amount);
            $amount = (float)$clean_amount;
        } else {
            $amount = (float)$raw_amount;
        }

        $payment_date = $this->input->post('payment_date');
        $payment_method = $this->input->post('payment_method');
        $notes = $this->input->post('notes');

        if (!$invoice_id || $amount <= 0 || !$payment_date) {
            echo json_encode([
                'success' => false, 
                'message' => 'Invoice, Tanggal, dan Nominal Pembayaran wajib diisi dan harus lebih besar dari 0!',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Begin transaction with row-level locking
        $this->db->trans_start();

        // 1. Lock invoice row FOR UPDATE
        $invoice = $this->db->query("SELECT * FROM invoices WHERE id = ? FOR UPDATE", [$invoice_id])->row();
        if (!$invoice) {
            $this->db->trans_rollback();
            echo json_encode([
                'success' => false, 
                'message' => 'Invoice tidak ditemukan.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        if (!in_array($invoice->status, ['APPROVED', 'PARTIAL'])) {
            $this->db->trans_rollback();
            echo json_encode([
                'success' => false, 
                'message' => 'Tagihan belum disetujui (status saat ini: ' . $invoice->status . '). Pembayaran hanya dapat dicatat untuk invoice yang berstatus APPROVED atau PARTIAL.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // 2. Compute current total paid under lock
        $paid_row = $this->db->query("SELECT COALESCE(SUM(amount), 0) as paid FROM payments WHERE invoice_id = ?", [$invoice_id])->row();
        $total_paid = (float)($paid_row ? $paid_row->paid : 0);
        $grand_total = floatval($invoice->grand_total);
        $unpaid_balance = max(0, round($grand_total - $total_paid, 2));

        if ($amount > ($unpaid_balance + 0.01)) {
            $this->db->trans_rollback();
            echo json_encode([
                'success' => false, 
                'message' => 'Nominal transfer (Rp ' . number_format($amount, 0, ',', '.') . ') melebihi sisa unpaid balance (Rp ' . number_format($unpaid_balance, 0, ',', '.') . ').',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Handle file upload
        $payment_proof_file = null;
        if (!empty($_FILES['payment_proof_file']['name'])) {
            if (!is_dir(FCPATH . 'uploads/payments/')) {
                mkdir(FCPATH . 'uploads/payments/', 0777, true);
            }

            $ext = pathinfo($_FILES['payment_proof_file']['name'], PATHINFO_EXTENSION);
            $filename = date('YmdHis') . '-' . uniqid() . '.' . $ext;
            $target = FCPATH . 'uploads/payments/' . $filename;

            if (move_uploaded_file($_FILES['payment_proof_file']['tmp_name'], $target)) {
                $payment_proof_file = $filename;
            }
        }

        $save_data = [
            'invoice_id'         => $invoice_id,
            'payment_date'       => date('Y-m-d H:i:s', strtotime($payment_date)),
            'amount'             => $amount,
            'payment_method'     => $payment_method ?: 'Bank Transfer',
            'payment_proof_file' => $payment_proof_file,
            'notes'              => $notes,
            'created_by'         => $this->aauth->get_user_id(),
            'created_at'         => date('Y-m-d H:i:s')
        ];

        $this->db->insert('payments', $save_data);

        // Compute new status
        $new_total_paid = $total_paid + $amount;
        $new_status = ($new_total_paid >= ($grand_total - 0.01)) ? 'PAID' : 'PARTIAL';
        $this->db->update('invoices', ['status' => $new_status], ['id' => $invoice_id]);

        // Audit log
        $this->db->insert('invoice_histories', [
            'invoice_id'    => $invoice_id,
            'action'        => 'PAYMENT_RECORDED',
            'notes'         => 'Pembayaran sebesar Rp ' . number_format($amount, 0, ',', '.') . ' dicatat via ' . ($payment_method ?: 'Bank Transfer') . '. Status invoice kini: ' . $new_status,
            'actor_user_id' => $this->aauth->get_user_id(),
            'created_at'    => date('Y-m-d H:i:s')
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            echo json_encode([
                'success' => false, 
                'message' => 'Gagal mencatat transaksi pembayaran.',
                'csrf_token' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        echo json_encode([
            'success' => true, 
            'message' => 'Pembayaran sebesar Rp ' . number_format($amount, 0, ',', '.') . ' berhasil dicatat! Status invoice: ' . $new_status,
            'csrf_token' => $this->security->get_csrf_hash()
        ]);
    }

    /**
     * Fetch audit log history for an invoice (JSON)
     */
    public function get_invoice_history_ajax($invoice_id) {
        header('Content-Type: application/json');
        $histories = $this->db->select('ih.*, COALESCE(u.full_name, u.username) as actor_name')
            ->from('invoice_histories ih')
            ->join('aauth_users u', 'u.id = ih.actor_user_id', 'left')
            ->where('ih.invoice_id', (int)$invoice_id)
            ->order_by('ih.id', 'ASC')
            ->get()->result();

        echo json_encode([
            'success' => true,
            'histories' => $histories
        ]);
    }

    /**
     * Securely stream or view invoice attachment uploaded by KOL
     */
    public function view_invoice_file($invoice_id)
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('administrator/login');
        }
        $invoice = $this->db->get_where('invoices', ['id' => (int)$invoice_id])->row();
        if (!$invoice || empty($invoice->invoice_file)) {
            show_404();
        }

        $file_path = $this->_resolve_file_path($invoice->invoice_file);
        if (!$file_path || !file_exists($file_path)) {
            show_error('Berkas lampiran invoice tidak ditemukan di server.', 404);
        }

        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        if ($ext === 'pdf') {
            $content_type = 'application/pdf';
        } elseif (in_array($ext, ['jpg', 'jpeg'])) {
            $content_type = 'image/jpeg';
        } elseif ($ext === 'png') {
            $content_type = 'image/png';
        } else {
            $content_type = mime_content_type($file_path) ?: 'application/octet-stream';
        }

        header('Content-Type: ' . $content_type);
        header('Content-Disposition: inline; filename="' . basename($file_path) . '"');
        header('Content-Length: ' . filesize($file_path));
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        readfile($file_path);
        exit;
    }

    /**
     * Resolves file path across local and shared sibling directories
     */
    private function _resolve_file_path($filepath)
    {
        if (empty($filepath)) return null;

        $paths = [
            FCPATH . ltrim($filepath, '/\\'),
            FCPATH . 'uploads/' . ltrim($filepath, '/\\'),
            dirname(FCPATH) . '/kol-system/' . ltrim($filepath, '/\\'),
            dirname(FCPATH) . '/kol-system-card-plugin/' . ltrim($filepath, '/\\'),
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return FCPATH . ltrim($filepath, '/\\');
    }
}
