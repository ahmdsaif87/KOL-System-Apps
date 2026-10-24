<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scraping_settings extends Admin {

    public function __construct()
    {
        parent::__construct();
        $this->load->helper('file');
    }

    public function index()
    {
        // Only allow superadmin (group 1)
        if (!$this->aauth->is_admin()) {
            show_error('Only Super Admins can access Scraping Settings', 403);
        }

        $this->load->config('apify');

        if ($this->input->post('save')) {
            $token = $this->input->post('apify_token');
            $actor_id = $this->input->post('apify_actor_id');

            // Generate config file content
            $config_content = "<?php\n\n";
            $config_content .= "\$config['apify_token'] = '{$token}';\n";
            $config_content .= "\$config['apify_actor_id'] = '{$actor_id}';\n";

            if (write_file(APPPATH . 'config/apify.php', $config_content)) {
                $this->session->set_flashdata('success', 'Apify Settings updated successfully!');
            } else {
                $this->session->set_flashdata('error', 'Failed to save settings. Please check directory permissions.');
            }
            redirect('administrator/scraping_settings');
        }

        $data = [
            'apify_token' => $this->config->item('apify_token'),
            'apify_actor_id' => $this->config->item('apify_actor_id')
        ];

        $this->template->title('Scraping Settings');
        $this->render('backend/standart/administrator/scraping/settings', $data);
    }
}
