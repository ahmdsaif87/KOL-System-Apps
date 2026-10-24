<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Dashboard Controller
*| --------------------------------------------------------------------------
*| For see your board
*|
*/
class Dashboard extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		if (is_kol_user() && !$this->aauth->is_admin()) {
			redirect('administrator/performance_card/my_card', 'refresh');
		}

		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/', 'refresh');
		}

		$this->load->model('Model_dashboard_metrics');

		$data = [
			'total_kols' => $this->Model_dashboard_metrics->get_total_kols(),
			'total_reach' => $this->Model_dashboard_metrics->get_total_reach(),
			'campaign_stats' => $this->Model_dashboard_metrics->get_campaign_stats(),
			'content_stats' => $this->Model_dashboard_metrics->get_content_stats(),
			'action_items' => $this->Model_dashboard_metrics->get_action_items(),
			'kols_by_tier' => $this->Model_dashboard_metrics->get_kols_by_tier(),
			'platform_distribution' => $this->Model_dashboard_metrics->get_platform_distribution(),
			'top_postings' => $this->Model_dashboard_metrics->get_top_postings(5),
			'avg_er' => $this->Model_dashboard_metrics->get_avg_er(),
			'recent_scrapes' => $this->Model_dashboard_metrics->get_recent_scrapes(),
			'stale_kols' => $this->Model_dashboard_metrics->get_stale_kols(),
		];
		
		$this->render('backend/standart/dashboard', $data);
	}

	public function chart()
	{
		if (!$this->aauth->is_allowed('dashboard')) {
			redirect('/','refresh');
		}

		$data = [];
		$this->render('backend/standart/chart', $data);
	}
}

/* End of file Dashboard.php */
/* Location: ./application/controllers/administrator/Dashboard.php */