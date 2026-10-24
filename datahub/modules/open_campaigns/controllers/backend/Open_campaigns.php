<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Open Campaigns Controller
 * 
 * Allows KOL creators to browse Open Post campaigns, evaluate their qualification,
 * and submit applications directly from their dashboard.
 */
class Open_campaigns extends Admin
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_open_campaigns');
        $this->load->library('qualification_evaluator');
        $this->load->helper('text');
        $this->lang->load('web_lang', $this->current_lang);
    }

    /**
     * Browse available Open Post campaigns
     */
    public function index()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('administrator/login', 'refresh');
        }

        $kol_id = get_current_user_kol_id();

        if (is_kol_user() && !$kol_id && !$this->aauth->is_admin()) {
            $this->session->set_flashdata('f_message', 'No KOL profile is currently linked to your account. Please contact an administrator.');
            $this->session->set_flashdata('f_type', 'warning');
            redirect('administrator/user/profile', 'refresh');
        }

        $campaigns = $this->model_open_campaigns->get_open_campaigns($kol_id);

        // Pre-evaluate qualification badge for each campaign if KOL is logged in
        if ($kol_id) {
            foreach ($campaigns as $camp) {
                $eval = $this->qualification_evaluator->evaluate($kol_id, $camp->id);
                $camp->evaluation = $eval;
            }
        }

        $this->data['campaigns'] = $campaigns;
        $this->data['kol_id'] = $kol_id;

        $this->template->title('Open Campaigns');
        $this->render('backend/standart/administrator/open_campaigns/open_campaigns_list', $this->data);
    }

    /**
     * View campaign details, brief, and qualification checklist
     *
     * @param int $campaign_id
     */
    public function view($campaign_id)
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('administrator/login', 'refresh');
        }

        $kol_id = get_current_user_kol_id();
        $campaign = $this->model_open_campaigns->get_campaign_detail($campaign_id);

        if (!$campaign) {
            $this->session->set_flashdata('f_message', 'Campaign tidak ditemukan atau sudah tidak aktif.');
            $this->session->set_flashdata('f_type', 'danger');
            redirect('administrator/open_campaigns', 'refresh');
        }

        $evaluation = null;
        if ($kol_id) {
            $evaluation = $this->qualification_evaluator->evaluate($kol_id, $campaign_id);
        }

        $this->data['campaign'] = $campaign;
        $this->data['kol_id'] = $kol_id;
        $this->data['evaluation'] = $evaluation;

        $this->template->title('Detail Campaign: ' . $campaign->name);
        $this->render('backend/standart/administrator/open_campaigns/open_campaigns_view', $this->data);
    }

    /**
     * Submit an application to an Open Post campaign (AJAX POST)
     *
     * @param int $campaign_id
     */
    public function apply($campaign_id)
    {
        if (!$this->aauth->is_loggedin()) {
            echo json_encode([
                'success' => false,
                'message' => 'Silakan login terlebih dahulu.',
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        header('Content-Type: application/json');

        $kol_id = get_current_user_kol_id();
        if (!$kol_id) {
            echo json_encode([
                'success' => false,
                'message' => 'Akun Anda belum terhubung dengan profil KOL.',
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $campaign_id = (int)$campaign_id;
        $campaign = $this->db->get_where('campaigns', ['id' => $campaign_id])->row();

        if (!$campaign || $campaign->sourcing_model !== 'OPEN_POST' || $campaign->status !== 'OPEN_REGISTRATION') {
            echo json_encode([
                'success' => false,
                'message' => 'Campaign ini tidak menerima pendaftaran.',
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Run full evaluation
        $evaluation = $this->qualification_evaluator->evaluate($kol_id, $campaign_id);

        if (!$evaluation['can_apply']) {
            echo json_encode([
                'success' => false,
                'message' => $evaluation['cannot_apply_reason'],
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        // Check if duplicate submission
        $existing = $this->db->get_where('campaign_applications', [
            'campaign_id' => $campaign_id,
            'kol_id'      => $kol_id
        ])->row();

        if ($existing) {
            echo json_encode([
                'success' => false,
                'message' => 'Anda sudah mendaftar pada campaign ini sebelumnya.',
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
            return;
        }

        $raw_rate = $this->input->post('proposed_rate');
        $clean_rate = preg_replace('/[^0-9]/', '', (string)$raw_rate);
        $proposed_rate = ($clean_rate !== '') ? (float)$clean_rate : null;
        $pitch_message = trim((string)$this->input->post('pitch_message'));

        $insert_data = [
            'campaign_id'         => $campaign_id,
            'kol_id'              => $kol_id,
            'is_qualified'        => $evaluation['is_qualified'] ? 1 : 0,
            'qualification_notes' => json_encode($evaluation['criteria']),
            'proposed_rate'       => $proposed_rate,
            'pitch_message'       => !empty($pitch_message) ? $pitch_message : null,
            'status'              => 'PENDING',
            'applied_at'          => date('Y-m-d H:i:s'),
            'created_at'          => date('Y-m-d H:i:s')
        ];

        $inserted = $this->db->insert('campaign_applications', $insert_data);

        if ($inserted) {
            echo json_encode([
                'success' => true,
                'message' => 'Pendaftaran Anda berhasil diajukan! Tim kami akan meninjau profil dan pengajuan Anda.',
                'csrfHash' => $this->security->get_csrf_hash(),
                'redirect' => site_url('administrator/open_campaigns/my_applications')
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem saat memproses pendaftaran.',
                'csrfHash' => $this->security->get_csrf_hash()
            ]);
        }
    }

    /**
     * My Applications tracking page
     */
    public function my_applications()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('administrator/login', 'refresh');
        }

        $kol_id = get_current_user_kol_id();

        if (is_kol_user() && !$kol_id && !$this->aauth->is_admin()) {
            $this->session->set_flashdata('f_message', 'No KOL profile is currently linked to your account. Please contact an administrator.');
            $this->session->set_flashdata('f_type', 'warning');
            redirect('administrator/user/profile', 'refresh');
        }

        $applications = [];
        if ($kol_id) {
            $applications = $this->model_open_campaigns->get_my_applications($kol_id);
        }

        $this->data['applications'] = $applications;
        $this->data['kol_id'] = $kol_id;

        $this->template->title('My Applications');
        $this->render('backend/standart/administrator/open_campaigns/my_applications', $this->data);
    }
}
