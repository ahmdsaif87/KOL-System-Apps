<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Campaign Kols Controller
*| --------------------------------------------------------------------------
*| Campaign Kols site
*|
*/
class Campaign_kols extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_campaign_kols');
		$this->load->model('kol_shipments/model_kol_shipments');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Campaign Kolss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('campaign_kols_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['campaign_kolss'] = $this->model_campaign_kols->get($filter, $field, $this->limit_page, $offset);
		$this->data['campaign_kols_counts'] = $this->model_campaign_kols->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/campaign_kols/index/',
			'total_rows'   => $this->model_campaign_kols->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['platforms'] = $this->db->where('is_active', 1)->get('platforms')->result();
		$this->data['variants'] = $this->db->where('is_active', 1)->get('variants')->result();
		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Campaign Kols List');
		$this->render('backend/standart/administrator/campaign_kols/campaign_kols_list', $this->data);
	}
	
	/**
	* Add new campaign_kolss
	*
	*/
	public function add()
	{
		$this->is_allowed('campaign_kols_add');

		$this->data['platforms'] = $this->db->where('is_active', 1)->get('platforms')->result();
		$this->data['variants'] = $this->db->where('is_active', 1)->get('variants')->result();

		$this->template->title('Dealing KOL New');
		$this->render('backend/standart/administrator/campaign_kols/campaign_kols_add', $this->data);
	}

	/**
	 * Get assigned active KOL IDs for a given campaign
	 * Used for dynamic multi-selection updates
	 */
	public function get_campaign_assigned_kols($campaign_id)
	{
		$this->is_allowed('campaign_kols_add');
		$assigned = $this->db->select('kol_id, status')
			->from('campaign_kols')
			->where('campaign_id', (int)$campaign_id)
			->where_not_in('status', ['REJECTED', 'CANCELLED'])
			->get()->result();

		$campaign = $this->db->select('id, name, sourcing_model, max_slots')
			->get_where('campaigns', ['id' => (int)$campaign_id])
			->row();

		echo json_encode([
			'success' => true,
			'campaign' => $campaign,
			'assigned_kol_ids' => !empty($assigned) ? array_values(array_map('intval', array_column($assigned, 'kol_id'))) : []
		]);
	}

	/**
	 * Quick Add KOL with Name only (for instant assignment)
	 *
	 * @return JSON
	 */
	public function quick_add_kol()
	{
		if (!$this->is_allowed('campaign_kols_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$name = trim($this->input->post('name', true));
		if (empty($name)) {
			echo json_encode([
				'success' => false,
				'message' => 'Nama KOL tidak boleh kosong.'
			]);
			return;
		}

		// Check if KOL with this name already exists
		$existing = $this->db->select('k.*, t.name as tier_name')
			->from('kols k')
			->join('tiers t', 't.id = k.tier_id', 'left')
			->where('LOWER(TRIM(k.name))', strtolower($name))
			->get()->row();

		if ($existing) {
			// If exists but not active, activate it so it can be assigned
			if ($existing->status !== 'ACTIVE') {
				$this->db->where('id', $existing->id)->update('kols', [
					'status' => 'ACTIVE',
					'updated_at' => date('Y-m-d H:i:s')
				]);
			}

			echo json_encode([
				'success' => true,
				'is_existing' => true,
				'message' => 'KOL "' . $existing->name . '" sudah ada di sistem dan otomatis dipilih.',
				'kol' => [
					'id' => (int)$existing->id,
					'name' => $existing->name,
					'tier_name' => !empty($existing->tier_name) ? $existing->tier_name : 'Nano',
					'is_blacklisted' => (int)$existing->is_blacklisted,
					'blacklist_reason' => $existing->blacklist_reason ?: ''
				]
			]);
			return;
		}

		// Get lowest/default active tier (Nano or first active)
		$default_tier = $this->db->where('is_active', 1)
			->order_by('min_followers', 'ASC')
			->get('tiers')->row();

		$tier_id = $default_tier ? (int)$default_tier->id : 1;
		$tier_name = $default_tier ? $default_tier->name : 'Nano';

		$save_data = [
			'name' => $name,
			'tier_id' => $tier_id,
			'status' => 'ACTIVE',
			'country' => 'Indonesia',
			'created_by' => get_user_data('id'),
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		];

		$this->db->insert('kols', $save_data);
		$kol_id = $this->db->insert_id();

		if ($kol_id) {
			echo json_encode([
				'success' => true,
				'is_existing' => false,
				'message' => 'KOL "' . $name . '" berhasil ditambahkan.',
				'kol' => [
					'id' => (int)$kol_id,
					'name' => $name,
					'tier_name' => $tier_name,
					'is_blacklisted' => 0,
					'blacklist_reason' => ''
				]
			]);
		} else {
			echo json_encode([
				'success' => false,
				'message' => 'Gagal menambahkan KOL ke database.'
			]);
		}
	}

	/**
	* Add New Campaign Kolss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('campaign_kols_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->form_validation->set_rules('campaign_id', 'Campaign', 'trim|required');
		$this->form_validation->set_rules('campaign_kols_deal_scope_name[]', 'Deal Scope', 'trim');
		$this->form_validation->set_rules('currency_code', 'Code Currency', 'trim|in_list[IDR,USD]|max_length[3]');

		if ($this->form_validation->run()) {
			$campaign_id = (int)$this->input->post('campaign_id');
			$kol_input = $this->input->post('kol_id');

			if (empty($kol_input)) {
				echo json_encode([
					'success' => false,
					'message' => 'Silakan pilih setidaknya satu Assignment KOL.'
				]);
				return;
			}

			$kol_ids = is_array($kol_input) ? $kol_input : [$kol_input];
			$kol_ids = array_values(array_filter(array_map('intval', $kol_ids)));

			if (empty($kol_ids)) {
				echo json_encode([
					'success' => false,
					'message' => 'Pilihan Assignment KOL tidak valid.'
				]);
				return;
			}

			// Prepare Deal Scope / Brief upload once for all selected KOLs
			if (!is_dir(FCPATH . '/uploads/campaign_kols/')) {
				mkdir(FCPATH . '/uploads/campaign_kols/', 0777, true);
			}

			$listed_image = [];
			if (count((array) $this->input->post('campaign_kols_deal_scope_name'))) {
				foreach ((array) $_POST['campaign_kols_deal_scope_name'] as $idx => $file_name) {
					if (empty($file_name)) continue;
					if (isset($_POST['campaign_kols_deal_scope_uuid'][$idx]) && !empty($_POST['campaign_kols_deal_scope_uuid'][$idx])) {
						$src = FCPATH . 'uploads/tmp/' . $_POST['campaign_kols_deal_scope_uuid'][$idx] . '/' . $file_name;
						if (is_file($src)) {
							$campaign_kols_deal_scope_name_copy = date('YmdHis') . '-' . $file_name;
							if (!@rename($src, FCPATH . 'uploads/campaign_kols/' . $campaign_kols_deal_scope_name_copy)) {
								@copy($src, FCPATH . 'uploads/campaign_kols/' . $campaign_kols_deal_scope_name_copy);
								@unlink($src);
							}
							$listed_image[] = $campaign_kols_deal_scope_name_copy;
						}
					} else {
						$listed_image[] = $file_name;
					}
				}
			}

			$deal_scope_str = !empty($listed_image) ? implode(',', $listed_image) : null;

			$status = 'NEGOTIATION';
			$allowed_vt_statuses = ['NEGOTIATION', 'ASSIGNED', 'ACTIVE', 'COMPLETED'];
			$jumlah_vt = in_array($status, $allowed_vt_statuses) ? (!empty($this->input->post('jumlah_vt')) ? (int)$this->input->post('jumlah_vt') : 0) : 0;
			$assigned_at = ($status === 'ASSIGNED') ? date('Y-m-d H:i:s') : null;

			$created_ids = [];
			$skipped_names = [];
			$assigned_by_user = get_user_data('id') ? get_user_data('id') : 1;
			$currency_code = !empty($this->input->post('currency_code')) ? $this->input->post('currency_code') : 'IDR';
			$deal_amount = ($this->input->post('deal_amount') !== null && $this->input->post('deal_amount') !== '') ? (float)preg_replace('/[^0-9]/', '', (string)$this->input->post('deal_amount')) : 0;

			$tasks_input = $this->input->post('tasks');
			$task_sync_allowed = ['ASSIGNED', 'ACTIVE', 'COMPLETED'];

			foreach ($kol_ids as $k_id) {
				// Check unique pair constraint
				$existing_pair = $this->db->get_where('campaign_kols', [
					'campaign_id' => $campaign_id,
					'kol_id'      => $k_id
				])->row();

				if ($existing_pair) {
					if (!in_array($existing_pair->status, ['REJECTED', 'CANCELLED'])) {
						$kol_info = $this->db->select('name')->get_where('kols', ['id' => $k_id])->row();
						$skipped_names[] = $kol_info ? $kol_info->name : ('KOL #' . $k_id);
						continue;
					}
				}

				$save_data = [
					'campaign_id' => $campaign_id,
					'kol_id' => $k_id,
					'status' => $status,
					'jumlah_vt' => $jumlah_vt,
					'assigned_at' => $assigned_at ?: date('Y-m-d H:i:s'),
					'deal_date' => null,
					'deal_amount' => $deal_amount,
					'task_count' => $jumlah_vt,
					'task_completed_count' => 0,
					'currency_code' => $currency_code,
					'assigned_by' => $assigned_by_user,
					'deal_scope' => $deal_scope_str
				];

				if ($existing_pair) {
					$this->db->where('id', $existing_pair->id)->update('campaign_kols', $save_data);
					$save_campaign_kols = $existing_pair->id;
				} else {
					$save_campaign_kols = $this->model_campaign_kols->store($save_data);
				}

				if ($save_campaign_kols) {
					$created_ids[] = $save_campaign_kols;

					// Process Task Configurations per KOL
					if (is_array($tasks_input) && count($tasks_input) > 0 && in_array($status, $task_sync_allowed)) {
						if ($existing_pair) {
							$this->db->where('campaign_kol_id', $save_campaign_kols)->delete('tasks');
							$this->db->where('campaign_kol_id', $save_campaign_kols)->delete('campaign_kol_variants');
						}

						$task_num = 1;
						foreach ($tasks_input as $t) {
							$variant_id = !empty($t['variant_id']) ? (int)$t['variant_id'] : 1;
							$this->db->query("INSERT IGNORE INTO campaign_kol_variants (campaign_kol_id, variant_id) VALUES (?, ?)", [$save_campaign_kols, $variant_id]);

							$task_payload = [
								'campaign_kol_id' => $save_campaign_kols,
								'variant_id' => $variant_id,
								'platform_id' => !empty($t['platform_id']) ? (int)$t['platform_id'] : 1,
								'content_type' => normalize_content_type(!empty($t['content_type']) ? $t['content_type'] : null),
								'task_number' => $task_num,
								'title' => !empty($t['title']) ? $t['title'] : 'Task #' . $task_num,
								'budget' => !empty($t['budget']) ? (float)str_replace(',', '.', str_replace('.', '', $t['budget'])) : 0,
								'currency_code' => $save_data['currency_code'],
								'estimated_posting_date' => !empty($t['estimated_posting_date']) ? $t['estimated_posting_date'] : date('Y-m-d'),
								'status' => 'DRAFT',
								'notes' => !empty($t['notes']) ? $t['notes'] : null,
								'created_by' => $assigned_by_user,
							];
							$this->db->insert('tasks', $task_payload);
							$new_task_id = $this->db->insert_id();
							if ($new_task_id) {
								$this->db->insert('briefs', [
									'task_id'        => $new_task_id,
									'title'          => $task_payload['title'] ?: ('Brief Task #' . $task_num),
									'content'        => $task_payload['notes'] ?: '',
									'file_path'      => null,
									'revision_count' => 0,
									'status'         => 'DRAFT',
									'created_by'     => $task_payload['created_by'],
									'created_at'     => date('Y-m-d H:i:s'),
									'updated_at'     => date('Y-m-d H:i:s')
								]);
							}
							$task_num++;
						}
						$this->db->where('id', $save_campaign_kols)->update('campaign_kols', ['task_count' => ($task_num - 1)]);
					}
				}
			}

			if (count($created_ids) > 0) {
				$primary_id = $created_ids[0];
				$count_success = count($created_ids);
				$success_msg = ($count_success === 1)
					? '1 KOL berhasil ditugaskan ke Dealing Campaign.'
					: ($count_success . ' KOL berhasil ditugaskan sekaligus ke Dealing Campaign.');

				if (!empty($skipped_names)) {
					$success_msg .= ' (' . count($skipped_names) . ' KOL dilewati karena sudah aktif: ' . implode(', ', $skipped_names) . ')';
				}

				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] = $primary_id;
					$this->data['message'] = $success_msg;
				} else {
					set_message($success_msg, 'success');
					$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/campaign_kols');
				}
			} else {
				$this->data['success'] = false;
				$this->data['message'] = !empty($skipped_names)
					? 'Semua KOL yang dipilih (' . implode(', ', $skipped_names) . ') sudah terdaftar aktif pada Campaign ini.'
					: 'Tidak ada KOL yang berhasil disimpan.';
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}

	public function edit($id)
	{
		$this->is_allowed('campaign_kols_update');

		$this->data['campaign_kols'] = $this->model_campaign_kols->find($id);
		$this->data['platforms'] = $this->db->where('is_active', 1)->get('platforms')->result();
		$this->data['variants'] = $this->db->where('is_active', 1)->get('variants')->result();
		$this->data['existing_tasks'] = $this->db->get_where('tasks', ['campaign_kol_id' => $id])->result();

		$this->template->title('Dealing KOL Update');
		$this->render('backend/standart/administrator/campaign_kols/campaign_kols_update', $this->data);
	}

	/**
	* Update Campaign Kolss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('campaign_kols_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('campaign_id', 'Campaign', 'trim|required');
		$this->form_validation->set_rules('kol_id', 'Assignment KOL', 'trim|required');
		$this->form_validation->set_rules('campaign_kols_deal_scope_name[]', 'Deal Scope', 'trim|required');
		$this->form_validation->set_rules('currency_code', 'Code Currency', 'trim|in_list[IDR,USD]|max_length[3]');
		
		if ($this->form_validation->run()) {
			$campaign_id = $this->input->post('campaign_id');
			$kol_id = $this->input->post('kol_id');

			// Check unique pair constraint for other records, allow if others are REJECTED or CANCELLED
			$this->db->where('campaign_id', $campaign_id);
			$this->db->where('kol_id', $kol_id);
			$this->db->where('id !=', $id);
			$this->db->where_not_in('status', ['REJECTED', 'CANCELLED']);
			$existing_pair = $this->db->get('campaign_kols')->row();

			if ($existing_pair) {
				echo json_encode([
					'success' => false,
					'message' => 'KOL ini sudah terdaftar pada Campaign yang dipilih dan statusnya masih aktif.'
				]);
				return;
			}

			$current_campaign_kol = isset($id) ? $this->model_campaign_kols->find($id) : null;
			$status = isset($id) ? $this->input->post('status') : 'NEGOTIATION';
			$allowed_vt_statuses = ['NEGOTIATION', 'ASSIGNED', 'ACTIVE', 'COMPLETED'];
			$jumlah_vt = in_array($status, $allowed_vt_statuses) ? (!empty($this->input->post('jumlah_vt')) ? (int)$this->input->post('jumlah_vt') : 0) : 0;
			$assigned_at = $current_campaign_kol ? $current_campaign_kol->assigned_at : null;
			$deal_date = $current_campaign_kol ? $current_campaign_kol->deal_date : null;
			if ($status === 'ASSIGNED' && empty($assigned_at)) {
				$assigned_at = date('Y-m-d H:i:s');
				$deal_date = date('Y-m-d');
			}
		
			$save_data = [
				'campaign_id' => $campaign_id,
				'kol_id' => $kol_id,
				'status' => $status,
				'jumlah_vt' => $jumlah_vt,
				'assigned_at' => $assigned_at,
				'deal_date' => $deal_date,
				'deal_amount' => ($this->input->post('deal_amount') !== null && $this->input->post('deal_amount') !== '') ? (float)preg_replace('/[^0-9]/', '', (string)$this->input->post('deal_amount')) : ($current_campaign_kol ? $current_campaign_kol->deal_amount : 0),
				'task_count' => $jumlah_vt,
				'task_completed_count' => $current_campaign_kol ? $current_campaign_kol->task_completed_count : 0,
				'currency_code' => !empty($this->input->post('currency_code')) ? $this->input->post('currency_code') : 'IDR',
			];

			if ($status === 'REJECTED') {
				$save_data['rejection_reason'] = $this->input->post('rejection_reason');
			} else {
				$save_data['rejection_reason'] = null;
			}
			foreach (['assigned_at', 'deal_date'] as $date_field) {
				if (array_key_exists($date_field, $save_data) && $save_data[$date_field] === null) {
					unset($save_data[$date_field]);
				}
			}

			if (!is_dir(FCPATH . '/uploads/campaign_kols/')) {
				mkdir(FCPATH . '/uploads/campaign_kols/', 0777, true);
			}

			$listed_image = [];
			if (count((array) $this->input->post('campaign_kols_deal_scope_name'))) {
				foreach ((array) $_POST['campaign_kols_deal_scope_name'] as $idx => $file_name) {
					if (empty($file_name)) continue;
					if (isset($_POST['campaign_kols_deal_scope_uuid'][$idx]) AND !empty($_POST['campaign_kols_deal_scope_uuid'][$idx])) {
						$src = FCPATH . 'uploads/tmp/' . $_POST['campaign_kols_deal_scope_uuid'][$idx] . '/' .  $file_name;
						if (is_file($src)) {
							$campaign_kols_deal_scope_name_copy = date('YmdHis') . '-' . $file_name;
							if (!@rename($src, FCPATH . 'uploads/campaign_kols/' . $campaign_kols_deal_scope_name_copy)) {
								@copy($src, FCPATH . 'uploads/campaign_kols/' . $campaign_kols_deal_scope_name_copy);
								@unlink($src);
							}
							$listed_image[] = $campaign_kols_deal_scope_name_copy;
						}
					} else {
						$listed_image[] = $file_name;
					}
				}
			}
			
			$save_data['deal_scope'] = implode(',', $listed_image);
			if (empty($save_data['deal_scope'])) {
				$this->data['success'] = false;
				$this->data['message'] = 'The Brief is required.';
				echo json_encode($this->data);
				return;
			}
		
			
			$save_campaign_kols = $this->model_campaign_kols->change($id, $save_data);

			if ($save_campaign_kols) {
				// Process Task Configurations (Only for ASSIGNED, ACTIVE, COMPLETED)
				$tasks_input = $this->input->post('tasks');
				$task_sync_allowed = ['ASSIGNED', 'ACTIVE', 'COMPLETED'];
				if (is_array($tasks_input) && count($tasks_input) > 0 && in_array($status, $task_sync_allowed)) {
					$task_num = 1;
					$saved_task_ids = [];
					foreach ($tasks_input as $t) {
						$variant_id = !empty($t['variant_id']) ? (int)$t['variant_id'] : 1;
						$this->db->query("INSERT IGNORE INTO campaign_kol_variants (campaign_kol_id, variant_id) VALUES (?, ?)", [$id, $variant_id]);

						$task_payload = [
							'campaign_kol_id' => $id,
							'variant_id' => $variant_id,
							'platform_id' => !empty($t['platform_id']) ? (int)$t['platform_id'] : 1,
							'content_type' => normalize_content_type(!empty($t['content_type']) ? $t['content_type'] : null),
							'task_number' => $task_num,
							'title' => !empty($t['title']) ? $t['title'] : 'Task #' . $task_num,
							'budget' => !empty($t['budget']) ? (float)str_replace(',', '.', str_replace('.', '', $t['budget'])) : 0,
							'currency_code' => $save_data['currency_code'],
							'estimated_posting_date' => !empty($t['estimated_posting_date']) ? $t['estimated_posting_date'] : date('Y-m-d'),
							'notes' => !empty($t['notes']) ? $t['notes'] : null,
						];

						if (!empty($t['id'])) {
							$this->db->where('id', $t['id'])->where('campaign_kol_id', $id)->update('tasks', $task_payload);
							$saved_task_ids[] = $t['id'];
						} else {
							$task_payload['status'] = 'DRAFT';
							$task_payload['created_by'] = get_user_data('id') ? get_user_data('id') : 1;
							$this->db->insert('tasks', $task_payload);
							$new_task_id = $this->db->insert_id();
							$saved_task_ids[] = $new_task_id;
							if ($new_task_id) {
								$this->db->insert('briefs', [
									'task_id'        => $new_task_id,
									'title'          => $task_payload['title'] ?: ('Brief Task #' . $task_num),
									'content'        => $task_payload['notes'] ?: '',
									'file_path'      => null,
									'revision_count' => 0,
									'status'         => 'DRAFT',
									'created_by'     => $task_payload['created_by'],
									'created_at'     => date('Y-m-d H:i:s'),
									'updated_at'     => date('Y-m-d H:i:s')
								]);
							}
						}
						$task_num++;
					}
					if (!empty($saved_task_ids)) {
						$this->db->where('campaign_kol_id', $id)->where_not_in('id', $saved_task_ids)->delete('tasks');
					}
					$this->db->where('id', $id)->update('campaign_kols', ['task_count' => count($saved_task_ids)]);
				} else {
					// When status is NEGOTIATION, REJECTED, or CANCELLED, remove all tasks and junction variants
					$this->db->where('campaign_kol_id', $id)->delete('tasks');
					$this->db->where('campaign_kol_id', $id)->delete('campaign_kol_variants');
					$this->db->where('id', $id)->update('campaign_kols', ['task_count' => 0]);
				}

				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/campaign_kols', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/campaign_kols');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/campaign_kols');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Campaign Kolss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('campaign_kols_delete');

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
		$error_records = [];

		foreach ($ids as $ck_id) {
			$result = $this->_remove($ck_id);
			if ($result === true) {
				$deleted_count++;
			} else {
				$error_records[] = "ID #{$ck_id}";
			}
		}

		if ($deleted_count > 0 && empty($error_records)) {
			if (count($ids) == 1) {
				set_message(cclang('has_been_deleted', 'campaign_kols'), 'success');
			} else {
				set_message("$deleted_count dealing records have been successfully deleted.", 'success');
			}
		} elseif ($deleted_count > 0 && !empty($error_records)) {
			set_message("$deleted_count dealing record(s) successfully deleted. Failed to delete: " . implode(', ', $error_records) . ".", 'warning');
		} else {
			set_message(cclang('error_delete', 'campaign_kols'), 'error');
		}

		redirect_back();
	}

		/**
	* View view Campaign Kolss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('campaign_kols_view');

		$this->data['campaign_kols'] = $this->model_campaign_kols->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Campaign Kols Detail');
		$this->render('backend/standart/administrator/campaign_kols/campaign_kols_view', $this->data);
	}
	
	/**
	* delete Campaign Kolss with full cascade
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$ck = $this->db->get_where('campaign_kols', ['id' => $id])->row();
		if (!$ck) {
			return false;
		}

		$this->db->trans_start();

		// 1. Find all tasks under this campaign_kol
		$tasks = $this->db->get_where('tasks', ['campaign_kol_id' => $id])->result();
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
		$shipments = $this->db->get_where('kol_shipments', ['campaign_kol_id' => $id])->result();
		foreach ($shipments as $s) {
			$this->db->delete('kol_shipment_items', ['shipment_id' => $s->id]);
		}
		$this->db->delete('kol_shipments', ['campaign_kol_id' => $id]);

		// 3. Delete junction variants & score histories
		$this->db->delete('campaign_kol_variants', ['campaign_kol_id' => $id]);
		$this->db->delete('score_histories', ['campaign_kol_id' => $id]);

		// 4. Delete campaign_kol record
		$this->model_campaign_kols->remove($id);

		$this->db->trans_complete();

		if ($this->db->trans_status() === TRUE) {
			if (!empty($ck->deal_scope)) {
				foreach ((array) explode(',', $ck->deal_scope) as $filename) {
					$path = FCPATH . 'uploads/campaign_kols/' . trim($filename);
					if (is_file($path)) {
						@unlink($path);
					}
				}
			}

			return true;
		}

		return false;
	}
	
	
	/**
	* Upload Image Campaign Kols	* 
	* @return JSON
	*/
	public function upload_deal_scope_file()
	{
		if (!$this->is_allowed('campaign_kols_add', false) && !$this->is_allowed('campaign_kols_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'campaign_kols',
		]);
	}

	/**
	* Delete Image Campaign Kols	* 
	* @return JSON
	*/
	public function delete_deal_scope_file($uuid)
	{
		if (!$this->is_allowed('campaign_kols_delete', false) && !$this->is_allowed('campaign_kols_update', false) && !$this->is_allowed('campaign_kols_add', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'deal_scope', 
            'upload_path_tmp'   => 'uploads/tmp/',
            'table_name'        => 'campaign_kols',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/campaign_kols/'
        ]);
	}

	/**
	* Get Image Campaign Kols	* 
	* @return JSON
	*/
	public function get_deal_scope_file($id)
	{
		if (!$this->is_allowed('campaign_kols_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$campaign_kols = $this->model_campaign_kols->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'deal_scope', 
            'table_name'        => 'campaign_kols',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/campaign_kols/',
            'delete_endpoint'   => 'administrator/campaign_kols/delete_deal_scope_file'
        ]);
	}
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('campaign_kols_export');

		$this->model_campaign_kols->export('campaign_kols', 'campaign_kols');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('campaign_kols_export');

		$this->model_campaign_kols->pdf('campaign_kols', 'campaign_kols');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('campaign_kols_export');

		$table = $title = 'campaign_kols';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_campaign_kols->find($id);
        $fields = $result->list_fields();

        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', [
            'data' => $data,
            'fields' => $fields,
            'title' => $title
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        $this->pdf->Output($table.'.pdf', 'H');
	}

	/**
	* Get tasks, shipping, and meta for Dealing KOL as JSON
	*
	* @param int $id
	* @return JSON
	*/
	public function get_tasks_json($id = null)
	{
		$this->output->enable_profiler(FALSE);

		if (!$this->is_allowed('campaign_kols_view', false) && !$this->is_allowed('campaign_kols_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			return;
		}

		$campaign_kol = $this->model_campaign_kols->find($id);
		if (!$campaign_kol) {
			echo json_encode([
				'success' => false,
				'message' => 'Data Dealing KOL tidak ditemukan.'
			]);
			return;
		}

		$tasks = $this->db->get_where('tasks', ['campaign_kol_id' => $id])->result();
		$kol_id = $campaign_kol->kol_id;
		$kol_platforms = $this->db->select('platforms.id, platforms.name, platforms.code, kol_platforms.username, kol_platforms.followers')
			->from('kol_platforms')
			->join('platforms', 'platforms.id = kol_platforms.platform_id', 'INNER')
			->where('kol_platforms.kol_id', $kol_id)
			->where('kol_platforms.is_active', 1)
			->get()->result();

		// Only return platforms that this KOL has, fallback to active platforms if none registered
		$platforms = !empty($kol_platforms) ? $kol_platforms : $this->db->where('is_active', 1)->get('platforms')->result();
		$variants = $this->db->where('is_active', 1)->get('variants')->result();

		$campaign = $this->db->get_where('campaigns', ['id' => $campaign_kol->campaign_id])->row();
		$kol = $this->db->get_where('kols', ['id' => $campaign_kol->kol_id])->row();

		// Shipment and items
		$shipment = $this->db->get_where('kol_shipments', ['campaign_kol_id' => $id])->row();
		$shipment_items = [];
		if ($shipment) {
			$shipment_items = $this->db->select('kol_shipment_items.*, products.name as product_name, products.sku as product_sku, products.stock as product_stock, products.price as product_price')
				->from('kol_shipment_items')
				->join('products', 'products.id = kol_shipment_items.product_id', 'LEFT')
				->where('kol_shipment_items.shipment_id', $shipment->id)
				->get()->result();
		}

		// Filter products to only those associated with the parent campaign
		$campaign_id = $campaign_kol->campaign_id;
		$campaign_products = $this->db->select('products.*')
			->from('campaign_products')
			->join('products', 'products.id = campaign_products.product_id', 'INNER')
			->where('campaign_products.campaign_id', $campaign_id)
			->where('products.is_active', 1)
			->order_by('products.category ASC, products.name ASC')
			->get()->result();

		// Use campaign products if defined, otherwise fallback to active products
		$products = !empty($campaign_products) ? $campaign_products : $this->db->where('is_active', 1)->order_by('category ASC, name ASC')->get('products')->result();

		// Calculate remaining budget
		$campaign_budget = $campaign ? (float)$campaign->budget : 0;
		$other_used_budget = 0;
		if ($campaign) {
			$this->db->select_sum('deal_amount');
			$this->db->where('campaign_id', $campaign_id);
			$this->db->where('id !=', $id);
			$this->db->where_in('status', ['ASSIGNED', 'ACTIVE', 'COMPLETED']);
			$query = $this->db->get('campaign_kols');
			$sum_res = $query->row();
			$other_used_budget = $sum_res ? (float)$sum_res->deal_amount : 0;
		}
		$campaign_remaining_budget = $campaign_budget - $other_used_budget;

		echo json_encode([
			'success' => true,
			'campaign_kol' => $campaign_kol,
			'campaign_name' => $campaign ? ($campaign->name ?? '') : '',
			'campaign_start_date' => $campaign ? ($campaign->start_date ?? '') : '',
			'campaign_end_date' => $campaign ? ($campaign->end_date ?? '') : '',
			'campaign_budget' => $campaign_budget,
			'campaign_remaining_budget' => $campaign_remaining_budget > 0 ? $campaign_remaining_budget : 0,
			'kol_name' => $kol ? ($kol->name ?? '') : '',
			'kol' => $kol,
			'tasks' => $tasks,
			'platforms' => $platforms,
			'variants' => $variants,
			'shipment' => $shipment,
			'shipment_items' => $shipment_items,
			'products' => $products
		]);
	}

	/**
	* Generate unique tracking/resi number based on courier
	*
	* @return JSON
	*/
	public function generate_tracking_no()
	{
		$this->output->enable_profiler(FALSE);

		$courier = trim($this->input->get_post('courier') ?? '');
		$prefix_map = [
			'JNE' => 'JNE',
			'J&T' => 'J&T',
			'SiCepat' => 'SCP',
			'AnterAja' => 'ANTR',
			'GoSend' => 'GOSEND',
			'GrabExpress' => 'GRAB',
			'Ninja Xpress' => 'NINJA',
			'POS Indonesia' => 'POS',
			'Lion Parcel' => 'LION',
			'Lainnya' => 'RESI'
		];

		$prefix = isset($prefix_map[$courier]) ? $prefix_map[$courier] : (preg_replace('/[^A-Za-z0-9&]/', '', $courier) ?: 'RESI');
		$max_attempts = 20;
		$unique_code = null;

		for ($i = 0; $i < $max_attempts; $i++) {
			$random_digits = mt_rand(1000000, 9999999);
			$candidate = $prefix . $random_digits;
			
			$exists = $this->db->get_where('kol_shipments', ['tracking_no' => $candidate])->row();
			if (!$exists) {
				$unique_code = $candidate;
				break;
			}
		}

		if (!$unique_code) {
			$unique_code = $prefix . time();
		}

		echo json_encode([
			'success' => true,
			'tracking_no' => $unique_code,
			'courier' => $courier
		]);
		exit;
	}

	/**
	* Update Status, Task configurations, and Shipment via AJAX (Unified Submit)
	*
	* @return JSON
	*/
	public function update_status_tasks_ajax()
	{
		$this->output->enable_profiler(FALSE);

		try {
			if (!$this->is_allowed('campaign_kols_update', false)) {
				echo json_encode([
					'success' => false,
					'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
				return;
			}

			$id = $this->input->post('id');
			$status = $this->input->post('status');
			$jumlah_vt = (int)$this->input->post('jumlah_vt');

			$valid_statuses = ['ASSIGNED', 'NEGOTIATION', 'ACTIVE', 'COMPLETED', 'CANCELLED', 'REJECTED'];
			if (!in_array($status, $valid_statuses)) {
				echo json_encode([
					'success' => false,
					'message' => 'Status tidak valid.'
				]);
				return;
			}

			$campaign_kol = $this->model_campaign_kols->find($id);
			if (!$campaign_kol) {
				echo json_encode([
					'success' => false,
					'message' => 'Data Dealing KOL tidak ditemukan.'
				]);
				return;
			}

			$deal_amount_raw = $this->input->post('deal_amount');
			$deal_amount = ($deal_amount_raw !== null && $deal_amount_raw !== '') ? (float)preg_replace('/[^0-9]/', '', (string)$deal_amount_raw) : null;

			// Check against campaign remaining budget
			$campaign_id = $campaign_kol->campaign_id;
			$campaign = $this->db->get_where('campaigns', ['id' => $campaign_id])->row();
			$campaign_budget = $campaign ? (float)$campaign->budget : 0;
			
			$this->db->select_sum('deal_amount');
			$this->db->where('campaign_id', $campaign_id);
			$this->db->where('id !=', $id);
			$this->db->where_in('status', ['ASSIGNED', 'ACTIVE', 'COMPLETED']);
			$query = $this->db->get('campaign_kols');
			$sum_res = $query->row();
			$other_used_budget = $sum_res ? (float)$sum_res->deal_amount : 0;

			$campaign_remaining_budget = $campaign_budget - $other_used_budget;

			// NEGOTIATION never consumes budget: force deal to 0 regardless of input
			if ($status === 'NEGOTIATION') {
				$deal_amount = 0;
			}

			if ($deal_amount !== null && $deal_amount > $campaign_remaining_budget) {
				echo json_encode([
					'success' => false,
					'message' => 'Deal amount melebihi sisa budget campaign.'
				]);
				return;
			}

			$this->db->trans_start();

			$allowed_vt_statuses = ['NEGOTIATION', 'ASSIGNED', 'ACTIVE', 'COMPLETED'];
			$save_jumlah_vt = in_array($status, $allowed_vt_statuses) ? $jumlah_vt : 0;

			$update_data = [
				'status' => $status,
				'jumlah_vt' => $save_jumlah_vt,
				'task_count' => $save_jumlah_vt,
				'updated_at' => date('Y-m-d H:i:s')
			];

			if ($status === 'REJECTED') {
				$update_data['rejection_reason'] = $this->input->post('rejection_reason');
			} else {
				$update_data['rejection_reason'] = null;
			}

			if ($status === 'ASSIGNED' && empty($campaign_kol->deal_date)) {
				$update_data['assigned_at'] = date('Y-m-d H:i:s');
				$update_data['deal_date'] = date('Y-m-d');
			}

			if ($deal_amount !== null) {
				$update_data['deal_amount'] = $deal_amount;
			}

			// Save Dealing KOL record
			$this->db->where('id', $id)->update('campaign_kols', $update_data);

		// Auto-Draft Contract saat Status DEAL (ASSIGNED/ACTIVE/COMPLETED)
		// Deal yang lompat langsung ke ACTIVE/COMPLETED tanpa lewat ASSIGNED tetap dapat kontrak.
		if (in_array($status, ['ASSIGNED', 'ACTIVE', 'COMPLETED']) && !in_array($campaign_kol->status, ['ASSIGNED', 'ACTIVE', 'COMPLETED'])) {
				// Cek apakah sudah ada kontrak untuk deal ini
				$this->db->where('campaign_kol_id', $id);
				$this->db->where_not_in('status', ['VOID', 'CANCELLED']); // Abaikan yang sudah void/cancel
				$existing_contract = $this->db->get('campaign_kol_contracts')->row();
				
				if (!$existing_contract) {
					// Generate contract number: CTR/YYYY/MM/xxx
					$year = date('Y');
					$month = date('m');
					
					$this->db->select('contract_no');
					$this->db->like('contract_no', 'CTR/'.$year.'/'.$month.'/', 'after');
					$this->db->order_by('id', 'DESC');
					$this->db->limit(1);
					$last_contract = $this->db->get('campaign_kol_contracts')->row();

					$next_num = 1;
					if ($last_contract) {
						$parts = explode('/', $last_contract->contract_no);
						$next_num = (int)end($parts) + 1;
					}
					$contract_no = 'CTR/'.$year.'/'.$month.'/'.str_pad($next_num, 3, '0', STR_PAD_LEFT);

					$this->db->insert('campaign_kol_contracts', [
						'campaign_kol_id' => $id,
						'contract_no'     => $contract_no,
						// 'contract_date'   => date('Y-m-d'), // not in schema
						'status'          => 'DRAFT',
						'created_at'      => date('Y-m-d H:i:s'),
					]);
				}
			}


			// Handle task items if status is ASSIGNED, ACTIVE, or COMPLETED
			$tasks_input = $this->input->post('tasks');
			$task_sync_allowed = ['ASSIGNED', 'ACTIVE', 'COMPLETED'];

			if (in_array($status, $task_sync_allowed) && is_array($tasks_input) && count($tasks_input) > 0) {
				$total_task_budget = 0;
				foreach ($tasks_input as $t) {
					if (empty($t['content_type']) || !isset($t['budget']) || $t['budget'] === '') {
						$this->db->trans_rollback();
						echo json_encode([
							'success' => false,
							'message' => 'Content Type dan Budget wajib diisi untuk setiap task.'
						]);
						return;
					}
					$b = (float)preg_replace('/[^0-9]/', '', (string)$t['budget']);
					if ($b < 0) {
						$this->db->trans_rollback();
						echo json_encode([
							'success' => false,
							'message' => 'Budget task tidak boleh kurang dari nol.'
						]);
						return;
					}
					$total_task_budget += $b;
				}
				if ($deal_amount === null || $deal_amount <= 0 || $total_task_budget != $deal_amount) {
					$this->db->trans_rollback();
					echo json_encode([
						'success' => false,
						'message' => 'Total budget task harus sama dengan nilai deal kesepakatan.'
					]);
					return;
				}

				$task_num = 1;
				$saved_task_ids = [];
				foreach ($tasks_input as $t) {
					$est_date_raw = !empty($t['estimated_posting_date']) ? trim($t['estimated_posting_date']) : date('Y-m-d');
					$est_date = date('Y-m-d');
					$formats = ['Y-m-d', 'm/d/Y', 'd/m/Y', 'd-m-Y', 'Y/m/d'];
					foreach ($formats as $fmt) {
						$d = DateTime::createFromFormat($fmt, $est_date_raw);
						if ($d && $d->format($fmt) === $est_date_raw) {
							$est_date = $d->format('Y-m-d');
							break;
						}
					}
					if ($est_date < date('Y-m-d')) {
						$this->db->trans_rollback();
						echo json_encode(['success' => false, 'message' => 'Estimasi posting tidak boleh di masa lalu (sebelum hari ini).']);
						return;
					}
					if ($campaign && !empty($campaign->start_date) && !empty($campaign->end_date)) {
						$camp_start = substr((string)$campaign->start_date, 0, 10);
						$camp_end = substr((string)$campaign->end_date, 0, 10);
						if ($est_date < $camp_start || $est_date > $camp_end) {
							$this->db->trans_rollback();
							echo json_encode(['success' => false, 'message' => 'Estimasi posting harus di dalam periode campaign (' . $camp_start . ' s/d ' . $camp_end . ').']);
							return;
						}
					}

					$variant_id = !empty($t['variant_id']) ? (int)$t['variant_id'] : 1;
					$this->db->query("INSERT IGNORE INTO campaign_kol_variants (campaign_kol_id, variant_id) VALUES (?, ?)", [$id, $variant_id]);

					$task_payload = [
						'campaign_kol_id' => $id,
						'variant_id' => $variant_id,
						'platform_id' => !empty($t['platform_id']) ? (int)$t['platform_id'] : 1,
						'content_type' => normalize_content_type(!empty($t['content_type']) ? $t['content_type'] : null),
						'task_number' => $task_num,
						'title' => !empty($t['title']) ? $t['title'] : 'Task #' . $task_num,
						'budget' => !empty($t['budget']) ? (float)str_replace(',', '.', str_replace('.', '', $t['budget'])) : 0,
						'currency_code' => !empty($campaign_kol->currency_code) ? $campaign_kol->currency_code : 'IDR',
						'estimated_posting_date' => $est_date,
						'notes' => !empty($t['notes']) ? $t['notes'] : null,
					];

					if (!empty($t['id'])) {
						$this->db->where('id', $t['id'])->where('campaign_kol_id', $id)->update('tasks', $task_payload);
						$saved_task_ids[] = $t['id'];
					} else {
						$task_payload['status'] = 'DRAFT';
						$task_payload['created_by'] = get_user_data('id') ? get_user_data('id') : 1;
						$this->db->insert('tasks', $task_payload);
						$new_task_id = $this->db->insert_id();
						$saved_task_ids[] = $new_task_id;
						if ($new_task_id) {
							$this->db->insert('briefs', [
								'task_id'        => $new_task_id,
								'title'          => $task_payload['title'] ?: ('Brief Task #' . $task_num),
								'content'        => $task_payload['notes'] ?: '',
								'file_path'      => null,
								'revision_count' => 0,
								'status'         => 'DRAFT',
								'created_by'     => $task_payload['created_by'],
								'created_at'     => date('Y-m-d H:i:s'),
								'updated_at'     => date('Y-m-d H:i:s')
							]);
						}
					}

					$task_num++;
				}
				if (!empty($saved_task_ids)) {
					$this->db->where('campaign_kol_id', $id)->where_not_in('id', $saved_task_ids)->delete('tasks');
				}
				$this->db->where('id', $id)->update('campaign_kols', ['task_count' => count($saved_task_ids)]);
			} else {
				// When status is NEGOTIATION, REJECTED, or CANCELLED, remove all tasks and junction variants
				$this->db->where('campaign_kol_id', $id)->delete('tasks');
				$this->db->where('campaign_kol_id', $id)->delete('campaign_kol_variants');
				$this->db->where('id', $id)->update('campaign_kols', ['task_count' => 0]);
			}

			// Handle Shipment and Shipment Items (Step 2)
			$shipment_input = $this->input->post('shipment');
			$shipment_items_input = $this->input->post('shipment_items');

			if (in_array($status, $task_sync_allowed) && is_array($shipment_input) && !empty($shipment_input['recipient_name'])) {
				$existing_shipment = $this->db->get_where('kol_shipments', ['campaign_kol_id' => $id])->row();
				$raw_fee = !empty($shipment_input['shipping_fee']) ? $shipment_input['shipping_fee'] : 0;
				$shipping_fee = (float)str_replace(',', '.', str_replace('.', '', $raw_fee));
				$shipment_status = !empty($shipment_input['status']) ? $shipment_input['status'] : 'PENDING';

				$shipment_data = [
					'campaign_kol_id' => $id,
					'recipient_name' => $shipment_input['recipient_name'],
					'shipping_address' => !empty($shipment_input['shipping_address']) ? $shipment_input['shipping_address'] : '-',
					'courier' => !empty($shipment_input['courier']) ? $shipment_input['courier'] : null,
					'tracking_no' => !empty($shipment_input['tracking_no']) ? $shipment_input['tracking_no'] : null,
					'shipping_fee' => $shipping_fee,
					'status' => $shipment_status,
					'notes' => !empty($shipment_input['notes']) ? $shipment_input['notes'] : null,
					'updated_at' => date('Y-m-d H:i:s')
				];
				if ($this->db->field_exists('recipient_phone', 'kol_shipments') && !empty($shipment_input['recipient_phone'])) {
					$shipment_data['recipient_phone'] = trim((string)$shipment_input['recipient_phone']);
				}

				if (!empty($shipment_input['shipped_at'])) {
					$shipment_data['shipped_at'] = $shipment_input['shipped_at'];
				}
				if (!empty($shipment_input['delivered_at'])) {
					$shipment_data['delivered_at'] = $shipment_input['delivered_at'];
				}

				if ($existing_shipment) {
					$shipment_id = $existing_shipment->id;
					$this->db->where('id', $shipment_id)->update('kol_shipments', $shipment_data);
				} else {
					$shipment_data['created_by'] = get_user_data('id') ? get_user_data('id') : 1;
					$shipment_data['created_at'] = date('Y-m-d H:i:s');
					$this->db->insert('kol_shipments', $shipment_data);
					$shipment_id = $this->db->insert_id();
				}

				// Handle Shipment Items and Stock Adjustment
				if ($shipment_id) {
					// Get existing shipment items to adjust stock delta
					$existing_items = $this->db->get_where('kol_shipment_items', ['shipment_id' => $shipment_id])->result();
					$existing_item_map = []; // product_id => qty
					foreach ($existing_items as $ei) {
						$existing_item_map[$ei->product_id] = (int)$ei->qty;
					}

					// Process new items
					$new_item_map = [];
					if (is_array($shipment_items_input)) {
						foreach ($shipment_items_input as $item) {
							$p_id = !empty($item['product_id']) ? (int)$item['product_id'] : 0;
							$p_qty = !empty($item['qty']) ? (int)$item['qty'] : 0;
							if ($p_id > 0 && $p_qty > 0) {
								$new_item_map[$p_id] = ($new_item_map[$p_id] ?? 0) + $p_qty;
							}
						}
					}

					// We no longer adjust stock directly on save. Stock is only deducted when the status changes to SHIPPED/DELIVERED.
					// If the shipment is already SHIPPED and we're modifying items, the stock history is not handled here, 
					// but it's recommended not to edit items after shipping. If they do, they can return it.
					// So we just update the items.

					// 3. Clear and re-insert shipment items
					$this->db->where('shipment_id', $shipment_id)->delete('kol_shipment_items');
					foreach ($new_item_map as $p_id => $qty) {
						$this->db->insert('kol_shipment_items', [
							'shipment_id' => $shipment_id,
							'product_id' => $p_id,
							'qty' => $qty,
							'created_at' => date('Y-m-d H:i:s')
						]);
					}

					// 4. Log stock changes after items are successfully saved
					if ($existing_shipment) {
						if ($existing_shipment->status !== $shipment_status) {
							$this->model_kol_shipments->log_stock_change($shipment_id, $existing_shipment->status, $shipment_status);
						}
					} else {
						if (in_array($shipment_status, ['SHIPPED', 'DELIVERED'])) {
							$this->model_kol_shipments->log_stock_change($shipment_id, 'PENDING', $shipment_status);
						}
					}
				}
			}

			$this->db->trans_complete();

			if ($this->db->trans_status() === FALSE) {
				$db_err = $this->db->error();
				log_message('error', '[update_status_tasks_ajax Trans Error] ' . json_encode($db_err));
				echo json_encode([
					'success' => false,
					'message' => 'Gagal menyimpan data (Database Transaction Error): ' . ($db_err['message'] ?? 'Unknown error')
				]);
				return;
			}

			echo json_encode([
				'success' => true,
				'message' => 'Status, konfigurasi task, dan pengiriman produk berhasil disimpan.',
				'new_status' => $status,
				'jumlah_vt' => $save_jumlah_vt,
				'deal_date' => $update_data['deal_date'] ?? $campaign_kol->deal_date,
				'deal_amount' => $update_data['deal_amount'] ?? $campaign_kol->deal_amount,
				'rejection_reason' => array_key_exists('rejection_reason', $update_data) ? $update_data['rejection_reason'] : $campaign_kol->rejection_reason,
				'task_count' => in_array($status, $task_sync_allowed) && is_array($tasks_input) ? count($tasks_input) : 0,
				'csrf_name' => $this->security->get_csrf_token_name(),
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
		} catch (Throwable $e) {
			log_message('error', '[update_status_tasks_ajax Exception] ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
			echo json_encode([
				'success' => false,
				'message' => 'Server Error: ' . $e->getMessage()
			]);
		}
	}
}

/* End of file campaign_kols.php */
/* Location: ./application/controllers/administrator/Campaign Kols.php */