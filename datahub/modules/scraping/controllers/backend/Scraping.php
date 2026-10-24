<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Scraping Controller
 * 
 * Handles the Scraping Monitor page and bulk/single scrape endpoints.
 */
class Scraping extends Admin
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('model_scraping');
        $this->lang->load('web_lang', $this->current_lang);
    }

    /**
     * Scraping Monitor — paginated list of kol_profile_snapshots.
     */
    public function index($offset = 0)
    {
        $this->is_allowed('scraping_list');

        $filter = [
            'platform_code' => $this->input->get('platform'),
            'kol_name'      => $this->input->get('q'),
            'date_from'     => $this->input->get('date_from'),
            'date_to'       => $this->input->get('date_to'),
        ];

        $limit = 20;
        $this->data['snapshots']    = $this->model_scraping->get_snapshots($filter, $limit, $offset);
        $this->data['total']        = $this->model_scraping->count_snapshots($filter);
        $this->data['filter']       = $filter;

        // Platforms list for filter dropdown
        $this->db->select('id, name, code');
        $this->db->from('platforms');
        $this->db->where_in('code', ['TIKTOK', 'INSTAGRAM', 'YOUTUBE']);
        $this->data['platforms'] = $this->db->get()->result();

        $config = [
            'base_url'    => 'administrator/scraping/index/',
            'total_rows'  => $this->data['total'],
            'per_page'    => $limit,
            'uri_segment' => 4,
        ];
        $this->data['pagination'] = $this->pagination($config);

        $this->template->title('Scraping Hub');
        $this->render('backend/standart/administrator/scraping/scraping_monitor', $this->data);
    }

    /**
     * Bulk scrape — AJAX POST with kol_ids[].
     */
    public function bulk()
    {
        if (!$this->is_allowed('scraping_add', false)) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $kol_platform_ids = $this->input->post('kol_platform_ids');
        if (empty($kol_platform_ids) || !is_array($kol_platform_ids)) {
            echo json_encode(['success' => false, 'message' => 'No platforms selected']);
            return;
        }

        $kol_platform_ids = array_unique($kol_platform_ids);
        $kol_platform_ids_str = implode(',', $kol_platform_ids);
        
        // Count distinct KOLs for selected platforms immediately
        $this->db->distinct();
        $this->db->select('kol_id');
        $this->db->from('kol_platforms');
        $this->db->where_in('id', $kol_platform_ids);
        $total_kols_init = $this->db->count_all_results();

        // 1. Insert Job Record with initial phase
        $this->db->insert('scraping_jobs', [
            'status' => 'pending',
            'total_kols' => $total_kols_init,
            'details_json' => json_encode([
                'phase' => 'queued',
                'phase_step' => 1,
                'phase_label' => 'Dalam antrean worker background...',
                'progress_percent' => 5,
                'total_platforms' => count($kol_platform_ids),
                'completed_platforms' => 0,
                'results' => []
            ]),
            'created_at' => date('Y-m-d H:i:s')
        ]);
        $job_id = $this->db->insert_id();

        // 2. Spawn Background Process via CLI
        // This avoids HTTP loopback deadlocks on single-threaded dev servers (php -S)
        $php_path = 'php'; 
        $index_path = FCPATH . 'index.php';
        
        // CodeIgniter CLI router rejects commas in URIs by default. Replace with dashes for CLI transport.
        $kol_platform_ids_safe = str_replace(',', '-', $kol_platform_ids_str);
        
        $cli_route = "cli_run/{$job_id}/{$kol_platform_ids_safe}";
        $cmd = "{$php_path} \"{$index_path}\" \"{$cli_route}\"";
        
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            pclose(popen("start \"\" /B {$cmd} > NUL", "r"));
        } else {
            exec("{$cmd} > /dev/null 2>&1 &");
        }

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true, 
            'message' => 'Job Started! The scraping process is now running in the background. You can check the Inbox icon in the top header for results.'
        ]);
        exit;
    }

    /**
     * Get inbox/notifications for background scraping jobs.
     */
    public function get_inbox()
    {
        // Auto-fail stale jobs running for more than 30 minutes to prevent infinite spinning
        $stale_cutoff = date('Y-m-d H:i:s', strtotime('-30 minutes'));
        $this->db->where_in('status', ['pending', 'processing'])
                 ->where('created_at <', $stale_cutoff)
                 ->update('scraping_jobs', [
                     'status' => 'failed',
                     'completed_at' => date('Y-m-d H:i:s'),
                     'details_json' => json_encode(['error_summary' => ['Timed out / Process was interrupted']])
                 ]);

        $this->db->select('id, status, total_kols, success_count, error_count, details_json, created_at, completed_at');
        $this->db->from('scraping_jobs');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(10);
        $jobs = $this->db->get()->result();

        $unread_count = 0;
        foreach ($jobs as $job) {
            // A simple logic: if it completed today, consider it "new" for badge purposes, 
            // or just count pending/processing ones.
            if (in_array($job->status, ['pending', 'processing'])) {
                $unread_count++;
            }
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'jobs' => $jobs, 'active_count' => $unread_count]);
        exit;
    }

    /**
     * Run a background scraping job via CLI route
     */
    public function run_background_job_cli($job_id = null, $kol_ids_str = null)
    {
        if (!is_cli()) {
            echo "This script can only be accessed via the command line";
            return;
        }

        if (empty($job_id) || empty($kol_ids_str)) {
            echo "Missing parameters\n";
            return;
        }

        // Restore commas that were replaced by dashes for CLI routing
        $kol_ids_str = str_replace('-', ',', $kol_ids_str);

        // To prevent timeout when running via CLI
        set_time_limit(0);
        ignore_user_abort(true);

        $this->db->where('id', $job_id)->update('scraping_jobs', ['status' => 'processing']);

        $kol_ids = array_unique(explode(',', $kol_ids_str));
        $total = count($kol_ids);
        $success_count = 0;
        $error_count = 0;
        $all_results = [];

        foreach ($kol_ids as $kol_id) {
            $platforms = $this->model_scraping->get_platforms_for_kol($kol_id);
            $kol_result = ['kol_id' => $kol_id, 'platforms' => []];

            if (empty($platforms)) {
                $kol_result['status'] = 'skipped';
                $kol_result['message'] = 'No active platforms';
                $error_count++;
            } else {
                $kol_result['status'] = 'success';
                $kol_success = true;
                foreach ($platforms as $p) {
                    $scrape = $this->model_scraping->force_scrape($p);
                    $kol_result['platforms'][] = [
                        'platform' => $p->platform_code,
                        'username' => $p->username,
                        'success'  => $scrape['success'],
                        'message'  => $scrape['message'],
                        'data'     => isset($scrape['data']) ? $scrape['data'] : null,
                    ];
                    if (!$scrape['success']) {
                        $kol_success = false;
                    }
                }
                
                if ($kol_success) {
                    $success_count++;
                } else {
                    $kol_result['status'] = 'partial';
                    $error_count++;
                }
            }
            $all_results[] = $kol_result;
        }

        $error_msgs = [];
        foreach ($all_results as $res) {
            if (isset($res['platforms'])) {
                foreach ($res['platforms'] as $plat) {
                    if (!$plat['success']) {
                        $error_msgs[] = "KOL {$res['kol_id']} ({$plat['platform']}): {$plat['message']}";
                    }
                }
            } elseif (isset($res['message'])) {
                $error_msgs[] = "KOL {$res['kol_id']}: {$res['message']}";
            }
        }
        
        $details_json = json_encode([
            'results' => $all_results,
            'error_summary' => $error_msgs
        ]);

        $this->db->where('id', $job_id)->update('scraping_jobs', [
            'status' => ($success_count == $total) ? 'completed' : 'failed',
            'success_count' => $success_count,
            'error_count' => $error_count,
            'details_json' => $details_json,
            'completed_at' => date('Y-m-d H:i:s')
        ]);
        
        echo "Job {$job_id} finished.\n";
        exit;
    }

    /**
     * Cancel a running/pending background job
     */
    public function cancel_job($job_id)
    {
        if (empty($job_id)) {
            echo json_encode(['success' => false]);
            return;
        }
        
        $this->db->where('id', $job_id)->where_in('status', ['pending', 'processing'])
                 ->update('scraping_jobs', [
                     'status' => 'failed', 
                     'error_count' => 1, 
                     'completed_at' => date('Y-m-d H:i:s'),
                     'details_json' => json_encode(['error_summary' => ['Cancelled by user']])
                 ]);
                 
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }

    /**
     * Get real-time status and details of a single scraping job
     */
    public function get_job_detail($job_id)
    {
        if (empty($job_id)) {
            echo json_encode(['success' => false, 'message' => 'Missing job ID']);
            return;
        }

        $job = $this->db->where('id', $job_id)->get('scraping_jobs')->row();
        if (!$job) {
            echo json_encode(['success' => false, 'message' => 'Job not found']);
            return;
        }

        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'job' => $job]);
        exit;
    }

    /**
     * Scrape a single KOL — AJAX POST.
     */
    public function scrape_single($kol_id)
    {
        if (is_kol_user() && !$this->aauth->is_admin()) {
            header('Content-Type: application/json', true, 403);
            echo json_encode(['success' => false, 'message' => 'KOLs are not authorized to trigger scraping.']);
            exit;
        }

        if (!$this->is_allowed('scraping_add', false)) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $platforms = $this->model_scraping->get_platforms_for_kol($kol_id);
        if (empty($platforms)) {
            echo json_encode(['success' => false, 'message' => 'No active platforms for this KOL']);
            return;
        }

        $results = [];
        $all_ok = true;
        foreach ($platforms as $p) {
            $scrape = $this->model_scraping->force_scrape($p);
            $results[] = [
                'platform' => $p->platform_code,
                'username' => $p->username,
                'success'  => $scrape['success'],
                'message'  => $scrape['message'],
                'data'     => isset($scrape['data']) ? $scrape['data'] : null,
            ];
            if (!$scrape['success']) $all_ok = false;
        }

        echo json_encode([
            'success' => $all_ok,
            'message' => $all_ok ? 'All platforms scraped' : 'Some platforms failed',
            'results' => $results,
        ]);
    }

    /**
     * Scrape all stale KOLs — AJAX POST.
     */
    public function stale()
    {
        if (!$this->is_allowed('scraping_add', false)) {
            echo json_encode(['success' => false, 'message' => 'Permission denied']);
            return;
        }

        $stale_ids = $this->model_scraping->get_stale_kol_ids();
        if (empty($stale_ids)) {
            echo json_encode(['success' => true, 'message' => 'No stale KOLs found']);
            return;
        }

        // Delegate to bulk logic
        $_POST['kol_ids'] = $stale_ids;
        $this->bulk();
    }

    /**
     * Proxies external images to bypass hotlink protection (e.g., Instagram CDNs)
     */
    public function proxy_image()
    {
        $url = $this->input->get('url');
        if (empty($url) || !preg_match('/^https?:\/\//i', $url)) {
            show_404();
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $data = curl_exec($ch);
        $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        curl_close($ch);

        if ($data) {
            header("Content-Type: $content_type");
            echo $data;
        } else {
            show_404();
        }
    }
}
