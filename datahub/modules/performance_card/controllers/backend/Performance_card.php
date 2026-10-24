<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Performance Card Controller
 * 
 * Renders the premium KOL Performance Card with platform stats and trend charts.
 */
class Performance_card extends Admin
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_performance_card');
        $this->lang->load('web_lang', $this->current_lang);
    }

    /**
     * Default index — redirect to my_card.
     */
    public function index()
    {
        $this->my_card();
    }

    /**
     * Redirect current user to their own performance card.
     */
    public function my_card()
    {
        if (!$this->aauth->is_loggedin()) {
            redirect('administrator/login', 'refresh');
        }

        $kol_id = get_current_user_kol_id();
        if ($kol_id) {
            redirect('administrator/performance_card/view/' . $kol_id, 'refresh');
        }

        if (is_kol_user()) {
            $this->session->set_flashdata('f_message', 'No KOL profile is currently linked to your account. Please contact an administrator.');
            $this->session->set_flashdata('f_type', 'warning');
            redirect('administrator/user/profile', 'refresh');
        } else {
            redirect('administrator/kols', 'refresh');
        }
    }

    /**
     * View the Performance Card for a specific KOL.
     */
    public function view($kol_id)
    {
        $t = $this->input->get('t');
        $sig = $this->input->get('sig');
        $is_iframe_auth = false;

        if ($t && $sig) {
            $expected_sig = hash_hmac('sha256', $kol_id . $t, KOL_SHARED_SECRET);
            if (hash_equals($expected_sig, $sig)) {
                // Token valid for 1 hour (resilient against slight clock drift)
                if (abs(time() - $t) <= 3600) {
                    $is_iframe_auth = true;
                }
            }
        }

        if (!$is_iframe_auth) {
            if (is_kol_user() && !$this->aauth->is_admin()) {
                $my_kol_id = get_current_user_kol_id();
                if (!$my_kol_id || (int)$my_kol_id !== (int)$kol_id) {
                    $this->session->set_flashdata('f_message', 'Access Denied: You can only access your own performance card.');
                    $this->session->set_flashdata('f_type', 'danger');
                    if ($my_kol_id) {
                        redirect('administrator/performance_card/view/' . $my_kol_id, 'refresh');
                    } else {
                        redirect('administrator/user/profile', 'refresh');
                    }
                    return;
                }
            } else {
                $this->is_allowed('kols_view');
            }
        }

        $kol = $this->model_performance_card->get_kol_with_details($kol_id);
        if (!$kol) {
            show_404();
            return;
        }

        $this->data['kol']            = $kol;
        $this->data['platform_stats'] = $this->model_performance_card->get_platform_stats($kol_id);
        $this->data['trend_data']     = $this->model_performance_card->get_snapshot_history($kol_id, 6);
        $this->data['latest_snaps']   = $this->model_performance_card->get_latest_snapshots($kol_id);
        $this->data['rate_matrix']    = $this->model_performance_card->get_rate_matrix($kol_id);

        $this->template->title('Performance Card — ' . $kol->name);
        $this->render('backend/standart/administrator/performance_card/performance_card_view', $this->data);
    }

    /**
     * AJAX endpoint — returns snapshot trend data as JSON for Chart.js.
     */
    public function card_data_ajax($kol_id)
    {
        if (is_kol_user() && !$this->aauth->is_admin()) {
            $my_kol_id = get_current_user_kol_id();
            if (!$my_kol_id || (int)$my_kol_id !== (int)$kol_id) {
                header('Content-Type: application/json', true, 403);
                echo json_encode(['success' => false, 'message' => 'Access Denied: You can only view data for your own performance card.']);
                exit;
            }
        } else {
            $this->is_allowed('kols_view');
        }

        $trend = $this->model_performance_card->get_snapshot_history($kol_id, 6);

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'data' => $trend]);
        exit;
    }

    /**
     * Export Performance Card as PDF.
     */
    public function export_pdf($kol_id)
    {
        $t = $this->input->get('t');
        $sig = $this->input->get('sig');
        $is_iframe_auth = false;

        if ($t && $sig) {
            $expected_sig = hash_hmac('sha256', $kol_id . $t, KOL_SHARED_SECRET);
            if (hash_equals($expected_sig, $sig)) {
                if (time() - $t <= 300) {
                    $is_iframe_auth = true;
                }
            }
        }

        if (!$is_iframe_auth) {
            if (is_kol_user() && !$this->aauth->is_admin()) {
                $my_kol_id = get_current_user_kol_id();
                if (!$my_kol_id || (int)$my_kol_id !== (int)$kol_id) {
                    show_error('Access Denied: You can only export your own performance card.', 403);
                    return;
                }
            } else {
                $this->is_allowed('kols_export');
            }
        }

        $kol = $this->model_performance_card->get_kol_with_details($kol_id);
        if (!$kol) {
            show_404();
            return;
        }

        $platform_stats = $this->model_performance_card->get_platform_stats($kol_id);
        $trend_data     = $this->model_performance_card->get_snapshot_history($kol_id, 6);
        $rate_matrix    = $this->model_performance_card->get_rate_matrix($kol_id);

        $this->load->library('HtmlPdf');

        $config = [
            'orientation' => 'p',
            'format'      => 'a4',
            'marges'      => [12, 10, 12, 12]
        ];

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('helvetica');

        $content = $this->pdf->loadHtmlPdf(
            'backend/standart/administrator/performance_card/performance_card_pdf',
            [
                'kol'            => $kol,
                'platform_stats' => $platform_stats,
                'trend_data'     => $trend_data,
                'rate_matrix'    => $rate_matrix,
            ],
            TRUE
        );

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output('Performance_Card_' . $kol->name . '.pdf', 'I');
    }
}
