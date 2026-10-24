<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My_invoices extends Admin
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_my_invoices');
        $this->lang->load('web_lang', $this->current_lang);
    }

    /**
     * Display list of KOL invoices and billable deals
     */
    public function index()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('administrator/login', 'refresh');
        }

        $kol_id = get_current_user_kol_id();
        if (!$kol_id) {
            $this->session->set_flashdata('f_message', 'Akun Anda belum terhubung dengan profil KOL. Silakan hubungi admin.');
            $this->session->set_flashdata('f_type', 'warning');
            redirect('administrator/dashboard', 'refresh');
        }

        $this->data['invoices'] = $this->model_my_invoices->get_invoices_by_kol($kol_id);
        $this->data['billable_deals'] = $this->model_my_invoices->get_billable_deals_by_kol($kol_id);
        $this->data['kol'] = $this->db->get_where('kols', ['id' => $kol_id])->row();
        $this->data['kol_id'] = $kol_id;

        $this->template->title('Tagihan Saya (My Invoices)');
        $this->render('backend/standart/administrator/my_invoices/my_invoices_list', $this->data);
    }

    /**
     * AJAX endpoint to fetch deal details and tax calculation preview
     */
    public function get_deal_detail_ajax($campaign_kol_id)
    {
        if (!$this->aauth->is_loggedin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $kol_id = get_current_user_kol_id();
        $deal = $this->db->select('
            ck.*,
            c.name as campaign_name,
            c.brand_name,
            k.name as kol_name,
            k.npwp_number,
            k.bank_name,
            k.bank_account_number,
            k.bank_account_name
        ')
        ->from('campaign_kols ck')
        ->join('campaigns c', 'c.id = ck.campaign_id')
        ->join('kols k', 'k.id = ck.kol_id')
        ->where('ck.id', (int)$campaign_kol_id)
        ->where('ck.kol_id', (int)$kol_id)
        ->get()->row();

        if (!$deal) {
            echo json_encode(['success' => false, 'message' => 'Data kerja sama tidak ditemukan.']);
            exit;
        }

        // Check active invoice
        $existing_active = $this->db->query("
            SELECT id, invoice_no, status FROM invoices 
            WHERE campaign_kol_id = ? AND status IN ('SUBMITTED', 'APPROVED', 'PARTIAL', 'PAID')
        ", [$deal->id])->row();

        if ($existing_active) {
            echo json_encode([
                'success' => false, 
                'message' => 'Deal ini sudah memiliki tagihan aktif (' . $existing_active->invoice_no . ' - ' . $existing_active->status . ').'
            ]);
            exit;
        }

        $subtotal = (float)$deal->deal_amount;
        $has_npwp = !empty(trim($deal->npwp_number ?? ''));
        
        // PPh 21 estimation: 2.5% with NPWP, 3.0% without NPWP
        $tax_type = 'PPH21';
        $tax_rate = $has_npwp ? 2.50 : 3.00;
        $tax_amount = round(($subtotal * ($tax_rate / 100)), 2);
        $net_amount = max(0, $subtotal - $tax_amount);

        echo json_encode([
            'success' => true,
            'deal' => [
                'campaign_kol_id' => $deal->id,
                'campaign_name'   => $deal->campaign_name,
                'brand_name'      => $deal->brand_name,
                'subtotal'        => $subtotal,
                'subtotal_fmt'    => number_format($subtotal, 0, ',', '.'),
                'has_npwp'        => $has_npwp,
                'npwp_number'     => $deal->npwp_number ?: '-',
                'tax_type'        => $tax_type,
                'tax_rate'        => $tax_rate,
                'tax_amount'      => $tax_amount,
                'tax_amount_fmt'  => number_format($tax_amount, 0, ',', '.'),
                'net_amount'      => $net_amount,
                'net_amount_fmt'  => number_format($net_amount, 0, ',', '.'),
                'bank_name'       => $deal->bank_name,
                'bank_account_number' => $deal->bank_account_number,
                'bank_account_name'   => $deal->bank_account_name
            ]
        ]);
        exit;
    }

    /**
     * Submit new invoice from KOL to Brand
     */
    public function submit_invoice()
    {
        header('Content-Type: application/json');

        $json_reply = function($success, $message, $extra = []) {
            echo json_encode(array_merge([
                'success'    => $success,
                'message'    => $message,
                'csrf_token' => $this->security->get_csrf_hash()
            ], $extra));
            exit;
        };

        if (!$this->aauth->is_loggedin()) {
            $json_reply(false, 'Unauthorized');
        }

        $kol_id = get_current_user_kol_id();
        if (!$kol_id) {
            $json_reply(false, 'Profil KOL tidak valid.');
        }

        $campaign_kol_id = (int)$this->input->post('campaign_kol_id');
        $bank_name = trim($this->input->post('bank_name') ?? '');
        $bank_account_number = trim($this->input->post('bank_account_number') ?? '');
        $bank_account_name = trim($this->input->post('bank_account_name') ?? '');

        if (empty($campaign_kol_id)) {
            $json_reply(false, 'Pilih kerja sama / campaign yang ingin ditagihkan.');
        }

        if (empty($bank_name) || empty($bank_account_number) || empty($bank_account_name)) {
            $json_reply(false, 'Data rekening bank pembayaran wajib diisi lengkap.');
        }

        // Verify deal ownership and eligibility
        $deal = $this->db->get_where('campaign_kols', [
            'id' => $campaign_kol_id,
            'kol_id' => $kol_id
        ])->row();

        if (!$deal) {
            $json_reply(false, 'Kerja sama tidak ditemukan atau bukan milik Anda.');
        }

        if ($deal->deal_amount <= 0) {
            $json_reply(false, 'Nominal kerja sama tidak valid atau bernilai 0.');
        }

        // Check if there is already an active invoice
        $existing_active = $this->db->query("
            SELECT id, invoice_no, status FROM invoices 
            WHERE campaign_kol_id = ? AND status IN ('SUBMITTED', 'APPROVED', 'PARTIAL', 'PAID')
        ", [$campaign_kol_id])->row();

        if ($existing_active) {
            $json_reply(false, 'Deal ini sudah memiliki tagihan aktif (' . $existing_active->invoice_no . '). Tidak dapat membuat tagihan ganda.');
        }

        // Check if resubmitting from a rejected invoice
        $parent_invoice_id = null;
        $rejected_prev = $this->db->query("
            SELECT id FROM invoices 
            WHERE campaign_kol_id = ? AND status = 'REJECTED' 
            ORDER BY id DESC LIMIT 1
        ", [$campaign_kol_id])->row();
        if ($rejected_prev) {
            $parent_invoice_id = $rejected_prev->id;
        }

        // Update KOL bank info if changed
        $kol = $this->db->get_where('kols', ['id' => $kol_id])->row();
        $update_kol = [];
        if ($kol->bank_name !== $bank_name) $update_kol['bank_name'] = $bank_name;
        if ($kol->bank_account_number !== $bank_account_number) $update_kol['bank_account_number'] = $bank_account_number;
        if ($kol->bank_account_name !== $bank_account_name) $update_kol['bank_account_name'] = $bank_account_name;
        if (!empty($update_kol)) {
            $this->db->where('id', $kol_id)->update('kols', $update_kol);
        }

        // File upload handling for optional signed invoice PDF / scan
        $invoice_file = null;
        if (!empty($_FILES['invoice_file']['name'])) {
            $upload_dir = FCPATH . 'uploads/invoices/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $config = [
                'upload_path'   => $upload_dir,
                'allowed_types' => 'pdf|jpg|jpeg|png|PDF|JPG|JPEG|PNG',
                'max_size'      => 5120, // 5MB
                'encrypt_name'  => TRUE
            ];

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('invoice_file')) {
                $json_reply(false, 'Gagal mengunggah berkas invoice: ' . $this->upload->display_errors('', ''));
            }
            $upload_data = $this->upload->data();
            $invoice_file = 'uploads/invoices/' . $upload_data['file_name'];
            $source_file = $upload_dir . $upload_data['file_name'];
            $sibling_dir = dirname(FCPATH) . DIRECTORY_SEPARATOR . 'kol-system' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'invoices' . DIRECTORY_SEPARATOR;
            if (is_dir($sibling_dir)) {
                @copy($source_file, $sibling_dir . $upload_data['file_name']);
            }
        }

        $subtotal = (float)$deal->deal_amount;
        $has_npwp = !empty(trim($kol->npwp_number ?? ''));
        $tax_type = 'PPH21';
        $tax_rate = $has_npwp ? 2.50 : 3.00;
        $tax_amount = round(($subtotal * ($tax_rate / 100)), 2);
        $grand_total = max(0, $subtotal - $tax_amount);

        // Concurrency-safe atomic monotonic invoice number
        $invoice_no = $this->model_my_invoices->generate_invoice_no();

        $invoice_data = [
            'campaign_kol_id'   => $campaign_kol_id,
            'invoice_no'        => $invoice_no,
            'subtotal'          => $subtotal,
            'tax_amount'        => $tax_amount,
            'grand_total'       => $grand_total,
            'tax_type'          => $tax_type,
            'tax_rate_percent'  => $tax_rate,
            'invoice_file'      => $invoice_file,
            'parent_invoice_id' => $parent_invoice_id,
            'due_date'          => date('Y-m-d', strtotime('+14 days')),
            'status'            => 'SUBMITTED',
            'created_by'        => $this->aauth->get_user_id(),
            'created_at'        => date('Y-m-d H:i:s'),
        ];

        // Begin transaction
        $this->db->trans_start();

        try {
            $this->db->insert('invoices', $invoice_data);
            $new_invoice_id = $this->db->insert_id();

            // Record history
            $history_notes = 'Invoice dibuat dan diajukan oleh KOL. Subtotal: Rp ' . number_format($subtotal, 0, ',', '.') . 
                             ', Estimasi Potongan Pajak (' . $tax_type . ' ' . $tax_rate . '%): Rp ' . number_format($tax_amount, 0, ',', '.') . 
                             ', Total Net Transfer: Rp ' . number_format($grand_total, 0, ',', '.');
            if ($parent_invoice_id) {
                $history_notes .= ' (Revisi atas tagihan sebelumnya #' . $parent_invoice_id . ')';
            }

            $this->db->insert('invoice_histories', [
                'invoice_id'    => $new_invoice_id,
                'action'        => 'SUBMITTED',
                'notes'         => $history_notes,
                'actor_user_id' => $this->aauth->get_user_id(),
                'created_at'    => date('Y-m-d H:i:s')
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status() === FALSE) {
                $json_reply(false, 'Gagal menyimpan invoice ke database. Kemungkinan terdapat invoice aktif lain untuk deal ini.');
            }

            $json_reply(true, 'Invoice ' . $invoice_no . ' berhasil diajukan! Tim Finance Brand akan memverifikasi tagihan dan memproses pembayaran.');

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $json_reply(false, 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    /**
     * AJAX endpoint to view invoice details, history, and status
     */
    public function get_invoice_detail_ajax($id)
    {
        if (!$this->aauth->is_loggedin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $kol_id = get_current_user_kol_id();
        $invoice = $this->model_my_invoices->get_invoice_detail($id, $kol_id);

        if (!$invoice) {
            echo json_encode(['success' => false, 'message' => 'Invoice tidak ditemukan atau Anda tidak memiliki akses.']);
            exit;
        }

        $histories = $this->model_my_invoices->get_invoice_histories($id);
        $payments = $this->db->get_where('payments', ['invoice_id' => $id])->result();

        echo json_encode([
            'success'   => true,
            'invoice'   => $invoice,
            'histories' => $histories,
            'payments'  => $payments
        ]);
        exit;
    }

    /**
     * Download Invoice PDF (Generated)
     */
    public function download_pdf($id)
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('administrator/login', 'refresh');
        }

        $kol_id = get_current_user_kol_id();
        $invoice = $this->model_my_invoices->get_invoice_detail($id, $kol_id);

        if (!$invoice) {
            show_error('Akses ditolak atau invoice tidak ditemukan.', 403);
        }

        // If KOL uploaded their own physical signed invoice, serve it or render the system PDF
        $custom_pdf = $this->_resolve_file_path($invoice->invoice_file ?? '');
        if (!empty($custom_pdf) && file_exists($custom_pdf) && strtolower(pathinfo($custom_pdf, PATHINFO_EXTENSION)) === 'pdf') {
            header('Content-Type: application/pdf');
            header('Content-Disposition: inline; filename="' . basename($custom_pdf) . '"');
            header('Content-Transfer-Encoding: binary');
            header('Accept-Ranges: bytes');
            readfile($custom_pdf);
            exit;
        }

        // Otherwise generate standardized vendor invoice PDF
        $this->load->library('HtmlPdf');
        $config = [
            'orientation' => 'p',
            'format'      => 'a4',
            'marges'      => [5, 5, 5, 5]
        ];

        $this->pdf = new HtmlPdf($config);

        // Prepare data for PDF view
        $content = $this->load->view('backend/standart/administrator/my_invoices/invoices_pdf', [
            'invoice' => $invoice
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output('Invoice_' . str_replace('/', '_', $invoice->invoice_no) . '.pdf', 'I');
        exit;
    }

    /**
     * Download Official Bukti Potong Pajak (PPh 21 / PPh 23)
     */
    public function download_bupot($id)
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('administrator/login', 'refresh');
        }

        $kol_id = get_current_user_kol_id();
        $invoice = $this->model_my_invoices->get_invoice_detail($id, $kol_id);

        if (!$invoice) {
            show_error('Akses ditolak atau invoice tidak ditemukan.', 403);
        }

        if (empty($invoice->bupot_file)) {
            show_error('Bukti Potong pajak untuk invoice ini belum diunggah oleh Tim Finance.', 404);
        }

        $file_path = $this->_resolve_file_path($invoice->bupot_file);
        if (!$file_path || !file_exists($file_path)) {
            show_error('Berkas Bukti Potong tidak ditemukan pada server.', 404);
        }

        $ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));
        $content_type = ($ext === 'pdf') ? 'application/pdf' : 'image/' . $ext;

        header('Content-Type: ' . $content_type);
        header('Content-Disposition: inline; filename="Bukti_Potong_' . str_replace('/', '_', $invoice->invoice_no) . '.' . $ext . '"');
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
            FCPATH . $filepath,
            FCPATH . 'uploads/' . $filepath,
            dirname(FCPATH) . '/KOL-System/' . $filepath,
            dirname(FCPATH) . '/KOL-System-card-generator-plugin/' . $filepath,
            dirname(FCPATH) . '/kol-system/' . $filepath,
            dirname(FCPATH) . '/kol-system-card-plugin/' . $filepath,
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return FCPATH . $filepath;
    }
}
