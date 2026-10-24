<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Keyapi_config Controller
 * 
 * Manages configuration, API token, and connectivity testing for KeyAPI (keyapi.ai) TikTok Shop GMV service.
 */
class Keyapi_config extends Admin
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper(['app', 'url']);
        $this->load->database();
        $this->lang->load('web_lang', $this->current_lang);
    }

    /**
     * Display KeyAPI Configuration Page
     */
    public function index()
    {
        if (!$this->aauth->is_admin() && !$this->aauth->is_member('Admin')) {
            $this->is_allowed('keyapi_config_view', false);
        }

        // Auto-register menu in database
        $this->_ensure_menu_exists();

        // Load static defaults from config file
        $this->config->load('keyapi', TRUE);
        $cfg = $this->config->item('keyapi') ?: [];

        // Build active configuration by merging cc_options with static config fallback
        $config_data = [
            'keyapi_api_key'         => get_option('keyapi_api_key',         $cfg['keyapi_api_key']         ?? ''),
            'keyapi_base_url'        => get_option('keyapi_base_url',        $cfg['keyapi_base_url']        ?? 'https://api.keyapi.ai'),
            'keyapi_cache_days'      => (int) get_option('keyapi_cache_days',      $cfg['keyapi_cache_days']      ?? 7),
            'keyapi_max_products'    => (int) get_option('keyapi_max_products',    $cfg['keyapi_max_products']    ?? 20),
            'keyapi_usd_to_idr_rate' => (float) get_option('keyapi_usd_to_idr_rate', $cfg['keyapi_usd_to_idr_rate'] ?? 16150),
        ];

        $this->data['config_data'] = $config_data;

        $this->template->title('Konfigurasi KeyAPI (TikTok Shop GMV)');
        $this->render('backend/standart/administrator/keyapi_config/keyapi_config', $this->data);
    }

    /**
     * Save KeyAPI Configuration
     */
    public function save()
    {
        if (!$this->aauth->is_admin() && !$this->aauth->is_member('Admin')) {
            echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki hak akses untuk mengubah konfigurasi ini.']);
            exit;
        }

        $api_key      = trim($this->input->post('keyapi_api_key'));
        $base_url     = trim($this->input->post('keyapi_base_url'));
        $cache_days   = (int) $this->input->post('keyapi_cache_days');
        $max_products = (int) $this->input->post('keyapi_max_products');
        $usd_rate     = (float) $this->input->post('keyapi_usd_to_idr_rate');

        if (empty($api_key)) {
            echo json_encode(['success' => false, 'message' => 'KeyAPI API Key tidak boleh kosong.']);
            exit;
        }

        if (empty($base_url)) {
            $base_url = 'https://api.keyapi.ai';
        }
        if ($cache_days <= 0)   $cache_days   = 7;
        if ($max_products <= 0) $max_products = 20;
        if ($usd_rate <= 0)     $usd_rate     = 16150;

        set_option('keyapi_api_key',         $api_key);
        set_option('keyapi_base_url',        $base_url);
        set_option('keyapi_cache_days',      $cache_days);
        set_option('keyapi_max_products',    $max_products);
        set_option('keyapi_usd_to_idr_rate', $usd_rate);

        // Also update application/config/keyapi.php file so file and database are always in sync
        $config_file = APPPATH . 'config/keyapi.php';
        if (is_writable($config_file)) {
            $content = "<?php\n"
                     . "defined('BASEPATH') OR exit('No direct script access allowed');\n\n"
                     . "/**\n"
                     . " * KeyAPI (keyapi.ai) TikTok Shop Analytics Configuration\n"
                     . " *\n"
                     . " * Updated via KeyAPI Configuration Admin Panel\n"
                     . " */\n"
                     . "\$config['keyapi_base_url']        = " . var_export($base_url, true) . ";\n"
                     . "\$config['keyapi_api_key']         = " . var_export($api_key, true) . ";\n"
                     . "\$config['keyapi_cache_days']      = " . $cache_days . ";\n"
                     . "\$config['keyapi_max_products']    = " . $max_products . ";\n"
                     . "\$config['keyapi_usd_to_idr_rate'] = " . $usd_rate . ";\n";
            @file_put_contents($config_file, $content);
        }

        echo json_encode([
            'success'   => true,
            'message'   => 'Konfigurasi KeyAPI berhasil disimpan ke database & config.',
            'csrf_name' => $this->security->get_csrf_token_name(),
            'csrf_hash' => $this->security->get_csrf_hash()
        ]);
        exit;
    }

    /**
     * Live Test Connection to KeyAPI
     */
    public function test_connection()
    {
        if (!$this->aauth->is_loggedin()) {
            echo json_encode([
                'success'   => false, 
                'message'   => 'Unauthorized',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            exit;
        }

        $key = trim($this->input->post('keyapi_api_key'));
        if (empty($key)) {
            $key = get_option('keyapi_api_key', '');
        }
        if (empty($key)) {
            echo json_encode([
                'success'   => false, 
                'message'   => 'KeyAPI API Key tidak boleh kosong.',
                'csrf_name' => $this->security->get_csrf_token_name(),
                'csrf_hash' => $this->security->get_csrf_hash()
            ]);
            exit;
        }

        $base_url = trim($this->input->post('keyapi_base_url')) ?: get_option('keyapi_base_url', 'https://api.keyapi.ai');
        $endpoint = rtrim($base_url, '/') . '/v1/tiktok/influencer/detail/analytics?unique_ids=mamankkuliner';

        $ch = curl_init($endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPGET        => true,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $key,
                'Accept: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);
        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error     = curl_error($ch);
        curl_close($ch);

        $csrf_name = $this->security->get_csrf_token_name();
        $csrf_hash = $this->security->get_csrf_hash();

        if ($error) {
            echo json_encode([
                'success'   => false, 
                'message'   => 'cURL Error: ' . $error,
                'csrf_name' => $csrf_name,
                'csrf_hash' => $csrf_hash
            ]);
            exit;
        }

        $data = json_decode($response, true);
        if ($http_code === 200 && isset($data['code']) && $data['code'] === 0) {
            $nick = $data['data'][0]['nick_name'] ?? 'Test Creator';
            $gmv  = $data['data'][0]['total_sale_gmv_amt'] ?? 0;
            echo json_encode([
                'success'   => true,
                'message'   => "Koneksi ke KeyAPI Berhasil! (Test Kreator: {$nick}, GMV: {$gmv})",
                'info'      => ['status' => 'OK', 'http_code' => $http_code],
                'csrf_name' => $csrf_name,
                'csrf_hash' => $csrf_hash
            ]);
        } else {
            $err = $data['message'] ?? ($data['msg'] ?? "HTTP {$http_code}: Token KeyAPI tidak valid atau kuota habis.");
            echo json_encode([
                'success'   => false, 
                'message'   => $err,
                'csrf_name' => $csrf_name,
                'csrf_hash' => $csrf_hash
            ]);
        }
        exit;
    }

    /**
     * Automatically ensure sub-menu exists under Configuration in menu table
     */
    private function _ensure_menu_exists()
    {
        $perms = [
            'keyapi_config_view'      => 'Permission for keyapi_config_view',
            'keyapi_config_update'    => 'Permission for keyapi_config_update',
            'menu_keyapi_tiktok_shop' => 'Menu KeyAPI TikTok Shop',
        ];
        foreach ($perms as $perm_name => $perm_def) {
            $perm_id = $this->aauth->get_perm_id($perm_name);
            if (!$perm_id) {
                $perm_id = $this->aauth->create_perm($perm_name, $perm_def);
            }
            if ($perm_id && !$this->aauth->is_group_allowed($perm_id, 'Admin')) {
                $this->aauth->allow_group('Admin', $perm_name);
            }
        }

        // Update existing Kalodata menu or insert KeyAPI menu
        $existing = $this->db->like('link', 'kalodata_config')->get('menu')->result();
        if (!empty($existing)) {
            foreach ($existing as $m) {
                $this->db->where('id', $m->id)->update('menu', [
                    'label' => 'KeyAPI TikTok Shop',
                    'link'  => '{admin_url}/keyapi_config',
                    'icon'  => 'fa-shopping-bag'
                ]);
            }
        } else {
            $parent = $this->db->get_where('menu', ['id' => 130])->row();
            if ($parent) {
                $exists = $this->db->get_where('menu', ['link' => '{admin_url}/keyapi_config'])->row();
                if (!$exists) {
                    $this->db->insert('menu', [
                        'label'      => 'KeyAPI TikTok Shop',
                        'type'       => 'menu',
                        'icon_color' => 'default',
                        'link'       => '{admin_url}/keyapi_config',
                        'parent'     => 130,
                        'sort'       => 59,
                        'icon'       => 'fa-shopping-bag',
                        'active'     => 1,
                        'app_id'     => 2,
                    ]);
                }
            }
        }
    }
}
