<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Register extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('kols/model_kols');
		$this->load->library('form_validation');
		$this->load->library('aauth');
	}

	public function index()
	{
		$this->template->title('KOL Registration');
		
		$this->data['categories'] = $this->db->where('is_active', 1)->get('categories')->result();
		$this->data['platforms'] = $this->db->where('is_active', 1)->get('platforms')->result();
		
		$this->render('register_kol', $this->data);
	}

	
	public function validate_phase1()
	{
		$this->form_validation->set_message('is_unique', 'The %s is already registered. Please use another one.');
		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[150]|is_unique[kols.name]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[30]|is_unique[kols.phone]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[200]|is_unique[kols.email]|is_unique[aauth_users.email]');
		$this->form_validation->set_rules('gender', 'Gender', 'trim|required');
		$this->form_validation->set_rules('address', 'Address', 'trim|required');
		$this->form_validation->set_rules('country', 'Country', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('categories[]', 'Categories', 'required');
		$this->form_validation->set_rules('kols_rate_card_file_name', 'Rate Card File', 'trim|required|max_length[500]');
		$this->form_validation->set_rules('avatar_url', 'Avatar URL', 'trim|required|max_length[2000]');

		$platform_ids = $this->input->post('platform_id');
		$platform_usernames = $this->input->post('platform_username');

		$has_valid_platform = false;
		$has_invalid_row = false;

		if (is_array($platform_ids) && count($platform_ids) > 0) {
			foreach ($platform_ids as $key => $pid) {
				$uname = isset($platform_usernames[$key]) ? trim($platform_usernames[$key]) : '';
				if (empty($pid) || empty($uname)) {
					$has_invalid_row = true;
				} else {
					$has_valid_platform = true;
				}
			}
		}

		if (!$has_valid_platform || $has_invalid_row) {
			echo json_encode([
				'success' => false,
				'message' => 'Harap tambahkan minimal 1 platform dan pastikan semua baris platform telah dipilih jenis platform serta diisi username/link-nya.'
			]);
			exit;
		}

		if ($this->form_validation->run()) {
			$name_slug = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($this->input->post('name')));
			$suggested_username = 'kol_' . substr($name_slug, 0, 20);

			echo json_encode([
				'success' => true,
				'kol_name' => $this->input->post('name'),
				'kol_email' => $this->input->post('email'),
				'suggested_username' => $suggested_username
			]);
			exit;
		} else {
			echo json_encode(['success' => false, 'message' => validation_errors()]);
			exit;
		}
	}


	public function submit_registration()
	{
		// Phase 1 Rules
		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[150]|is_unique[kols.name]');
		$this->form_validation->set_rules('phone', 'Phone', 'trim|required|max_length[30]|is_unique[kols.phone]');
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[200]|is_unique[kols.email]|is_unique[aauth_users.email]');
		$this->form_validation->set_rules('gender', 'Gender', 'trim|required');
		$this->form_validation->set_rules('address', 'Address', 'trim|required');
		$this->form_validation->set_rules('country', 'Country', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('categories[]', 'Categories', 'required');
		$this->form_validation->set_rules('kols_rate_card_file_name', 'Rate Card File', 'trim|required|max_length[500]');
		$this->form_validation->set_rules('avatar_url', 'Avatar URL', 'trim|required|max_length[2000]');

		$platform_ids = $this->input->post('platform_id');
		$platform_usernames = $this->input->post('platform_username');

		$has_valid_platform = false;
		$has_invalid_row = false;

		if (is_array($platform_ids) && count($platform_ids) > 0) {
			foreach ($platform_ids as $key => $pid) {
				$uname = isset($platform_usernames[$key]) ? trim($platform_usernames[$key]) : '';
				if (empty($pid) || empty($uname)) {
					$has_invalid_row = true;
				} else {
					$has_valid_platform = true;
				}
			}
		}

		if (!$has_valid_platform || $has_invalid_row) {
			echo json_encode([
				'success' => false,
				'message' => 'Harap tambahkan minimal 1 platform dan pastikan semua baris platform telah diisi dengan lengkap.'
			]);
			exit;
		}

		// Phase 2 Rules
		$this->form_validation->set_rules('username', 'Username', 'trim|required|min_length[3]|max_length[50]');
		$this->form_validation->set_rules('user_email', 'User Email', 'trim|required|valid_email|max_length[200]|is_unique[aauth_users.email]');
		$this->form_validation->set_message('is_unique', 'The %s is already registered. Please use another one.');
		$this->form_validation->set_rules('full_name', 'Full Name', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[5]|max_length[30]');
		$this->form_validation->set_rules('passconf', 'Password Confirmation', 'trim|required|matches[password]');

		if ($this->form_validation->run()) {
			// Save KOL Profile first
			$kols_rate_card_file_uuid = $this->input->post('kols_rate_card_file_uuid');
			$kols_rate_card_file_name = $this->input->post('kols_rate_card_file_name');
		
			$save_data = [
				'name' => $this->input->post('name'),
				'phone' => $this->input->post('phone'),
				'email' => $this->input->post('email'),
				'gender' => $this->input->post('gender'),
				'address' => $this->input->post('address'),
				'country' => $this->input->post('country'),
				'avatar_url' => $this->input->post('avatar_url'),
				'tier_id' => 1,
				'status' => 'PENDING',
			];

			if (!is_dir(FCPATH . '/uploads/kols/')) {
				mkdir(FCPATH . '/uploads/kols/');
			}

			if (!empty($kols_rate_card_file_name)) {
				$kols_rate_card_file_name_copy = date('YmdHis') . '-' . $kols_rate_card_file_name;
				rename(FCPATH . 'uploads/tmp/' . $kols_rate_card_file_uuid . '/' . $kols_rate_card_file_name, 
						FCPATH . 'uploads/kols/' . $kols_rate_card_file_name_copy);
				$save_data['rate_card_file'] = $kols_rate_card_file_name_copy;
			}
		
			$save_kols = $this->model_kols->store($save_data);

			if ($save_kols) {
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

				$platform_ids = $this->input->post('platform_id');
				$platform_usernames = $this->input->post('platform_username');
				if (is_array($platform_ids)) {
					foreach ($platform_ids as $key => $pid) {
						if (!empty($pid) && !empty($platform_usernames[$key])) {
							$this->db->insert('kol_platforms', [
								'kol_id' => $save_kols,
								'platform_id' => $pid,
								'username' => $platform_usernames[$key],
								'is_active' => 1,
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s')
							]);
						}
					}
				}

				// Now save the User
				$username = trim($this->input->post('username'));
				$user_email = trim($this->input->post('user_email'));
				$password = $this->input->post('password');
				$full_name = trim($this->input->post('full_name'));

				$save_user = $this->aauth->create_user($user_email, $password, $username, [
					'full_name' => $full_name,
					'avatar'    => 'default.png'
				]);

				if ($save_user) {
					// Ban the user initially until approved
					$this->aauth->ban_user($save_user);
					
					// Link KOL and User
					$this->aauth->set_user_var('kol_id', $save_kols, $save_user);

					echo json_encode([
						'success' => true,
						'message' => 'Registration successful. Your account is pending admin approval.',
						'redirect' => site_url('web/register/success')
					]);
					exit;
				} else {
					echo json_encode(['success' => false, 'message' => $this->aauth->print_errors() ?: 'Failed to create user account.']);
					exit;
				}
			} else {
				echo json_encode(['success' => false, 'message' => cclang('data_not_change')]);
				exit;
			}
		} else {
			echo json_encode(['success' => false, 'message' => validation_errors()]);
			exit;
		}
	}

	public function upload_rate_card_file()
	{
		$uuid = $this->input->post('qquuid');
		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'kols',
		]);
	}

	public function delete_rate_card_file($uuid)
	{
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

	public function success()
	{
		$this->template->title('Registration Successful');
		$this->render('register_success', $this->data);
	}
}
