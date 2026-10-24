<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Kols Controller
*| --------------------------------------------------------------------------
*| Kols site
*|
*/
class Kols extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_kols');
		$this->load->helper('text');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Kolss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('kols_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$perf_platform      = $this->input->get('perf_platform');
		$perf_min_views     = $this->input->get('perf_min_views');
		$perf_max_views     = $this->input->get('perf_max_views');
		$perf_min_er        = $this->input->get('perf_min_er');
		$perf_max_er        = $this->input->get('perf_max_er');
		$perf_min_followers = $this->input->get('perf_min_followers');
		$perf_min_gmv       = $this->input->get('perf_min_gmv');
		$perf_status        = $this->input->get('perf_status');
		$perf_sort          = $this->input->get('perf_sort');

		$filter_category    = $this->input->get('category');
		$filter_tier        = $this->input->get('tier');
		$filter_gender      = $this->input->get('gender');

		$perf_params = [
			'category'           => $filter_category,
			'tier'               => $filter_tier,
			'gender'             => $filter_gender,
			'perf_platform'      => $perf_platform,
			'perf_min_views'     => $perf_min_views,
			'perf_max_views'     => $perf_max_views,
			'perf_min_er'        => $perf_min_er,
			'perf_max_er'        => $perf_max_er,
			'perf_min_followers' => $perf_min_followers,
			'perf_min_gmv'       => $perf_min_gmv,
			'perf_status'        => $perf_status,
			'perf_sort'          => $perf_sort,
		];

		$this->data['kolss'] = $this->model_kols->get($filter, $field, $this->limit_page, $offset, [], $perf_params);
		$this->data['kols_counts'] = $this->model_kols->count_all($filter, $field, $perf_params);

		$this->data['filter_category']    = $filter_category;
		$this->data['filter_tier']        = $filter_tier;
		$this->data['filter_gender']      = $filter_gender;
		$this->data['perf_platform']      = $perf_platform;
		$this->data['perf_min_views']     = $perf_min_views;
		$this->data['perf_max_views']     = $perf_max_views;
		$this->data['perf_min_er']        = $perf_min_er;
		$this->data['perf_max_er']        = $perf_max_er;
		$this->data['perf_min_followers'] = $perf_min_followers;
		$this->data['perf_min_gmv']       = $perf_min_gmv;
		$this->data['perf_status']        = $perf_status;
		$this->data['perf_sort']          = $perf_sort;

		// Load available platforms for filter dropdown
		$this->data['all_platforms'] = $this->db->order_by('name', 'ASC')->get('platforms')->result();

		// Load available categories and tiers for filter dropdowns
		$this->data['all_categories'] = $this->db->where('is_active', 1)->order_by('name', 'ASC')->get('categories')->result();
		$this->data['all_tiers'] = $this->db->where('is_active', 1)->order_by('min_followers', 'ASC')->get('tiers')->result();

		// Load platform accounts for all listed KOLs
		$kol_ids = !empty($this->data['kolss']) ? array_map(function($k) { return $k->id; }, $this->data['kolss']) : [];
		$platforms_by_kol = [];
		$categories_by_kol = [];
		if (!empty($kol_ids)) {
			$k_plats = $this->db->select('kol_platforms.*, platforms.name as platform_name, platforms.code as platform_code, platforms.icon_url, platforms.color_hex')
				->from('kol_platforms')
				->join('platforms', 'platforms.id = kol_platforms.platform_id', 'LEFT')
				->where_in('kol_platforms.kol_id', $kol_ids)
				->get()->result();
			foreach ($k_plats as $kp) {
				$platforms_by_kol[$kp->kol_id][] = $kp;
			}

			$k_cats = $this->db->select('kol_categories.*, categories.name as category_name')
				->from('kol_categories')
				->join('categories', 'categories.id = kol_categories.category_id', 'INNER')
				->where_in('kol_categories.kol_id', $kol_ids)
				->get()->result();
			foreach ($k_cats as $kc) {
				$categories_by_kol[$kc->kol_id][] = $kc;
			}
		}
		$this->data['platforms_by_kol'] = $platforms_by_kol;
		$this->data['categories_by_kol'] = $categories_by_kol;

		// Load per-platform rate cards for all listed KOLs (1 query, grouped)
		$rates_by_kol = [];
		if (!empty($kol_ids)) {
			$k_rates = $this->db->select('kp.kol_id, kp.id as kol_platform_id, kp.username, kp.followers, platforms.name as platform_name, platforms.code as platform_code, rc.content_type, rc.rate')
				->from('kol_rate_cards rc')
				->join('kol_platforms kp', 'kp.id = rc.kol_platform_id', 'INNER')
				->join('platforms', 'platforms.id = kp.platform_id', 'LEFT')
				->where_in('kp.kol_id', $kol_ids)
				->where('kp.is_active', 1)
				->where('rc.is_active', 1)
				->where('rc.currency_code', 'IDR')
				->order_by('kp.followers', 'DESC')
				->order_by('platforms.name', 'ASC')
				->get()->result();
			foreach ($k_rates as $kr) {
				$ckol = $kr->kol_id;
				$cpid = $kr->kol_platform_id;
				if (!isset($rates_by_kol[$ckol][$cpid])) {
					$rates_by_kol[$ckol][$cpid] = [
						'platform_name' => $kr->platform_name,
						'platform_code' => $kr->platform_code,
						'username'      => $kr->username,
						'followers'     => $kr->followers,
						'rates'         => [],
					];
				}
				$rates_by_kol[$ckol][$cpid]['rates'][normalize_content_type($kr->content_type)] = (float)$kr->rate;
			}
		}
		$this->data['rates_by_kol'] = $rates_by_kol;

		$config = [
			'base_url'     => 'administrator/kols/index/',
			'total_rows'   => $this->data['kols_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Kols List');
		$this->render('backend/standart/administrator/kols/kols_list', $this->data);
	}

	/**
	* Add new kolss
	*
	*/
	public function add()
	{
		$this->is_allowed('kols_add');

		$this->template->title('Kols New');
		$this->render('backend/standart/administrator/kols/kols_add', $this->data);
	}

	/**
	* Add New Kolss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('kols_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[200]');
		$this->form_validation->set_rules('gender', 'Gender', 'trim|required');
		$this->form_validation->set_rules('address', 'Address', 'trim|required');
		$this->form_validation->set_rules('country', 'Country', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('categories[]', 'Categories', 'required');
		$this->form_validation->set_rules('kols_rate_card_file_name', 'Rate Card File', 'trim|required|max_length[500]');
		$this->form_validation->set_rules('avatar_url', 'Avatar URL', 'trim|required|max_length[2000]');
		
		$this->form_validation->set_rules('bank_name', 'Bank Name', 'trim|max_length[100]');
		$this->form_validation->set_rules('bank_account_number', 'Bank Account Number', 'trim|max_length[50]');
		$this->form_validation->set_rules('bank_account_name', 'Bank Account Name', 'trim|max_length[150]');
		$this->form_validation->set_rules('npwp_number', 'NPWP Number', 'trim|max_length[50]');
		$this->form_validation->set_rules('kols_npwp_file_name', 'NPWP File', 'trim|max_length[500]');
		$this->form_validation->set_rules('ktp_number', 'KTP Number', 'trim|max_length[50]');
		$this->form_validation->set_rules('kols_ktp_file_name', 'KTP File', 'trim|max_length[500]');

		if ($this->form_validation->run()) {
			$kols_rate_card_file_uuid = $this->input->post('kols_rate_card_file_uuid');
			$kols_rate_card_file_name = $this->input->post('kols_rate_card_file_name');
			
			$kols_npwp_file_uuid = $this->input->post('kols_npwp_file_uuid');
			$kols_npwp_file_name = $this->input->post('kols_npwp_file_name');
			
			$kols_ktp_file_uuid = $this->input->post('kols_ktp_file_uuid');
			$kols_ktp_file_name = $this->input->post('kols_ktp_file_name');
		
			$save_data = [
				'name' => $this->input->post('name'),
				'phone' => $this->input->post('phone'),
				'email' => $this->input->post('email'),
				'gender' => $this->input->post('gender'),
				'address' => $this->input->post('address'),
				'country' => $this->input->post('country'),
				'avatar_url' => $this->input->post('avatar_url'),
				'bank_name' => $this->input->post('bank_name'),
				'bank_account_number' => $this->input->post('bank_account_number'),
				'bank_account_name' => $this->input->post('bank_account_name'),
				'npwp_number' => $this->input->post('npwp_number'),
				'ktp_number' => $this->input->post('ktp_number'),
				'tier_id' => 1,
				'status' => 'ACTIVE',
			];

			if (!is_dir(FCPATH . '/uploads/kols/')) {
				mkdir(FCPATH . '/uploads/kols/');
			}

			if (!empty($kols_rate_card_file_name)) {
				$kols_rate_card_file_name_copy = date('YmdHis') . '-' . $kols_rate_card_file_name;

				rename(FCPATH . 'uploads/tmp/' . $kols_rate_card_file_uuid . '/' . $kols_rate_card_file_name, 
						FCPATH . 'uploads/kols/' . $kols_rate_card_file_name_copy);

				if (!is_file(FCPATH . '/uploads/kols/' . $kols_rate_card_file_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading file'
						]);
					exit;
				}

				$save_data['rate_card_file'] = $kols_rate_card_file_name_copy;
			}
			
			if (!empty($kols_npwp_file_name)) {
				$kols_npwp_file_name_copy = date('YmdHis') . '-' . $kols_npwp_file_name;
				rename(FCPATH . 'uploads/tmp/' . $kols_npwp_file_uuid . '/' . $kols_npwp_file_name, 
						FCPATH . 'uploads/kols/' . $kols_npwp_file_name_copy);

				if (!is_file(FCPATH . '/uploads/kols/' . $kols_npwp_file_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading NPWP file'
						]);
					exit;
				}
				$save_data['npwp_file'] = $kols_npwp_file_name_copy;
			}
			
			if (!empty($kols_ktp_file_name)) {
				$kols_ktp_file_name_copy = date('YmdHis') . '-' . $kols_ktp_file_name;
				rename(FCPATH . 'uploads/tmp/' . $kols_ktp_file_uuid . '/' . $kols_ktp_file_name, 
						FCPATH . 'uploads/kols/' . $kols_ktp_file_name_copy);

				if (!is_file(FCPATH . '/uploads/kols/' . $kols_ktp_file_name_copy)) {
					echo json_encode([
						'success' => false,
						'message' => 'Error uploading KTP file'
						]);
					exit;
				}
				$save_data['ktp_file'] = $kols_ktp_file_name_copy;
			}
		
			
			$save_kols = $this->model_kols->store($save_data);
            

			if ($save_kols) {
				// Save Categories
				$categories = $this->input->post('categories');
				if (is_array($categories)) {
					foreach ($categories as $cat_id) {
						$this->db->insert('kol_categories', [
							'kol_id' => $save_kols,
							'category_id' => $cat_id,
							'created_at' => date('Y-m-d H:i:s')
						]);
					}
				}

				// Save Platforms
				$platform_ids = $this->input->post('platform_id');
				$platform_usernames = $this->input->post('platform_username');
				if (is_array($platform_ids)) {
					$plats_map = [];
					$all_p = $this->db->get('platforms')->result();
					foreach ($all_p as $ap) {
						$plats_map[$ap->id] = $ap;
					}

					foreach ($platform_ids as $key => $pid) {
						if (!empty($pid) && !empty($platform_usernames[$key])) {
							$p_obj = isset($plats_map[$pid]) ? $plats_map[$pid] : null;
							$auto_url = get_kol_profile_url('', $platform_usernames[$key], $p_obj ? $p_obj->code : '', $p_obj ? $p_obj->name : '');
							$this->db->insert('kol_platforms', [
								'kol_id' => $save_kols,
								'platform_id' => $pid,
								'username' => $platform_usernames[$key],
								'profile_url' => ($auto_url !== 'javascript:void(0);') ? $auto_url : null,
								'is_active' => 1,
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s')
							]);
						}
					}
				}

				$name_slug = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($this->input->post('name')));
				$suggested_username = 'kol_' . substr($name_slug, 0, 20);

				$this->data['kol_id'] = $save_kols;
				$this->data['kol_name'] = $this->input->post('name');
				$this->data['kol_email'] = $this->input->post('email');
				$this->data['suggested_username'] = $suggested_username;

				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_kols;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/kols/view/' . $save_kols, 'View Kols'),
						anchor('administrator/kols', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/kols/view/' . $save_kols, 'View Kols')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kols');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kols');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
		exit;
	}

	/**
	* Phase 2: Add User Account for newly created KOL
	*
	* @return JSON
	*/
	public function add_save_user()
	{
		if (!$this->is_allowed('kols_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$this->load->library('aauth');
		$this->form_validation->set_rules('kol_id', 'KOL ID', 'trim|required|numeric');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[3]|max_length[50]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[200]');
		$this->form_validation->set_rules('full_name', 'Full Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]|max_length[30]');
		$this->form_validation->set_rules('passconf', 'Password Confirmation', 'trim|required|matches[password]');

		if ($this->form_validation->run()) {
			$kol_id = (int)$this->input->post('kol_id');
			$kol = $this->db->get_where('kols', ['id' => $kol_id])->row();
			if (!$kol) {
				echo json_encode([
					'success' => false,
					'message' => 'KOL profile not found in database.'
				]);
				exit;
			}

			$username = trim($this->input->post('username'));
			$email = trim($this->input->post('email'));
			$password = $this->input->post('password');
			$full_name = trim($this->input->post('full_name'));

			if ($this->aauth->user_exist_by_username($username)) {
				echo json_encode([
					'success' => false,
					'message' => 'Username "' . $username . '" is already taken. Please choose another username.'
				]);
				exit;
			}

			if ($this->aauth->user_exist_by_email($email)) {
				echo json_encode([
					'success' => false,
					'message' => 'Email "' . $email . '" is already registered. Please use another email address.'
				]);
				exit;
			}

			$save_user = $this->aauth->create_user($email, $password, $username, [
				'full_name' => $full_name,
				'avatar'    => 'default.png'
			]);

			if ($save_user) {
				// Assign to Kol group (id 7)
				$kol_group = $this->db->get_where('aauth_groups', ['LOWER(name)' => 'kol'])->row();
				if ($kol_group) {
					$this->aauth->add_member($save_user, $kol_group->id);
				}

				// Set user variable 'kol_id'
				$this->aauth->set_user_var('kol_id', $kol_id, $save_user);

				set_message('KOL profile and User login account successfully created.', 'success');
				echo json_encode([
					'success'  => true,
					'message'  => 'KOL profile and User login account created successfully.',
					'redirect' => site_url('administrator/kols')
				]);
				exit;
			} else {
				echo json_encode([
					'success' => false,
					'message' => $this->aauth->print_errors() ?: 'Failed to create user account.'
				]);
				exit;
			}
		} else {
			echo json_encode([
				'success' => false,
				'message' => validation_errors()
			]);
			exit;
		}
	}
	

	
	/**
	 * AJAX endpoint to sync a single platform by its ID and username.
	 */
	public function sync_platform_ajax()
	{
		if (!$this->is_allowed('kols_view', false) && !$this->is_allowed('kols_add', false)) {
			echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
			exit;
		}

		if (session_status() === PHP_SESSION_ACTIVE) {
			session_write_close();
		}

		$platform_id = (int)$this->input->post('platform_id');
		$username    = trim($this->input->post('username') ?? '');
		$kp_id       = (int)$this->input->post('kol_platform_id');

		if (empty($platform_id) || empty($username)) {
			echo json_encode(['success' => false, 'message' => 'Platform ID dan Username harus diisi.']);
			exit;
		}

		$platform = $this->db->get_where('platforms', ['id' => $platform_id])->row();
		if (!$platform) {
			echo json_encode(['success' => false, 'message' => 'Platform tidak valid.']);
			exit;
		}

		$this->load->model('kol_platforms/model_kol_platforms');
		$result = $this->model_kol_platforms->sync_profile_raw($platform->code, $username);

		// If kol_platform_id provided and sync succeeded, persist to DB
		if (!empty($result['success']) && !empty($result['data']) && $kp_id > 0) {
			$d = $result['data'];
			$this->db->where('id', $kp_id)->update('kol_platforms', [
				'followers'      => $d['followers'],
				'avg_views'      => $d['avg_views'],
				'avg_er'         => $d['avg_er'],
				'last_synced_at' => date('Y-m-d H:i:s')
			]);
		}

		$result['csrf_hash'] = $this->security->get_csrf_hash();
		$this->output
			->set_content_type('application/json')
			->set_output(json_encode($result));
		return;
	}
	
	/**
	 * AJAX endpoint to sync Apify intelligence data (Followers, Avg Views, Avg ER)
	 * for all connected platforms of a KOL.
	 */
	public function sync_apify_intelligence($kol_id = null)
	{
		// Prevent PHP timeout on multi-platform scraping runs
		@set_time_limit(300);
		@ini_set('max_execution_time', 300);

		if (!$this->is_allowed('kols_view', false)) {
			echo json_encode(['success' => false, 'message' => 'Akses ditolak. Silakan login ulang.']);
			exit;
		}

		// Release PHP session lock immediately so the user can freely reload and navigate
		if (session_status() === PHP_SESSION_ACTIVE) {
			session_write_close();
		}

		$kol_id = (int)($kol_id ?: $this->input->post('kol_id'));
		if ($kol_id <= 0) {
			echo json_encode(['success' => false, 'message' => 'ID KOL tidak valid.']);
			exit;
		}

		$kol = $this->db->get_where('kols', ['id' => $kol_id])->row();
		if (!$kol) {
			echo json_encode(['success' => false, 'message' => 'KOL tidak ditemukan.']);
			exit;
		}

		$platforms = $this->db->select('kol_platforms.*, platforms.code as platform_code, platforms.name as platform_name')
			->from('kol_platforms')
			->join('platforms', 'platforms.id = kol_platforms.platform_id', 'INNER')
			->where('kol_platforms.kol_id', $kol_id)
			->get()->result();

		if (empty($platforms)) {
			echo json_encode(['success' => false, 'message' => 'KOL ini belum memiliki akun platform / media sosial yang terhubung.']);
			exit;
		}

		$this->load->model('kol_platforms/model_kol_platforms');

		$synced_platforms = [];
		$total_followers = 0;

		foreach ($platforms as $kp) {
			$res = $this->model_kol_platforms->sync_profile_raw($kp->platform_code, $kp->username);
			if (!empty($res['success']) && !empty($res['data'])) {
				$d = $res['data'];
				$update_plat = [
					'followers'      => $d['followers'],
					'avg_views'      => $d['avg_views'],
					'avg_er'         => $d['avg_er'],
					'last_synced_at' => date('Y-m-d H:i:s')
				];
				$this->db->where('id', $kp->id)->update('kol_platforms', $update_plat);

				$total_followers     += (int)$d['followers'];

				$synced_platforms[] = [
					'platform_name' => $kp->platform_name,
					'username'      => $kp->username,
					'followers'     => $d['followers'],
					'avg_views'     => $d['avg_views'],
					'avg_er'        => round($d['avg_er'] * 100, 2)
				];
			}
		}

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success'               => true,
				'message'               => "Sinkronisasi Apify selesai untuk " . count($synced_platforms) . " platform!",
				'synced_platforms'      => $synced_platforms,
				'last_synced'           => date('d M Y, H:i'),
				'csrf_hash'             => $this->security->get_csrf_hash()
			]));
		return;
	}
	


		/**
	* View view Kolss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('kols_view');

		$this->data['kols'] = $this->model_kols->join_avaiable()->filter_avaiable()->find($id);
		$this->data['kol_platforms'] = $this->db->select('kol_platforms.*, platforms.name as platform_name, platforms.code as platform_code, platforms.icon_url, platforms.color_hex')
			->from('kol_platforms')
			->join('platforms', 'platforms.id = kol_platforms.platform_id', 'LEFT')
			->where('kol_platforms.kol_id', $id)
			->get()->result();
		$this->data['kol_categories'] = $this->db->select('kol_categories.*, categories.name as category_name')
			->from('kol_categories')
			->join('categories', 'categories.id = kol_categories.category_id', 'INNER')
			->where('kol_categories.kol_id', $id)
			->get()->result();

		// -------------------------------------------------------
		// Task 6: ER (FROM APIFY DATA)
		// -------------------------------------------------------
		$total_er = 0;
		$platforms_count = count($this->data['kol_platforms']);

		if ($platforms_count > 0) {
			foreach ($this->data['kol_platforms'] as $kp) {
				$total_er += (float)($kp->avg_er ?? 0);
			}
			$kol_er = $total_er / $platforms_count;
		} else {
			$kol_er = null;
		}

		$kol_er_pct = ($kol_er !== null) ? round($kol_er * 100, 2) : null;
		$this->data['kol_er_pct']      = $kol_er_pct;

		// -------------------------------------------------------
		// Benchmark ER — self-excluded, null-guarded
		$kol_tier_id = $this->data['kols'] ? (int)$this->data['kols']->tier_id : 0;

		// Tier benchmark — EXCLUDE this KOL from the peer average
		$benchmark_row = $this->db
			->select('AVG(pm.engagement_rate) AS avg_er')
			->from('kols k')
			->join('campaign_kols ck',   'ck.kol_id = k.id',            'LEFT')
			->join('tasks ta',           'ta.campaign_kol_id = ck.id',  'LEFT')
			->join('postings p',         'p.task_id = ta.id',           'LEFT')
			->join('posting_metrics pm', 'pm.id = p.latest_metric_id',  'LEFT')
			->where('k.tier_id', $kol_tier_id)
			->where('k.id !=', (int)$id) // self-exclusion — not comparing against self
			->get()->row();
		$benchmark_er = ($benchmark_row && $benchmark_row->avg_er !== null)
			? (float)$benchmark_row->avg_er
			: null; // null = no peer data in this tier

		// 4-state null guard — prevents silent "always Underperform" bug
		if ($benchmark_er === null) {
			$benchmark_badge = '<span class="badge-status badge-default-soft">No Benchmark</span>';
		} elseif ($kol_er === null) {
			$benchmark_badge = '<span class="badge-status badge-default-soft">No ER Data</span>';
		} elseif ($kol_er >= $benchmark_er) {
			$benchmark_badge = '<span class="badge-status badge-success-soft">Outperform</span>';
		} else {
			$benchmark_badge = '<span class="badge-status badge-warning-soft">Underperform</span>';
		}

		$this->data['benchmark_er']    = $benchmark_er;
		$this->data['kol_er']          = $kol_er;
		$this->data['benchmark_badge'] = $benchmark_badge;

		// Last 5 ER snapshots for sparkline (CSS bar chart in view)
		$er_trend = $this->db
			->select('pm.engagement_rate, pm.snapshot_at')
			->from('posting_metrics pm')
			->join('postings p',       'p.id  = pm.posting_id',         'INNER')
			->join('tasks ta',         'ta.id = p.task_id',             'INNER')
			->join('campaign_kols ck', 'ck.id = ta.campaign_kol_id',   'INNER')
			->where('ck.kol_id', $id)
			->where('pm.engagement_rate IS NOT NULL')
			->order_by('pm.snapshot_at', 'DESC')
			->limit(5)
			->get()->result();

		// Override the profile ER with the latest actual campaign ER if available
		if (!empty($er_trend)) {
			$kol_er = (float)$er_trend[0]->engagement_rate;
			$this->data['kol_er'] = $kol_er;
			$this->data['kol_er_pct'] = round($kol_er * 100, 2);

			if ($benchmark_er === null) {
				$this->data['benchmark_badge'] = '<span class="badge-status badge-default-soft">No Benchmark</span>';
			} elseif ($kol_er >= $benchmark_er) {
				$this->data['benchmark_badge'] = '<span class="badge-status badge-success-soft">Outperform</span>';
			} else {
				$this->data['benchmark_badge'] = '<span class="badge-status badge-warning-soft">Underperform</span>';
			}
		}

		$this->data['er_trend'] = array_reverse($er_trend); // oldest first for left-to-right sparkline

		// -------------------------------------------------------
		// Task 7: Anomaly Detection — Authenticity Score
		// -------------------------------------------------------
		$thresholds = $this->_get_anomaly_thresholds();

		// Already available from existing query: $this->data['kol_platforms']
		$platforms = $this->data['kol_platforms'];

		// Tier name from $this->data['kols']
		$tier_name = $this->data['kols']->tiers_name ?? 'Micro';
		$er_threshold = $thresholds['er_by_tier'][$tier_name] ?? $thresholds['er_default'];

		// --- Sub-score 1: ER Score (0–100) ---
		// Aggregate the KOL's weighted avg ER across all platforms
		$total_followers = 0;
		$weighted_er_sum = 0;
		if (!empty($platforms)) {
			foreach ($platforms as $p) {
				$f = (int)($p->followers ?? 0);
				$e = (float)($p->avg_er ?? 0);
				$total_followers += $f;
				$weighted_er_sum += $e * $f;
			}
		}
		$agg_er = ($total_followers > 0) ? $weighted_er_sum / $total_followers : null;

		if ($agg_er === null || $total_followers == 0) {
			$er_score = null;  // no data
		} elseif ($agg_er >= $er_threshold) {
			$er_score = 0;     // healthy ER → no suspicion
		} else {
			// Linear scale: how far below threshold (capped at 100)
			$er_score = min(100, round((1 - ($agg_er / $er_threshold)) * 100));
		}

		// --- Sub-score 2: View Ratio Score (0–100) ---
		$total_avg_views = 0;
		if (!empty($platforms)) {
			foreach ($platforms as $p) {
				$total_avg_views += (int)($p->avg_views ?? 0);
			}
		}
		$view_ratio = ($total_followers > 0) ? $total_avg_views / $total_followers : null;

		$view_ratio_score = null;
		if ($view_ratio !== null) {
			foreach ($thresholds['view_ratio'] as $rule) {
				if ($view_ratio >= $rule['min']) {
					$view_ratio_score = $rule['score'];
					break;  // first match (highest ratio) wins
				}
			}
		}

		// --- Sub-score 3: Growth Score (0–100) from kol_profile_snapshots ---
		// BUGFIX: Group by DATE and sum the MAX followers per platform to prevent cross-platform snapshot mismatches.
		$snapshots_query = "
			SELECT DATE(snapshot_date) as snap_date, SUM(max_followers) as total_followers 
			FROM (
				SELECT platform_id, DATE(snapshot_date) as snapshot_date, MAX(followers_count) as max_followers 
				FROM kol_profile_snapshots 
				WHERE kol_id = " . (int)$id . " 
				GROUP BY platform_id, DATE(snapshot_date)
			) AS daily_max 
			GROUP BY snap_date 
			ORDER BY snap_date DESC 
			LIMIT 5
		";
		$snapshots = $this->db->query($snapshots_query)->result();

		$growth_score = null;
		$growth_rate = null;
		if (!empty($snapshots) && count($snapshots) >= 2) {
			$newest = (int)$snapshots[0]->total_followers;
			$oldest = (int)$snapshots[count($snapshots) - 1]->total_followers;
			if ($oldest > 0) {
				$growth_rate = ($newest - $oldest) / $oldest;
				$growth_score = 0;
				foreach ($thresholds['growth_rate'] as $rule) {
					if ($growth_rate < $rule['max']) {
						$growth_score = $rule['score'];
						break;
					}
				}
			}
		}

		// --- Composite Anomaly Score ---
		$w = $thresholds['weights'];
		$components = [
			'er'         => $er_score,
			'view_ratio' => $view_ratio_score,
			'growth'     => $growth_score,
		];
		$active_weight = 0;
		$weighted_sum  = 0;
		foreach ($components as $key => $val) {
			if ($val !== null) {
				$weighted_sum  += $val * $w[$key];
				$active_weight += $w[$key];
			}
		}
		$anomaly_score = ($active_weight > 0) ? round($weighted_sum / $active_weight * 100) / 100 : null;
		$anomaly_score = ($anomaly_score !== null) ? (int)round($anomaly_score) : null;

		// --- Risk Level ---
		$risk_level = null;
		$risk_badge = null;
		$risk_color = '#94a3b8';
		if ($anomaly_score !== null) {
			foreach ($thresholds['risk_levels'] as $info) {
				if ($anomaly_score < $info['max']) {
					$risk_level = $info['label'];
					$risk_badge = $info['badge'];
					$risk_color = $info['color'];
					break;
				}
			}
		}

		// Pass to view
		$this->data['anomaly_score']    = $anomaly_score;
		$this->data['er_score']         = $er_score;
		$this->data['view_ratio_score'] = $view_ratio_score;
		$this->data['growth_score']     = $growth_score;
		$this->data['risk_level']       = $risk_level;
		$this->data['risk_badge']       = $risk_badge;
		$this->data['risk_color']       = $risk_color;
		$this->data['anomaly_breakdown'] = [
			'er_threshold'   => $er_threshold,
			'agg_er'         => $agg_er,
			'view_ratio'     => $view_ratio,
			'growth_rate'    => $growth_rate,
			'snapshot_count' => !empty($snapshots) ? count($snapshots) : 0,
		];

		// Phase 3: Fetch KOL Reviews & Rating Summary
		$reviews = $this->db
			->select('kr.*, u.full_name as reviewer_name, u.username as reviewer_username, c.name as campaign_name')
			->from('kol_reviews kr')
			->join('aauth_users u', 'u.id = kr.reviewed_by', 'LEFT')
			->join('campaign_kols ck', 'ck.id = kr.campaign_kol_id', 'LEFT')
			->join('campaigns c', 'c.id = ck.campaign_id', 'LEFT')
			->where('kr.kol_id', $id)
			->order_by('kr.created_at', 'DESC')
			->get()->result();

		$avg_comm = 0; $avg_dead = 0; $avg_qual = 0; $avg_overall = 0;
		if (!empty($reviews)) {
			$cnt = count($reviews);
			$sum_comm = array_sum(array_column($reviews, 'communication_rating'));
			$sum_dead = array_sum(array_column($reviews, 'deadline_adherence_rating'));
			$sum_qual = array_sum(array_column($reviews, 'content_quality_rating'));
			$sum_over = array_sum(array_column($reviews, 'overall_rating'));
			$avg_comm = round($sum_comm / $cnt, 1);
			$avg_dead = round($sum_dead / $cnt, 1);
			$avg_qual = round($sum_qual / $cnt, 1);
			$avg_overall = round($sum_over / $cnt, 2);
		}

		$this->data['reviews'] = $reviews;
		$this->data['review_summary'] = [
			'count' => count($reviews),
			'avg_comm' => $avg_comm,
			'avg_dead' => $avg_dead,
			'avg_qual' => $avg_qual,
			'avg_overall' => $avg_overall,
		];

		// Available campaigns for review tagging
		$this->data['kol_campaigns'] = $this->db
			->select('ck.id as campaign_kol_id, c.id as campaign_id, c.name as campaign_name')
			->from('campaign_kols ck')
			->join('campaigns c', 'c.id = ck.campaign_id', 'INNER')
			->where('ck.kol_id', $id)
			->order_by('c.id', 'DESC')
			->get()->result();

		// Lookalike / Similar KOL Recommendations (Hybrid Jaccard Engine)
		$this->data['similar_kols'] = $this->model_kols->get_similar_kols($id, 4);

		// Active campaigns for assignment modal
		$this->data['all_active_campaigns'] = $this->db->select('id, name, status')
			->where_in('status', ['DRAFT', 'ACTIVE', 'OPEN_REGISTRATION'])
			->order_by('id', 'DESC')
			->get('campaigns')->result();

		$this->data['gmv_summary'] = null;
		$this->data['gmv_top']     = [];
		try {
			$tbl_exists = $this->db->query("SHOW TABLES LIKE 'kol_shop_snapshots'")->num_rows();
			if ($tbl_exists) {
				// Latest snapshot date for this KOL
				$latest_date = $this->db
					->select_max('snapshot_at')
					->where('kol_id', $id)
					->get('kol_shop_snapshots')
					->row();

				if ($latest_date && !empty($latest_date->snapshot_at)) {
					// Aggregate summary from latest snapshot batch
					$this->data['gmv_summary'] = $this->db
						->select('COUNT(*) as product_count, SUM(gmv_estimate) as total_gmv, SUM(sold_count) as total_sold, snapshot_at')
						->where('kol_id', $id)
						->where('snapshot_at', $latest_date->snapshot_at)
						->get('kol_shop_snapshots')
						->row();

					// Top 15 products by GMV from latest snapshot
					$this->data['gmv_top'] = $this->db
						->where('kol_id', $id)
						->where('snapshot_at', $latest_date->snapshot_at)
						->order_by('gmv_estimate', 'DESC')
						->limit(15)
						->get('kol_shop_snapshots')
						->result();
				}
			}
		} catch (Throwable $e) {
			log_message('error', '[Kols::view] GMV data load failed: ' . $e->getMessage());
		}

		$this->template->title('Kols Detail');
		$this->render('backend/standart/administrator/kols/kols_view', $this->data);
	}

	/**
	 * Centralized Threshold Configuration for Anomaly Detection
	 *
	 * @return array
	 */
	private function _get_anomaly_thresholds()
	{
		return [
			// ER thresholds per tier name (minimum expected ER as decimal, e.g. 0.03 = 3%)
			'er_by_tier' => [
				'Nano'  => 0.03,   // 3%
				'Micro' => 0.015,  // 1.5% (default fallback)
				'Mid'   => 0.01,   // 1.0%
				'Macro' => 0.005,  // 0.5%
				'Mega'  => 0.003,  // 0.3%
			],
			'er_default' => 0.015,

			// View ratio = avg_views / followers → penalty score
			// Format: [min => ratio, score => penalty] — evaluated top-down, first match wins
			'view_ratio' => [
				['min' => 0.15, 'score' => 0],    // ≥15% → no suspicion
				['min' => 0.10, 'score' => 15],
				['min' => 0.05, 'score' => 40],
				['min' => 0.02, 'score' => 70],
				['min' => 0.00, 'score' => 100],  // <2% → maximum suspicion
			],

			// Growth rate between oldest & newest snapshot → penalty score
			// Format: [max => growth_rate, score => penalty] — evaluated top-down, first match wins
			'growth_rate' => [
				['max' => 0.10,          'score' => 0],    // <10% growth
				['max' => 0.25,          'score' => 20],   // 10% - 24.9%
				['max' => 0.50,          'score' => 50],   // 25% - 49.9%
				['max' => 1.00,          'score' => 80],   // 50% - 99.9%
				['max' => PHP_FLOAT_MAX, 'score' => 100],  // ≥100% → maximum suspicion
			],

			// Component weights for composite score
			'weights' => [
				'er'         => 0.40,
				'view_ratio' => 0.30,
				'growth'     => 0.30,
			],

			// Risk level brackets [max => max_score_exclusive, label => ..., badge => ..., color => ...]
			'risk_levels' => [
				['max' => 26,  'label' => 'LOW',      'badge' => 'label-success', 'color' => '#16a34a'],
				['max' => 51,  'label' => 'MEDIUM',   'badge' => 'label-warning', 'color' => '#eab308'],
				['max' => 76,  'label' => 'HIGH',     'badge' => 'label-danger',  'color' => '#f97316'],
				['max' => 101, 'label' => 'CRITICAL', 'badge' => 'label-danger',  'color' => '#dc2626'],
			],
		];
	}

	/**
	* Toggle Blacklist Status for KOL
	*
	* @param int $id
	*/
	public function toggle_blacklist($id = null)
	{
		if (!$this->is_allowed('kols_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$kol = $this->model_kols->find($id);
		if (!$kol) {
			echo json_encode(['success' => false, 'message' => 'KOL tidak ditemukan']);
			exit;
		}

		$is_blacklisted = (int)$kol->is_blacklisted;
		$new_status = $is_blacklisted ? 0 : 1;
		$reason = $new_status ? trim($this->input->post('reason') ?: 'Tidak disebutkan') : null;
		$blacklisted_at = $new_status ? date('Y-m-d H:i:s') : null;

		$this->db->where('id', $id)->update('kols', [
			'is_blacklisted'   => $new_status,
			'blacklist_reason' => $reason,
			'blacklisted_at'   => $blacklisted_at,
			'updated_at'       => date('Y-m-d H:i:s')
		]);

		$msg = $new_status ? "KOL {$kol->name} telah dimasukkan ke daftar Blacklist." : "KOL {$kol->name} telah dihapus dari daftar Blacklist.";

		if ($this->input->is_ajax_request()) {
			echo json_encode([
				'success' => true,
				'message' => $msg,
				'is_blacklisted' => $new_status,
				'blacklist_reason' => $reason,
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
			exit;
		}

		set_message($msg, 'success');
		redirect_back();
	}

	/**
	* Add Performance Review for KOL
	*
	* @param int $kol_id
	*/
	public function add_review($kol_id = null)
	{
		if (!$this->is_allowed('kols_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$kol = $this->model_kols->find($kol_id);
		if (!$kol) {
			echo json_encode(['success' => false, 'message' => 'KOL tidak ditemukan']);
			exit;
		}

		$comm = max(1, min(5, (int)$this->input->post('communication_rating')));
		$dead = max(1, min(5, (int)$this->input->post('deadline_adherence_rating')));
		$qual = max(1, min(5, (int)$this->input->post('content_quality_rating')));
		$notes = trim($this->input->post('review_notes') ?: '');
		$campaign_kol_id = $this->input->post('campaign_kol_id') ? (int)$this->input->post('campaign_kol_id') : null;

		$overall = round(($comm + $dead + $qual) / 3, 2);
		$user_id = get_user_data('id') ?: 1;

		$this->db->insert('kol_reviews', [
			'kol_id'                    => $kol_id,
			'campaign_kol_id'           => $campaign_kol_id,
			'communication_rating'      => $comm,
			'deadline_adherence_rating' => $dead,
			'content_quality_rating'    => $qual,
			'overall_rating'            => $overall,
			'review_notes'              => $notes,
			'reviewed_by'               => $user_id,
			'created_at'                => date('Y-m-d H:i:s')
		]);

		$msg = 'Review performa KOL berhasil disimpan!';

		if ($this->input->is_ajax_request()) {
			echo json_encode([
				'success' => true,
				'message' => $msg,
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
			exit;
		}

		set_message($msg, 'success');
		redirect('administrator/kols/view/' . $kol_id);
	}

	/**
	* View Performance Card
	*
	* @var $id String
	*/
	public function performance_card($id = null)
	{
		$this->is_allowed('kols_view');

		$timestamp = time();
		$signature = hash_hmac('sha256', $id . $timestamp, KOL_SHARED_SECRET);
		$base_url = base_url();
		$parsed_url = parse_url($base_url);
		if (strpos($base_url, 'kol-system') !== false) {
			$plugin_base_url = str_replace('kol-system', 'kol-system-card-plugin', $base_url);
			$plugin_base_url = preg_replace('/:80(\/|$)/', '$1', $plugin_base_url);
		} elseif (isset($parsed_url['port']) && !in_array((int)$parsed_url['port'], [80, 443])) {
			$plugin_port = (int)$parsed_url['port'] + 1;
			$plugin_base_url = $parsed_url['scheme'] . "://" . $parsed_url['host'] . ":" . $plugin_port . "/";
		} else {
			$plugin_base_url = $base_url;
		}
		$this->data['iframe_url'] = $plugin_base_url . "administrator/performance_card/view/{$id}?t={$timestamp}&sig={$signature}&is_iframe=true";
		$this->data['kol_id'] = $id;

		$this->template->title('Performance Card');
		$this->render('backend/standart/administrator/kols/kols_performance_card', $this->data);
	}

	/**
	* Approve KOL
	*
	* @var $id String
	*/
	public function approve($id = null)
	{
		if (!$this->is_allowed('kols_approval', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$kol = $this->model_kols->find($id);
		if (!$kol) {
			echo json_encode([
				'success' => false,
				'message' => 'KOL not found'
			]);
			exit;
		}

		$note = $this->input->post('note');
		$user_id = get_user_data('id');
		$now = date('Y-m-d H:i:s');

		$update_data = [
			'approval_status' => 'APPROVED',
			'status'          => 'active',
		];

		if (!empty($note)) {
			$update_data['approval_note'] = $note;
		}

		$updated = $this->model_kols->change($id, $update_data);

		if ($updated !== false) {
			$this->db->insert('kol_approval_histories', [
				'kol_id'     => $id,
				'action'     => 'APPROVED',
				'note'       => !empty($note) ? $note : 'Approved by Leader',
				'acted_by'   => $user_id,
				'created_at' => $now
			]);

			if ($this->input->is_ajax_request()) {
				echo json_encode([
					'success' => true,
					'message' => 'KOL "' . $kol->name . '" has been successfully approved.'
				]);
				exit;
			} else {
				set_message('KOL "' . $kol->name . '" has been successfully approved.', 'success');
				redirect_back();
			}
		} else {
			if ($this->input->is_ajax_request()) {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to approve KOL.'
				]);
				exit;
			} else {
				set_message('Failed to approve KOL.', 'error');
				redirect_back();
			}
		}
	}

	/**
	* Reject KOL
	*
	* @var $id String
	*/
	public function reject($id = null)
	{
		if (!$this->is_allowed('kols_approval', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$kol = $this->model_kols->find($id);
		if (!$kol) {
			echo json_encode([
				'success' => false,
				'message' => 'KOL not found'
			]);
			exit;
		}

		$note = $this->input->post('note');
		$user_id = get_user_data('id');
		$now = date('Y-m-d H:i:s');

		$update_data = [
			'approval_status' => 'REJECTED',
		];

		if (!empty($note)) {
			$update_data['approval_note'] = $note;
		}

		$updated = $this->model_kols->change($id, $update_data);

		if ($updated !== false) {
			$this->db->insert('kol_approval_histories', [
				'kol_id'     => $id,
				'action'     => 'REJECTED',
				'note'       => !empty($note) ? $note : 'Rejected by Leader',
				'acted_by'   => $user_id,
				'created_at' => $now
			]);

			if ($this->input->is_ajax_request()) {
				echo json_encode([
					'success' => true,
					'message' => 'KOL "' . $kol->name . '" has been rejected.'
				]);
				exit;
			} else {
				set_message('KOL "' . $kol->name . '" has been rejected.', 'warning');
				redirect_back();
			}
		} else {
			if ($this->input->is_ajax_request()) {
				echo json_encode([
					'success' => false,
					'message' => 'Failed to reject KOL.'
				]);
				exit;
			} else {
				set_message('Failed to reject KOL.', 'error');
				redirect_back();
			}
		}
	}
	
	/**
	* delete Kolss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('kols_delete');

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
		$error_kols = [];

		foreach ($ids as $k_id) {
			$result = $this->_remove($k_id);
			if ($result['status'] === true) {
				$deleted_count++;
			} else {
				$name = !empty($result['name']) ? $result['name'] : "ID #{$k_id}";
				if ($result['reason'] === 'in_campaign') {
					$error_kols[] = "{$name} (masih terdaftar dalam {$result['campaign_count']} campaign)";
				} else {
					$error_kols[] = $name;
				}
			}
		}

		if ($deleted_count > 0 && empty($error_kols)) {
			if (count($ids) == 1) {
				set_message(cclang('has_been_deleted', 'KOL'), 'success');
			} else {
				set_message("{$deleted_count} KOL berhasil dihapus.", 'success');
			}
		} elseif ($deleted_count > 0 && !empty($error_kols)) {
			$err_msg = "{$deleted_count} KOL berhasil dihapus, namun beberapa KOL gagal: " . implode(', ', $error_kols);
			set_message($err_msg, 'warning');
		} else {
			if (!empty($error_kols)) {
				set_message("Gagal menghapus KOL: " . implode(', ', $error_kols), 'error');
			} else {
				set_message(cclang('error_delete', 'KOL'), 'error');
			}
		}

		redirect_back();
	}

	/**
	* delete Kolss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$kols = $this->model_kols->find($id);
		if (!$kols) {
			return ['status' => false, 'name' => null, 'reason' => 'not_found'];
		}

		$kol_name = $kols->name;

		// Check if KOL is assigned to any campaign
		$campaign_count = $this->db->where('kol_id', $id)->count_all_results('campaign_kols');
		if ($campaign_count > 0) {
			return [
				'status' => false,
				'name' => $kol_name,
				'reason' => 'in_campaign',
				'campaign_count' => $campaign_count
			];
		}

		// Perform deletion in transaction
		$this->db->trans_start();

		// Clean up dependent child records
		$this->db->delete('campaign_applications', ['kol_id' => $id]);
		$this->db->delete('kol_approval_histories', ['kol_id' => $id]);
		$this->db->delete('kol_categories', ['kol_id' => $id]);
		
		$platforms = $this->db->get_where('kol_platforms', ['kol_id' => $id])->result();
		foreach ($platforms as $p) {
			$this->db->delete('kol_rate_cards', ['kol_platform_id' => $p->id]);
		}
		$this->db->delete('kol_platforms', ['kol_id' => $id]);
		$this->db->delete('score_histories', ['kol_id' => $id]);
		
		$this->model_kols->remove($id);

		$this->db->trans_complete();

		if ($this->db->trans_status() === TRUE) {
			// Clean up uploaded files only after DB commit
			if (!empty($kols->profile_url)) {
				$path = FCPATH . '/uploads/kols/' . $kols->profile_url;
				if (is_file($path)) {
					@unlink($path);
				}
			}
			
			if (!empty($kols->rate_card_file)) {
				foreach ((array) explode(',', $kols->rate_card_file) as $filename) {
					$path = FCPATH . '/uploads/kols/' . trim($filename);
					if (is_file($path)) {
						@unlink($path);
					}
				}
			}

			return ['status' => true, 'name' => $kol_name, 'reason' => null];
		} else {
			return ['status' => false, 'name' => $kol_name, 'reason' => 'db_error'];
		}
	}
	
	/**
	* Upload Image Kols	* 
	* @return JSON
	*/
	public function upload_profile_url_file()
	{
		if (!$this->is_allowed('kols_add', false) && !$this->is_allowed('kols_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'kols',
		]);
	}

	/**
	* Delete Image Kols	* 
	* @return JSON
	*/
	public function delete_profile_url_file($uuid)
	{
		if (!$this->is_allowed('kols_delete', false) && !$this->is_allowed('kols_update', false) && !$this->is_allowed('kols_add', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'profile_url', 
            'upload_path_tmp'   => 'uploads/tmp/',
            'table_name'        => 'kols',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/kols/'
        ]);
	}

	/**
	* Get Image Kols	* 
	* @return JSON
	*/
	public function get_profile_url_file($id)
	{
		if (!$this->is_allowed('kols_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$kols = $this->model_kols->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'profile_url', 
            'table_name'        => 'kols',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/kols/',
            'delete_endpoint'   => 'administrator/kols/delete_profile_url_file'
        ]);
	}
	
	
	/**
	* Upload Image Kols	* 
	* @return JSON
	*/
	public function upload_rate_card_file_file()
	{
		if (!$this->is_allowed('kols_add', false) && !$this->is_allowed('kols_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'kols',
		]);
	}

	/**
	* Delete Image Kols	* 
	* @return JSON
	*/
	public function delete_rate_card_file_file($uuid)
	{
		if (!$this->is_allowed('kols_delete', false) && !$this->is_allowed('kols_update', false) && !$this->is_allowed('kols_add', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'rate_card_file', 
            'upload_path_tmp'   => 'uploads/tmp/',
            'table_name'        => 'kols',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/kols/'
        ]);
	}

	/**
	* Get Image Kols	* 
	* @return JSON
	*/
	public function get_rate_card_file_file($id)
	{
		if (!$this->is_allowed('kols_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$kols = $this->model_kols->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'rate_card_file', 
            'table_name'        => 'kols',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/kols/',
            'delete_endpoint'   => 'administrator/kols/delete_rate_card_file_file'
        ]);
	}

	public function upload_npwp_file_file()
	{
		if (!$this->is_allowed('kols_add', false) && !$this->is_allowed('kols_update', false)) {
			echo json_encode(['success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')]);
			exit;
		}
		$uuid = $this->input->post('qquuid');
		echo $this->upload_file(['uuid' => $uuid, 'table_name' => 'kols']);
	}

	public function delete_npwp_file_file($uuid)
	{
		if (!$this->is_allowed('kols_delete', false) && !$this->is_allowed('kols_update', false) && !$this->is_allowed('kols_add', false)) {
			echo json_encode(['success' => false, 'error' => cclang('sorry_you_do_not_have_permission_to_access')]);
			exit;
		}
		echo $this->delete_file(['uuid' => $uuid, 'delete_by' => $this->input->get('by'), 'field_name' => 'npwp_file', 'upload_path_tmp' => './uploads/tmp/', 'table_name' => 'kols', 'primary_key' => 'id', 'upload_path' => 'uploads/kols/']);
	}

	public function get_npwp_file_file($id)
	{
		if (!$this->is_allowed('kols_update', false)) {
			echo json_encode(['success' => false, 'message' => 'Image not loaded, you do not have permission to access']);
			exit;
		}
		$kols = $this->model_kols->find($id);
		echo $this->get_file(['uuid' => $id, 'delete_by' => 'id', 'field_name' => 'npwp_file', 'table_name' => 'kols', 'primary_key' => 'id', 'upload_path' => 'uploads/kols/', 'delete_endpoint' => 'administrator/kols/delete_npwp_file_file']);
	}
	
	public function upload_ktp_file_file()
	{
		if (!$this->is_allowed('kols_add', false) && !$this->is_allowed('kols_update', false)) {
			echo json_encode(['success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')]);
			exit;
		}
		$uuid = $this->input->post('qquuid');
		echo $this->upload_file(['uuid' => $uuid, 'table_name' => 'kols']);
	}

	public function delete_ktp_file_file($uuid)
	{
		if (!$this->is_allowed('kols_delete', false) && !$this->is_allowed('kols_update', false) && !$this->is_allowed('kols_add', false)) {
			echo json_encode(['success' => false, 'error' => cclang('sorry_you_do_not_have_permission_to_access')]);
			exit;
		}
		echo $this->delete_file(['uuid' => $uuid, 'delete_by' => $this->input->get('by'), 'field_name' => 'ktp_file', 'upload_path_tmp' => './uploads/tmp/', 'table_name' => 'kols', 'primary_key' => 'id', 'upload_path' => 'uploads/kols/']);
	}

	public function get_ktp_file_file($id)
	{
		if (!$this->is_allowed('kols_update', false)) {
			echo json_encode(['success' => false, 'message' => 'Image not loaded, you do not have permission to access']);
			exit;
		}
		$kols = $this->model_kols->find($id);
		echo $this->get_file(['uuid' => $id, 'delete_by' => 'id', 'field_name' => 'ktp_file', 'table_name' => 'kols', 'primary_key' => 'id', 'upload_path' => 'uploads/kols/', 'delete_endpoint' => 'administrator/kols/delete_ktp_file_file']);
	}
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('kols_export');

		$this->model_kols->export('kols', 'kols');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('kols_export');

		$this->model_kols->pdf('kols', 'kols');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('kols_export');

		$table = $title = 'kols';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_kols->find($id);
        $fields = $result->list_fields();

        $content = $this->pdf->loadHtmlPdf('core_template/pdf/pdf_single', [
            'data' => $data,
            'fields' => $fields,
            'title' => $title
        ], TRUE);

        $this->pdf->initialize($config);
        $this->pdf->pdf->SetDisplayMode('fullpage');
        $this->pdf->writeHTML($content);
        if (ob_get_length()) {
            ob_end_clean();
        }
        $this->pdf->Output($table.'_'.$id.'.pdf', 'I');
	}

	/**
	 * Task 7: Multi-sheet Excel Import
	 * POST /administrator/kols/import_excel
	 *
	 * Sheet names expected: "KOL Profiles" (Sheet 1) and "Rate Cards" (Sheet 2).
	 * Falls back to index 0 / 1 if named sheets are not found, with a warning.
	 *
	 * Dedup key: email (primary), phone (fallback). Name alone is NOT a dedup key.
	 * All DB writes use CI3 Query Builder (parameterized). Wrapped in a transaction.
	 * File validation: extension + MIME type + max 5 MB + max 500 data rows.
	 */
	public function import_excel()
	{
		if (!$this->is_allowed('kols_add', false)) {
			echo json_encode(['success' => false, 'message' => 'Akses ditolak.']);
			exit;
		}

		// --- File validation ---
		$file = $_FILES['import_file'] ?? null;
		if (empty($file) || $file['error'] !== UPLOAD_ERR_OK) {
			echo json_encode(['success' => false, 'message' => 'File tidak ditemukan atau gagal diupload.']);
			exit;
		}

		$ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
		if ($ext !== 'xlsx') {
			echo json_encode(['success' => false, 'message' => 'File harus berformat .xlsx.']);
			exit;
		}

		// MIME validation — rejects renamed .php or other non-xlsx files
		$finfo    = new finfo(FILEINFO_MIME_TYPE);
		$mimetype = $finfo->file($file['tmp_name']);
		$allowed_mimes = [
			'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'application/zip', // some servers report this for xlsx
		];
		if (!in_array($mimetype, $allowed_mimes)) {
			echo json_encode(['success' => false, 'message' => 'File MIME type tidak valid. Pastikan file adalah .xlsx asli.']);
			exit;
		}

		if ($file['size'] > 5 * 1024 * 1024) {
			echo json_encode(['success' => false, 'message' => 'Ukuran file maksimal 5 MB.']);
			exit;
		}

		// --- Load PhpSpreadsheet ---
		require_once FCPATH . 'vendor/autoload.php';
		try {
			$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file['tmp_name']);
		} catch (\Exception $e) {
			echo json_encode(['success' => false, 'message' => 'Gagal membaca file Excel: ' . $e->getMessage()]);
			exit;
		}

		// --- Sheet resolution: by name first, fallback to index ---
		$warnings = [];
		$sheet1 = $spreadsheet->getSheetByName('KOL Profiles');
		if (!$sheet1) {
			$sheet1 = $spreadsheet->getSheet(0);
			$warnings[] = 'Sheet "KOL Profiles" tidak ditemukan. Menggunakan sheet pertama sebagai fallback.';
		}
		$sheet2 = $spreadsheet->getSheetByName('Rate Cards');
		if (!$sheet2) {
			$sheet_count = $spreadsheet->getSheetCount();
			$sheet2 = $sheet_count > 1 ? $spreadsheet->getSheet(1) : null;
			if ($sheet2) {
				$warnings[] = 'Sheet "Rate Cards" tidak ditemukan. Menggunakan sheet kedua sebagai fallback.';
			}
		}

		if (!$sheet1) {
			echo json_encode(['success' => false, 'message' => 'File tidak memiliki sheet yang dapat dibaca.']);
			exit;
		}

		// --- Row cap check (Sheet 1) ---
		$highest_row = $sheet1->getHighestDataRow();
		$data_rows   = max(0, $highest_row - 1); // subtract header row
		if ($data_rows > 500) {
			echo json_encode([
				'success' => false,
				'message' => "Sheet 'KOL Profiles' memiliki {$data_rows} baris data. Maksimal 500 baris per import.",
			]);
			exit;
		}

		// --- Pre-load lookup tables ---
		$tiers_map     = [];
		$tiers_result  = $this->db->select('id, name')->get('tiers')->result();
		foreach ($tiers_result as $t) {
			$tiers_map[mb_strtolower(trim($t->name))] = $t->id;
		}

		$platforms_map = [];
		$plats_result  = $this->db->select('id, name, code')->get('platforms')->result();
		foreach ($plats_result as $p) {
			$platforms_map[mb_strtolower(trim($p->name))] = $p->id;
			$platforms_map[mb_strtolower(trim($p->code))] = $p->id;
		}

		// --- Counters ---
		$inserted_kols      = 0;
		$skipped_kols       = 0;
		$inserted_rate_cards = 0;
		$skipped_rate_cards  = 0;
		$errors             = [];
		$kol_name_to_id     = []; // for Sheet 2 matching: normalized_name => kol_id

		// --- Transaction start ---
		$this->db->trans_start();

		// ====================================================
		// Sheet 1 — KOL Profiles
		// Columns (1-indexed): Name, Phone, Email, Tier, City,
		//   Province, Address, Country, Agency, Status
		// ====================================================
		$highest_col = $sheet1->getHighestDataColumn();
		for ($row = 2; $row <= $highest_row; $row++) {
			$name     = trim((string)($sheet1->getCell('A'.$row)->getValue() ?? ''));
			$phone    = trim((string)($sheet1->getCell('B'.$row)->getValue() ?? ''));
			$email    = trim((string)($sheet1->getCell('C'.$row)->getValue() ?? ''));
			$tier_raw = trim((string)($sheet1->getCell('D'.$row)->getValue() ?? ''));
			$city     = trim((string)($sheet1->getCell('E'.$row)->getValue() ?? ''));
			$province = trim((string)($sheet1->getCell('F'.$row)->getValue() ?? ''));
			$address  = trim((string)($sheet1->getCell('G'.$row)->getValue() ?? ''));
			$country  = trim((string)($sheet1->getCell('H'.$row)->getValue() ?? '')) ?: 'Indonesia';
			$agency   = trim((string)($sheet1->getCell('I'.$row)->getValue() ?? ''));
			$status   = strtoupper(trim((string)($sheet1->getCell('J'.$row)->getValue() ?? ''))) ?: 'ACTIVE';

			if (empty($name)) {
				continue; // skip blank rows
			}

			// Dedup: email (primary) then phone — NOT name
			$existing_kol = null;
			if (!empty($email)) {
				$existing_kol = $this->db->where('email', $email)->get('kols')->row();
			}
			if (!$existing_kol && !empty($phone)) {
				$existing_kol = $this->db->where('phone', $phone)->get('kols')->row();
			}

			if ($existing_kol) {
				// Reuse existing kol_id — don't insert duplicate
				$kol_name_to_id[mb_strtolower($name)] = $existing_kol->id;
				$skipped_kols++;
				$errors[] = "Sheet1 baris {$row}: KOL \"{$name}\" sudah ada (email/phone cocok), dilewati.";
				continue;
			}

			// Tier lookup
			$tier_id = $tiers_map[mb_strtolower($tier_raw)] ?? null;
			if (!$tier_id) {
				$errors[] = "Sheet1 baris {$row}: Tier \"{$tier_raw}\" tidak ditemukan. KOL \"{$name}\" dilewati.";
				$skipped_kols++;
				continue;
			}

			$status_allowed = ['ACTIVE', 'INACTIVE'];
			if (!in_array($status, $status_allowed)) {
				$status = 'ACTIVE';
			}

			// Insert via CI3 Query Builder — parameterized, no string SQL
			$insert_data = [
				'name'            => $name,
				'phone'           => $phone ?: null,
				'email'           => $email ?: null,
				'tier_id'         => $tier_id,
				'city'            => $city ?: null,
				'province'        => $province ?: null,
				'address'         => $address ?: null,
				'country'         => $country,
				'status'          => $status,
				'approval_status' => 'PENDING',
				'created_by'      => get_user_data('id'),
				'created_at'      => date('Y-m-d H:i:s'),
				'updated_at'      => date('Y-m-d H:i:s'),
			];
			$this->db->insert('kols', $insert_data);
			$new_kol_id = $this->db->insert_id();

			if ($new_kol_id) {
				$kol_name_to_id[mb_strtolower($name)] = $new_kol_id;
				$inserted_kols++;
			} else {
				$errors[] = "Sheet1 baris {$row}: Gagal menyimpan KOL \"{$name}\".";
				$skipped_kols++;
			}
		}

		// ====================================================
		// Sheet 2 — Rate Cards (optional)
		// Columns: KOL Name, Platform Name/Code, Content Type,
		//   Rate, Currency Code, Notes
		// ====================================================
		if ($sheet2) {
			$rc_highest_row = $sheet2->getHighestDataRow();
			for ($row = 2; $row <= $rc_highest_row; $row++) {
				$kol_name_raw  = trim((string)($sheet2->getCell('A'.$row)->getValue() ?? ''));
				$platform_raw  = trim((string)($sheet2->getCell('B'.$row)->getValue() ?? ''));
				$content_type  = trim((string)($sheet2->getCell('C'.$row)->getValue() ?? ''));
				$rate_raw      = trim((string)($sheet2->getCell('D'.$row)->getValue() ?? ''));
				$currency      = strtoupper(trim((string)($sheet2->getCell('E'.$row)->getValue() ?? ''))) ?: 'IDR';
				$notes         = trim((string)($sheet2->getCell('F'.$row)->getValue() ?? ''));

				if (empty($kol_name_raw) || empty($content_type)) {
					continue; // skip blank rows
				}

				// Match KOL by normalized name to the in-memory map
				$kol_key    = mb_strtolower($kol_name_raw);
				$kol_id_rc  = $kol_name_to_id[$kol_key] ?? null;
				if (!$kol_id_rc) {
					$errors[] = "Sheet2 baris {$row}: KOL \"{$kol_name_raw}\" tidak ditemukan. Baris dilewati.";
					$skipped_rate_cards++;
					continue;
				}

				// Match platform
				$platform_key = mb_strtolower($platform_raw);
				$platform_id  = $platforms_map[$platform_key] ?? null;
				if (!$platform_id) {
					$errors[] = "Sheet2 baris {$row}: Platform \"{$platform_raw}\" tidak ditemukan. Baris dilewati.";
					$skipped_rate_cards++;
					continue;
				}

				// Find kol_platform_id
				$kol_plat = $this->db
					->where('kol_id', $kol_id_rc)
					->where('platform_id', $platform_id)
					->get('kol_platforms')->row();
				if (!$kol_plat) {
					$errors[] = "Sheet2 baris {$row}: KOL \"{$kol_name_raw}\" belum memiliki platform \"{$platform_raw}\" terdaftar. Baris dilewati.";
					$skipped_rate_cards++;
					continue;
				}

				$rate = is_numeric($rate_raw) ? (float)$rate_raw : 0;

				// INSERT IGNORE respects uk_kol_rate_cards (kol_platform_id, content_type, currency_code)
				$this->db->query(
					"INSERT IGNORE INTO kol_rate_cards (kol_platform_id, content_type, rate, currency_code, notes, is_active, created_at, updated_at)
					 VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())",
					[$kol_plat->id, $content_type, $rate, $currency, $notes ?: null]
				);
				$affected = $this->db->affected_rows();
				if ($affected > 0) {
					$inserted_rate_cards++;
				} else {
					$skipped_rate_cards++;
					$errors[] = "Sheet2 baris {$row}: Rate card untuk \"{$kol_name_raw}\" / \"{$content_type}\" sudah ada (dilewati).";
				}
			}
		}

		// --- Transaction complete / rollback ---
		$this->db->trans_complete();

		if ($this->db->trans_status() === false) {
			echo json_encode([
				'success'  => false,
				'message'  => 'Transaksi gagal. Semua perubahan dibatalkan (rollback).',
				'errors'   => $errors,
				'csrf_hash' => $this->security->get_csrf_hash(),
			]);
			exit;
		}

		echo json_encode([
			'success'              => true,
			'message'              => 'Import selesai.',
			'warnings'             => $warnings,
			'inserted_kols'        => $inserted_kols,
			'skipped_kols'         => $skipped_kols,
			'inserted_rate_cards'  => $inserted_rate_cards,
			'skipped_rate_cards'   => $skipped_rate_cards,
			'errors'               => $errors,
			'csrf_hash'            => $this->security->get_csrf_hash(),
		]);
		exit;
	}

	/**
	 * KOL Recommendations Generator Page
	 */
	public function rekomendasi()
	{
		$this->is_allowed('kols_view');

		$baseline_kol_id = (int)$this->input->get('baseline_kol_id');
		$this->data['baseline_kol_id'] = $baseline_kol_id;
		$this->data['meta'] = $this->model_kols->get_recommendation_meta();

		// If baseline_kol_id provided, preload baseline details and initial lookalike matches
		if ($baseline_kol_id > 0) {
			$this->data['baseline_kol'] = $this->db->select('kols.*, tiers.name as tier_name')
				->from('kols')
				->join('tiers', 'tiers.id = kols.tier_id', 'LEFT')
				->where('kols.id', $baseline_kol_id)
				->get()->row();
			$this->data['initial_recommendations'] = $this->model_kols->get_similar_kols($baseline_kol_id, 6);
		} else {
			$this->data['baseline_kol'] = null;
			$this->data['initial_recommendations'] = [];
		}

		$this->template->title('KOL Recommendations Generator');
		$this->render('backend/standart/administrator/kols/kols_rekomendasi', $this->data);
	}

	/**
	 * AJAX endpoint to run the recommendation generator
	 */
	public function generate_recommendations_ajax()
	{
		if (!$this->is_allowed('kols_view', false)) {
			echo json_encode([
				'success'   => false,
				'message'   => 'Access denied.',
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
			exit;
		}

		$criteria = [
			'baseline_kol_id' => (int)$this->input->post('baseline_kol_id'),
			'service_type'    => $this->input->post('service_type'),
			'categories'      => $this->input->post('categories') ?: [],
			'platforms'       => $this->input->post('platforms') ?: [],
			'tiers'           => $this->input->post('tiers') ?: [],
			'min_followers'   => $this->input->post('min_followers'),
			'max_followers'   => $this->input->post('max_followers'),
			'min_views'       => $this->input->post('min_views'),
			'min_er'          => $this->input->post('min_er'),
			'max_budget'      => $this->input->post('max_budget'),
			'gender'          => $this->input->post('gender'),
			'city'            => $this->input->post('city'),
			'priority'        => $this->input->post('priority') ?: 'match',
		];

		$limit = (int)($this->input->post('limit') ?: 10);
		$limit = max(1, min(50, $limit));

		try {
			$results = $this->model_kols->generate_recommendations($criteria, $limit);

			echo json_encode([
				'success'   => true,
				'count'     => count($results),
				'results'   => $results,
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
		} catch (Throwable $e) {
			log_message('error', 'generate_recommendations_ajax error: ' . $e->getMessage());
			echo json_encode([
				'success'   => false,
				'message'   => 'An error occurred while generating recommendations: ' . $e->getMessage(),
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
		}
		exit;
	}

	/**
	 * AJAX endpoint to fetch similar lookalike KOLs for a chosen reference KOL
	 */
	public function get_similar_kols_ajax()
	{
		if (!$this->is_allowed('kols_view', false)) {
			echo json_encode([
				'success'   => false,
				'message'   => 'Access denied.',
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
			exit;
		}

		$kol_id = (int)$this->input->post('kol_id');
		$limit = max(1, min(50, (int)($this->input->post('limit') ?: 6)));

		if ($kol_id <= 0) {
			echo json_encode([
				'success'   => false,
				'message'   => 'Invalid reference KOL ID.',
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
			exit;
		}

		try {
			$results = $this->model_kols->get_similar_kols($kol_id, $limit);
			echo json_encode([
				'success'   => true,
				'count'     => count($results),
				'results'   => $results,
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
		} catch (Throwable $e) {
			log_message('error', 'get_similar_kols_ajax error: ' . $e->getMessage());
			echo json_encode([
				'success'   => false,
				'message'   => 'Error fetching lookalikes: ' . $e->getMessage(),
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
		}
		exit;
	}

	/**
	 * AJAX endpoint to assign recommended KOLs into Campaign Dealing (campaign_kols)
	 */
	public function assign_to_campaign_ajax()
	{
		if (!$this->is_allowed('campaign_kols_add', false) && !$this->is_allowed('kols_update', false)) {
			echo json_encode([
				'success'   => false,
				'message'   => 'Access denied. You do not have permission to assign KOLs to campaigns.',
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
			exit;
		}

		$campaign_id = (int)$this->input->post('campaign_id');
		$kol_ids = (array)$this->input->post('kol_ids');
		$status = $this->input->post('status') ?: 'NEGOTIATION';
		if (!in_array($status, ['NEGOTIATION', 'ASSIGNED', 'ACTIVE'])) {
			$status = 'NEGOTIATION';
		}

		if ($campaign_id <= 0 || empty($kol_ids)) {
			echo json_encode([
				'success'   => false,
				'message'   => 'Please select a valid campaign and at least one KOL.',
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
			exit;
		}

		$campaign = $this->db->get_where('campaigns', ['id' => $campaign_id])->row();
		if (!$campaign) {
			echo json_encode([
				'success'   => false,
				'message'   => 'Selected campaign not found.',
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
			exit;
		}

		$added_count = 0;
		$skipped_count = 0;

		$this->db->trans_start();

		foreach ($kol_ids as $kid) {
			$kid = (int)$kid;
			if ($kid <= 0) continue;

			// Check unique constraint uk_campaign_kols_pair (campaign_id, kol_id)
			$existing = $this->db->where('campaign_id', $campaign_id)
				->where('kol_id', $kid)
				->where_not_in('status', ['REJECTED', 'CANCELLED'])
				->get('campaign_kols')->row();

			if ($existing) {
				$skipped_count++;
				continue;
			}

			$insert_data = [
				'campaign_id' => $campaign_id,
				'kol_id'      => $kid,
				'status'      => $status,
				'jumlah_vt'   => 1,
				'deal_scope'  => 'Video/VT',
				'created_at'  => date('Y-m-d H:i:s'),
				'updated_at'  => date('Y-m-d H:i:s')
			];

			$this->db->insert('campaign_kols', $insert_data);
			$added_count++;
		}

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			echo json_encode([
				'success'   => false,
				'message'   => 'Database transaction error occurred.',
				'csrf_hash' => $this->security->get_csrf_hash()
			]);
			exit;
		}

		$msg = "Successfully assigned {$added_count} KOL(s) to campaign '{$campaign->name}'.";
		if ($skipped_count > 0) {
			$msg .= " ({$skipped_count} KOL(s) were already assigned and skipped).";
		}

		echo json_encode([
			'success'       => true,
			'message'       => $msg,
			'added_count'   => $added_count,
			'skipped_count' => $skipped_count,
			'csrf_hash'     => $this->security->get_csrf_hash()
		]);
		exit;
	}
}


/* End of file kols.php */
/* Location: ./application/controllers/administrator/Kols.php */


/* End of file kols.php */
/* Location: ./application/controllers/administrator/Kols.php */