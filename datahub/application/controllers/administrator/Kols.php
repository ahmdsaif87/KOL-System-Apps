<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Kols extends Admin {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Model_kols');
    }

    public function index()
    {
        $this->is_allowed('kols_list');

        $data = [
            'kols' => $this->Model_kols->get_kols()
        ];

        $this->template->title('KOL List');
        $this->render('backend/standart/administrator/kols/kols_list', $data);
    }

    public function view($id = null)
    {
        $this->is_allowed('kols_view');

        if (!$id) {
            redirect('administrator/kols');
        }

        $kol = $this->Model_kols->get_kol($id);
        if (!$kol) {
            show_404();
        }

        $platforms = $this->Model_kols->get_kol_platforms($id);
        
        // Prepare chart data
        $historical = $this->Model_kols->get_historical_snapshots($id);
        $chart_data = [];
        $chart_labels = [];
        foreach ($historical as $snap) {
            $date = date('M d', strtotime($snap->snapshot_date));
            if (!in_array($date, $chart_labels)) {
                $chart_labels[] = $date;
            }
            if (!isset($chart_data[$snap->code])) {
                $chart_data[$snap->code] = [];
            }
            $chart_data[$snap->code][$date] = $snap->average_views_last_30;
        }

        // Fill missing dates with previous value for continuity
        foreach ($chart_data as $platform => &$data_points) {
            $formatted_points = [];
            $last_val = 0;
            foreach ($chart_labels as $label) {
                if (isset($data_points[$label])) {
                    $last_val = $data_points[$label];
                }
                $formatted_points[] = $last_val;
            }
            $data_points = $formatted_points;
        }

        $data = [
            'kol' => $kol,
            'platforms' => $platforms,
            'chart_labels' => json_encode($chart_labels),
            'chart_data' => json_encode($chart_data)
        ];

        $this->template->title('KOL Performance Card');
        $this->render('backend/standart/administrator/kols/kols_view', $data);
    }

    /**
     * AJAX: Trigger GMV sync for a single KOL via FastMoss API.
     * Pattern: CSRF + session_write_close + cache-hit guard (no Apify call if fresh).
     *
     * POST /administrator/kols/sync_gmv_ajax/{kol_id}
     */
    public function sync_gmv_ajax($kol_id = null)
    {
        // Auth + CSRF
        if (!$this->aauth->is_loggedin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
        }
        $kol_id = (int)$kol_id;
        if ($kol_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'KOL ID tidak valid.']); exit;
        }

        // Release session lock early (long-running cURL)
        session_write_close();

        try {
            $this->load->library('Keyapi_gmv');
            $result = $this->keyapi_gmv->sync_kol($kol_id, true);

            $new_csrf = $this->security->get_csrf_hash();
            echo json_encode([
                'success'    => in_array($result['status'], ['OK', 'SKIP', 'EMPTY']),
                'status'     => $result['status'],
                'message'    => $result['message'],
                'count'      => $result['count'],
                'csrf_token' => $new_csrf,
            ]);
        } catch (Throwable $e) {
            log_message('error', "[Kols::sync_gmv_ajax] kol_id={$kol_id}: " . $e->getMessage());
            echo json_encode([
                'success' => false,
                'status'  => 'FAILED',
                'message' => 'Terjadi kesalahan server: ' . $e->getMessage(),
                'count'   => 0,
            ]);
        }
        exit;
    }
}
