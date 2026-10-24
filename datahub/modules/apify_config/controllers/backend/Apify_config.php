<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Apify_config Controller
 * 
 * Manages dynamic configuration for the Apify Scraper service,
 * including API token, network parameters, and platform actor IDs.
 */
class Apify_config extends Admin
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['app', 'url']);
        $this->load->database();
        $this->lang->load('web_lang', $this->current_lang);
    }

    /**
     * Display the Apify Configuration Form
     */
    public function index()
    {
        $this->is_allowed('apify_config_view', false);

        // Auto-register menu if not exists in database
        $this->_ensure_menu_exists();

        // Load static defaults from config file
        $this->config->load('apify', TRUE);
        $cfg = $this->config->item('apify') ?: [];

        // Build active configuration by merging cc_options with static config fallback
        $config_data = [
            'apify_api_token'       => get_option('apify_api_token', $cfg['apify_api_token'] ?? ''),
            'apify_base_url'        => get_option('apify_base_url', $cfg['apify_base_url'] ?? 'https://api.apify.com/v2'),
            'apify_max_retries'     => get_option('apify_max_retries', $cfg['apify_max_retries'] ?? 3),
            'apify_timeout'         => get_option('apify_timeout', $cfg['apify_timeout'] ?? 240),
            'apify_results_limit'   => get_option('apify_results_limit', 30),

            // Post Scraper Actors (Defaulted to high-efficiency cost-saving actors)
            'actor_tiktok'          => get_option('apify_actor_tiktok', $cfg['apify_actors']['TIKTOK'] ?? 'apidojo~tiktok-profile-scraper-api'),
            'actor_instagram'       => get_option('apify_actor_instagram', $cfg['apify_actors']['INSTAGRAM'] ?? 'apidojo~instagram-scraper-api'),
            'actor_youtube'         => get_option('apify_actor_youtube', $cfg['apify_actors']['YOUTUBE'] ?? 'streamers~youtube-channel-scraper'),
            'actor_twitter'         => get_option('apify_actor_twitter', $cfg['apify_actors']['TWITTER'] ?? 'apidojo~tweet-scraper'),
            'actor_facebook'        => get_option('apify_actor_facebook', $cfg['apify_actors']['FACEBOOK'] ?? 'apify~facebook-posts-scraper'),

            // Profile Scraper Actors
            'profile_actor_tiktok'    => get_option('apify_profile_actor_tiktok', $cfg['apify_profile_actors']['TIKTOK'] ?? 'apidojo~tiktok-profile-scraper-api'),
            'profile_actor_instagram' => get_option('apify_profile_actor_instagram', $cfg['apify_profile_actors']['INSTAGRAM'] ?? 'apidojo~instagram-scraper-api'),
            'profile_actor_youtube'   => get_option('apify_profile_actor_youtube', $cfg['apify_profile_actors']['YOUTUBE'] ?? 'streamers~youtube-channel-scraper'),
            'profile_actor_twitter'   => get_option('apify_profile_actor_twitter', $cfg['apify_profile_actors']['TWITTER'] ?? 'apidojo~tweet-scraper'),
            'profile_actor_facebook'  => get_option('apify_profile_actor_facebook', $cfg['apify_profile_actors']['FACEBOOK'] ?? 'apify~facebook-pages-scraper'),
        ];

        $this->data['config_data'] = $config_data;

        $this->template->title('Konfigurasi Apify Scraper');
        $this->render('backend/standart/administrator/apify_config/apify_config', $this->data);
    }

    /**
     * Save Apify Configuration
     */
    public function save()
    {
        if (!$this->aauth->is_admin() && !$this->aauth->is_member('Admin')) {
            echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengubah konfigurasi ini.']);
            exit;
        }

        $token        = trim($this->input->post('apify_api_token'));
        $base_url     = trim($this->input->post('apify_base_url'));
        $timeout      = (int)$this->input->post('apify_timeout');
        $max_retries  = (int)$this->input->post('apify_max_retries');
        $limit        = (int)$this->input->post('apify_results_limit');

        if (empty($token)) {
            echo json_encode(['success' => false, 'message' => 'Apify API Token tidak boleh kosong.']);
            exit;
        }

        if (empty($base_url)) {
            $base_url = 'https://api.apify.com/v2';
        }

        if ($timeout <= 0) $timeout = 240;
        if ($max_retries <= 0) $max_retries = 3;
        if ($limit <= 0) $limit = 30;

        // Save core settings
        set_option('apify_api_token', $token);
        set_option('apify_base_url', $base_url);
        set_option('apify_timeout', $timeout);
        set_option('apify_max_retries', $max_retries);
        set_option('apify_results_limit', $limit);

        // Save post scraper actors
        set_option('apify_actor_tiktok', trim($this->input->post('actor_tiktok')));
        set_option('apify_actor_instagram', trim($this->input->post('actor_instagram')));
        set_option('apify_actor_youtube', trim($this->input->post('actor_youtube')));
        set_option('apify_actor_twitter', trim($this->input->post('actor_twitter')));
        set_option('apify_actor_facebook', trim($this->input->post('actor_facebook')));

        // Save profile scraper actors
        set_option('apify_profile_actor_tiktok', trim($this->input->post('profile_actor_tiktok')));
        set_option('apify_profile_actor_instagram', trim($this->input->post('profile_actor_instagram')));
        set_option('apify_profile_actor_youtube', trim($this->input->post('profile_actor_youtube')));
        set_option('apify_profile_actor_twitter', trim($this->input->post('profile_actor_twitter')));
        set_option('apify_profile_actor_facebook', trim($this->input->post('profile_actor_facebook')));

        echo json_encode([
            'success' => true,
            'message' => 'Konfigurasi Apify berhasil disimpan.'
        ]);
        exit;
    }

    /**
     * Reset Actors to Recommended Cost-Optimized Presets (API Dojo & Streamers)
     */
    public function reset_to_recommended()
    {
        if (!$this->aauth->is_admin() && !$this->aauth->is_member('Admin')) {
            echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengubah konfigurasi ini.']);
            exit;
        }

        set_option('apify_actor_tiktok', 'apidojo~tiktok-profile-scraper-api');
        set_option('apify_actor_instagram', 'apidojo~instagram-scraper-api');
        set_option('apify_actor_youtube', 'streamers~youtube-channel-scraper');
        set_option('apify_actor_twitter', 'apidojo~tweet-scraper');
        set_option('apify_actor_facebook', 'apify~facebook-posts-scraper');

        set_option('apify_profile_actor_tiktok', 'apidojo~tiktok-profile-scraper-api');
        set_option('apify_profile_actor_instagram', 'apidojo~instagram-scraper-api');
        set_option('apify_profile_actor_youtube', 'streamers~youtube-channel-scraper');
        set_option('apify_profile_actor_twitter', 'apidojo~tweet-scraper');
        set_option('apify_profile_actor_facebook', 'apify~facebook-pages-scraper');

        echo json_encode([
            'success' => true,
            'message' => 'Berhasil mereset Actor ID ke rekomendasi ekonomis (API Dojo & Streamers). Penghematan estimasi ~75-80%!',
            'actors' => [
                'actor_tiktok'             => 'apidojo~tiktok-profile-scraper-api',
                'actor_instagram'          => 'apidojo~instagram-scraper-api',
                'actor_youtube'            => 'streamers~youtube-channel-scraper',
                'actor_twitter'            => 'apidojo~tweet-scraper',
                'actor_facebook'           => 'apify~facebook-posts-scraper',
                'profile_actor_tiktok'     => 'apidojo~tiktok-profile-scraper-api',
                'profile_actor_instagram'  => 'apidojo~instagram-scraper-api',
                'profile_actor_youtube'    => 'streamers~youtube-channel-scraper',
                'profile_actor_twitter'    => 'apidojo~tweet-scraper',
                'profile_actor_facebook'   => 'apify~facebook-pages-scraper',
            ]
        ]);
        exit;
    }

    /**
     * Live Test Connection to Apify API
     */
    public function test_connection()
    {
        if (!$this->aauth->is_loggedin()) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized']);
            exit;
        }

        $token = trim($this->input->post('token'));
        if (empty($token)) {
            $token = get_option('apify_api_token');
        }

        if (empty($token)) {
            echo json_encode(['success' => false, 'message' => 'Token Apify tidak boleh kosong untuk melakukan pengetesan.']);
            exit;
        }

        $base_url = get_option('apify_base_url', 'https://api.apify.com/v2');
        $url = rtrim($base_url, '/') . '/users/me?token=' . urlencode($token);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            echo json_encode([
                'success' => false,
                'message' => 'cURL Error: ' . $error
            ]);
            exit;
        }

        $data = json_decode($response, true);

        if ($http_code === 200 && isset($data['data'])) {
            $user = $data['data'];
            $username = $user['username'] ?? ($user['email'] ?? 'User');
            $plan = isset($user['plan']['id']) ? strtoupper($user['plan']['id']) : 'FREE';
            
            // Format usage / credits if available
            $limits = $user['limits'] ?? [];
            $monthlyUsage = isset($limits['monthlyUsageUsd']) ? '$' . number_format($limits['monthlyUsageUsd'], 2) : '-';

            echo json_encode([
                'success' => true,
                'message' => 'Koneksi ke Apify Berhasil!',
                'user' => [
                    'id'           => $user['id'] ?? '-',
                    'username'     => $username,
                    'email'        => $user['email'] ?? '-',
                    'plan'         => $plan,
                    'monthly_usage' => $monthlyUsage
                ]
            ]);
            exit;
        } else {
            $err_msg = isset($data['error']['message']) ? $data['error']['message'] : "HTTP {$http_code}: Token tidak valid atau akun tidak ditemukan.";
            echo json_encode([
                'success' => false,
                'message' => $err_msg
            ]);
            exit;
        }
    }

    /**
     * Automatically ensure sub-menu exists under Configuration in menu table
     */
    private function _ensure_menu_exists()
    {
        $app_id = (int)$this->config->item('app_id') ?: 2;

        // 1. Check parent 'Konfigurasi' menu
        $parent1 = $this->db->get_where('menu', ['id' => 130])->row();
        if ($parent1) {
            $exists1 = $this->db->get_where('menu', [
                'parent' => 130,
                'link'   => '{admin_url}/apify_config'
            ])->row();

            if (!$exists1) {
                $this->db->insert('menu', [
                    'label'        => 'Apify Scraper',
                    'type'         => 'menu',
                    'icon_color'   => 'default',
                    'link'         => '{admin_url}/apify_config',
                    'sort'         => 7,
                    'parent'       => 130,
                    'icon'         => 'fa-cloud-download',
                    'menu_type_id' => 1,
                    'active'       => 1,
                    'app_id'       => $app_id
                ]);
            }
        }

        $parent2 = $this->db->get_where('menu', ['id' => 131])->row();
        if ($parent2) {
            $exists2 = $this->db->get_where('menu', [
                'parent' => 131,
                'link'   => '{admin_url}/apify_config'
            ])->row();

            if (!$exists2) {
                $this->db->insert('menu', [
                    'label'        => 'Apify Scraper',
                    'type'         => 'menu',
                    'icon_color'   => 'default',
                    'link'         => '{admin_url}/apify_config',
                    'sort'         => 107,
                    'parent'       => 131,
                    'icon'         => 'fa-cloud-download',
                    'menu_type_id' => 2,
                    'active'       => 1,
                    'app_id'       => $app_id
                ]);
            }
        }
    }
}
