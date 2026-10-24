<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My_contracts extends Admin
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_my_contracts');
        $this->lang->load('web_lang', $this->current_lang);
    }

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

        $this->data['contracts'] = $this->model_my_contracts->get_contracts_by_kol($kol_id);
        $this->data['kol_id'] = $kol_id;

        $this->template->title('Kontrak Saya (My Contracts)');
        $this->render('backend/standart/administrator/my_contracts/my_contracts_list', $this->data);
    }

    public function get_contract_html_ajax($id)
    {
        if (!$this->aauth->is_loggedin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $kol_id = get_current_user_kol_id();
        $contract = $this->model_my_contracts->get_contract_detail($id, $kol_id);

        if (!$contract) {
            echo json_encode(['success' => false, 'message' => 'Kontrak tidak ditemukan atau Anda tidak memiliki akses.']);
            exit;
        }

        $html = $contract->contract_html;
        if (empty($html)) {
            $template = $this->db->order_by('id', 'DESC')->get('contract_templates')->row();
            if ($template) {
                $html = $template->content;
                $html = str_replace('{KOL_NAME}', $contract->kol_name ?? '', $html);
                $html = str_replace('{KOL_ADDRESS}', $contract->address ?? '', $html);
                $html = str_replace('{KOL_KTP}', $contract->ktp_number ?? '', $html);
                $html = str_replace('{KOL_NPWP}', $contract->npwp_number ?? '', $html);
                $html = str_replace('{BANK_NAME}', $contract->bank_name ?? '', $html);
                $html = str_replace('{BANK_ACCOUNT}', $contract->bank_account_number ?? '', $html);
                $html = str_replace('{BANK_OWNER}', $contract->bank_account_name ?? '', $html);
                $html = str_replace('{CAMPAIGN_NAME}', $contract->campaign_name ?? '', $html);
                $html = str_replace('{BRAND_NAME}', $contract->brand_name ?? '', $html);
                $html = str_replace('{DEAL_AMOUNT}', 'Rp ' . number_format($contract->deal_amount ?? 0, 0, ',', '.'), $html);
                $html = str_replace('{CURRENT_DATE}', date('d F Y', strtotime($contract->created_at ?? date('Y-m-d'))), $html);
                $html = str_replace('{CONTRACT_NO}', $contract->contract_no ?? 'DRAFT', $html);
                
                $this->db->where('id', $id)->update('campaign_kol_contracts', ['contract_html' => $html]);
            }
        }

        // Render existing Admin Signature if present
        $admin_sign_path = $this->_resolve_file_path($contract->admin_signature_image ?? '');
        $admin_sign_html = '';
        if (!empty($admin_sign_path) && file_exists($admin_sign_path)) {
            $type = pathinfo($admin_sign_path, PATHINFO_EXTENSION);
            $data = file_get_contents($admin_sign_path);
            $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            $admin_sign_html = '<img src="' . $base64 . '" style="max-height: 45px;">';
        } else {
            $admin_sign_html = '<div style="font-size: 9.5px; color: #94a3b8; font-style: italic; padding-top: 15px;">(Admin Signed)</div>';
        }

        echo json_encode([
            'success' => true,
            'html' => $html,
            'contract_no' => $contract->contract_no,
            'kol_name' => $contract->kol_name,
            'admin_signature_image' => $contract->admin_signature_image,
            'admin_sign_html' => $admin_sign_html,
            'status' => $contract->status
        ]);
        exit;
    }

    public function kol_sign_action()
    {
        if (!$this->aauth->is_loggedin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $kol_id = get_current_user_kol_id();
        $id = (int)$this->input->post('id');
        $contract = $this->model_my_contracts->get_contract_detail($id, $kol_id);

        if (!$contract) {
            echo json_encode(['success' => false, 'message' => 'Kontrak tidak ditemukan']);
            exit;
        }

        $upload_path = FCPATH . 'uploads/campaign_kol_contracts/';
        if (!is_dir($upload_path)) mkdir($upload_path, 0777, true);

        $signature_image_name = '';

        // Case 1: Draw Signature (base64)
        $signature_data = $this->input->post('signature');
        if (!empty($signature_data)) {
            $signature_image_name = 'kol_sign_' . $id . '_' . time() . '.png';
            $signature_image_path = $upload_path . $signature_image_name;
            
            $signature_data = str_replace('data:image/png;base64,', '', $signature_data);
            $signature_data = str_replace(' ', '+', $signature_data);
            file_put_contents($signature_image_path, base64_decode($signature_data));
        } 
        // Case 2: Upload File
        elseif (isset($_FILES['contract_file']) && $_FILES['contract_file']['error'] === 0) {
            $ext = pathinfo($_FILES['contract_file']['name'], PATHINFO_EXTENSION);
            $signature_image_name = 'kol_sign_' . $id . '_' . time() . '.' . $ext;
            move_uploaded_file($_FILES['contract_file']['tmp_name'], $upload_path . $signature_image_name);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tanda tangan atau file belum disertakan.']);
            exit;
        }

        // Build Final Document with both signatures
        $this->load->library('HtmlPdf');
        $config = ['orientation' => 'p', 'format' => 'a4', 'marges' => [14, 14, 14, 14]];
        $this->pdf = new HtmlPdf($config);

        $admin_name = 'KOL-System Team / Authorized';
        $kol_name = $contract->kol_name ?? 'KOL / Talent';

        $admin_sign_file = $this->_resolve_file_path($contract->admin_signature_image ?? '');
        $kol_sign_file = $this->_resolve_file_path($signature_image_name);

        $html = $contract->contract_html;
        $html .= '<br><br>
            <table style="width: 100%;" cellspacing="0" cellpadding="0">
                <tr>
                    <td style="width: 50%; text-align: center; vertical-align: top;">
                        <span style="font-size: 10px; color: #475569; font-weight: bold;">Pihak Kedua (KOL / Talent)</span><br><br>
                        <img src="' . $kol_sign_file . '" height="50" style="height: 50px;"><br><br>
                        <span style="font-size: 10.5px; font-weight: bold; color: #0f172a; text-decoration: underline;">' . htmlspecialchars($kol_name) . '</span><br>
                        <span style="font-size: 8.5px; color: #64748b;">KOL / Creator</span>
                    </td>
                    <td style="width: 50%; text-align: center; vertical-align: top;">
                        <span style="font-size: 10px; color: #475569; font-weight: bold;">Pihak Pertama (Brand / Agency)</span><br><br>
                        <img src="' . $admin_sign_file . '" height="50" style="height: 50px;"><br><br>
                        <span style="font-size: 10.5px; font-weight: bold; color: #0f172a; text-decoration: underline;">' . htmlspecialchars($admin_name) . '</span><br>
                        <span style="font-size: 8.5px; color: #64748b;">KOL-System Team / Authorized</span>
                    </td>
                </tr>
            </table>';

        if (session_status() === PHP_SESSION_ACTIVE) session_write_close();

        $content = '<page backtop="14mm" backbottom="14mm" backleft="14mm" backright="14mm">' . $html . '</page>';
        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);

        $safe_contract_no = str_replace(['/', '\\'], '_', $contract->contract_no);
        $pdf_name = 'contract_' . $safe_contract_no . '_' . time() . '.pdf';
        $this->pdf->Output($upload_path . $pdf_name, 'F');

        // Mirror to sibling directory if exists
        $sibling_dir = dirname(FCPATH) . '/kol-system/uploads/campaign_kol_contracts/';
        if (is_dir($sibling_dir)) {
            @copy($upload_path . $pdf_name, $sibling_dir . $pdf_name);
        }

        $save_data = [
            'kol_signature_image' => $signature_image_name,
            'contract_file' => $pdf_name,
            'status' => 'ACTIVE',
            'signed_at' => date('Y-m-d H:i:s')
        ];

        $this->db->where('id', $id)->update('campaign_kol_contracts', $save_data);

        // Update campaign_kols dealing status to ACTIVE
        if (!empty($contract->campaign_kol_id)) {
            $this->db->where('id', $contract->campaign_kol_id)->update('campaign_kols', [
                'status' => 'ACTIVE',
                'deal_date' => date('Y-m-d'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        // Note: Under Vendor Invoicing Model (v2.0), invoices are self-submitted 
        // by the KOL through 'Tagihan Saya' (My Invoices) rather than auto-created by Brand.

        echo json_encode([
            'success' => true,
            'message' => 'Kontrak berhasil ditandatangani dan diaktifkan!'
        ]);
        exit;
    }

    /**
     * Secure PDF Streaming Endpoint
     */
    public function download_pdf($id)
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('administrator/login', 'refresh');
        }

        $kol_id = get_current_user_kol_id();
        $contract = $this->model_my_contracts->get_contract_detail($id, $kol_id);

        if (!$contract || empty($contract->contract_file)) {
            show_404();
        }

        $file_path = $this->_resolve_file_path($contract->contract_file);
        if (!$file_path || !file_exists($file_path)) {
            show_404();
        }

        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . basename($contract->contract_file) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Accept-Ranges: bytes');
        readfile($file_path);
        exit;
    }

    /**
     * Resolves file path across local and shared sibling directories
     */
    private function _resolve_file_path($filename)
    {
        if (empty($filename)) return null;

        $paths = [
            FCPATH . 'uploads/campaign_kol_contracts/' . $filename,
            dirname(FCPATH) . '/campaign/uploads/campaign_kol_contracts/' . $filename,
            dirname(FCPATH) . '/datahub/uploads/campaign_kol_contracts/' . $filename,
            // fallback nama folder lama (kalau pernah dideploy dengan nama tersebut)
            dirname(FCPATH) . '/KOL-System/uploads/campaign_kol_contracts/' . $filename,
            dirname(FCPATH) . '/KOL-System-card-generator-plugin/uploads/campaign_kol_contracts/' . $filename,
        ];

        foreach ($paths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        return $paths[0]; // fallback
    }
}
