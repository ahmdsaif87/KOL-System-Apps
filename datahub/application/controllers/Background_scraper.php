<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Background_scraper extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
        if (!is_cli()) {
            exit('No direct script access allowed');
        }
        $this->db = $this->load->database('', TRUE);
        if (!class_exists('CI_Model')) {
            require_once BASEPATH . 'core/Model.php';
        }
        require_once FCPATH . 'modules/scraping/models/Model_scraping.php';
        $this->model_scraping = new Model_scraping();
    }

    /**
     * Run a background scraping job concurrently.
     * Usage: php index.php cli/Background_scraper run <job_id> <hyphen_separated_kol_ids>
     */
    public function run($job_id = null, $kol_ids_str = null)
    {
        if (empty($job_id) || empty($kol_ids_str)) {
            log_message('error', 'Background_scraper: Missing job_id or kol_ids');
            return;
        }

        // Update job status to processing
        $this->db->where('id', $job_id)->update('scraping_jobs', [
            'status' => 'processing'
        ]);

        $kol_platform_ids = explode('-', $kol_ids_str);
        
        // 1. Fetch specifically selected platforms
        $this->db->select('kp.id as kol_platform_id, kp.kol_id, kp.platform_id, kp.username, kp.profile_url, p.code as platform_code');
        $this->db->from('kol_platforms kp');
        $this->db->join('platforms p', 'p.id = kp.platform_id');
        $this->db->where_in('kp.id', $kol_platform_ids);
        $this->db->where('kp.is_active', 1);
        $all_platforms = $this->db->get()->result();

        $unique_kol_ids = [];
        foreach ($all_platforms as $p) {
            $unique_kol_ids[$p->kol_id] = true;
        }
        $kol_ids = array_keys($unique_kol_ids);
        $total_kols = count($kol_ids);
        $total_platforms = count($all_platforms);

        if (empty($all_platforms)) {
            $this->db->where('id', $job_id)->update('scraping_jobs', [
                'status' => 'failed',
                'total_kols' => 0,
                'error_count' => 1,
                'details_json' => json_encode([
                    'phase' => 'failed',
                    'phase_step' => 1,
                    'phase_label' => 'Gagal: Tidak ada platform aktif',
                    'progress_percent' => 0,
                    'error_summary' => ['No active platforms found for selected IDs']
                ]),
                'completed_at' => date('Y-m-d H:i:s')
            ]);
            echo "Job {$job_id} finished (no active platforms).\n";
            return;
        }

        // Phase 1: Inisialisasi
        $this->db->where('id', $job_id)->update('scraping_jobs', [
            'status' => 'processing',
            'total_kols' => $total_kols,
            'details_json' => json_encode([
                'phase' => 'initializing',
                'phase_step' => 1,
                'phase_label' => 'Menginisialisasi koneksi dan data platform...',
                'progress_percent' => 10,
                'total_platforms' => $total_platforms,
                'completed_platforms' => 0,
                'results' => []
            ])
        ]);

        // 2. Spawn concurrent processes using proc_open
        $processes = [];
        $fcpath = FCPATH;
        foreach ($all_platforms as $p) {
            // Command to run the single platform scraper (quote path for Windows directory spaces)
            $cmd = "php \"{$fcpath}index.php\" cli_scrape_platform {$p->kol_platform_id}";
            $descriptorspec = [
                0 => ["pipe", "r"],  // stdin
                1 => ["pipe", "w"],  // stdout
                2 => ["pipe", "w"]   // stderr
            ];
            
            $process = proc_open($cmd, $descriptorspec, $pipes);
            if (is_resource($process)) {
                // Set streams to non-blocking to prevent deadlocks
                stream_set_blocking($pipes[1], 0);
                stream_set_blocking($pipes[2], 0);
                
                $processes[] = [
                    'process' => $process,
                    'pipes' => $pipes,
                    'platform' => $p,
                    'stdout' => '',
                    'stderr' => ''
                ];
            }
        }

        // Phase 2: Pengambilan Data (Scraping)
        $this->db->where('id', $job_id)->update('scraping_jobs', [
            'details_json' => json_encode([
                'phase' => 'scraping',
                'phase_step' => 2,
                'phase_label' => "Menghubungi API untuk {$total_platforms} platform akun...",
                'progress_percent' => 20,
                'total_platforms' => $total_platforms,
                'completed_platforms' => 0,
                'results' => []
            ])
        ]);

        // 3. Wait for all processes to finish concurrently with timeout & progress tracking
        $start_time = time();
        $timeout_seconds = max(180, count($all_platforms) * 60); // 3 minutes minimum or 60s per platform
        $last_update_time = time();
        $last_completed = 0;
        $active = true;

        while ($active) {
            // Prevent hanging processes indefinitely
            if ((time() - $start_time) > $timeout_seconds) {
                log_message('error', "Background_scraper: Job {$job_id} reached process timeout ({$timeout_seconds}s). Terminating remaining processes.");
                foreach ($processes as &$proc) {
                    $status = proc_get_status($proc['process']);
                    if ($status['running']) {
                        proc_terminate($proc['process']);
                        $proc['stderr'] .= "\n[Process timed out after {$timeout_seconds}s]";
                    }
                }
                break;
            }

            $active = false;
            $current_completed = 0;
            foreach ($processes as &$proc) {
                // Read available output so pipes don't fill up and block
                $proc['stdout'] .= stream_get_contents($proc['pipes'][1]);
                $proc['stderr'] .= stream_get_contents($proc['pipes'][2]);
                
                $status = proc_get_status($proc['process']);
                if ($status['running']) {
                    $active = true;
                } else {
                    $current_completed++;
                }
            }

            // Update live progress in DB if completed count changed
            if ($current_completed != $last_completed && (time() - $last_update_time) >= 1) {
                $last_completed = $current_completed;
                $last_update_time = time();
                $calc_percent = 20 + (int)(($current_completed / max(1, $total_platforms)) * 60); // 20% to 80%
                $this->db->where('id', $job_id)->update('scraping_jobs', [
                    'details_json' => json_encode([
                        'phase' => 'scraping',
                        'phase_step' => 2,
                        'phase_label' => "Mengambil data profil: {$current_completed} dari {$total_platforms} platform selesai...",
                        'progress_percent' => min(80, $calc_percent),
                        'total_platforms' => $total_platforms,
                        'completed_platforms' => $current_completed,
                        'results' => []
                    ])
                ]);
            }

            if ($active) {
                usleep(500000); // Sleep for 0.5 seconds
            }
        }

        // Phase 3: Rekapitulasi & Kalkulasi Metrik
        $this->db->where('id', $job_id)->update('scraping_jobs', [
            'details_json' => json_encode([
                'phase' => 'calculating',
                'phase_step' => 3,
                'phase_label' => 'Menghitung Engagement Rate, Followers, & menyimpan snapshot...',
                'progress_percent' => 88,
                'total_platforms' => $total_platforms,
                'completed_platforms' => $total_platforms,
                'results' => []
            ])
        ]);

        // 4. Collect results
        $results_by_kol = [];
        foreach ($kol_ids as $kol_id) {
            $results_by_kol[$kol_id] = [
                'kol_id' => $kol_id,
                'status' => 'pending',
                'platforms' => []
            ];
        }

        foreach ($processes as &$proc) {
            // Final read to ensure we got everything
            $proc['stdout'] .= stream_get_contents($proc['pipes'][1]);
            $proc['stderr'] .= stream_get_contents($proc['pipes'][2]);
            
            fclose($proc['pipes'][0]);
            fclose($proc['pipes'][1]);
            fclose($proc['pipes'][2]);
            proc_close($proc['process']);
            
            $p = $proc['platform'];
            
            // Try to extract JSON from stdout (it might have other PHP warnings/notices)
            $output = trim($proc['stdout']);
            $parsed_res = null;
            
            // Simple extraction: find the last valid JSON object in output
            if (preg_match('/\{.*\}/s', $output, $matches)) {
                $parsed_res = json_decode($matches[0], true);
            }
            
            if ($parsed_res && isset($parsed_res['success'])) {
                $results_by_kol[$p->kol_id]['platforms'][] = [
                    'platform' => $p->platform_code,
                    'username' => $p->username,
                    'success'  => $parsed_res['success'],
                    'message'  => $parsed_res['message'],
                    'data'     => isset($parsed_res['data']) ? $parsed_res['data'] : null
                ];
            } else {
                $results_by_kol[$p->kol_id]['platforms'][] = [
                    'platform' => $p->platform_code,
                    'username' => $p->username,
                    'success'  => false,
                    'message'  => "Process failed to output valid JSON. Stderr: " . substr($proc['stderr'], 0, 100),
                    'data'     => null
                ];
            }
        }

        // 5. Aggregate and calculate final success/error counts
        $success_count = 0;
        $error_count = 0;
        $error_msgs = [];
        $all_results = [];

        foreach ($results_by_kol as $kol_id => $kol_result) {
            if (empty($kol_result['platforms'])) {
                $kol_result['status'] = 'skipped';
                $kol_result['message'] = 'No active platforms';
                $error_count++;
            } else {
                $kol_success = true;
                foreach ($kol_result['platforms'] as $plat) {
                    if (!$plat['success']) {
                        $kol_success = false;
                        $error_msgs[] = "KOL {$kol_id} ({$plat['platform']}): {$plat['message']}";
                    }
                }
                
                if ($kol_success) {
                    $kol_result['status'] = 'success';
                    $success_count++;
                } else {
                    $kol_result['status'] = 'partial';
                    $error_count++;
                }
            }
            $all_results[] = $kol_result;
        }

        // Phase 4: Selesai
        $details_json = json_encode([
            'phase' => ($success_count == $total_kols) ? 'completed' : 'completed_with_errors',
            'phase_step' => 4,
            'phase_label' => ($success_count == $total_kols) ? 'Scraping Berhasil Selesai' : 'Scraping Selesai (Beberapa Error)',
            'progress_percent' => 100,
            'total_platforms' => $total_platforms,
            'completed_platforms' => $total_platforms,
            'results' => $all_results,
            'error_summary' => $error_msgs
        ]);

        // 6. Finish the job
        $this->db->where('id', $job_id)->update('scraping_jobs', [
            'status' => ($success_count == $total_kols) ? 'completed' : 'failed',
            'success_count' => $success_count,
            'error_count' => $error_count,
            'details_json' => $details_json,
            'completed_at' => date('Y-m-d H:i:s')
        ]);

        echo "Job {$job_id} finished.\n";
    }

    /**
     * Worker method to scrape a single platform.
     * Usage: php index.php cli_scrape_platform <kol_platform_id>
     */
    public function scrape_platform($kol_platform_id)
    {
        if (empty($kol_platform_id)) {
            echo json_encode(['success' => false, 'message' => 'Missing kol_platform_id']);
            return;
        }

        $platform = $this->db->select('kp.*, kp.id as kol_platform_id, p.code as platform_code')
                             ->from('kol_platforms kp')
                             ->join('platforms p', 'p.id = kp.platform_id')
                             ->where('kp.id', $kol_platform_id)
                             ->get()->row();

        if (!$platform) {
            echo json_encode(['success' => false, 'message' => 'Platform not found']);
            return;
        }

        // Run the actual heavy scraping operation
        $scrape = $this->model_scraping->force_scrape($platform);

        // Output JSON specifically formatted for the parent process to parse
        echo json_encode([
            'kol_platform_id' => $kol_platform_id,
            'kol_id' => $platform->kol_id,
            'platform_code' => $platform->platform_code,
            'username' => $platform->username,
            'success' => $scrape['success'],
            'message' => $scrape['message'],
            'data'    => isset($scrape['data']) ? $scrape['data'] : null
        ]);
    }
}
