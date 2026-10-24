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
	 * AJAX endpoint to sync Apify intelligence data (Followers, Avg Views, Avg ER, Endorse Ratio)
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
		$total_sponsored = 0;
		$total_scraped_posts = 0;
		$total_followers = 0;

		foreach ($platforms as $kp) {
			$res = $this->model_kol_platforms->sync_profile_raw($kp->platform_code, $kp->username);
			if (!empty($res['success']) && !empty($res['data'])) {
				$d = $res['data'];
				// Update kol_platforms
				$this->db->where('id', $kp->id)->update('kol_platforms', [
					'followers'      => $d['followers'],
					'avg_views'      => $d['avg_views'],
					'avg_er'         => $d['avg_er'],
					'endorse_ratio'  => $d['endorse_ratio'] ?? 0,
					'last_synced_at' => date('Y-m-d H:i:s')
				]);

				$total_sponsored     += (int)($d['sponsored_posts'] ?? 0);
				$total_scraped_posts += (int)($d['post_count'] ?? 0);
				$total_followers     += (int)$d['followers'];

				$synced_platforms[] = [
					'platform_name' => $kp->platform_name,
					'username'      => $kp->username,
					'followers'     => $d['followers'],
					'avg_views'     => $d['avg_views'],
					'avg_er'        => round($d['avg_er'] * 100, 2),
					'endorse_ratio' => $d['endorse_ratio'] ?? 0,
					'sponsored'     => $d['sponsored_posts'] ?? 0,
					'sample_posts'  => $d['post_count'] ?? 0
				];
			}
		}

		$overall_endorse_ratio = ($total_scraped_posts > 0)
			? round(($total_sponsored / $total_scraped_posts) * 100, 1)
			: 0;

		$this->output
			->set_content_type('application/json')
			->set_output(json_encode([
				'success'               => true,
				'message'               => "Sinkronisasi Apify selesai untuk " . count($synced_platforms) . " platform!",
				'overall_endorse_ratio' => $overall_endorse_ratio,
				'total_sponsored'       => $total_sponsored,
				'total_sample_posts'    => $total_scraped_posts,
				'synced_platforms'      => $synced_platforms,
				'last_synced'           => date('d M Y, H:i'),
				'csrf_hash'             => $this->security->get_csrf_hash()
			]));
		return;
	}

	/**
	 * AJAX: Trigger GMV sync for a single KOL via KeyAPI.
	 * POST /administrator/kols/sync_gmv_ajax/{kol_id}
	 */
	public function sync_gmv_ajax($kol_id = null)
	{
		if (!$this->is_allowed('kols_view', false) && !$this->is_allowed('kols_update', false)) {
			echo json_encode(['success' => false, 'message' => 'Unauthorized']); exit;
		}
		$kol_id = (int)($kol_id ?: $this->input->post('kol_id'));
		if ($kol_id <= 0) {
			echo json_encode(['success' => false, 'message' => 'KOL ID tidak valid.']); exit;
		}

		if (session_status() === PHP_SESSION_ACTIVE) {
			session_write_close();
		}

		try {
			$this->load->library('Keyapi_gmv');
			$result = $this->keyapi_gmv->sync_kol($kol_id, true);

			$new_csrf = $this->security->get_csrf_hash();
			echo json_encode([
				'success'    => in_array($result['status'], ['OK', 'SKIP', 'EMPTY']),
				'status'     => $result['status'],
				'message'    => $result['message'],
				'count'      => $result['count'] ?? 0,
				'csrf_token' => $new_csrf,
				'csrf_hash'  => $new_csrf,
			]);
		} catch (Throwable $e) {
			log_message('error', "[Kols::sync_gmv_ajax] kol_id={$kol_id}: " . $e->getMessage());
			echo json_encode([
				'success'    => false,
				'status'     => 'FAILED',
				'message'    => 'Terjadi kesalahan server: ' . $e->getMessage(),
				'count'      => 0,
				'csrf_hash'  => $this->security->get_csrf_hash(),
			]);
		}
		exit;
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
						anchor('administrator/kols/edit/' . $save_kols, 'Edit Kols'),
						anchor('administrator/kols', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/kols/edit/' . $save_kols, 'Edit Kols')
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

			$save_user = $this->aauth->create_user($email, $password, $username, [
				'full_name' => $full_name,
				'avatar'    => 'default.png'
			]);

			if ($save_user) {
				// Assign to Kol group
				$kol_group = $this->db->get_where('aauth_groups', ['LOWER(name)' => 'kol'])->row();
				if ($kol_group) {
					$this->aauth->add_member($save_user, $kol_group->id);
				}

				// Set user variable 'kol_id'
				$this->aauth->set_user_var('kol_id', $kol_id, $save_user);

				set_message('KOL profile and User login account successfully created.', 'success');
				echo json_encode([
					'success' => true,
					'message' => 'KOL profile and User login account created successfully.',
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
	* Update view Kolss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('kols_update');

		$this->data['kols'] = $this->model_kols->find($id);
		$this->data['kol_categories'] = $this->db->where('kol_id', $id)->get('kol_categories')->result();
		$this->data['kol_platforms'] = $this->db->where('kol_id', $id)->get('kol_platforms')->result();
		$this->template->title('Kols Update');
		$this->render('backend/standart/administrator/kols/kols_update', $this->data);
	}

	/**
	* Update Kolss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('kols_update', false)) {
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
			];

			if (!is_dir(FCPATH . '/uploads/kols/')) {
				mkdir(FCPATH . '/uploads/kols/');
			}

			if (!empty($kols_rate_card_file_uuid)) {
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
			
			if (!empty($kols_npwp_file_uuid)) {
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
			
			if (!empty($kols_ktp_file_uuid)) {
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
		
			
			$save_kols = $this->model_kols->change($id, $save_data);

			if ($save_kols !== false && $save_kols >= 0) {
				// Clear old categories (categories don't store scraping data, so deleting is fine)
				$this->db->where('kol_id', $id)->delete('kol_categories');

				// Save Categories
				$categories = $this->input->post('categories');
				if (is_array($categories)) {
					foreach ($categories as $cat_id) {
						$this->db->insert('kol_categories', [
							'kol_id' => $id,
							'category_id' => $cat_id,
							'created_at' => date('Y-m-d H:i:s')
						]);
					}
				}

				// Safely Save Platforms to avoid losing scraped data
				$existing_platforms = $this->db->where('kol_id', $id)->get('kol_platforms')->result();
				$existing_map = [];
				foreach ($existing_platforms as $ep) {
					$existing_map[$ep->platform_id] = $ep;
				}

				$platform_ids = $this->input->post('platform_id');
				$platform_usernames = $this->input->post('platform_username');
				$submitted_pids = [];
				
				if (is_array($platform_ids)) {
					$plats_map = [];
					$all_p = $this->db->get('platforms')->result();
					foreach ($all_p as $ap) {
						$plats_map[$ap->id] = $ap;
					}

					foreach ($platform_ids as $key => $pid) {
						if (!empty($pid) && !empty($platform_usernames[$key])) {
							$submitted_pids[] = $pid;
							$p_obj = isset($plats_map[$pid]) ? $plats_map[$pid] : null;

							if (isset($existing_map[$pid])) {
								$auto_url = get_kol_profile_url($existing_map[$pid]->profile_url, $platform_usernames[$key], $p_obj ? $p_obj->code : '', $p_obj ? $p_obj->name : '');
								$update_data = [
									'username' => $platform_usernames[$key],
									'updated_at' => date('Y-m-d H:i:s')
								];
								if (empty($existing_map[$pid]->profile_url) && $auto_url !== 'javascript:void(0);') {
									$update_data['profile_url'] = $auto_url;
								}
								$this->db->where('id', $existing_map[$pid]->id)->update('kol_platforms', $update_data);
							} else {
								// Insert new platform
								$auto_url = get_kol_profile_url('', $platform_usernames[$key], $p_obj ? $p_obj->code : '', $p_obj ? $p_obj->name : '');
								$this->db->insert('kol_platforms', [
									'kol_id' => $id,
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
				}

				// Delete removed platforms
				foreach ($existing_map as $pid => $ep) {
					if (!in_array($pid, $submitted_pids)) {
						$this->db->where('id', $ep->id)->delete('kol_platforms');
					}
				}

				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/kols', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
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
		$remove = false;

		if (!empty($id)) {
			$remove = $this->_remove($id);
		} elseif (count($arr_id) >0) {
			foreach ($arr_id as $id) {
				$remove = $this->_remove($id);
			}
		}

		if ($remove) {
            set_message(cclang('has_been_deleted', 'kols'), 'success');
        } else {
            set_message(cclang('error_delete', 'kols'), 'error');
        }

		redirect_back();
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

		// ── GMV Porto data (Fase D — guarded, table may not exist) ──────────
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
			// non-fatal: leave gmv_summary null
		}

		$this->template->title('Kols Detail');
		$this->render('backend/standart/administrator/kols/kols_view', $this->data);
	}
	
	/**
	* delete Kolss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$kols = $this->model_kols->find($id);

		if (!empty($kols->rate_card_file)) {
			$path = FCPATH . '/uploads/kols/' . $kols->rate_card_file;

			if (is_file($path)) {
				$delete_file = unlink($path);
			}
		}
		
		
		return $this->model_kols->remove($id);
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
            'upload_path_tmp'   => './uploads/tmp/',
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
        $this->pdf->Output($table.'.pdf', 'H');
	}

	public function approve($id)
	{
		$this->is_allowed('kols_update');
		
		$kol = $this->model_kols->find($id);
		if ($kol && strtoupper($kol->status) === 'PENDING') {
			// Update status to ACTIVE
			$this->model_kols->change($id, ['status' => 'ACTIVE']);
			
			// Find user by email
			$user = $this->db->get_where('aauth_users', ['email' => $kol->email])->row();
			if ($user) {
				// Unban user
				$this->aauth->unban_user($user->id);
				
				// Assign to KOL group
				$kol_group = $this->db->get_where('aauth_groups', ['LOWER(name)' => 'kol'])->row();
				if ($kol_group) {
					$this->aauth->add_member($user->id, $kol_group->id);
				}
				
				set_message('KOL approved and user account activated successfully.', 'success');
			} else {
				set_message('KOL approved, but associated user account was not found.', 'warning');
			}
		}
		
		redirect_back();
	}

	public function reject($id)
	{
		$this->is_allowed('kols_update');
		
		$kol = $this->model_kols->find($id);
		if ($kol && strtoupper($kol->status) === 'PENDING') {
			// Find user by email and delete
			$user = $this->db->get_where('aauth_users', ['email' => $kol->email])->row();
			if ($user) {
				$this->aauth->delete_user($user->id);
			}
			
			// Delete KOL record
			$this->model_kols->remove($id);
			
			set_message('KOL rejected and associated user account deleted.', 'success');
		}
		
		redirect_back();
	}

	public function my_rates()
	{
		$user_id = get_user_data('id');
		if (!$user_id) {
			redirect('administrator/login');
		}

		$kol_id = null;
		if (function_exists('get_current_user_kol_id')) {
			$kol_id = get_current_user_kol_id($user_id);
		} else {
			$this->load->library('aauth');
			$kol_id = $this->aauth->get_user_var('kol_id', $user_id);
		}
		
		if (!$kol_id) {
			$kol = $this->db->get_where('kols', ['created_by' => $user_id])->row();
			if ($kol) {
				$kol_id = $kol->id;
			} else {
				$this->session->set_flashdata('f_message', 'Anda belum terhubung dengan profil KOL mana pun.');
				$this->session->set_flashdata('f_type', 'warning');
				redirect('administrator/dashboard');
			}
		}

		$kol = $this->model_kols->find($kol_id);
		$this->data['kol'] = $kol;

		// Platform-aware rates: active platforms + existing kol_rate_cards matrix
		$rate_types = kol_rate_content_types();
		$platforms = $this->db->select('kol_platforms.id as kol_platform_id, kol_platforms.username, kol_platforms.profile_url, kol_platforms.followers, kol_platforms.avg_views, kol_platforms.last_synced_at, platforms.id as platform_id, platforms.name as platform_name, platforms.code as platform_code')
			->from('kol_platforms')
			->join('platforms', 'platforms.id = kol_platforms.platform_id', 'LEFT')
			->where('kol_platforms.kol_id', $kol_id)
			->where('kol_platforms.is_active', 1)
			->order_by('kol_platforms.followers', 'DESC')
			->order_by('platforms.name', 'ASC')
			->get()->result();

		$plat_ids = array_map(function($p) { return $p->kol_platform_id; }, $platforms);
		$rate_map = [];
		if (!empty($plat_ids)) {
			$rows = $this->db->where_in('kol_platform_id', $plat_ids)
				->where('is_active', 1)
				->where('currency_code', 'IDR')
				->get('kol_rate_cards')->result();
			foreach ($rows as $r) {
				$rate_map[$r->kol_platform_id][normalize_content_type($r->content_type)] = $r->rate;
			}
		}

		// Only show platforms the KOL actually uses: has followers or already has rates
		$platforms = array_values(array_filter($platforms, function($p) use ($rate_map) {
			return (int)$p->followers > 0 || !empty($rate_map[$p->kol_platform_id]);
		}));

		// Per-platform progress summary for accordion headers
		foreach ($platforms as $p) {
			$filled = 0;
			$min = null;
			foreach ($rate_types as $t => $meta) {
				if (isset($rate_map[$p->kol_platform_id][$t]) && (float)$rate_map[$p->kol_platform_id][$t] > 0) {
					$filled++;
					$rv = (float)$rate_map[$p->kol_platform_id][$t];
					$min = ($min === null || $rv < $min) ? $rv : $min;
				}
			}
			$p->rates_filled = $filled;
			$p->rates_total = count($rate_types);
			$p->rates_min = $min;
		}

		$this->data['rate_types'] = $rate_types;
		$this->data['rate_platforms'] = $platforms;
		$this->data['rate_map'] = $rate_map;
		$this->data['has_any_platform'] = !empty($plat_ids);
		
		$this->template->title('Rate Saya');
		$this->render('backend/standart/administrator/kols/my_rates', $this->data);
	}

	public function my_rates_save()
	{
		$user_id = get_user_data('id');
		if (!$user_id) {
			redirect('administrator/login');
		}

		$kol_id = null;
		if (function_exists('get_current_user_kol_id')) {
			$kol_id = get_current_user_kol_id($user_id);
		} else {
			$this->load->library('aauth');
			$kol_id = $this->aauth->get_user_var('kol_id', $user_id);
		}

		if (!$kol_id) {
			$kol = $this->db->get_where('kols', ['created_by' => $user_id])->row();
			if ($kol) {
				$kol_id = $kol->id;
			} else {
				$this->session->set_flashdata('f_message', 'Anda belum terhubung dengan profil KOL mana pun.');
				$this->session->set_flashdata('f_type', 'warning');
				redirect('administrator/dashboard');
			}
		}

		$rate_types = kol_rate_content_types();
		$type_slugs = kol_rate_type_slugs();
		$matrix = $this->input->post('rates');

		// Guard: only platforms owned by this KOL may be written
		$owned = $this->db->select('id')->from('kol_platforms')->where('kol_id', $kol_id)->where('is_active', 1)->get()->result();
		$owned_ids = array_map(function($r) { return (string)$r->id; }, $owned);

		if (!is_array($matrix) || empty($matrix)) {
			$this->session->set_flashdata('f_message', 'Tidak ada data rate yang diterima server. Silakan muat ulang halaman lalu coba lagi.');
			$this->session->set_flashdata('f_type', 'danger');
			redirect('administrator/kols/my_rates');
		}

		$this->db->trans_start();

		if (is_array($matrix)) {
			foreach ($matrix as $plat_id => $type_rates) {
				if (!in_array((string)$plat_id, $owned_ids, true) || !is_array($type_rates)) {
					continue;
				}
				foreach ($type_rates as $slug => $nominal) {
					// POST keys must be slugs (CI3 coerces keys with spaces to 0)
					if (!isset($type_slugs[$slug])) {
						continue;
					}
					$content_type = $type_slugs[$slug];
					$clean = str_replace(['.', ','], '', trim((string)$nominal));
					$existing = $this->db->get_where('kol_rate_cards', [
						'kol_platform_id' => $plat_id,
						'content_type'    => $content_type,
						'currency_code'   => 'IDR',
					])->row();
					if ($clean !== '' && is_numeric($clean) && (float)$clean > 0) {
						$row_data = [
							'kol_platform_id' => $plat_id,
							'content_type'    => $content_type,
							'rate'            => (float)$clean,
							'currency_code'   => 'IDR',
							'is_active'       => 1,
						];
						if ($existing) {
							$this->db->where('id', $existing->id)->update('kol_rate_cards', $row_data);
						} else {
							$this->db->insert('kol_rate_cards', $row_data);
						}
					} elseif ($existing) {
						// Emptied cell => remove the offering row
						$this->db->where('id', $existing->id)->delete('kol_rate_cards');
					}
				}
			}
		}

		// Sync legacy kols.price_* summary columns (MIN per type across platforms)
		$mins = [];
		foreach (array_keys($rate_types) as $t) {
			$row = $this->db->select('MIN(rate) as min_rate')
				->from('kol_rate_cards rc')
				->join('kol_platforms kp', 'kp.id = rc.kol_platform_id', 'INNER')
				->where('kp.kol_id', $kol_id)
				->where('kp.is_active', 1)
				->where('rc.content_type', $t)
				->where('rc.is_active', 1)
				->where('rc.currency_code', 'IDR')
				->get()->row();
			$mins[$t] = ($row && $row->min_rate !== null) ? (float)$row->min_rate : null;
		}
		$long_min = $mins['Long Form Video'];
		$update_data = [
			'price_vt'        => $mins['Short Form Video'],
			'price_live'      => $mins['Live Streaming'],
			'price_long_form' => $long_min,
			'price_unboxing'  => $long_min,
		];

		$this->db->where('id', $kol_id);
		$this->db->update('kols', $update_data);

		$this->db->trans_complete();

		if ($this->db->trans_status() === FALSE) {
			$this->session->set_flashdata('f_message', 'Gagal menyimpan rate. Silakan coba lagi.');
			$this->session->set_flashdata('f_type', 'danger');
		} else {
			$this->session->set_flashdata('f_message', 'Harga rate card berhasil diperbarui.');
			$this->session->set_flashdata('f_type', 'success');
		}
		
		redirect('administrator/kols/my_rates');
	}
}


/* End of file kols.php */
/* Location: ./application/controllers/administrator/Kols.php */
