<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Campaigns Controller
*| --------------------------------------------------------------------------
*| Campaigns site
*|
*/
class Campaigns extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_campaigns');
		$this->lang->load('web_lang', $this->current_lang);

		// Automatically update campaign statuses based on start_date and end_date
		$this->_auto_update_campaign_statuses();
	}

	/**
	* show all Campaignss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('campaigns_list');

		// Automatically update campaign statuses based on start_date and end_date
		$this->_auto_update_campaign_statuses();

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['campaignss'] = $this->model_campaigns->get($filter, $field, $this->limit_page, $offset);
		$this->data['campaigns_counts'] = $this->model_campaigns->count_all($filter, $field);

		// Load associated products, spent budget, and achieved metrics for each campaign
		$campaign_ids = !empty($this->data['campaignss']) ? array_map(function($c) { return $c->id; }, $this->data['campaignss']) : [];
		$products_by_campaign = [];
		$spent_by_campaign = [];
		$metrics_by_campaign = [];
		$applications_by_campaign = [];
		if (!empty($campaign_ids)) {
			$c_prods = $this->db->select('campaign_products.campaign_id, products.id as product_id, products.name as product_name, products.sku as product_sku, products.category as product_category, products.price as product_price')
				->from('campaign_products')
				->join('products', 'products.id = campaign_products.product_id', 'INNER')
				->where_in('campaign_products.campaign_id', $campaign_ids)
				->get()->result();
			foreach ($c_prods as $cp) {
				$products_by_campaign[$cp->campaign_id][] = $cp;
			}

			$c_deals = $this->db->select('campaign_id, SUM(deal_amount) as total_spent')
				->from('campaign_kols')
				->where_in('campaign_id', $campaign_ids)
				->where_in('status', ['ASSIGNED', 'ACTIVE', 'COMPLETED'])
				->group_by('campaign_id')
				->get()->result();
			foreach ($c_deals as $cd) {
				$spent_by_campaign[$cd->campaign_id] = (float)$cd->total_spent;
			}

			$c_metrics = $this->db->select('campaign_kols.campaign_id, SUM(posting_metrics.views) as total_actual_views, SUM(posting_metrics.likes + posting_metrics.comments + posting_metrics.shares + posting_metrics.saves) as total_actual_engagements, AVG(posting_metrics.engagement_rate) as avg_actual_er, COUNT(posting_metrics.id) as total_metrics_count')
				->from('campaign_kols')
				->join('tasks', 'tasks.campaign_kol_id = campaign_kols.id', 'INNER')
				->join('postings', 'postings.task_id = tasks.id', 'INNER')
				->join('posting_metrics', 'posting_metrics.id = postings.latest_metric_id', 'INNER')
				->where_in('campaign_kols.campaign_id', $campaign_ids)
				->group_by('campaign_kols.campaign_id')
				->get()->result();
			foreach ($c_metrics as $cm) {
				$metrics_by_campaign[$cm->campaign_id] = $cm;
			}

			$c_apps = $this->db->select('campaign_id, 
				COUNT(id) as total_applicants, 
				SUM(CASE WHEN status = "PENDING" THEN 1 ELSE 0 END) as pending_applicants,
				SUM(CASE WHEN status = "ACCEPTED" THEN 1 ELSE 0 END) as accepted_applicants')
				->from('campaign_applications')
				->where_in('campaign_id', $campaign_ids)
				->group_by('campaign_id')
				->get()->result();
			foreach ($c_apps as $ca) {
				$applications_by_campaign[$ca->campaign_id] = $ca;
			}
		}
		$this->data['products_by_campaign'] = $products_by_campaign;
		$this->data['spent_by_campaign'] = $spent_by_campaign;
		$this->data['metrics_by_campaign'] = $metrics_by_campaign;
		$this->data['applications_by_campaign'] = $applications_by_campaign;

		$config = [
			'base_url'     => 'administrator/campaigns/index/',
			'total_rows'   => $this->model_campaigns->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Campaigns List');
		$this->render('backend/standart/administrator/campaigns/campaigns_list', $this->data);
	}
	
	/**
	* Add new campaignss
	*
	*/
	public function add()
	{
		$this->is_allowed('campaigns_add');

		$this->data['products']   = $this->db->where('is_active', 1)->order_by('category ASC, name ASC')->get('products')->result();
		$this->data['tiers']      = $this->db->where('is_active', 1)->order_by('id ASC')->get('tiers')->result();
		$this->data['categories'] = $this->db->where('is_active', 1)->order_by('name ASC')->get('categories')->result();
		$this->data['platforms']  = $this->db->where('is_active', 1)->order_by('name ASC')->get('platforms')->result();

		$this->template->title('Campaigns New');
		$this->render('backend/standart/administrator/campaigns/campaigns_add', $this->data);
	}

	/**
	* Add New Campaignss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('campaigns_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('name', 'Name', 'trim|max_length[200]|required');
		$this->form_validation->set_rules('budget', 'Budget', 'trim|max_length[15]|required|numeric');
		$this->form_validation->set_rules('currency_code', 'Code Currency', 'trim|in_list[IDR,USD]|max_length[3]|required');
		$this->form_validation->set_rules('product_ids[]', 'Products (Produk)', 'trim|required');
		$this->form_validation->set_rules('campaigns_sow_name[]', 'Scope Of Work', 'trim|required');
		$this->form_validation->set_rules('start_date', 'Start Campaign', 'trim|required');
		$this->form_validation->set_rules('end_date', 'End Date', 'trim|required|callback_check_date_range');
		$this->form_validation->set_rules('description', 'Description', 'trim|required');
		$this->form_validation->set_rules('campaign_type', 'Type Campaign', 'trim|in_list[PAID,BARTER,AFFILIATE,SEEDING,EVENT]|required');
		$this->form_validation->set_rules('sourcing_model', 'Sourcing Model', 'trim|in_list[APPOINTED,OPEN_POST]|required');
		$this->form_validation->set_rules('brand_name', 'Brand Name', 'trim|max_length[150]');
		$this->form_validation->set_rules('client_name', 'Client Name', 'trim|max_length[150]');
		$this->form_validation->set_rules('brand_category', 'Brand Categories', 'trim|max_length[100]');
		$this->form_validation->set_rules('objective', 'Objective Target', 'trim');
		$this->form_validation->set_rules('hashtag', 'Hashtag', 'trim');
		$this->form_validation->set_rules('key_message', 'Key Message', 'trim');
		$this->form_validation->set_rules('target_views', 'Target Views', 'trim|required|numeric');
		$this->form_validation->set_rules('target_er', 'Target Engagement Rate', 'trim|required|numeric');
		$this->form_validation->set_rules('target_cpv', 'Target CPV', 'trim|required|numeric');
		$this->form_validation->set_rules('target_cpe', 'Target CPE', 'trim|required|numeric');
		

		if ($this->form_validation->run()) {
			$start_date = $this->input->post('start_date');
			$end_date   = $this->input->post('end_date');
			$today      = date('Y-m-d');

			// No master approval: initial status depends on start_date & sourcing model
			// Future start_date => DRAFT (scheduled). Otherwise OPEN_POST => OPEN_REGISTRATION, APPOINTED (internal) => ACTIVE.
			$sourcing_for_status = $this->input->post('sourcing_model') ? $this->input->post('sourcing_model') : 'APPOINTED';
			if (!empty($start_date) && $start_date > $today) {
				$initial_status = 'DRAFT';
			} elseif ($sourcing_for_status === 'OPEN_POST') {
				$initial_status = 'OPEN_REGISTRATION';
			} else {
				$initial_status = 'ACTIVE';
			}

			$save_data = [
				'name' => $this->input->post('name'),
				'budget' => $this->input->post('budget'),
				'currency_code' => $this->input->post('currency_code'),
				'start_date' => $this->input->post('start_date'),
				'end_date' => $this->input->post('end_date'),
				'status' => $initial_status,
				'description' => $this->input->post('description'),
				'campaign_type' => $this->input->post('campaign_type'),
				'sourcing_model' => $this->input->post('sourcing_model') ? $this->input->post('sourcing_model') : 'APPOINTED',
				'application_deadline' => !empty($this->input->post('application_deadline')) ? $this->input->post('application_deadline') : null,
				'max_slots' => (!empty($this->input->post('max_slots')) && is_numeric($this->input->post('max_slots'))) ? (int)$this->input->post('max_slots') : null,
				'min_followers' => (!empty($this->input->post('min_followers')) && is_numeric($this->input->post('min_followers'))) ? (int)$this->input->post('min_followers') : 0,
				'min_er' => (!empty($this->input->post('min_er')) && is_numeric($this->input->post('min_er'))) ? (float)$this->input->post('min_er') : 0.00,
				'brand_name' => $this->input->post('brand_name'),
				'client_name' => $this->input->post('client_name'),
				'brand_category' => $this->input->post('brand_category'),
				'target_audience' => $this->input->post('target_audience'),
				'objective' => $this->input->post('objective'),
				'hashtag' => $this->input->post('hashtag'),
				'key_message' => $this->input->post('key_message'),
				'target_views' => ($this->input->post('target_views') !== null && $this->input->post('target_views') !== '') ? (int)$this->input->post('target_views') : 0,
				'target_er' => ($this->input->post('target_er') !== null && $this->input->post('target_er') !== '') ? (float)$this->input->post('target_er') : 0.00,
				'target_cpv' => ($this->input->post('target_cpv') !== null && $this->input->post('target_cpv') !== '') ? (float)$this->input->post('target_cpv') : null,
				'target_cpe' => ($this->input->post('target_cpe') !== null && $this->input->post('target_cpe') !== '') ? (float)$this->input->post('target_cpe') : null,
				'terms_and_conditions' => $this->input->post('terms_and_conditions'),
				'pic_user_id' => !empty($this->input->post('pic_user_id')) ? $this->input->post('pic_user_id') : null,
				'created_by' => get_user_data('id') ? get_user_data('id') : 1,
			];

			if (!is_dir(FCPATH . '/uploads/campaigns/')) {
				mkdir(FCPATH . '/uploads/campaigns/', 0777, true);
			}

			$listed_image = [];
			if (count((array) $this->input->post('campaigns_sow_name'))) {
				foreach ((array) $_POST['campaigns_sow_name'] as $idx => $file_name) {
					if (empty($file_name)) continue;
					if (isset($_POST['campaigns_sow_uuid'][$idx]) AND !empty($_POST['campaigns_sow_uuid'][$idx])) {
						$src = FCPATH . 'uploads/tmp/' . $_POST['campaigns_sow_uuid'][$idx] . '/' .  $file_name;
						if (is_file($src)) {
							$campaigns_sow_name_copy = date('YmdHis') . '-' . $file_name;
							rename($src, FCPATH . 'uploads/campaigns/' . $campaigns_sow_name_copy);
							$listed_image[] = $campaigns_sow_name_copy;
						}
					} else {
						$listed_image[] = $file_name;
					}
				}
			}

			$save_data['sow'] = implode(',', $listed_image);
		
			
			$save_campaigns = $this->model_campaigns->store($save_data);
            

			if ($save_campaigns) {
				// Save selected campaign products
				$product_ids = $this->input->post('product_ids');
				if (is_array($product_ids)) {
					foreach ($product_ids as $pid) {
						$pid = (int)$pid;
						if ($pid > 0) {
							$this->db->insert('campaign_products', [
								'campaign_id' => $save_campaigns,
								'product_id'  => $pid,
								'created_at'  => date('Y-m-d H:i:s')
							]);
						}
					}
				}

				// Sync Open Post Criteria Junctions
				if ($save_data['sourcing_model'] === 'OPEN_POST') {
					$allowed_tiers = $this->input->post('allowed_tiers');
					if (is_array($allowed_tiers)) {
						foreach ($allowed_tiers as $tid) {
							$tid = (int)$tid;
							if ($tid > 0) {
								$this->db->insert('campaign_allowed_tiers', [
									'campaign_id' => $save_campaigns,
									'tier_id'     => $tid,
									'created_at'  => date('Y-m-d H:i:s')
								]);
							}
						}
					}

					$allowed_categories = $this->input->post('allowed_categories');
					if (is_array($allowed_categories)) {
						foreach ($allowed_categories as $cid) {
							$cid = (int)$cid;
							if ($cid > 0) {
								$this->db->insert('campaign_allowed_categories', [
									'campaign_id' => $save_campaigns,
									'category_id' => $cid,
									'created_at'  => date('Y-m-d H:i:s')
								]);
							}
						}
					}

					$required_platforms = $this->input->post('required_platforms');
					if (is_array($required_platforms)) {
						foreach ($required_platforms as $pid) {
							$pid = (int)$pid;
							if ($pid > 0) {
								$this->db->insert('campaign_required_platforms', [
									'campaign_id' => $save_campaigns,
									'platform_id' => $pid,
									'created_at'  => date('Y-m-d H:i:s')
								]);
							}
						}
					}
				}

				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_campaigns;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/campaigns/edit/' . $save_campaigns, 'Edit Campaigns'),
						anchor('administrator/campaigns', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/campaigns/edit/' . $save_campaigns, 'Edit Campaigns')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/campaigns');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/campaigns');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}


		/**
	* Update view Campaignss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('campaigns_update');

		$this->data['campaigns'] = $this->model_campaigns->find($id);
		$this->data['products'] = $this->db->where('is_active', 1)->order_by('category ASC, name ASC')->get('products')->result();
		$selected_products = $this->db->get_where('campaign_products', ['campaign_id' => $id])->result();
		$this->data['selected_product_ids'] = array_map(function($p) { return $p->product_id; }, $selected_products);

		$this->data['tiers'] = $this->db->where('is_active', 1)->order_by('id ASC')->get('tiers')->result();
		$selected_tiers = $this->db->get_where('campaign_allowed_tiers', ['campaign_id' => $id])->result();
		$this->data['selected_tier_ids'] = array_map(function($t) { return $t->tier_id; }, $selected_tiers);

		$this->data['categories'] = $this->db->where('is_active', 1)->order_by('name ASC')->get('categories')->result();
		$selected_categories = $this->db->get_where('campaign_allowed_categories', ['campaign_id' => $id])->result();
		$this->data['selected_category_ids'] = array_map(function($c) { return $c->category_id; }, $selected_categories);

		$this->data['platforms'] = $this->db->where('is_active', 1)->order_by('name ASC')->get('platforms')->result();
		$selected_platforms = $this->db->get_where('campaign_required_platforms', ['campaign_id' => $id])->result();
		$this->data['selected_platform_ids'] = array_map(function($p) { return $p->platform_id; }, $selected_platforms);

		$this->template->title('Campaigns Update');
		$this->render('backend/standart/administrator/campaigns/campaigns_update', $this->data);
	}

	/**
	* Update Campaignss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('campaigns_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('name', 'Name', 'trim|max_length[200]|required');
		$this->form_validation->set_rules('budget', 'Budget', 'trim|max_length[15]|required|numeric');
		$this->form_validation->set_rules('currency_code', 'Code Currency', 'trim|in_list[IDR,USD]|max_length[3]|required');
		$this->form_validation->set_rules('product_ids[]', 'Products (Produk)', 'trim|required');
		$this->form_validation->set_rules('campaigns_sow_name[]', 'Scope Of Work', 'trim|required');
		$this->form_validation->set_rules('start_date', 'Start Campaign', 'trim|required');
		$this->form_validation->set_rules('end_date', 'End Date', 'trim|required|callback_check_date_range');
		$this->form_validation->set_rules('status', 'Status', 'trim|in_list[DRAFT,OPEN_REGISTRATION,ACTIVE,COMPLETED,CANCELLED]|required');
		$this->form_validation->set_rules('description', 'Description', 'trim|required');
		$this->form_validation->set_rules('campaign_type', 'Type Campaign', 'trim|in_list[PAID,BARTER,AFFILIATE,SEEDING,EVENT]|required');
		$this->form_validation->set_rules('sourcing_model', 'Sourcing Model', 'trim|in_list[APPOINTED,OPEN_POST]|required');
		$this->form_validation->set_rules('brand_name', 'Brand Name', 'trim|max_length[150]');
		$this->form_validation->set_rules('client_name', 'Client Name', 'trim|max_length[150]');
		$this->form_validation->set_rules('brand_category', 'Brand Categories', 'trim|max_length[100]');
		$this->form_validation->set_rules('objective', 'Objective Target', 'trim');
		$this->form_validation->set_rules('hashtag', 'Hashtag', 'trim');
		$this->form_validation->set_rules('key_message', 'Key Message', 'trim');
		$this->form_validation->set_rules('target_views', 'Target Views', 'trim|required|numeric');
		$this->form_validation->set_rules('target_er', 'Target Engagement Rate', 'trim|required|numeric');
		$this->form_validation->set_rules('target_cpv', 'Target CPV', 'trim|required|numeric');
		$this->form_validation->set_rules('target_cpe', 'Target CPE', 'trim|required|numeric');
		
		if ($this->form_validation->run()) {
			$existing_campaign = $this->model_campaigns->find($id);
			if ($existing_campaign && in_array($existing_campaign->status, ['COMPLETED', 'CANCELLED'], true)) {
				$this->data['success'] = false;
				$this->data['message'] = 'Campaign sudah ' . $existing_campaign->status . ' dan tidak dapat diubah lagi.';
				echo json_encode($this->data);
				return;
			}
			if ($existing_campaign && !$this->_is_campaign_transition_allowed($existing_campaign->status, $this->input->post('status'))) {
				$this->data['success'] = false;
				$this->data['message'] = 'Transisi status ' . $existing_campaign->status . ' ke ' . $this->input->post('status') . ' tidak diizinkan.';
				echo json_encode($this->data);
				return;
			}

			$save_data = [
				'name' => $this->input->post('name'),
				'budget' => $this->input->post('budget'),
				'currency_code' => $this->input->post('currency_code'),
				'start_date' => $this->input->post('start_date'),
				'end_date' => $this->input->post('end_date'),
				'status' => $this->input->post('status'),
				'description' => $this->input->post('description'),
				'campaign_type' => $this->input->post('campaign_type'),
				'sourcing_model' => $this->input->post('sourcing_model') ? $this->input->post('sourcing_model') : 'APPOINTED',
				'application_deadline' => !empty($this->input->post('application_deadline')) ? $this->input->post('application_deadline') : null,
				'max_slots' => (!empty($this->input->post('max_slots')) && is_numeric($this->input->post('max_slots'))) ? (int)$this->input->post('max_slots') : null,
				'min_followers' => (!empty($this->input->post('min_followers')) && is_numeric($this->input->post('min_followers'))) ? (int)$this->input->post('min_followers') : 0,
				'min_er' => (!empty($this->input->post('min_er')) && is_numeric($this->input->post('min_er'))) ? (float)$this->input->post('min_er') : 0.00,
				'brand_name' => $this->input->post('brand_name'),
				'client_name' => $this->input->post('client_name'),
				'brand_category' => $this->input->post('brand_category'),
				'target_audience' => $this->input->post('target_audience'),
				'objective' => $this->input->post('objective'),
				'hashtag' => $this->input->post('hashtag'),
				'key_message' => $this->input->post('key_message'),
				'target_views' => ($this->input->post('target_views') !== null && $this->input->post('target_views') !== '') ? (int)$this->input->post('target_views') : 0,
				'target_er' => ($this->input->post('target_er') !== null && $this->input->post('target_er') !== '') ? (float)$this->input->post('target_er') : 0.00,
				'target_cpv' => ($this->input->post('target_cpv') !== null && $this->input->post('target_cpv') !== '') ? (float)$this->input->post('target_cpv') : null,
				'target_cpe' => ($this->input->post('target_cpe') !== null && $this->input->post('target_cpe') !== '') ? (float)$this->input->post('target_cpe') : null,
				'terms_and_conditions' => $this->input->post('terms_and_conditions'),
				'pic_user_id' => !empty($this->input->post('pic_user_id')) ? $this->input->post('pic_user_id') : null,
			];

			if (!is_dir(FCPATH . '/uploads/campaigns/')) {
				mkdir(FCPATH . '/uploads/campaigns/', 0777, true);
			}

			$listed_image = [];
			if (count((array) $this->input->post('campaigns_sow_name'))) {
				foreach ((array) $_POST['campaigns_sow_name'] as $idx => $file_name) {
					if (empty($file_name)) continue;
					if (isset($_POST['campaigns_sow_uuid'][$idx]) AND !empty($_POST['campaigns_sow_uuid'][$idx])) {
						$src = FCPATH . 'uploads/tmp/' . $_POST['campaigns_sow_uuid'][$idx] . '/' .  $file_name;
						if (is_file($src)) {
							$campaigns_sow_name_copy = date('YmdHis') . '-' . $file_name;
							rename($src, FCPATH . 'uploads/campaigns/' . $campaigns_sow_name_copy);
							$listed_image[] = $campaigns_sow_name_copy;
						}
					} else {
						$listed_image[] = $file_name;
					}
				}
			}
			
			if (!empty($listed_image)) {
				$save_data['sow'] = implode(',', $listed_image);
			}
		
			
			$save_campaigns = $this->model_campaigns->change($id, $save_data);

			if ($save_campaigns !== false) {
				// Sync campaign products
				$product_ids = $this->input->post('product_ids');
				$this->db->where('campaign_id', $id)->delete('campaign_products');
				if (is_array($product_ids)) {
					foreach ($product_ids as $pid) {
						$pid = (int)$pid;
						if ($pid > 0) {
							$this->db->insert('campaign_products', [
								'campaign_id' => $id,
								'product_id'  => $pid,
								'created_at'  => date('Y-m-d H:i:s')
							]);
						}
					}
				}

				// Sync Open Post Criteria Junctions
				$this->db->where('campaign_id', $id)->delete('campaign_allowed_tiers');
				$this->db->where('campaign_id', $id)->delete('campaign_allowed_categories');
				$this->db->where('campaign_id', $id)->delete('campaign_required_platforms');

				if ($save_data['sourcing_model'] === 'OPEN_POST') {
					$allowed_tiers = $this->input->post('allowed_tiers');
					if (is_array($allowed_tiers)) {
						foreach ($allowed_tiers as $tid) {
							$tid = (int)$tid;
							if ($tid > 0) {
								$this->db->insert('campaign_allowed_tiers', [
									'campaign_id' => $id,
									'tier_id'     => $tid,
									'created_at'  => date('Y-m-d H:i:s')
								]);
							}
						}
					}

					$allowed_categories = $this->input->post('allowed_categories');
					if (is_array($allowed_categories)) {
						foreach ($allowed_categories as $cid) {
							$cid = (int)$cid;
							if ($cid > 0) {
								$this->db->insert('campaign_allowed_categories', [
									'campaign_id' => $id,
									'category_id' => $cid,
									'created_at'  => date('Y-m-d H:i:s')
								]);
							}
						}
					}

					$required_platforms = $this->input->post('required_platforms');
					if (is_array($required_platforms)) {
						foreach ($required_platforms as $pid) {
							$pid = (int)$pid;
							if ($pid > 0) {
								$this->db->insert('campaign_required_platforms', [
									'campaign_id' => $id,
									'platform_id' => $pid,
									'created_at'  => date('Y-m-d H:i:s')
								]);
							}
						}
					}
				}

				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/campaigns', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/campaigns');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/campaigns');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Campaignss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('campaigns_delete');

		$this->load->helper('file');

		$arr_id = $this->input->get('id');
		if (!empty($id)) {
			$ids = [$id];
		} elseif (!empty($arr_id) && is_array($arr_id)) {
			$ids = $arr_id;
		} else {
			set_message(cclang('please_choose_bulk_action_first'), 'warning');
			redirect_back();
			return;
		}

		$deleted_count = 0;
		$error_campaigns = [];

		foreach ($ids as $camp_id) {
			$result = $this->_remove($camp_id);
			if ($result['status'] === true) {
				$deleted_count++;
			} else {
				$error_campaigns[] = !empty($result['name']) ? $result['name'] : "ID #{$camp_id}";
			}
		}

		if ($deleted_count > 0 && empty($error_campaigns)) {
			if (count($ids) == 1) {
				set_message(cclang('has_been_deleted', 'campaigns'), 'success');
			} else {
				set_message("$deleted_count campaigns have been successfully deleted.", 'success');
			}
		} elseif ($deleted_count > 0 && !empty($error_campaigns)) {
			set_message("$deleted_count campaign(s) successfully deleted. Failed to delete: " . implode(', ', $error_campaigns) . ".", 'warning');
		} else {
			set_message(cclang('error_delete', 'campaigns'), 'error');
		}

		redirect_back();
	}

		/**
	* View view Campaignss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('campaigns_view');

		$this->data['campaigns'] = $this->model_campaigns->join_avaiable()->filter_avaiable()->find($id);
		$this->data['campaign_products'] = $this->db->select('campaign_products.*, products.name as product_name, products.sku as product_sku, products.category as product_category, products.price as product_price, products.stock as product_stock')
			->from('campaign_products')
			->join('products', 'products.id = campaign_products.product_id', 'INNER')
			->where('campaign_products.campaign_id', $id)
			->get()->result();

		$spent_row = $this->db->select('SUM(deal_amount) as total_spent')
			->from('campaign_kols')
			->where('campaign_id', $id)
			->where_in('status', ['ASSIGNED', 'ACTIVE', 'COMPLETED'])
			->get()->row();
		$this->data['total_spent'] = $spent_row ? (float)$spent_row->total_spent : 0;
		$this->data['remaining_budget'] = $this->data['campaigns'] ? ((float)$this->data['campaigns']->budget - $this->data['total_spent']) : 0;

		// Open Post Criteria & Applicants Data
		$this->data['allowed_tiers'] = $this->db->select('t.id, t.name')
			->from('campaign_allowed_tiers cat')
			->join('tiers t', 't.id = cat.tier_id', 'inner')
			->where('cat.campaign_id', $id)
			->get()->result();

		$this->data['allowed_categories'] = $this->db->select('c.id, c.name')
			->from('campaign_allowed_categories cac')
			->join('categories c', 'c.id = cac.category_id', 'inner')
			->where('cac.campaign_id', $id)
			->get()->result();

		$this->data['required_platforms'] = $this->db->select('p.id, p.name, p.code')
			->from('campaign_required_platforms crp')
			->join('platforms p', 'p.id = crp.platform_id', 'inner')
			->where('crp.campaign_id', $id)
			->get()->result();

		$applicants = $this->db->select('ca.*, k.name as kol_name, k.email as kol_email, k.avatar_url, t.name as kol_tier_name, u.full_name as reviewer_name')
			->from('campaign_applications ca')
			->join('kols k', 'k.id = ca.kol_id', 'inner')
			->join('tiers t', 't.id = k.tier_id', 'left')
			->join('aauth_users u', 'u.id = ca.reviewed_by', 'left')
			->where('ca.campaign_id', $id)
			->order_by('ca.applied_at', 'DESC')
			->get()->result();

		$accepted_count = 0;
		foreach ($applicants as $app) {
			if ($app->status === 'ACCEPTED') {
				$accepted_count++;
			}
			$app->platforms = $this->db->select('kp.*, p.name as platform_name, p.code as platform_code')
				->from('kol_platforms kp')
				->join('platforms p', 'p.id = kp.platform_id', 'inner')
				->where('kp.kol_id', $app->kol_id)
				->where('kp.is_active', 1)
				->get()->result();
		}
		$this->data['applicants'] = $applicants;
		$this->data['accepted_applicants_count'] = $accepted_count;

		$this->template->title('Campaigns Detail');
		$this->render('backend/standart/administrator/campaigns/campaigns_view', $this->data);
	}

	/**
	 * Accept a campaign applicant and convert to campaign_kols dealing
	 */
	public function accept_applicant($application_id)
	{
		$this->is_allowed('campaigns_update');
		header('Content-Type: application/json');

		$app_id = (int)$application_id;
		$application = $this->db->get_where('campaign_applications', ['id' => $app_id])->row();

		if (!$application) {
			echo json_encode([
				'success' => false,
				'message' => 'Application not found.',
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		if ($application->status === 'ACCEPTED') {
			echo json_encode([
				'success' => false,
				'message' => 'Applicant has already been accepted.',
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$campaign = $this->db->get_where('campaigns', ['id' => $application->campaign_id])->row();
		if (!$campaign) {
			echo json_encode([
				'success' => false,
				'message' => 'Campaign not found.',
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		// Slot Quota Validation
		if (!empty($campaign->max_slots) && $campaign->max_slots > 0) {
			$current_accepted = $this->db->from('campaign_applications')
				->where('campaign_id', $campaign->id)
				->where('status', 'ACCEPTED')
				->count_all_results();

			$current_assigned = $this->db->from('campaign_kols')
				->where('campaign_id', $campaign->id)
				->where_in('status', ['NEGOTIATION', 'ASSIGNED', 'ACTIVE', 'COMPLETED'])
				->count_all_results();

			$effective_taken = max($current_accepted, $current_assigned);
			if ($effective_taken >= (int)$campaign->max_slots) {
				echo json_encode([
					'success' => false,
					'message' => 'Cannot accept applicant: Campaign quota is full (' . $effective_taken . '/' . $campaign->max_slots . ' slots).',
					'csrfHash' => $this->security->get_csrf_hash()
				]);
				return;
			}
		}

		$user_id = get_user_data('id') ?: 1;

		$this->db->trans_start();

		// 1. Assign to campaign_kols as NEGOTIATION (allows staff to configure VT tasks & sample shipments)
		$existing_ck = $this->db->get_where('campaign_kols', [
			'campaign_id' => $application->campaign_id,
			'kol_id'      => $application->kol_id
		])->row();

		if (!$existing_ck) {
			$this->db->insert('campaign_kols', [
				'campaign_id'      => $application->campaign_id,
				'kol_id'           => $application->kol_id,
				'deal_amount'      => 0.00,
				'currency_code'    => !empty($campaign->currency_code) ? $campaign->currency_code : 'IDR',
				'status'           => 'NEGOTIATION',
				'assigned_by'      => $user_id,
				'assigned_at'      => date('Y-m-d H:i:s'),
				'created_at'       => date('Y-m-d H:i:s')
			]);
		} else {
			$this->db->where('id', $existing_ck->id)->update('campaign_kols', [
				'status'      => 'NEGOTIATION',
				'deal_amount' => 0.00,
				'updated_at'  => date('Y-m-d H:i:s')
			]);
		}

		// 2. Mark application accepted
		$this->db->where('id', $app_id)->update('campaign_applications', [
			'status'      => 'ACCEPTED',
			'reviewed_by' => $user_id,
			'reviewed_at' => date('Y-m-d H:i:s')
		]);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode([
				'success' => false,
				'message' => 'Database error while accepting applicant.',
				'csrfHash' => $this->security->get_csrf_hash()
			]);
		} else {
			echo json_encode([
				'success' => true,
				'message' => 'Applicant accepted and added to campaign dealing (Status: NEGOTIATION). You can now configure VT and shipments.',
				'csrfHash' => $this->security->get_csrf_hash()
			]);
		}
	}

	/**
	 * Reject a campaign applicant
	 */
	public function reject_applicant($application_id)
	{
		$this->is_allowed('campaigns_update');
		header('Content-Type: application/json');

		$app_id = (int)$application_id;
		$application = $this->db->get_where('campaign_applications', ['id' => $app_id])->row();

		if (!$application) {
			echo json_encode([
				'success' => false,
				'message' => 'Application not found.',
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$reason = trim((string)$this->input->post('rejection_reason'));
		$user_id = get_user_data('id') ?: 1;

		$this->db->where('id', $app_id)->update('campaign_applications', [
			'status'           => 'REJECTED',
			'rejection_reason' => $reason,
			'reviewed_by'      => $user_id,
			'reviewed_at'      => date('Y-m-d H:i:s')
		]);

		echo json_encode([
			'success' => true,
			'message' => 'Applicant has been rejected.',
			'csrfHash' => $this->security->get_csrf_hash()
		]);
	}

	/**
	 * ACTION: Consolidated Financial & Performance Analytics for Campaign
	 *
	 * @param int $id
	 */
	public function analytics($id)
	{
		$this->is_allowed('campaigns_view');

		$campaign = $this->model_campaigns->join_avaiable()->filter_avaiable()->find($id);
		if (!$campaign) {
			set_message('Campaign tidak ditemukan.', 'error');
			redirect('administrator/campaigns');
			return;
		}

		$this->data['campaign'] = $campaign;

		// 1. Fetch all assigned KOLs for this campaign (spent only counts ASSIGNED+; NEGOTIATION deal is 0)
		$campaign_kols = $this->db->select('campaign_kols.*, kols.name as kol_name, kols.avatar_url as kol_avatar, kols.email as kol_email, kols.phone as kol_phone, kols.bank_name, kols.bank_account_number, kols.bank_account_name, tiers.name as tier_name')
			->join('kols', 'kols.id = campaign_kols.kol_id', 'left')
			->join('tiers', 'tiers.id = kols.tier_id', 'left')
			->where('campaign_kols.campaign_id', $id)
			->where_in('campaign_kols.status', ['ASSIGNED', 'ACTIVE', 'COMPLETED', 'NEGOTIATION'])
			->order_by('campaign_kols.deal_amount', 'DESC')
			->get('campaign_kols')
			->result();

		$total_budget = (float)$campaign->budget;
		$total_deals = 0;
		$total_invoiced = 0;
		$total_paid = 0;
		$total_views = 0;
		$total_likes = 0;
		$total_comments = 0;
		$total_shares = 0;
		$total_saves = 0;
		$total_engagements = 0;
		$er_samples = [];

		$kol_analytics = [];

		foreach ($campaign_kols as $ck) {
			$ck_id = $ck->id;
			$deal_amount = (float)$ck->deal_amount;
			if (in_array($ck->status, ['ASSIGNED', 'ACTIVE', 'COMPLETED'], true)) {
				$total_deals += $deal_amount;
			}

			// Invoices for this KOL
			$inv_row = $this->db->select('SUM(grand_total) as total_invoiced, COUNT(id) as inv_count')
				->where('campaign_kol_id', $ck_id)
				->where('status !=', 'CANCELLED')
				->get('invoices')
				->row();
			$invoiced_amount = $inv_row && $inv_row->total_invoiced ? (float)$inv_row->total_invoiced : 0;
			$inv_count = $inv_row ? (int)$inv_row->inv_count : 0;
			$total_invoiced += $invoiced_amount;

			// Payments for this KOL
			$pay_row = $this->db->select('SUM(payments.amount) as total_paid, COUNT(payments.id) as pay_count')
				->join('invoices', 'invoices.id = payments.invoice_id', 'inner')
				->where('invoices.campaign_kol_id', $ck_id)
				->where('invoices.status !=', 'CANCELLED')
				->get('payments')
				->row();
			$paid_amount = $pay_row && $pay_row->total_paid ? (float)$pay_row->total_paid : 0;
			$total_paid += $paid_amount;

			// Payment status
			$unpaid_balance = max(0, $invoiced_amount - $paid_amount);
			if ($invoiced_amount <= 0) {
				$payment_status = 'NO_INVOICE';
			} elseif ($paid_amount >= $invoiced_amount) {
				$payment_status = 'PAID';
			} elseif ($paid_amount > 0) {
				$payment_status = 'PARTIAL';
			} else {
				$payment_status = 'UNPAID';
			}

			// Tasks stats
			$tasks = $this->db->select('id, status, title')->where('campaign_kol_id', $ck_id)->get('tasks')->result();
			$task_total = count($tasks);
			$task_completed = 0;
			foreach ($tasks as $t) {
				if (in_array($t->status, ['POSTED', 'DONE'])) {
					$task_completed++;
				}
			}
			$task_progress_pct = $task_total > 0 ? round(($task_completed / $task_total) * 100, 1) : 0;

			// Metrics stats
			$metric_row = $this->db->select('SUM(posting_metrics.views) as views, SUM(posting_metrics.likes) as likes, SUM(posting_metrics.comments) as comments, SUM(posting_metrics.shares) as shares, SUM(posting_metrics.saves) as saves, AVG(posting_metrics.engagement_rate) as avg_er')
				->join('tasks', 'tasks.id = postings.task_id', 'inner')
				->join('posting_metrics', 'posting_metrics.id = postings.latest_metric_id', 'inner')
				->where('tasks.campaign_kol_id', $ck_id)
				->get('postings')
				->row();

			$kol_views = $metric_row && $metric_row->views ? (int)$metric_row->views : 0;
			$kol_likes = $metric_row && $metric_row->likes ? (int)$metric_row->likes : 0;
			$kol_comments = $metric_row && $metric_row->comments ? (int)$metric_row->comments : 0;
			$kol_shares = $metric_row && $metric_row->shares ? (int)$metric_row->shares : 0;
			$kol_saves = $metric_row && $metric_row->saves ? (int)$metric_row->saves : 0;
			$kol_engagements = $kol_likes + $kol_comments + $kol_shares + $kol_saves;
			$kol_avg_er = $metric_row && $metric_row->avg_er ? (float)$metric_row->avg_er : 0;

			$total_views += $kol_views;
			$total_likes += $kol_likes;
			$total_comments += $kol_comments;
			$total_shares += $kol_shares;
			$total_saves += $kol_saves;
			$total_engagements += $kol_engagements;
			if ($kol_avg_er > 0) {
				$er_samples[] = $kol_avg_er;
			}

			// Calculated CPV & CPE for this KOL
			$effective_spend = $paid_amount > 0 ? $paid_amount : $deal_amount;
			$kol_cpv = $kol_views > 0 ? round($effective_spend / $kol_views, 2) : 0;
			$kol_cpe = $kol_engagements > 0 ? round($effective_spend / $kol_engagements, 2) : 0;

			$kol_analytics[] = [
				'campaign_kol'      => $ck,
				'deal_amount'       => $deal_amount,
				'invoiced_amount'   => $invoiced_amount,
				'paid_amount'       => $paid_amount,
				'unpaid_balance'    => $unpaid_balance,
				'payment_status'    => $payment_status,
				'task_total'        => $task_total,
				'task_completed'    => $task_completed,
				'task_progress_pct' => $task_progress_pct,
				'views'             => $kol_views,
				'likes'             => $kol_likes,
				'comments'          => $kol_comments,
				'engagements'       => $kol_engagements,
				'avg_er'            => $kol_avg_er,
				'cpv'               => $kol_cpv,
				'cpe'               => $kol_cpe,
			];
		}

		$this->data['kol_analytics'] = $kol_analytics;

		// Summary figures
		$this->data['total_budget'] = $total_budget;
		$this->data['total_deals'] = $total_deals;
		$this->data['total_invoiced'] = $total_invoiced;
		$this->data['total_paid'] = $total_paid;
		$this->data['remaining_budget'] = max(0, $total_budget - $total_deals);
		$this->data['unpaid_balance'] = max(0, $total_invoiced - $total_paid);
		$this->data['budget_utilization_pct'] = $total_budget > 0 ? round(($total_deals / $total_budget) * 100, 1) : 0;
		$this->data['invoice_paid_pct'] = $total_invoiced > 0 ? round(($total_paid / $total_invoiced) * 100, 1) : 0;

		$this->data['total_views'] = $total_views;
		$this->data['total_likes'] = $total_likes;
		$this->data['total_comments'] = $total_comments;
		$this->data['total_engagements'] = $total_engagements;
		$this->data['avg_er'] = count($er_samples) > 0 ? (array_sum($er_samples) / count($er_samples)) : 0;

		$effective_campaign_spend = $total_paid > 0 ? $total_paid : $total_deals;
		$this->data['blended_cpv'] = $total_views > 0 ? round($effective_campaign_spend / $total_views, 2) : 0;
		$this->data['blended_cpe'] = $total_engagements > 0 ? round($effective_campaign_spend / $total_engagements, 2) : 0;

		// Target comparisons
		$target_views = (int)$campaign->target_views;
		$target_er = (float)$campaign->target_er;
		$target_cpv = ($campaign->target_cpv !== null && (float)$campaign->target_cpv > 0) ? (float)$campaign->target_cpv : ($target_views > 0 ? round($total_budget / $target_views, 2) : 0);
		$target_cpe = ($campaign->target_cpe !== null && (float)$campaign->target_cpe > 0) ? (float)$campaign->target_cpe : ($target_views > 0 && $target_er > 0 ? round($total_budget / ($target_views * $target_er), 2) : 0);

		$this->data['target_views'] = $target_views;
		$this->data['target_er'] = $target_er;
		$this->data['target_cpv'] = $target_cpv;
		$this->data['target_cpe'] = $target_cpe;

		$this->data['views_achievement_pct'] = $target_views > 0 ? round(($total_views / $target_views) * 100, 1) : 0;
		$this->data['er_achievement_pct'] = $target_er > 0 ? round(($this->data['avg_er'] / $target_er) * 100, 1) : 0;

		$this->template->title('Campaign Financial & Performance Analytics - ' . $campaign->name);
		$this->render('backend/standart/administrator/campaigns/campaigns_analytics', $this->data);
	}
	
	/**
	* delete Campaignss with full cascade cleanup
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$campaigns = $this->model_campaigns->find($id);
		if (!$campaigns) {
			return ['status' => false, 'name' => null, 'reason' => 'not_found'];
		}

		$camp_name = $campaigns->name;

		// Perform deletion in transaction
		$this->db->trans_start();

		// 1. Cascade delete all campaign_kols and their tasks/shipments/briefs/drafts/postings
		$campaign_kols = $this->db->get_where('campaign_kols', ['campaign_id' => $id])->result();
		foreach ($campaign_kols as $ck) {
			$this->_remove_campaign_kol($ck->id);
		}

		// 2. Clean up junction tables
		$this->db->delete('campaign_variants', ['campaign_id' => $id]);
		$this->db->delete('campaign_products', ['campaign_id' => $id]);

		// 3. Remove campaign record
		$this->model_campaigns->remove($id);

		$this->db->trans_complete();

		if ($this->db->trans_status() === TRUE) {
			if (!empty($campaigns->sow)) {
				foreach ((array) explode(',', $campaigns->sow) as $filename) {
					$path = FCPATH . '/uploads/campaigns/' . trim($filename);
					if (is_file($path)) {
						@unlink($path);
					}
				}
			}

			return ['status' => true, 'name' => $camp_name, 'reason' => null];
		} else {
			return ['status' => false, 'name' => $camp_name, 'reason' => 'db_error'];
		}
	}

	/**
	* Internal helper to cascade delete a campaign_kol and all its child tasks/shipments/briefs/drafts/postings
	*/
	private function _remove_campaign_kol($campaign_kol_id)
	{
		$ck = $this->db->get_where('campaign_kols', ['id' => $campaign_kol_id])->row();
		if (!$ck) {
			return false;
		}

		// 1. Find all tasks under this campaign_kol
		$tasks = $this->db->get_where('tasks', ['campaign_kol_id' => $campaign_kol_id])->result();
		foreach ($tasks as $t) {
			$task_id = $t->id;

			// Delete postings and metrics
			$postings = $this->db->get_where('postings', ['task_id' => $task_id])->result();
			foreach ($postings as $p) {
				$this->db->delete('apify_jobs', ['posting_id' => $p->id]);
				$this->db->delete('posting_metrics', ['posting_id' => $p->id]);
			}
			$this->db->delete('postings', ['task_id' => $task_id]);

			// Delete drafts and histories
			$drafts = $this->db->get_where('drafts', ['task_id' => $task_id])->result();
			foreach ($drafts as $d) {
				$this->db->delete('draft_review_histories', ['draft_id' => $d->id]);
			}
			$this->db->delete('drafts', ['task_id' => $task_id]);

			// Delete briefs and histories
			$briefs = $this->db->get_where('briefs', ['task_id' => $task_id])->result();
			foreach ($briefs as $b) {
				$this->db->delete('brief_approval_histories', ['brief_id' => $b->id]);
				$this->db->delete('brief_content_histories', ['brief_id' => $b->id]);
			}
			$this->db->delete('briefs', ['task_id' => $task_id]);

			// Delete task histories
			$this->db->delete('task_status_histories', ['task_id' => $task_id]);
			$this->db->delete('task_budget_histories', ['task_id' => $task_id]);
			$this->db->delete('task_rate_histories', ['task_id' => $task_id]);

			// Delete task
			$this->db->delete('tasks', ['id' => $task_id]);
		}

		// 2. Delete kol_shipments and shipment items
		$shipments = $this->db->get_where('kol_shipments', ['campaign_kol_id' => $campaign_kol_id])->result();
		foreach ($shipments as $s) {
			$this->db->delete('kol_shipment_items', ['shipment_id' => $s->id]);
		}
		$this->db->delete('kol_shipments', ['campaign_kol_id' => $campaign_kol_id]);

		// 3. Delete junction variants & score histories
		$this->db->delete('campaign_kol_variants', ['campaign_kol_id' => $campaign_kol_id]);
		$this->db->delete('score_histories', ['campaign_kol_id' => $campaign_kol_id]);

		// 4. Delete uploaded deal scope files
		if (!empty($ck->deal_scope)) {
			foreach ((array) explode(',', $ck->deal_scope) as $filename) {
				$path = FCPATH . 'uploads/campaign_kols/' . trim($filename);
				if (is_file($path)) {
					@unlink($path);
				}
			}
		}

		// 5. Delete campaign_kol record
		return $this->db->delete('campaign_kols', ['id' => $campaign_kol_id]);
	}
	
	
	/**
	* Upload Image Campaigns	* 
	* @return JSON
	*/
	public function upload_sow_file()
	{
		if (!$this->is_allowed('campaigns_add', false) && !$this->is_allowed('campaigns_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'campaigns',
		]);
	}

	/**
	* Delete Image Campaigns	* 
	* @return JSON
	*/
	public function delete_sow_file($uuid)
	{
		if (!$this->is_allowed('campaigns_delete', false) && !$this->is_allowed('campaigns_update', false) && !$this->is_allowed('campaigns_add', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'sow', 
            'upload_path_tmp'   => 'uploads/tmp/',
            'table_name'        => 'campaigns',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/campaigns/'
        ]);
	}

	/**
	* Get Image Campaigns	* 
	* @return JSON
	*/
	public function get_sow_file($id)
	{
		if (!$this->is_allowed('campaigns_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$campaigns = $this->model_campaigns->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'sow', 
            'table_name'        => 'campaigns',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/campaigns/',
            'delete_endpoint'   => 'administrator/campaigns/delete_sow_file'
        ]);
	}
	
	/**
	* Export all campaigns with aggregated metrics & targets to excel (.xlsx)
	*
	* @return Files Excel .xlsx
	*/
	public function export()
	{
		$this->is_allowed('campaigns_export');

		if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
			require_once FCPATH . 'vendor/autoload.php';
		}

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$campaigns = $this->model_campaigns->get($filter, $field, 0, 0);

		$campaign_ids = !empty($campaigns) ? array_map(function($c) { return $c->id; }, $campaigns) : [];
		$spent_by_campaign = [];
		$metrics_by_campaign = [];
		$kols_count_by_campaign = [];
		$tasks_count_by_campaign = [];

		if (!empty($campaign_ids)) {
			$c_deals = $this->db->select('campaign_id, SUM(CASE WHEN status IN ("ASSIGNED","ACTIVE","COMPLETED") THEN deal_amount ELSE 0 END) as total_spent, COUNT(id) as total_kols, SUM(jumlah_vt) as total_vts, SUM(task_completed_count) as total_completed_vts')
				->from('campaign_kols')
				->where_in('campaign_id', $campaign_ids)
				->group_by('campaign_id')
				->get()->result();
			foreach ($c_deals as $cd) {
				$spent_by_campaign[$cd->campaign_id] = (float)$cd->total_spent;
				$kols_count_by_campaign[$cd->campaign_id] = (int)$cd->total_kols;
				$tasks_count_by_campaign[$cd->campaign_id] = ['total_vts' => (int)$cd->total_vts, 'completed_vts' => (int)$cd->total_completed_vts];
			}

			$c_metrics = $this->db->select('campaign_kols.campaign_id, SUM(posting_metrics.views) as total_actual_views, SUM(posting_metrics.likes + posting_metrics.comments + posting_metrics.shares + posting_metrics.saves) as total_actual_engagements, AVG(posting_metrics.engagement_rate) as avg_actual_er, COUNT(posting_metrics.id) as total_metrics_count')
				->from('campaign_kols')
				->join('tasks', 'tasks.campaign_kol_id = campaign_kols.id', 'INNER')
				->join('postings', 'postings.task_id = tasks.id', 'INNER')
				->join('posting_metrics', 'posting_metrics.id = postings.latest_metric_id', 'INNER')
				->where_in('campaign_kols.campaign_id', $campaign_ids)
				->group_by('campaign_kols.campaign_id')
				->get()->result();
			foreach ($c_metrics as $cm) {
				$metrics_by_campaign[$cm->campaign_id] = $cm;
			}
		}

		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
		$spreadsheet->setActiveSheetIndex(0);
		$sheet = $spreadsheet->getActiveSheet();
		$sheet->setTitle('All Campaigns');

		$headers = [
			'A1' => 'No',
			'B1' => 'Campaign ID',
			'C1' => 'Nama Campaign',
			'D1' => 'Brand',
			'E1' => 'Client',
			'F1' => 'Type',
			'G1' => 'Status',
			'H1' => 'Budget (IDR)',
			'I1' => 'Spent (IDR)',
			'J1' => 'Sisa Budget (IDR)',
			'K1' => 'Start Date',
			'L1' => 'End Date',
			'M1' => 'Target Views',
			'N1' => 'Actual Views',
			'O1' => 'Views Achievement (%)',
			'P1' => 'Target ER (%)',
			'Q1' => 'Actual ER (%)',
			'R1' => 'Target CPV (IDR)',
			'S1' => 'Actual CPV (IDR)',
			'T1' => 'Target CPE (IDR)',
			'U1' => 'Actual CPE (IDR)',
			'V1' => 'Total KOLs',
			'W1' => 'Total VTs',
			'X1' => 'Completed VTs',
			'Y1' => 'PIC User'
		];

		foreach ($headers as $cell => $val) {
			$sheet->setCellValue($cell, $val);
		}

		// Style header
		$sheet->getStyle('A1:Y1')->applyFromArray([
			'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
			'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4B6E']],
			'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER]
		]);

		$row = 2;
		$no = 1;
		foreach ($campaigns as $c) {
			$budget = (float)$c->budget;
			$spent = isset($spent_by_campaign[$c->id]) ? $spent_by_campaign[$c->id] : 0;
			$remaining = $budget - $spent;

			$t_views = (int)$c->target_views;
			$act_views = isset($metrics_by_campaign[$c->id]) ? (int)$metrics_by_campaign[$c->id]->total_actual_views : 0;
			$views_pct = ($t_views > 0) ? round(($act_views / $t_views) * 100, 2) : 0;

			$t_er = (float)$c->target_er * 100;
			$act_er = isset($metrics_by_campaign[$c->id]) ? round((float)$metrics_by_campaign[$c->id]->avg_actual_er * 100, 2) : 0;

			$t_cpv = isset($c->target_cpv) && $c->target_cpv !== null ? (float)$c->target_cpv : ($t_views > 0 ? round($budget / $t_views, 2) : 0);
			$act_cpv = ($act_views > 0) ? round($spent / $act_views, 2) : 0;

			$act_engagements = isset($metrics_by_campaign[$c->id]) ? (int)$metrics_by_campaign[$c->id]->total_actual_engagements : 0;
			$t_cpe = isset($c->target_cpe) && $c->target_cpe !== null ? (float)$c->target_cpe : ($t_views > 0 && $t_er > 0 ? round($budget / ($t_views * ($t_er / 100)), 2) : 0);
			$act_cpe = ($act_engagements > 0) ? round($spent / $act_engagements, 2) : 0;

			$total_kols = isset($kols_count_by_campaign[$c->id]) ? $kols_count_by_campaign[$c->id] : 0;
			$total_vts = isset($tasks_count_by_campaign[$c->id]['total_vts']) ? $tasks_count_by_campaign[$c->id]['total_vts'] : 0;
			$comp_vts = isset($tasks_count_by_campaign[$c->id]['completed_vts']) ? $tasks_count_by_campaign[$c->id]['completed_vts'] : 0;

			$sheet->setCellValue('A' . $row, $no++);
			$sheet->setCellValue('B' . $row, $c->id);
			$sheet->setCellValue('C' . $row, $c->name);
			$sheet->setCellValue('D' . $row, $c->brand_name ?: '-');
			$sheet->setCellValue('E' . $row, $c->client_name ?: '-');
			$sheet->setCellValue('F' . $row, $c->campaign_type);
			$sheet->setCellValue('G' . $row, $c->status);
			$sheet->setCellValue('H' . $row, $budget);
			$sheet->setCellValue('I' . $row, $spent);
			$sheet->setCellValue('J' . $row, $remaining);
			$sheet->setCellValue('K' . $row, $c->start_date);
			$sheet->setCellValue('L' . $row, $c->end_date);
			$sheet->setCellValue('M' . $row, $t_views);
			$sheet->setCellValue('N' . $row, $act_views);
			$sheet->setCellValue('O' . $row, $views_pct);
			$sheet->setCellValue('P' . $row, $t_er);
			$sheet->setCellValue('Q' . $row, $act_er);
			$sheet->setCellValue('R' . $row, $t_cpv);
			$sheet->setCellValue('S' . $row, $act_cpv);
			$sheet->setCellValue('T' . $row, $t_cpe);
			$sheet->setCellValue('U' . $row, $act_cpe);
			$sheet->setCellValue('V' . $row, $total_kols);
			$sheet->setCellValue('W' . $row, $total_vts);
			$sheet->setCellValue('X' . $row, $comp_vts);
			$sheet->setCellValue('Y' . $row, $c->pic_user_name ?: '-');

			$row++;
		}

		foreach (range('A', 'Y') as $col) {
			$sheet->getColumnDimension($col)->setAutoSize(true);
		}

		$filename = 'all_campaigns_report_' . date('Y-m-d_His') . '.xlsx';
		if (ob_get_length()) ob_end_clean();
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$objWriter->save('php://output');
		exit;
	}

	/**
	 * Export Detailed Excel for a Single Campaign (Multi-Sheet .xlsx)
	 *
	 * @param int $id
	 */
	public function export_single($id = null)
	{
		$this->is_allowed('campaigns_export');

		if (!$id) {
			set_message('Campaign ID tidak valid.', 'error');
			redirect_back();
			return;
		}

		$campaign = $this->model_campaigns->join_avaiable()->find($id);
		if (!$campaign) {
			set_message('Campaign tidak ditemukan.', 'error');
			redirect_back();
			return;
		}

		if (!class_exists('\\PhpOffice\\PhpSpreadsheet\\Spreadsheet')) {
			require_once FCPATH . 'vendor/autoload.php';
		}

		// 1. Fetch Deals / Assigned KOLs
		$deals = $this->db->select('campaign_kols.*, kols.name as kol_name, tiers.name as tier_name, (SELECT GROUP_CONCAT(categories.name SEPARATOR ", ") FROM kol_categories JOIN categories ON categories.id = kol_categories.category_id WHERE kol_categories.kol_id = kols.id) as kol_categories')
			->join('kols', 'kols.id = campaign_kols.kol_id', 'left')
			->join('tiers', 'tiers.id = kols.tier_id', 'left')
			->where('campaign_kols.campaign_id', $id)
			->order_by('campaign_kols.id', 'ASC')
			->get('campaign_kols')
			->result();

		$spent_budget = 0;
		foreach ($deals as $d) {
			if (in_array($d->status, ['ASSIGNED', 'ACTIVE', 'COMPLETED'], true)) {
				$spent_budget += (float)$d->deal_amount;
			}
		}

		// 2. Fetch Tasks & Metrics
		$tasks = $this->db->select('tasks.*, kols.name as kol_name, platforms.name as platform_name, variants.name as variant_name, postings.live_url as post_live_url, posting_metrics.views, posting_metrics.likes, posting_metrics.comments, posting_metrics.shares, posting_metrics.saves, posting_metrics.engagement_rate, posting_metrics.cpv')
			->join('campaign_kols', 'campaign_kols.id = tasks.campaign_kol_id', 'inner')
			->join('kols', 'kols.id = campaign_kols.kol_id', 'left')
			->join('platforms', 'platforms.id = tasks.platform_id', 'left')
			->join('variants', 'variants.id = tasks.variant_id', 'left')
			->join('postings', 'postings.task_id = tasks.id', 'left')
			->join('posting_metrics', 'posting_metrics.id = postings.latest_metric_id', 'left')
			->where('campaign_kols.campaign_id', $id)
			->order_by('tasks.id', 'ASC')
			->get('tasks')
			->result();

		$total_actual_views = 0;
		$total_actual_engagements = 0;
		$er_sum = 0;
		$er_count = 0;

		foreach ($tasks as $t) {
			$total_actual_views += (int)($t->views ?? 0);
			$eng = (int)($t->likes ?? 0) + (int)($t->comments ?? 0) + (int)($t->shares ?? 0) + (int)($t->saves ?? 0);
			$total_actual_engagements += $eng;
			if ($t->engagement_rate !== null) {
				$er_sum += (float)$t->engagement_rate;
				$er_count++;
			}
		}
		$avg_actual_er = $er_count > 0 ? round(($er_sum / $er_count) * 100, 2) : 0;

		$spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();

		// ==========================================
		// SHEET 1: CAMPAIGN OVERVIEW & TARGETS
		// ==========================================
		$spreadsheet->setActiveSheetIndex(0);
		$sheet1 = $spreadsheet->getActiveSheet();
		$sheet1->setTitle('Campaign Overview');

		$sheet1->setCellValue('A1', 'LAPORAN DETAIL CAMPAIGN');
		$sheet1->mergeCells('A1:D1');
		$sheet1->getStyle('A1')->getFont()->setBold(true)->setSize(14);

		$info = [
			['Nama Campaign', $campaign->name],
			['Brand', $campaign->brand_name ?: '-'],
			['Client', $campaign->client_name ?: '-'],
			['Tipe Campaign', $campaign->campaign_type],
			['Status', $campaign->status],
			['Periode', $campaign->start_date . ' s/d ' . $campaign->end_date],
			['PIC', $campaign->pic_user_name ?: '-'],
			['', ''],
			['METRIK ANGGARAN', 'NILAI (IDR)'],
			['Total Budget', (float)$campaign->budget],
			['Total Spent (Realisasi Deal)', $spent_budget],
			['Sisa Anggaran', (float)$campaign->budget - $spent_budget],
			['', ''],
			['PERBANDINGAN TARGET & REALISASI', 'TARGET', 'AKTUAL / REALISASI', 'PENCAPAIAN (%)'],
			['Views', (int)$campaign->target_views, $total_actual_views, $campaign->target_views > 0 ? round(($total_actual_views / $campaign->target_views) * 100, 2) . '%' : '-'],
			['Engagement Rate (ER)', ((float)$campaign->target_er * 100) . '%', $avg_actual_er . '%', ($campaign->target_er > 0) ? round(($avg_actual_er / ((float)$campaign->target_er * 100)) * 100, 2) . '%' : '-'],
			['Cost Per View (CPV)', (float)($campaign->target_cpv ?: ($campaign->target_views > 0 ? round($campaign->budget / $campaign->target_views, 2) : 0)), $total_actual_views > 0 ? round($spent_budget / $total_actual_views, 2) : 0, '-'],
			['Cost Per Engagement (CPE)', (float)($campaign->target_cpe ?: ($campaign->target_views > 0 && $campaign->target_er > 0 ? round($campaign->budget / ($campaign->target_views * (float)$campaign->target_er), 2) : 0)), $total_actual_engagements > 0 ? round($spent_budget / $total_actual_engagements, 2) : 0, '-'],
		];

		$r = 3;
		foreach ($info as $item) {
			$sheet1->setCellValue('A' . $r, $item[0]);
			if (isset($item[1])) $sheet1->setCellValue('B' . $r, $item[1]);
			if (isset($item[2])) $sheet1->setCellValue('C' . $r, $item[2]);
			if (isset($item[3])) $sheet1->setCellValue('D' . $r, $item[3]);
			if (empty($item[0])) {
				// empty spacer
			} elseif (strpos($item[0], 'LAPORAN') !== false || strpos($item[0], 'METRIK') !== false || strpos($item[0], 'PERBANDINGAN') !== false) {
				$sheet1->getStyle('A' . $r . ':D' . $r)->getFont()->setBold(true);
				$sheet1->getStyle('A' . $r . ':D' . $r)->getFill()->applyFromArray([
					'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
					'startColor' => ['rgb' => 'E8F1FB']
				]);
			}
			$r++;
		}
		$sheet1->getColumnDimension('A')->setWidth(30);
		$sheet1->getColumnDimension('B')->setWidth(25);
		$sheet1->getColumnDimension('C')->setWidth(25);
		$sheet1->getColumnDimension('D')->setWidth(20);

		// ==========================================
		// SHEET 2: ASSIGNED KOLS
		// ==========================================
		$sheet2 = $spreadsheet->createSheet(1);
		$sheet2->setTitle('Assigned KOLs');

		$kol_headers = ['A1' => 'No', 'B1' => 'Dealing ID', 'C1' => 'Nama KOL', 'D1' => 'Tier', 'E1' => 'Kategori', 'F1' => 'Status Dealing', 'G1' => 'Deal Amount (IDR)', 'H1' => 'Jumlah VT', 'I1' => 'Completed VT', 'J1' => 'Deal Date', 'K1' => 'Score'];
		foreach ($kol_headers as $cell => $val) {
			$sheet2->setCellValue($cell, $val);
		}
		$sheet2->getStyle('A1:K1')->applyFromArray([
			'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
			'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4B6E']],
			'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
		]);

		$kr = 2;
		$kno = 1;
		foreach ($deals as $d) {
			$sheet2->setCellValue('A' . $kr, $kno++);
			$sheet2->setCellValue('B' . $kr, $d->id);
			$sheet2->setCellValue('C' . $kr, $d->kol_name ?: '-');
			$sheet2->setCellValue('D' . $kr, $d->tier_name ?: '-');
			$sheet2->setCellValue('E' . $kr, $d->kol_categories ?: '-');
			$sheet2->setCellValue('F' . $kr, $d->status);
			$sheet2->setCellValue('G' . $kr, (float)$d->deal_amount);
			$sheet2->setCellValue('H' . $kr, (int)$d->jumlah_vt);
			$sheet2->setCellValue('I' . $kr, (int)$d->task_completed_count);
			$sheet2->setCellValue('J' . $kr, $d->deal_date ?: '-');
			$sheet2->setCellValue('K' . $kr, $d->collaboration_score !== null ? (float)$d->collaboration_score : '-');
			$kr++;
		}
		foreach (range('A', 'K') as $col) {
			$sheet2->getColumnDimension($col)->setAutoSize(true);
		}

		// ==========================================
		// SHEET 3: TASK & VIDEO PERFORMANCE
		// ==========================================
		$sheet3 = $spreadsheet->createSheet(2);
		$sheet3->setTitle('Video Performance');

		$task_headers = [
			'A1' => 'No', 'B1' => 'Task ID', 'C1' => 'Task #', 'D1' => 'Task Title', 'E1' => 'KOL Name', 'F1' => 'Platform', 'G1' => 'Variant / Konsep', 'H1' => 'Content Type', 'I1' => 'Est Date', 'J1' => 'Status', 'K1' => 'Live URL',
			'L1' => 'Budget (IDR)', 'M1' => 'Views', 'N1' => 'Likes', 'O1' => 'Comments', 'P1' => 'Shares', 'Q1' => 'Saves', 'R1' => 'ER (%)', 'S1' => 'CPV (IDR)', 'T1' => 'CPE (IDR)'
		];
		foreach ($task_headers as $cell => $val) {
			$sheet3->setCellValue($cell, $val);
		}
		$sheet3->getStyle('A1:T1')->applyFromArray([
			'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
			'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4B6E']],
			'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER]
		]);

		$tr = 2;
		$tno = 1;
		foreach ($tasks as $t) {
			$tb = (float)preg_replace('/[^0-9]/', '', (string)$t->budget);
			$tv = (int)($t->views ?? 0);
			$tl = (int)($t->likes ?? 0);
			$tc = (int)($t->comments ?? 0);
			$tsh = (int)($t->shares ?? 0);
			$tsa = (int)($t->saves ?? 0);
			$teng = $tl + $tc + $tsh + $tsa;
			$ter = ($t->engagement_rate !== null) ? round((float)$t->engagement_rate * 100, 2) : ($tv > 0 ? round(($teng / $tv) * 100, 2) : 0);
			$tcpv = ($tv > 0 && $tb > 0) ? round($tb / $tv, 2) : 0;
			$tcpe = ($teng > 0 && $tb > 0) ? round($tb / $teng, 2) : 0;

			$sheet3->setCellValue('A' . $tr, $tno++);
			$sheet3->setCellValue('B' . $tr, $t->id);
			$sheet3->setCellValue('C' . $tr, $t->task_number);
			$sheet3->setCellValue('D' . $tr, $t->title);
			$sheet3->setCellValue('E' . $tr, $t->kol_name ?: '-');
			$sheet3->setCellValue('F' . $tr, $t->platform_name ?: '-');
			$sheet3->setCellValue('G' . $tr, $t->variant_name ?: '-');
			$sheet3->setCellValue('H' . $tr, $t->content_type ?: '-');
			$sheet3->setCellValue('I' . $tr, $t->estimated_posting_date);
			$sheet3->setCellValue('J' . $tr, $t->status);
			$sheet3->setCellValue('K' . $tr, $t->post_live_url ?: ($t->live_url ?: '-'));
			$sheet3->setCellValue('L' . $tr, $tb);
			$sheet3->setCellValue('M' . $tr, $tv);
			$sheet3->setCellValue('N' . $tr, $tl);
			$sheet3->setCellValue('O' . $tr, $tc);
			$sheet3->setCellValue('P' . $tr, $tsh);
			$sheet3->setCellValue('Q' . $tr, $tsa);
			$sheet3->setCellValue('R' . $tr, $ter);
			$sheet3->setCellValue('S' . $tr, $tcpv);
			$sheet3->setCellValue('T' . $tr, $tcpe);
			$tr++;
		}
		foreach (range('A', 'T') as $col) {
			$sheet3->getColumnDimension($col)->setAutoSize(true);
		}

		$spreadsheet->setActiveSheetIndex(0);

		$clean_name = preg_replace('/[^A-Za-z0-9_-]/', '_', $campaign->name);
		$filename = 'campaign_' . $campaign->id . '_' . $clean_name . '_' . date('Ymd_His') . '.xlsx';
		if (ob_get_length()) ob_end_clean();
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
		header('Content-Disposition: attachment;filename="' . $filename . '"');
		header('Cache-Control: max-age=0');
		$objWriter = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
		$objWriter->save('php://output');
		exit;
	}

	/**
	* Export to PDF (All Campaigns with custom 14 columns)
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('campaigns_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$campaigns = $this->model_campaigns->get($filter, $field, 0, 0);

		$campaign_ids = !empty($campaigns) ? array_map(function($c) { return $c->id; }, $campaigns) : [];
		$spent_by_campaign = [];
		$metrics_by_campaign = [];
		$products_by_campaign = [];

		if (!empty($campaign_ids)) {
			// Deals spent (only ASSIGNED+ counts; NEGOTIATION is 0)
			$c_deals = $this->db->select('campaign_id, SUM(CASE WHEN status IN ("ASSIGNED","ACTIVE","COMPLETED") THEN deal_amount ELSE 0 END) as total_spent')
				->from('campaign_kols')
				->where_in('campaign_id', $campaign_ids)
				->group_by('campaign_id')
				->get()->result();
			foreach ($c_deals as $cd) {
				$spent_by_campaign[$cd->campaign_id] = (float)$cd->total_spent;
			}

			// Posting metrics
			$c_metrics = $this->db->select('campaign_kols.campaign_id, SUM(posting_metrics.views) as total_actual_views, SUM(posting_metrics.likes + posting_metrics.comments + posting_metrics.shares + posting_metrics.saves) as total_actual_engagements, AVG(posting_metrics.engagement_rate) as avg_actual_er')
				->from('campaign_kols')
				->join('tasks', 'tasks.campaign_kol_id = campaign_kols.id', 'INNER')
				->join('postings', 'postings.task_id = tasks.id', 'INNER')
				->join('posting_metrics', 'posting_metrics.id = postings.latest_metric_id', 'INNER')
				->where_in('campaign_kols.campaign_id', $campaign_ids)
				->group_by('campaign_kols.campaign_id')
				->get()->result();
			foreach ($c_metrics as $cm) {
				$metrics_by_campaign[$cm->campaign_id] = $cm;
			}

			// Products
			$c_prods = $this->db->select('campaign_products.campaign_id, products.name as product_name')
				->from('campaign_products')
				->join('products', 'products.id = campaign_products.product_id', 'INNER')
				->where_in('campaign_products.campaign_id', $campaign_ids)
				->get()->result();
			foreach ($c_prods as $cp) {
				$products_by_campaign[$cp->campaign_id][] = $cp->product_name;
			}
		}

		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'l',
            'format' => 'a4',
            'marges' => array(6, 6, 6, 6)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('Arial'); 

        $content = $this->pdf->loadHtmlPdf('backend/standart/administrator/campaigns/campaigns_pdf_all', [
            'campaigns' => $campaigns,
            'spent_by_campaign' => $spent_by_campaign,
            'metrics_by_campaign' => $metrics_by_campaign,
            'products_by_campaign' => $products_by_campaign
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);

        $filename = 'all_campaigns_report_' . date('Y-m-d_His') . '.pdf';

        if (ob_get_length()) {
            ob_end_clean();
        }
        $this->pdf->Output($filename, 'I');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('campaigns_list');

		if (!$id) {
			set_message('Campaign ID tidak valid.', 'error');
			redirect_back();
			return;
		}

		$campaign = $this->model_campaigns->join_avaiable()->find($id);
		if (!$campaign) {
			set_message('Campaign tidak ditemukan.', 'error');
			redirect_back();
			return;
		}

		// 1. Fetch Products
		$products = $this->db->select('campaign_products.campaign_id, products.id as product_id, products.name as product_name, products.sku as product_sku, products.category as product_category, products.price as product_price')
			->from('campaign_products')
			->join('products', 'products.id = campaign_products.product_id', 'INNER')
			->where('campaign_products.campaign_id', $id)
			->get()->result();

		// 2. Fetch Deals / Assigned KOLs
		$deals = $this->db->select('campaign_kols.*, kols.name as kol_name, tiers.name as tier_name, (SELECT GROUP_CONCAT(categories.name SEPARATOR ", ") FROM kol_categories JOIN categories ON categories.id = kol_categories.category_id WHERE kol_categories.kol_id = kols.id) as kol_categories')
			->join('kols', 'kols.id = campaign_kols.kol_id', 'left')
			->join('tiers', 'tiers.id = kols.tier_id', 'left')
			->where('campaign_kols.campaign_id', $id)
			->order_by('campaign_kols.id', 'ASC')
			->get('campaign_kols')
			->result();

		$total_spent = 0;
		$total_vts = 0;
		$completed_vts = 0;
		foreach ($deals as $d) {
			if (in_array($d->status, ['ASSIGNED', 'ACTIVE', 'COMPLETED'], true)) {
				$total_spent += (float)$d->deal_amount;
			}
			$total_vts += (int)$d->jumlah_vt;
			$completed_vts += (int)$d->task_completed_count;
		}
		$remaining_budget = (float)$campaign->budget - $total_spent;

		// 3. Fetch Tasks & Metrics
		$tasks = $this->db->select('tasks.*, kols.name as kol_name, platforms.name as platform_name, variants.name as variant_name, postings.live_url as post_live_url, posting_metrics.views, posting_metrics.likes, posting_metrics.comments, posting_metrics.shares, posting_metrics.saves, posting_metrics.engagement_rate, posting_metrics.cpv')
			->join('campaign_kols', 'campaign_kols.id = tasks.campaign_kol_id', 'inner')
			->join('kols', 'kols.id = campaign_kols.kol_id', 'left')
			->join('platforms', 'platforms.id = tasks.platform_id', 'left')
			->join('variants', 'variants.id = tasks.variant_id', 'left')
			->join('postings', 'postings.task_id = tasks.id', 'left')
			->join('posting_metrics', 'posting_metrics.id = postings.latest_metric_id', 'left')
			->where('campaign_kols.campaign_id', $id)
			->order_by('tasks.id', 'ASC')
			->get('tasks')
			->result();

		$total_actual_views = 0;
		$total_actual_engagements = 0;
		$er_sum = 0;
		$er_count = 0;

		foreach ($tasks as $t) {
			$total_actual_views += (int)($t->views ?? 0);
			$eng = (int)($t->likes ?? 0) + (int)($t->comments ?? 0) + (int)($t->shares ?? 0) + (int)($t->saves ?? 0);
			$total_actual_engagements += $eng;
			if ($t->engagement_rate !== null) {
				$er_sum += (float)$t->engagement_rate;
				$er_count++;
			}
		}
		$avg_actual_er = $er_count > 0 ? round(($er_sum / $er_count) * 100, 2) : 0;

		$target_views = (int)$campaign->target_views;
		$target_er = (float)$campaign->target_er;
		$target_cpv = isset($campaign->target_cpv) && $campaign->target_cpv !== null ? (float)$campaign->target_cpv : ($target_views > 0 ? round((float)$campaign->budget / $target_views, 2) : 0);
		$actual_cpv = ($total_actual_views > 0) ? round($total_spent / $total_actual_views, 2) : 0;
		$target_cpe = isset($campaign->target_cpe) && $campaign->target_cpe !== null ? (float)$campaign->target_cpe : ($target_views > 0 && $target_er > 0 ? round((float)$campaign->budget / ($target_views * $target_er), 2) : 0);
		$actual_cpe = ($total_actual_engagements > 0) ? round($total_spent / $total_actual_engagements, 2) : 0;

		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'l',
            'format' => 'a4',
            'marges' => array(7, 7, 7, 7)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('Arial'); 

        $content = $this->pdf->loadHtmlPdf('backend/standart/administrator/campaigns/campaigns_pdf_single', [
            'campaign' => $campaign,
            'products' => $products,
            'deals' => $deals,
            'tasks' => $tasks,
            'total_spent' => $total_spent,
            'remaining_budget' => $remaining_budget,
            'total_vts' => $total_vts,
            'completed_vts' => $completed_vts,
            'total_actual_views' => $total_actual_views,
            'total_actual_engagements' => $total_actual_engagements,
            'avg_actual_er' => $avg_actual_er,
            'target_cpv' => $target_cpv,
            'actual_cpv' => $actual_cpv,
            'target_cpe' => $target_cpe,
            'actual_cpe' => $actual_cpe
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);

        $clean_name = preg_replace('/[^A-Za-z0-9_-]/', '_', $campaign->name);
        $filename = 'campaign_' . $campaign->id . '_' . $clean_name . '_breakdown.pdf';

        if (ob_get_length()) {
            ob_end_clean();
        }
        $this->pdf->Output($filename, 'I');
	}

	public function check_date_range($end_date)
	{
		$start_date = $this->input->post('start_date');
		if (!empty($start_date) && !empty($end_date)) {
			if (strtotime($end_date) < strtotime($start_date)) {
				$this->form_validation->set_message('check_date_range', 'End Date (Done Date) cannot be earlier than Start Campaign Date.');
				return FALSE;
			}
		}
		return TRUE;
	}

	/**
	 * Automatically update campaign statuses based on dates:
	 * - DRAFT (scheduled, no approval needed) -> OPEN_REGISTRATION / ACTIVE when start_date <= today
	 * - OPEN_REGISTRATION -> ACTIVE when start_date <= today (and end_date >= today)
	 * - ACTIVE/OPEN_REGISTRATION -> COMPLETED when end_date < today
	 * - COMPLETED / CANCELLED are terminal and never auto-changed.
	 */
	protected function _auto_update_campaign_statuses()
	{
		$today = date('Y-m-d');
		$now = date('Y-m-d H:i:s');

		// 0. DRAFT scheduled -> live when start_date has arrived (sourcing-aware, no approval needed)
		$this->db->where('status', 'DRAFT')
			->where('start_date <=', $today)
			->where('start_date IS NOT NULL', null, false)
			->where('end_date >=', $today)
			->where('sourcing_model', 'OPEN_POST')
			->update('campaigns', [
				'status'     => 'OPEN_REGISTRATION',
				'updated_at' => $now
			]);
		$this->db->where('status', 'DRAFT')
			->where('start_date <=', $today)
			->where('start_date IS NOT NULL', null, false)
			->where('end_date >=', $today)
			->where('sourcing_model !=', 'OPEN_POST')
			->update('campaigns', [
				'status'     => 'ACTIVE',
				'updated_at' => $now
			]);

		// 1. OPEN_REGISTRATION -> ACTIVE if start_date has arrived
		$this->db->where('status', 'OPEN_REGISTRATION')
			->where('start_date <=', $today)
			->where('start_date IS NOT NULL', null, false)
			->where('end_date >=', $today)
			->update('campaigns', [
				'status'     => 'ACTIVE',
				'updated_at' => $now
			]);

		// 2. ACTIVE / OPEN_REGISTRATION -> COMPLETED if end_date has passed
		$this->db->where_in('status', ['ACTIVE', 'OPEN_REGISTRATION'])
			->where('end_date <', $today)
			->where('end_date IS NOT NULL', null, false)
			->update('campaigns', [
				'status'     => 'COMPLETED',
				'updated_at' => $now
			]);
	}

	/**
	 * Allowed manual status transitions. COMPLETED / CANCELLED are terminal.
	 */
	protected function _is_campaign_transition_allowed($from, $to)
	{
		$from = strtoupper((string)$from);
		$to = strtoupper((string)$to);
		if ($from === $to) {
			return true;
		}
		if (in_array($from, ['COMPLETED', 'CANCELLED'], true)) {
			return false;
		}
		$allowed = [
			'DRAFT'             => ['OPEN_REGISTRATION', 'ACTIVE', 'CANCELLED'],
			'OPEN_REGISTRATION' => ['ACTIVE', 'CANCELLED', 'COMPLETED'],
			'ACTIVE'            => ['COMPLETED', 'CANCELLED'],
		];
		return isset($allowed[$from]) && in_array($to, $allowed[$from], true);
	}

	/**
	 * Backwards-compatibility alias
	 */
	protected function _auto_activate_draft_campaigns()
	{
		$this->_auto_update_campaign_statuses();
	}

	/**
	 * Update Campaign Status via AJAX (from list / view dropdown)
	 * Restricted to Superadmin & Leader KOL (Masterkol)
	 *
	 * @param int $id
	 */
	public function update_status($id = null)
	{
		header('Content-Type: application/json');
		
		$is_admin = $this->aauth->is_admin() || $this->aauth->is_member('Admin');
		$is_leader = $this->aauth->is_member('Leader KOL');
		$has_permission = $this->is_allowed('campaigns_update', false);

		if (!$is_admin && !$is_leader && !$has_permission) {
			echo json_encode([
				'success'  => false,
				'message'  => 'Hanya Superadmin dan Leader KOL (Masterkol) yang diizinkan mengubah status campaign.',
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$campaign_id = (int)($id ?: $this->input->post('id'));
		$status = strtoupper(trim($this->input->post('status')));
		$valid_statuses = ['DRAFT', 'OPEN_REGISTRATION', 'ACTIVE', 'COMPLETED', 'CANCELLED'];

		if (!$campaign_id || !in_array($status, $valid_statuses)) {
			echo json_encode([
				'success'  => false,
				'message'  => 'Parameter status atau ID campaign tidak valid.',
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$campaign = $this->db->get_where('campaigns', ['id' => $campaign_id])->row();
		if (!$campaign) {
			echo json_encode([
				'success'  => false,
				'message'  => 'Campaign tidak ditemukan.',
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		if (in_array($campaign->status, ['COMPLETED', 'CANCELLED'], true)) {
			echo json_encode([
				'success'  => false,
				'message'  => 'Campaign sudah ' . $campaign->status . ' dan tidak dapat diubah lagi.',
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		if (!$this->_is_campaign_transition_allowed($campaign->status, $status)) {
			echo json_encode([
				'success'  => false,
				'message'  => 'Transisi status ' . $campaign->status . ' ke ' . $status . ' tidak diizinkan.',
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$this->db->where('id', $campaign_id)->update('campaigns', [
			'status'     => $status,
			'updated_at' => date('Y-m-d H:i:s')
		]);

		echo json_encode([
			'success'  => true,
			'message'  => "Status campaign \"{$campaign->name}\" berhasil diubah menjadi {$status}.",
			'status'   => $status,
			'csrfHash' => $this->security->get_csrf_hash()
		]);
	}
}


/* End of file campaigns.php */
/* Location: ./application/controllers/administrator/Campaigns.php */