<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Campaign Kol Contracts Controller
*| --------------------------------------------------------------------------
*| Campaign Kol Contracts site
*|
*/
class Campaign_kol_contracts extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_campaign_kol_contracts');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Campaign Kol Contractss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('campaign_kol_contracts_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['campaign_kol_contractss'] = $this->model_campaign_kol_contracts->get($filter, $field, $this->limit_page, $offset);
		$this->data['campaign_kol_contracts_counts'] = $this->model_campaign_kol_contracts->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/campaign_kol_contracts/index/',
			'total_rows'   => $this->model_campaign_kol_contracts->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Campaign Kol Contracts List');
		$this->render('backend/standart/administrator/campaign_kol_contracts/campaign_kol_contracts_list', $this->data);
	}
	
	/**
	* Add new campaign_kol_contractss
	*
	*/
	public function add()
	{
		$this->is_allowed('campaign_kol_contracts_add');

		$this->template->title('Campaign Kol Contracts New');
		$this->render('backend/standart/administrator/campaign_kol_contracts/campaign_kol_contracts_add', $this->data);
	}

	/**
	* Add New Campaign Kol Contractss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('campaign_kol_contracts_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('campaign_kol_id', 'Campaign Kol Id', 'trim|required');

		$this->form_validation->set_rules('campaign_kol_contracts_contract_file_name[]', 'Contract File', 'trim|max_length[500]');

		if ($this->form_validation->run()) {
		
			$today = date('Ymd');
			$this->db->like('contract_no', 'CTR-'.$today);
			$this->db->order_by('id', 'DESC');
			$last_contract = $this->db->get('campaign_kol_contracts')->row();
			if ($last_contract) {
				$last_no = explode('-', $last_contract->contract_no);
				$next_no = sprintf('%04d', intval(end($last_no)) + 1);
			} else {
				$next_no = '0001';
			}
			$generated_contract_no = 'CTR-' . $today . '-' . $next_no;

			$save_data = [
				'campaign_kol_id' => $this->input->post('campaign_kol_id'),
				'contract_no' => $generated_contract_no,
				'status' => 'DRAFT',
				'signed_at' => NULL,
				'created_by' => $this->aauth->get_user_id(),
			];

			if (!is_dir(FCPATH . '/uploads/campaign_kol_contracts/')) {
				mkdir(FCPATH . '/uploads/campaign_kol_contracts/', 0777, true);
			}

			// Generate HTML from template
			$campaign_kol = $this->db->get_where('campaign_kols', ['id' => $this->input->post('campaign_kol_id')])->row();
			if ($campaign_kol) {
				$template = $this->db->order_by('id', 'DESC')->get('contract_templates')->row();
				if ($template) {
					$kol = $this->db->get_where('kols', ['id' => $campaign_kol->kol_id])->row();
					$campaign = $this->db->get_where('campaigns', ['id' => $campaign_kol->campaign_id])->row();

					$default_snk = "1. Pihak Kedua wajib memproduksi dan menayangkan konten promosi sesuai brief, format, dan jadwal yang disepakati.\n"
						. "2. Pihak Kedua dilarang menghapus atau mengarsipkan konten yang telah diunggah sekurang-kurangnya selama 30 (tiga puluh) hari kalender.\n"
						. "3. Pihak Pertama (Brand) berhak menggunakan materi konten untuk keperluan promosi dan whitelisting digital ads selama 90 (sembilan puluh) hari.\n"
						. "4. Pihak Kedua bersedia melakukan revisi minor maksimal sebanyak 2 (dua) kali sebelum materi konten dipublikasikan.\n"
						. "5. Honorarium diproses sesuai invoice yang diterbitkan di awal setelah penandatanganan perjanjian ini.";

					$snk = $this->input->post('terms_and_conditions');
					if (empty($snk)) {
						$snk = !empty($campaign->terms_and_conditions) ? $campaign->terms_and_conditions : $default_snk;
					}
					$save_data['terms_and_conditions'] = $snk;
					
					$html = $template->content;
					$html = str_replace('{KOL_NAME}', $kol ? $kol->name : '', $html);
					$html = str_replace('{KOL_ADDRESS}', $kol ? $kol->address : '', $html);
					$html = str_replace('{KOL_KTP}', $kol ? $kol->ktp_number : '', $html);
					$html = str_replace('{KOL_NPWP}', $kol ? $kol->npwp_number : '', $html);
					$html = str_replace('{BANK_NAME}', $kol ? $kol->bank_name : '', $html);
					$html = str_replace('{BANK_ACCOUNT}', $kol ? $kol->bank_account_number : '', $html);
					$html = str_replace('{BANK_OWNER}', $kol ? $kol->bank_account_name : '', $html);
					$html = str_replace('{CAMPAIGN_NAME}', $campaign ? $campaign->name : '', $html);
					$html = str_replace('{BRAND_NAME}', $campaign ? $campaign->brand_name : '', $html);
					$html = str_replace('{DEAL_AMOUNT}', 'Rp ' . number_format($campaign_kol->deal_amount, 0, ',', '.'), $html);
					$html = str_replace('{CURRENT_DATE}', date('d F Y'), $html);
					$html = str_replace('{CONTRACT_NO}', $generated_contract_no, $html);
					$html = str_replace('{TERMS_AND_CONDITIONS}', nl2br(htmlspecialchars($snk)), $html);
					
					$save_data['contract_html'] = $html;
				}
			}

			if (count((array) $this->input->post('campaign_kol_contracts_contract_file_name'))) {
				foreach ((array) $_POST['campaign_kol_contracts_contract_file_name'] as $idx => $file_name) {
					$campaign_kol_contracts_contract_file_name_copy = date('YmdHis') . '-' . $file_name;

					rename(FCPATH . 'uploads/tmp/' . $_POST['campaign_kol_contracts_contract_file_uuid'][$idx] . '/' .  $file_name, 
							FCPATH . 'uploads/campaign_kol_contracts/' . $campaign_kol_contracts_contract_file_name_copy);

					$listed_image[] = $campaign_kol_contracts_contract_file_name_copy;

					if (!is_file(FCPATH . '/uploads/campaign_kol_contracts/' . $campaign_kol_contracts_contract_file_name_copy)) {
						echo json_encode([
							'success' => false,
							'message' => 'Error uploading file'
							]);
						exit;
					}
				}

				$save_data['contract_file'] = implode(',', $listed_image);
			}
		
			
			$save_campaign_kol_contracts = $this->model_campaign_kol_contracts->store($save_data);
            

			if ($save_campaign_kol_contracts) {
				if (in_array($this->input->post('status'), ['SIGNED', 'ACTIVE'])) {
					$this->_auto_generate_invoice_for_contract($save_campaign_kol_contracts);
				}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_campaign_kol_contracts;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/campaign_kol_contracts/edit/' . $save_campaign_kol_contracts, 'Edit Campaign Kol Contracts'),
						anchor('administrator/campaign_kol_contracts', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/campaign_kol_contracts/edit/' . $save_campaign_kol_contracts, 'Edit Campaign Kol Contracts')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/campaign_kol_contracts');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/campaign_kol_contracts');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}

	/**
	* Create DRAFT contract in one click (used by Finance Hub "Buat Kontrak").
	* Same numbering + HTML template as add_save, returns JSON.
	*
	* @return JSON
	*/
	public function create_draft_ajax()
	{
		if (!$this->is_allowed('campaign_kol_contracts_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$campaign_kol_id = (int)$this->input->post('campaign_kol_id');
		if ($campaign_kol_id <= 0) {
			echo json_encode(['success' => false, 'message' => 'Dealing KOL tidak valid.']);
			exit;
		}

		$campaign_kol = $this->db->get_where('campaign_kols', ['id' => $campaign_kol_id])->row();
		if (!$campaign_kol) {
			echo json_encode(['success' => false, 'message' => 'Data dealing tidak ditemukan.']);
			exit;
		}

		$this->db->where('campaign_kol_id', $campaign_kol_id);
		$this->db->where_not_in('status', ['VOID', 'CANCELLED']);
		if ($this->db->get('campaign_kol_contracts')->row()) {
			echo json_encode(['success' => false, 'message' => 'Dealing ini sudah memiliki kontrak aktif.']);
			exit;
		}

		$save_data = $this->_build_draft_contract_data($campaign_kol);
		$new_id = $this->model_campaign_kol_contracts->store($save_data);

		if ($new_id) {
			echo json_encode([
				'success' => true,
				'message' => 'Kontrak DRAFT ' . $save_data['contract_no'] . ' berhasil dibuat. Silakan lanjut Sign.',
				'contract_id' => $new_id,
				'contract_no' => $save_data['contract_no']
			]);
		} else {
			echo json_encode(['success' => false, 'message' => 'Gagal membuat kontrak DRAFT.']);
		}
		exit;
	}

	/**
	* Build DRAFT contract row data (numbering + HTML from template).
	* Shared by add_save flow and create_draft_ajax/backfill.
	*
	* @param object $campaign_kol campaign_kols row
	* @return array save_data ready for store()
	*/
	private function _build_draft_contract_data($campaign_kol)
	{
		$today = date('Ymd');
		$this->db->like('contract_no', 'CTR-' . $today);
		$this->db->order_by('id', 'DESC');
		$last_contract = $this->db->get('campaign_kol_contracts')->row();
		if ($last_contract) {
			$last_no = explode('-', $last_contract->contract_no);
			$next_no = sprintf('%04d', intval(end($last_no)) + 1);
		} else {
			$next_no = '0001';
		}
		$generated_contract_no = 'CTR-' . $today . '-' . $next_no;

		$kol = $this->db->get_where('kols', ['id' => $campaign_kol->kol_id])->row();
		$campaign = $this->db->get_where('campaigns', ['id' => $campaign_kol->campaign_id])->row();

		// Default S&K Brand
		$default_snk = "1. Pihak Kedua wajib memproduksi dan menayangkan konten promosi sesuai brief, format, dan jadwal yang disepakati.\n"
			. "2. Pihak Kedua dilarang menghapus atau mengarsipkan konten yang telah diunggah sekurang-kurangnya selama 30 (tiga puluh) hari kalender.\n"
			. "3. Pihak Pertama (Brand) berhak menggunakan materi konten untuk keperluan promosi dan whitelisting digital ads selama 90 (sembilan puluh) hari.\n"
			. "4. Pihak Kedua bersedia melakukan revisi minor maksimal sebanyak 2 (dua) kali sebelum materi konten dipublikasikan.\n"
			. "5. Honorarium diproses sesuai invoice yang diterbitkan di awal setelah penandatanganan perjanjian ini.";

		$snk = !empty($campaign->terms_and_conditions) ? $campaign->terms_and_conditions : $default_snk;

		$save_data = [
			'campaign_kol_id' => $campaign_kol->id,
			'contract_no' => $generated_contract_no,
			'status' => 'DRAFT',
			'terms_and_conditions' => $snk,
			'signed_at' => NULL,
			'created_by' => method_exists($this->aauth, 'get_user_id') ? $this->aauth->get_user_id() : 1,
		];

		$template = $this->db->order_by('id', 'DESC')->get('contract_templates')->row();
		if ($template) {
			$html = $template->content;
			$html = str_replace('{KOL_NAME}', $kol ? $kol->name : '', $html);
			$html = str_replace('{KOL_ADDRESS}', $kol ? ($kol->address ?? '') : '', $html);
			$html = str_replace('{KOL_KTP}', $kol ? ($kol->ktp_number ?? '') : '', $html);
			$html = str_replace('{KOL_NPWP}', $kol ? ($kol->npwp_number ?? '') : '', $html);
			$html = str_replace('{BANK_NAME}', $kol ? ($kol->bank_name ?? '') : '', $html);
			$html = str_replace('{BANK_ACCOUNT}', $kol ? ($kol->bank_account_number ?? '') : '', $html);
			$html = str_replace('{BANK_OWNER}', $kol ? ($kol->bank_account_name ?? '') : '', $html);
			$html = str_replace('{CAMPAIGN_NAME}', $campaign ? $campaign->name : '', $html);
			$html = str_replace('{BRAND_NAME}', $campaign ? ($campaign->brand_name ?? '') : '', $html);
			$html = str_replace('{DEAL_AMOUNT}', 'Rp ' . number_format((float)$campaign_kol->deal_amount, 0, ',', '.'), $html);
			$html = str_replace('{CURRENT_DATE}', date('d F Y'), $html);
			$html = str_replace('{CONTRACT_NO}', $generated_contract_no, $html);
			$html = str_replace('{TERMS_AND_CONDITIONS}', nl2br(htmlspecialchars($snk)), $html);

			$save_data['contract_html'] = $html;
		}

		return $save_data;
	}
	
		/**
	* Update view Campaign Kol Contractss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('campaign_kol_contracts_update');

		$this->data['campaign_kol_contracts'] = $this->model_campaign_kol_contracts->find($id);

		$this->template->title('Campaign Kol Contracts Update');
		$this->render('backend/standart/administrator/campaign_kol_contracts/campaign_kol_contracts_update', $this->data);
	}

	/**
	* Update Campaign Kol Contractss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('campaign_kol_contracts_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('campaign_kol_id', 'Campaign Kol Id', 'trim|required');
		$this->form_validation->set_rules('contract_no', 'Contract No', 'trim|required|max_length[100]');
		$this->form_validation->set_rules('campaign_kol_contracts_contract_file_name[]', 'Contract File', 'trim|max_length[500]');
		$this->form_validation->set_rules('status', 'Status', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$old_data = $this->model_campaign_kol_contracts->find($id);

			$save_data = [
				'campaign_kol_id' => $this->input->post('campaign_kol_id'),
				'contract_no' => $this->input->post('contract_no'),
				'status' => $this->input->post('status'),
			];

			if ($this->input->post('terms_and_conditions') !== null) {
				$save_data['terms_and_conditions'] = trim($this->input->post('terms_and_conditions'));
			}

			if ($this->input->post('status') == 'SIGNED' || $this->input->post('status') == 'ACTIVE') {
				if ($old_data->status != 'SIGNED' && $old_data->status != 'ACTIVE') {
					$save_data['signed_at'] = date('Y-m-d H:i:s');
				} else {
					$save_data['signed_at'] = $old_data->signed_at; // keep existing
				}
			} else {
				$save_data['signed_at'] = NULL;
			}

			$listed_image = [];
			// Generate HTML from template
			$campaign_kol = $this->db->get_where('campaign_kols', ['id' => $this->input->post('campaign_kol_id')])->row();
			if ($campaign_kol) {
				$template = $this->db->order_by('id', 'DESC')->get('contract_templates')->row();
				if ($template) {
					$kol = $this->db->get_where('kols', ['id' => $campaign_kol->kol_id])->row();
					$campaign = $this->db->get_where('campaigns', ['id' => $campaign_kol->campaign_id])->row();

					$default_snk = "1. Pihak Kedua wajib memproduksi dan menayangkan konten promosi sesuai brief, format, dan jadwal yang disepakati.\n"
						. "2. Pihak Kedua dilarang menghapus atau mengarsipkan konten yang telah diunggah sekurang-kurangnya selama 30 (tiga puluh) hari kalender.\n"
						. "3. Pihak Pertama (Brand) berhak menggunakan materi konten untuk keperluan promosi dan whitelisting digital ads selama 90 (sembilan puluh) hari.\n"
						. "4. Pihak Kedua bersedia melakukan revisi minor maksimal sebanyak 2 (dua) kali sebelum materi konten dipublikasikan.\n"
						. "5. Honorarium diproses sesuai invoice yang diterbitkan di awal setelah penandatanganan perjanjian ini.";

					$snk = !empty($save_data['terms_and_conditions']) ? $save_data['terms_and_conditions'] : (!empty($old_data->terms_and_conditions) ? $old_data->terms_and_conditions : ($campaign ? $campaign->terms_and_conditions : $default_snk));
					
					$html = $template->content;
					$html = str_replace('{KOL_NAME}', $kol ? $kol->name : '', $html);
					$html = str_replace('{KOL_ADDRESS}', $kol ? $kol->address : '', $html);
					$html = str_replace('{KOL_KTP}', $kol ? $kol->ktp_number : '', $html);
					$html = str_replace('{KOL_NPWP}', $kol ? $kol->npwp_number : '', $html);
					$html = str_replace('{BANK_NAME}', $kol ? $kol->bank_name : '', $html);
					$html = str_replace('{BANK_ACCOUNT}', $kol ? $kol->bank_account_number : '', $html);
					$html = str_replace('{BANK_OWNER}', $kol ? $kol->bank_account_name : '', $html);
					$html = str_replace('{CAMPAIGN_NAME}', $campaign ? $campaign->name : '', $html);
					$html = str_replace('{BRAND_NAME}', $campaign ? $campaign->brand_name : '', $html);
					$html = str_replace('{DEAL_AMOUNT}', 'Rp ' . number_format($campaign_kol->deal_amount, 0, ',', '.'), $html);
					$html = str_replace('{CURRENT_DATE}', date('d F Y'), $html);
					$html = str_replace('{CONTRACT_NO}', $this->input->post('contract_no'), $html);
					$html = str_replace('{TERMS_AND_CONDITIONS}', nl2br(htmlspecialchars($snk)), $html);
					
					$save_data['contract_html'] = $html;
				}
			}

			if (count((array) $this->input->post('campaign_kol_contracts_contract_file_name'))) {
				foreach ((array) $_POST['campaign_kol_contracts_contract_file_name'] as $idx => $file_name) {
					if (isset($_POST['campaign_kol_contracts_contract_file_uuid'][$idx]) AND !empty($_POST['campaign_kol_contracts_contract_file_uuid'][$idx])) {
						$campaign_kol_contracts_contract_file_name_copy = date('YmdHis') . '-' . $file_name;

						rename(FCPATH . 'uploads/tmp/' . $_POST['campaign_kol_contracts_contract_file_uuid'][$idx] . '/' .  $file_name, 
								FCPATH . 'uploads/campaign_kol_contracts/' . $campaign_kol_contracts_contract_file_name_copy);

						$listed_image[] = $campaign_kol_contracts_contract_file_name_copy;

						if (!is_file(FCPATH . '/uploads/campaign_kol_contracts/' . $campaign_kol_contracts_contract_file_name_copy)) {
							echo json_encode([
								'success' => false,
								'message' => 'Error uploading file'
								]);
							exit;
						}
					} else {
						$listed_image[] = $file_name;
					}
				}
			}
			
			$save_data['contract_file'] = implode(',', $listed_image);
		
			
			$save_campaign_kol_contracts = $this->model_campaign_kol_contracts->change($id, $save_data);

			if ($save_campaign_kol_contracts) {
				// Feature 4: Auto-generate invoice when contract is signed / active
				if (in_array($this->input->post('status'), ['SIGNED', 'ACTIVE'])) {
					$this->_auto_generate_invoice_for_contract($id);
				}
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/campaign_kol_contracts', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/campaign_kol_contracts');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/campaign_kol_contracts');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* KOL's Personal Contract List ("Kontrak Saya")
	* Dedicated page for logged-in KOL users (same access pattern as my_tasks)
	*/
	public function my_contracts()
	{
		if (!$this->aauth->is_loggedin()) {
			redirect('administrator/login', 'refresh');
		}

		$kol_id = get_current_user_kol_id();

		if (!$kol_id && !$this->aauth->is_admin()) {
			$this->data['unlinked_kol'] = true;
			$this->data['contracts'] = [];
			$this->template->title('Kontrak Saya');
			$this->render('backend/standart/administrator/campaign_kol_contracts/kol_my_contracts', $this->data);
			return;
		}

		$this->db->select('campaign_kol_contracts.*, campaigns.name as campaign_name, campaigns.brand_name, campaign_kols.deal_amount, kols.name as kol_name, invoices.id as invoice_id, invoices.invoice_no, invoices.status as invoice_status, invoices.grand_total as invoice_grand_total, invoices.tax_amount as invoice_tax_amount, invoices.bupot_file')
			->from('campaign_kol_contracts')
			->join('campaign_kols', 'campaign_kols.id = campaign_kol_contracts.campaign_kol_id', 'INNER')
			->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'LEFT')
			->join('kols', 'kols.id = campaign_kols.kol_id', 'LEFT')
			->join('invoices', 'invoices.campaign_kol_id = campaign_kols.id AND invoices.status != "CANCELLED"', 'LEFT');

		if ($kol_id) {
			$this->db->where('campaign_kols.kol_id', $kol_id);
		}

		$this->db->order_by('campaign_kol_contracts.id', 'DESC');
		$this->data['contracts'] = $this->db->get()->result();
		$this->data['unlinked_kol'] = false;
		$this->data['kol_id'] = $kol_id;

		$this->template->title('Kontrak Saya');
		$this->render('backend/standart/administrator/campaign_kol_contracts/kol_my_contracts', $this->data);
	}
	
	public function sign_contract_action()
	{
		ini_set("display_errors", 1);
		error_reporting(E_ALL);
		if (!$this->is_allowed('campaign_kol_contracts_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$id = $this->input->post('id');
		$old_data = $this->model_campaign_kol_contracts->find($id);

		if (!$old_data) {
			echo json_encode(['success' => false, 'message' => 'Contract not found']);
			exit;
		}

		$upload_path = './uploads/campaign_kol_contracts/';
		if (!is_dir($upload_path)) {
			mkdir($upload_path, 0777, true);
		}

		$config['upload_path']   = $upload_path;
		$config['allowed_types'] = 'pdf|jpg|jpeg|png|gif';
		$config['max_size']      = 0;
		$config['file_name']     = date('YmdHis') . '_' . $_FILES['contract_file']['name'];

		$this->load->library('upload', $config);
		$this->upload->initialize($config);

		if (!$this->upload->do_upload('contract_file')) {
			echo json_encode(['success' => false, 'message' => $this->upload->display_errors('','')]);
			exit;
		}

		$upload_data = $this->upload->data();
		$file_name = $upload_data['file_name'];

		$save_data = [
			'contract_file' => $file_name,
			'admin_signature_image' => $file_name,
			'status' => 'PENDING'
		];

		$snk = $this->input->post('terms_and_conditions');
		if ($snk !== null && $snk !== '') {
			$save_data['terms_and_conditions'] = trim($snk);
		}

		$save_campaign_kol_contracts = $this->model_campaign_kol_contracts->change($id, $save_data);

		if ($save_campaign_kol_contracts) {
			echo json_encode([
				'success' => true, 
				'message' => 'Dokumen tanda tangan Brand berhasil diupload. Dokumen MoU telah dikirim ke Portal KOL untuk ditandatangani.'
			]);
		} else {
			echo json_encode(['success' => false, 'message' => 'Gagal memperbarui data kontrak di database.']);
		}
		exit;
	}

	
	/**
	* delete Campaign Kol Contractss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('campaign_kol_contracts_delete');

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
            set_message(cclang('has_been_deleted', 'campaign_kol_contracts'), 'success');
        } else {
            set_message(cclang('error_delete', 'campaign_kol_contracts'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Campaign Kol Contractss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('campaign_kol_contracts_view');

		$this->data['campaign_kol_contracts'] = $this->model_campaign_kol_contracts->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Campaign Kol Contracts Detail');
		$this->render('backend/standart/administrator/campaign_kol_contracts/campaign_kol_contracts_view', $this->data);
	}
	
	/**
	* delete Campaign Kol Contractss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		return $this->model_campaign_kol_contracts->change($id, ['status' => 'CANCELLED']);
	}
	
	
	/**
	* Upload Image Campaign Kol Contracts	* 
	* @return JSON
	*/
	public function upload_contract_file_file()
	{
		if (!$this->is_allowed('campaign_kol_contracts_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$uuid = $this->input->post('qquuid');

		echo $this->upload_file([
			'uuid' 		 	=> $uuid,
			'table_name' 	=> 'campaign_kol_contracts',
		]);
	}

	/**
	* Delete Image Campaign Kol Contracts	* 
	* @return JSON
	*/
	public function delete_contract_file_file($uuid)
	{
		if (!$this->is_allowed('campaign_kol_contracts_delete', false)) {
			echo json_encode([
				'success' => false,
				'error' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		echo $this->delete_file([
            'uuid'              => $uuid, 
            'delete_by'         => $this->input->get('by'), 
            'field_name'        => 'contract_file', 
            'upload_path_tmp'   => './uploads/tmp/',
            'table_name'        => 'campaign_kol_contracts',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/campaign_kol_contracts/'
        ]);
	}

	/**
	* Get Image Campaign Kol Contracts	* 
	* @return JSON
	*/
	public function get_contract_file_file($id)
	{
		if (!$this->is_allowed('campaign_kol_contracts_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => 'Image not loaded, you do not have permission to access'
				]);
			exit;
		}

		$campaign_kol_contracts = $this->model_campaign_kol_contracts->find($id);

		echo $this->get_file([
            'uuid'              => $id, 
            'delete_by'         => 'id', 
            'field_name'        => 'contract_file', 
            'table_name'        => 'campaign_kol_contracts',
            'primary_key'       => 'id',
            'upload_path'       => 'uploads/campaign_kol_contracts/',
            'delete_endpoint'   => 'administrator/campaign_kol_contracts/delete_contract_file_file'
        ]);
	}
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('campaign_kol_contracts_export');

		$this->model_campaign_kol_contracts->export('campaign_kol_contracts', 'campaign_kol_contracts');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('campaign_kol_contracts_export');

		$this->model_campaign_kol_contracts->pdf('campaign_kol_contracts', 'campaign_kol_contracts');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('campaign_kol_contracts_export');

		$table = $title = 'campaign_kol_contracts';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_campaign_kol_contracts->find($id);
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

	public function admin_counter_sign_action()
	{
		$id = (int)$this->input->post('id');
		$signature_data = $this->input->post('signature');
		$snk = $this->input->post('terms_and_conditions');
		$contract = $this->model_campaign_kol_contracts->find($id);

		if (!$contract) {
			echo json_encode(['success' => false, 'message' => 'Contract not found']);
			exit;
		}

		$upload_path = FCPATH . 'uploads/campaign_kol_contracts/';
		if (!is_dir($upload_path)) mkdir($upload_path, 0777, true);
		
		$signature_image_name = 'admin_sign_' . $id . '_' . time() . '.png';
		$signature_image_path = $upload_path . $signature_image_name;
		
		$signature_data = str_replace('data:image/png;base64,', '', $signature_data);
		$signature_data = str_replace(' ', '+', $signature_data);
		file_put_contents($signature_image_path, base64_decode($signature_data));

		$campaign_kol = $this->db->get_where('campaign_kols', ['id' => $contract->campaign_kol_id])->row();
		$kol = $campaign_kol ? $this->db->get_where('kols', ['id' => $campaign_kol->kol_id])->row() : null;
		$campaign = $campaign_kol ? $this->db->get_where('campaigns', ['id' => $campaign_kol->campaign_id])->row() : null;
		$kol_name = $kol ? $kol->name : 'KOL / Talent';

		// Update S&K if provided
		if ($snk !== null && trim($snk) !== '') {
			$this->db->where('id', $id)->update('campaign_kol_contracts', ['terms_and_conditions' => trim($snk)]);
			$contract->terms_and_conditions = trim($snk);
		}

		$current_snk = !empty($contract->terms_and_conditions) ? $contract->terms_and_conditions : ($campaign ? $campaign->terms_and_conditions : '');

		// Ensure HTML has S&K
		$html = $contract->contract_html;
		if (empty($html) || strpos($html, '{TERMS_AND_CONDITIONS}') !== false) {
			$template = $this->db->order_by('id', 'DESC')->get('contract_templates')->row();
			if ($template) {
				$html = $template->content;
				$html = str_replace('{KOL_NAME}', $kol ? $kol->name : '', $html);
				$html = str_replace('{KOL_ADDRESS}', $kol ? ($kol->address ?? '') : '', $html);
				$html = str_replace('{KOL_KTP}', $kol ? ($kol->ktp_number ?? '') : '', $html);
				$html = str_replace('{KOL_NPWP}', $kol ? ($kol->npwp_number ?? '') : '', $html);
				$html = str_replace('{BANK_NAME}', $kol ? ($kol->bank_name ?? '') : '', $html);
				$html = str_replace('{BANK_ACCOUNT}', $kol ? ($kol->bank_account_number ?? '') : '', $html);
				$html = str_replace('{BANK_OWNER}', $kol ? ($kol->bank_account_name ?? '') : '', $html);
				$html = str_replace('{CAMPAIGN_NAME}', $campaign ? $campaign->name : '', $html);
				$html = str_replace('{BRAND_NAME}', $campaign ? ($campaign->brand_name ?? '') : '', $html);
				$html = str_replace('{DEAL_AMOUNT}', 'Rp ' . number_format((float)($campaign_kol ? $campaign_kol->deal_amount : 0), 0, ',', '.'), $html);
				$html = str_replace('{CURRENT_DATE}', date('d F Y'), $html);
				$html = str_replace('{CONTRACT_NO}', $contract->contract_no, $html);
				$html = str_replace('{TERMS_AND_CONDITIONS}', nl2br(htmlspecialchars($current_snk)), $html);
				$this->db->where('id', $id)->update('campaign_kol_contracts', ['contract_html' => $html]);
			}
		}

		$this->load->library('HtmlPdf');
		$config = ['orientation' => 'p', 'format' => 'a4', 'marges' => [14, 14, 14, 14]];
		$this->pdf = new HtmlPdf($config);
		$admin_user = $this->aauth->get_user();
		$admin_name = $admin_user ? $admin_user->full_name : 'KOL-System Team';
		
		$kol_sign_file = $contract->kol_signature_image ? $this->_resolve_file_path($contract->kol_signature_image) : null;
		$admin_sign_file = $this->_resolve_file_path($signature_image_name);

		$pdf_html = $html;
		$pdf_html .= '<br><br>
			<table style="width: 100%;" cellspacing="0" cellpadding="0">
				<tr>
					<td style="width: 50%; text-align: center; vertical-align: top;">
						<span style="font-size: 10px; color: #475569; font-weight: bold;">Pihak Kedua (KOL / Talent)</span><br><br>';
		if ($kol_sign_file && file_exists($kol_sign_file)) {
			$pdf_html .= '<img src="' . $kol_sign_file . '" height="50" style="height: 50px;"><br><br>';
		} else {
			$pdf_html .= '<span style="font-size: 10px; color: #94a3b8; font-style: italic;">(Menunggu tanda tangan)</span><br><br>';
		}
		$pdf_html .= '		<span style="font-size: 10.5px; font-weight: bold; color: #0f172a; text-decoration: underline;">' . htmlspecialchars($kol_name) . '</span><br>
						<span style="font-size: 8.5px; color: #64748b;">KOL / Creator</span>
					</td>
					<td style="width: 50%; text-align: center; vertical-align: top;">
						<span style="font-size: 10px; color: #475569; font-weight: bold;">Pihak Pertama (Brand / Agency)</span><br><br>';
		if ($admin_sign_file && file_exists($admin_sign_file)) {
			$pdf_html .= '<img src="' . $admin_sign_file . '" height="50" style="height: 50px;"><br><br>';
		} else {
			$pdf_html .= '<span style="font-size: 10px; color: #0284c7; font-weight: bold;">[Admin Signed]</span><br><br>';
		}
		$pdf_html .= '		<span style="font-size: 10.5px; font-weight: bold; color: #0f172a; text-decoration: underline;">' . htmlspecialchars($admin_name) . '</span><br>
						<span style="font-size: 8.5px; color: #64748b;">KOL-System Team / Authorized</span>
					</td>
				</tr>
			</table>';

		// Sanitize font families for HTML2PDF / TCPDF compatibility
		$pdf_html = str_ireplace(['font-family: monospace;', 'font-family: monospace'], 'font-family: courier;', $pdf_html);
		$pdf_html = str_ireplace('monospace', 'courier', $pdf_html);

		if (session_status() === PHP_SESSION_ACTIVE) session_write_close();

		$safe_contract_no = str_replace(['/', '\\'], '_', $contract->contract_no);
		$pdf_name = 'contract_' . $safe_contract_no . '_' . time() . '.pdf';

		try {
			$content = '<page backtop="14mm" backbottom="14mm" backleft="14mm" backright="14mm">' . $pdf_html . '</page>';
			$this->pdf->initialize($config);
			$this->pdf->pdf->SetDisplayMode('fullpage');
			$this->pdf->writeHTML($content);
			$this->pdf->Output($upload_path . $pdf_name, 'F');
		} catch (Throwable $e) {
			log_message('error', 'PDF Generation Error: ' . $e->getMessage());
			echo json_encode([
				'success' => false,
				'message' => 'Gagal membuat file PDF kontrak: ' . $e->getMessage()
			]);
			exit;
		}

		$save_data = [
			'admin_signature_image' => $signature_image_name,
			'status' => 'PENDING'
		];
		
		// If KOL already signed, activate
		if ($contract->kol_signature_image && file_exists($upload_path . $contract->kol_signature_image)) {
			$sibling_dir = dirname(FCPATH) . '/kol-system-card-plugin/uploads/campaign_kol_contracts/';
			if (is_dir($sibling_dir)) {
				@copy($upload_path . $pdf_name, $sibling_dir . $pdf_name);
			}

			$save_data['contract_file'] = $pdf_name;
			$save_data['status'] = 'ACTIVE';
			$save_data['signed_at'] = date('Y-m-d H:i:s');
		}
		
		$updated = $this->model_campaign_kol_contracts->change($id, $save_data);
		if ($updated) {
			echo json_encode([
				'success' => true, 
				'message' => 'Tanda tangan Brand berhasil disimpan. Dokumen MoU siap ditandatangani oleh KOL melalui Portal KOL.'
			]);
			exit;
		} else {
			echo json_encode(['success' => false, 'message' => 'Failed to save updates']);
			exit;
		}
	}

	public function get_contract_html_ajax($id)
	{
		$contract = $this->db->get_where('campaign_kol_contracts', ['id' => $id])->row();
		if ($contract) {
			$campaign_kol = $this->db->get_where('campaign_kols', ['id' => $contract->campaign_kol_id])->row();
			$kol = $campaign_kol ? $this->db->get_where('kols', ['id' => $campaign_kol->kol_id])->row() : null;
			$campaign = $campaign_kol ? $this->db->get_where('campaigns', ['id' => $campaign_kol->campaign_id])->row() : null;
			$kol_name = $kol ? $kol->name : 'KOL / Talent';
			$admin_user = $this->aauth->get_user();
			$admin_name = $admin_user ? $admin_user->full_name : 'KOL-System Team';

			$default_snk = "1. Pihak Kedua wajib memproduksi dan menayangkan konten promosi sesuai brief, format, dan jadwal yang disepakati.\n"
				. "2. Pihak Kedua dilarang menghapus atau mengarsipkan konten yang telah diunggah sekurang-kurangnya selama 30 (tiga puluh) hari kalender.\n"
				. "3. Pihak Pertama (Brand) berhak menggunakan materi konten untuk keperluan promosi dan whitelisting digital ads selama 90 (sembilan puluh) hari.\n"
				. "4. Pihak Kedua bersedia melakukan revisi minor maksimal sebanyak 2 (dua) kali sebelum materi konten dipublikasikan.\n"
				. "5. Honorarium diproses sesuai invoice yang diterbitkan di awal setelah penandatanganan perjanjian ini.";

			$snk = !empty($contract->terms_and_conditions) ? $contract->terms_and_conditions : (!empty($campaign->terms_and_conditions) ? $campaign->terms_and_conditions : $default_snk);

			$html = $contract->contract_html;
			if (empty($html) || strpos($html, '{TERMS_AND_CONDITIONS}') !== false || strpos($html, 'SYARAT & KETENTUAN KHUSUS') === false) {
				$template = $this->db->order_by('id', 'DESC')->get('contract_templates')->row();
				if ($template) {
					$html = $template->content;
					$html = str_replace('{KOL_NAME}', $kol ? $kol->name : '', $html);
					$html = str_replace('{KOL_ADDRESS}', $kol ? ($kol->address ?? '') : '', $html);
					$html = str_replace('{KOL_KTP}', $kol ? ($kol->ktp_number ?? '') : '', $html);
					$html = str_replace('{KOL_NPWP}', $kol ? ($kol->npwp_number ?? '') : '', $html);
					$html = str_replace('{BANK_NAME}', $kol ? ($kol->bank_name ?? '') : '', $html);
					$html = str_replace('{BANK_ACCOUNT}', $kol ? ($kol->bank_account_number ?? '') : '', $html);
					$html = str_replace('{BANK_OWNER}', $kol ? ($kol->bank_account_name ?? '') : '', $html);
					$html = str_replace('{CAMPAIGN_NAME}', $campaign ? $campaign->name : '', $html);
					$html = str_replace('{BRAND_NAME}', $campaign ? ($campaign->brand_name ?? '') : '', $html);
					$html = str_replace('{DEAL_AMOUNT}', 'Rp ' . number_format((float)($campaign_kol ? $campaign_kol->deal_amount : 0), 0, ',', '.'), $html);
					$html = str_replace('{CURRENT_DATE}', date('d F Y'), $html);
					$html = str_replace('{CONTRACT_NO}', $contract ? $contract->contract_no : 'DRAFT', $html);
					$html = str_replace('{TERMS_AND_CONDITIONS}', nl2br(htmlspecialchars($snk)), $html);
					
					// Save it back to DB
					$this->db->where('id', $id)->update('campaign_kol_contracts', [
						'contract_html' => $html,
						'terms_and_conditions' => $snk
					]);
				}
			}

			$preview_html = $html . $this->_build_preview_signature_html($contract, $admin_name, $kol_name);

			echo json_encode([
				'success' => true, 
				'html' => $preview_html,
				'kol_name' => $kol_name,
				'admin_name' => $admin_name,
				'terms_and_conditions' => $snk,
				'contract_no' => $contract->contract_no,
				'deal_amount' => $campaign_kol ? (float)$campaign_kol->deal_amount : 0
			]);
			exit;
		} else {
			echo json_encode(['success' => false]);
			exit;
		}
	}

	/**
	 * Save updated S&K Brand and re-render MoU HTML (Feature 3)
	 */
	public function save_contract_snk_ajax()
	{
		if (!$this->is_allowed('campaign_kol_contracts_update', false) && !$this->is_allowed('campaign_kols_list', false)) {
			echo json_encode(['success' => false, 'message' => cclang('sorry_you_do_not_have_permission_to_access')]);
			exit;
		}

		$id = (int)$this->input->post('id');
		$snk = trim($this->input->post('terms_and_conditions') ?? '');

		$contract = $this->db->get_where('campaign_kol_contracts', ['id' => $id])->row();
		if (!$contract) {
			echo json_encode(['success' => false, 'message' => 'Kontrak tidak ditemukan.']);
			exit;
		}

		$campaign_kol = $this->db->get_where('campaign_kols', ['id' => $contract->campaign_kol_id])->row();
		$kol = $campaign_kol ? $this->db->get_where('kols', ['id' => $campaign_kol->kol_id])->row() : null;
		$campaign = $campaign_kol ? $this->db->get_where('campaigns', ['id' => $campaign_kol->campaign_id])->row() : null;
		$kol_name = $kol ? $kol->name : 'KOL / Talent';
		$admin_user = $this->aauth->get_user();
		$admin_name = $admin_user ? $admin_user->full_name : 'KOL-System Team';

		$template = $this->db->order_by('id', 'DESC')->get('contract_templates')->row();
		if ($template) {
			$html = $template->content;
			$html = str_replace('{KOL_NAME}', $kol ? $kol->name : '', $html);
			$html = str_replace('{KOL_ADDRESS}', $kol ? ($kol->address ?? '') : '', $html);
			$html = str_replace('{KOL_KTP}', $kol ? ($kol->ktp_number ?? '') : '', $html);
			$html = str_replace('{KOL_NPWP}', $kol ? ($kol->npwp_number ?? '') : '', $html);
			$html = str_replace('{BANK_NAME}', $kol ? ($kol->bank_name ?? '') : '', $html);
			$html = str_replace('{BANK_ACCOUNT}', $kol ? ($kol->bank_account_number ?? '') : '', $html);
			$html = str_replace('{BANK_OWNER}', $kol ? ($kol->bank_account_name ?? '') : '', $html);
			$html = str_replace('{CAMPAIGN_NAME}', $campaign ? $campaign->name : '', $html);
			$html = str_replace('{BRAND_NAME}', $campaign ? ($campaign->brand_name ?? '') : '', $html);
			$html = str_replace('{DEAL_AMOUNT}', 'Rp ' . number_format((float)($campaign_kol ? $campaign_kol->deal_amount : 0), 0, ',', '.'), $html);
			$html = str_replace('{CURRENT_DATE}', date('d F Y'), $html);
			$html = str_replace('{CONTRACT_NO}', $contract->contract_no, $html);
			$html = str_replace('{TERMS_AND_CONDITIONS}', nl2br(htmlspecialchars($snk)), $html);

			$this->db->where('id', $id)->update('campaign_kol_contracts', [
				'terms_and_conditions' => $snk,
				'contract_html' => $html
			]);

			$contract->terms_and_conditions = $snk;
			$contract->contract_html = $html;
			$preview_html = $html . $this->_build_preview_signature_html($contract, $admin_name, $kol_name);

			echo json_encode([
				'success' => true,
				'message' => 'Syarat & Ketentuan (S&K) Brand berhasil diperbarui pada MoU.',
				'html' => $preview_html,
				'terms_and_conditions' => $snk
			]);
			exit;
		} else {
			echo json_encode(['success' => false, 'message' => 'Template kontrak tidak ditemukan.']);
			exit;
		}
	}

	/**
	 * Build signature section for A4 Document preview
	 */
	private function _build_preview_signature_html($contract, $admin_name, $kol_name)
	{
		$admin_sign_img = '';
		if (!empty($contract->admin_signature_image)) {
			$admin_sign_url = base_url('uploads/campaign_kol_contracts/' . $contract->admin_signature_image);
			$admin_sign_img = '<img src="' . $admin_sign_url . '" style="height: 52px; max-width: 140px; object-fit: contain; display: block; margin: 0 auto 4px auto;">
				<div style="font-size: 9px; color: #16a34a; font-weight: 600;"><i class="fa fa-check-circle"></i> Ditandatangani Brand</div>';
		} else {
			$admin_sign_img = '<div style="height: 52px; border: 1px dashed #cbd5e1; border-radius: 4px; background: #f8fafc; color: #94a3b8; font-size: 10px; font-style: italic; line-height: 52px; text-align: center;">(Menunggu tanda tangan)</div>';
		}

		$kol_sign_img = '';
		if (!empty($contract->kol_signature_image)) {
			$kol_sign_url = base_url('uploads/campaign_kol_contracts/' . $contract->kol_signature_image);
			$kol_sign_img = '<img src="' . $kol_sign_url . '" style="height: 52px; max-width: 140px; object-fit: contain; display: block; margin: 0 auto 4px auto;">
				<div style="font-size: 9px; color: #16a34a; font-weight: 600;"><i class="fa fa-check-circle"></i> Ditandatangani KOL</div>';
		} else {
			$kol_sign_img = '<div style="height: 52px; border: 1px dashed #cbd5e1; border-radius: 4px; background: #f8fafc; color: #94a3b8; font-size: 10px; font-style: italic; line-height: 52px; text-align: center;">(Menunggu tanda tangan)</div>';
		}

		return '
		<div id="preview-signature-box" style="margin-top: 25px; padding-top: 15px; border-top: 1px solid #cbd5e1;">
			<table style="width: 100%; border-collapse: collapse;" cellspacing="0" cellpadding="0">
				<tr>
					<td style="width: 50%; text-align: center; vertical-align: top; padding: 0 12px;">
						<div style="font-size: 10.5px; font-weight: bold; color: #475569; text-transform: uppercase; margin-bottom: 6px;">Pihak Pertama (Brand / Agency)</div>
						<div id="preview-admin-sign-slot" style="min-height: 56px; margin-bottom: 4px;">' . $admin_sign_img . '</div>
						<div style="font-size: 11px; font-weight: bold; color: #0f172a; text-decoration: underline;">' . htmlspecialchars($admin_name) . '</div>
						<div style="font-size: 9.5px; color: #64748b;">KOL-System Team / Authorized Brand</div>
					</td>
					<td style="width: 50%; text-align: center; vertical-align: top; padding: 0 12px;">
						<div style="font-size: 10.5px; font-weight: bold; color: #475569; text-transform: uppercase; margin-bottom: 6px;">Pihak Kedua (KOL / Talent)</div>
						<div id="preview-kol-sign-slot" style="min-height: 56px; margin-bottom: 4px;">' . $kol_sign_img . '</div>
						<div style="font-size: 11px; font-weight: bold; color: #0f172a; text-decoration: underline;">' . htmlspecialchars($kol_name) . '</div>
						<div style="font-size: 9.5px; color: #64748b;">KOL / Creator / Talent</div>
					</td>
				</tr>
			</table>
		</div>';
	}

	/**
	 * KOL signs the MoU from "Kontrak Saya" (Feature 3)
	 */
	public function kol_sign_action()
	{
		if (!$this->aauth->is_loggedin()) {
			echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu.']);
			exit;
		}

		$id = (int)$this->input->post('id');
		$signature_data = $this->input->post('signature');
		$contract = $this->model_campaign_kol_contracts->find($id);

		if (!$contract) {
			echo json_encode(['success' => false, 'message' => 'Kontrak tidak ditemukan.']);
			exit;
		}

		$campaign_kol = $this->db->get_where('campaign_kols', ['id' => $contract->campaign_kol_id])->row();
		if (!$campaign_kol) {
			echo json_encode(['success' => false, 'message' => 'Data deal tidak ditemukan.']);
			exit;
		}

		// Security: verify that current logged-in user is the KOL or admin
		$current_kol_id = get_current_user_kol_id();
		if (!$this->aauth->is_admin() && $current_kol_id != $campaign_kol->kol_id) {
			echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki hak untuk menandatangani kontrak ini.']);
			exit;
		}

		$upload_path = FCPATH . 'uploads/campaign_kol_contracts/';
		if (!is_dir($upload_path)) mkdir($upload_path, 0777, true);

		$signature_image_name = 'kol_sign_' . $id . '_' . time() . '.png';
		$signature_image_path = $upload_path . $signature_image_name;

		$signature_data = str_replace('data:image/png;base64,', '', $signature_data);
		$signature_data = str_replace(' ', '+', $signature_data);
		file_put_contents($signature_image_path, base64_decode($signature_data));

		$this->load->library('HtmlPdf');
		$config = ['orientation' => 'p', 'format' => 'a4', 'marges' => [14, 14, 14, 14]];
		$this->pdf = new HtmlPdf($config);

		$kol = $this->db->get_where('kols', ['id' => $campaign_kol->kol_id])->row();
		$kol_name = $kol ? $kol->name : 'KOL / Talent';
		$admin_user = $contract->created_by ? $this->db->get_where('aauth_users', ['id' => $contract->created_by])->row() : null;
		$admin_name = $admin_user ? $admin_user->full_name : 'KOL-System Team';

		$kol_sign_file = $this->_resolve_file_path($signature_image_name);
		$admin_sign_file = $contract->admin_signature_image ? $this->_resolve_file_path($contract->admin_signature_image) : null;

		$pdf_html = $contract->contract_html;
		$pdf_html .= '<br><br>
			<table style="width: 100%;" cellspacing="0" cellpadding="0">
				<tr>
					<td style="width: 50%; text-align: center; vertical-align: top;">
						<span style="font-size: 10px; color: #475569; font-weight: bold;">Pihak Kedua (KOL / Talent)</span><br><br>';
		if ($kol_sign_file && file_exists($kol_sign_file)) {
			$pdf_html .= '<img src="' . $kol_sign_file . '" height="50" style="height: 50px;"><br><br>';
		} else {
			$pdf_html .= '<span style="font-size: 10px; color: #0284c7; font-weight: bold;">[KOL Signed]</span><br><br>';
		}
		$pdf_html .= '		<span style="font-size: 10.5px; font-weight: bold; color: #0f172a; text-decoration: underline;">' . htmlspecialchars($kol_name) . '</span><br>
						<span style="font-size: 8.5px; color: #64748b;">KOL / Creator</span>
					</td>
					<td style="width: 50%; text-align: center; vertical-align: top;">
						<span style="font-size: 10px; color: #475569; font-weight: bold;">Pihak Pertama (Brand / Agency)</span><br><br>';
		if ($admin_sign_file && file_exists($admin_sign_file)) {
			$pdf_html .= '<img src="' . $admin_sign_file . '" height="50" style="height: 50px;"><br><br>';
		} else {
			$pdf_html .= '<span style="font-size: 10px; color: #94a3b8; font-style: italic;">(Menunggu tanda tangan Admin)</span><br><br>';
		}
		$pdf_html .= '		<span style="font-size: 10.5px; font-weight: bold; color: #0f172a; text-decoration: underline;">' . htmlspecialchars($admin_name) . '</span><br>
						<span style="font-size: 8.5px; color: #64748b;">KOL-System Team / Authorized</span>
					</td>
				</tr>
			</table>';

		// Sanitize font families for HTML2PDF / TCPDF compatibility
		$pdf_html = str_ireplace(['font-family: monospace;', 'font-family: monospace'], 'font-family: courier;', $pdf_html);
		$pdf_html = str_ireplace('monospace', 'courier', $pdf_html);

		if (session_status() === PHP_SESSION_ACTIVE) session_write_close();

		$safe_contract_no = str_replace(['/', '\\'], '_', $contract->contract_no);
		$pdf_name = 'contract_' . $safe_contract_no . '_' . time() . '.pdf';

		try {
			$content = '<page backtop="14mm" backbottom="14mm" backleft="14mm" backright="14mm">' . $pdf_html . '</page>';
			$this->pdf->initialize($config);
			$this->pdf->pdf->SetDisplayMode('fullpage');
			$this->pdf->writeHTML($content);
			$this->pdf->Output($upload_path . $pdf_name, 'F');
		} catch (Throwable $e) {
			log_message('error', 'PDF Generation Error: ' . $e->getMessage());
			echo json_encode([
				'success' => false,
				'message' => 'Gagal membuat file PDF kontrak: ' . $e->getMessage()
			]);
			exit;
		}

		$sibling_dir = dirname(FCPATH) . '/kol-system-card-plugin/uploads/campaign_kol_contracts/';
		if (is_dir($sibling_dir)) {
			@copy($upload_path . $pdf_name, $sibling_dir . $pdf_name);
		}

		$save_data = [
			'kol_signature_image' => $signature_image_name,
			'contract_file' => $pdf_name,
			'status' => 'ACTIVE',
			'signed_at' => date('Y-m-d H:i:s')
		];

		$this->model_campaign_kol_contracts->change($id, $save_data);

		// Feature 4: Issue invoice by KOL upon signing MoU
		$issue_invoice = $this->input->post('issue_invoice');
		$invoice_id = null;
		if ($issue_invoice !== '0' && $issue_invoice !== false && $issue_invoice !== 0) {
			$invoice_id = $this->_auto_generate_invoice_for_contract($id, $this->aauth->get_user_id());
		}

		$msg = 'Surat Perjanjian (MoU) berhasil ditandatangani dan aktif.';
		if ($invoice_id) {
			$msg .= ' Invoice honorarium telah diterbitkan dan diajukan ke Brand.';
		}

		echo json_encode([
			'success' => true,
			'message' => $msg,
			'invoice_id' => $invoice_id
		]);
		exit;
	}

	/**
	 * KOL manually issues invoice for an active contract from Portal KOL (Feature 4)
	 */
	public function kol_issue_invoice_ajax()
	{
		if (!$this->aauth->is_loggedin()) {
			echo json_encode(['success' => false, 'message' => 'Silakan login terlebih dahulu.']);
			exit;
		}

		$contract_id = (int)$this->input->post('contract_id');
		$contract = $this->db->get_where('campaign_kol_contracts', ['id' => $contract_id])->row();
		if (!$contract) {
			echo json_encode(['success' => false, 'message' => 'Kontrak tidak ditemukan.']);
			exit;
		}

		$ck = $this->db->get_where('campaign_kols', ['id' => $contract->campaign_kol_id])->row();
		if (!$ck) {
			echo json_encode(['success' => false, 'message' => 'Data deal tidak ditemukan.']);
			exit;
		}

		$current_kol_id = get_current_user_kol_id();
		if (!$this->aauth->is_admin() && $current_kol_id != $ck->kol_id) {
			echo json_encode(['success' => false, 'message' => 'Anda tidak memiliki hak untuk menerbitkan invoice kontrak ini.']);
			exit;
		}

		if (!in_array($contract->status, ['SIGNED', 'ACTIVE'])) {
			echo json_encode(['success' => false, 'message' => 'Kontrak harus berstatus ACTIVE / SIGNED untuk dapat menerbitkan invoice.']);
			exit;
		}

		$invoice_id = $this->_auto_generate_invoice_for_contract($contract_id, $this->aauth->get_user_id());
		if ($invoice_id) {
			$inv = $this->db->get_where('invoices', ['id' => $invoice_id])->row();
			echo json_encode([
				'success' => true,
				'message' => 'Invoice ' . ($inv ? $inv->invoice_no : '') . ' berhasil diterbitkan dan diajukan ke Brand!',
				'invoice_id' => $invoice_id,
				'invoice_no' => ($inv ? $inv->invoice_no : '')
			]);
		} else {
			echo json_encode(['success' => false, 'message' => 'Gagal menerbitkan invoice atau invoice sudah diterbitkan sebelumnya.']);
		}
		exit;
	}

	/**
	 * Manual trigger to generate initial invoice from Finance Hub (Feature 4)
	 */
	public function generate_initial_invoice_ajax()
	{
		$contract_id = (int)$this->input->post('contract_id');
		$campaign_kol_id = (int)$this->input->post('campaign_kol_id');

		if ($contract_id <= 0 && $campaign_kol_id > 0) {
			$contract = $this->db->where('campaign_kol_id', $campaign_kol_id)
				->where_not_in('status', ['VOID', 'CANCELLED'])
				->order_by('id', 'DESC')
				->get('campaign_kol_contracts')
				->row();
			if ($contract) {
				$contract_id = $contract->id;
			}
		}

		if ($contract_id <= 0) {
			echo json_encode(['success' => false, 'message' => 'Kontrak tidak ditemukan.']);
			exit;
		}

		$invoice_id = $this->_auto_generate_invoice_for_contract($contract_id);
		if ($invoice_id) {
			$inv = $this->db->get_where('invoices', ['id' => $invoice_id])->row();
			echo json_encode([
				'success' => true,
				'message' => 'Invoice awal ' . ($inv ? $inv->invoice_no : '') . ' berhasil diterbitkan!',
				'invoice_id' => $invoice_id,
				'invoice_no' => ($inv ? $inv->invoice_no : '')
			]);
		} else {
			echo json_encode(['success' => false, 'message' => 'Gagal menerbitkan invoice awal.']);
		}
		exit;
	}

	/**
	 * Auto generate invoice in early stage after contract sign (Feature 4).
	 * Creates invoice with SUBMITTED status and PPh tax calculation.
	 *
	 * @param int $contract_id
	 * @return int|bool invoice_id or false
	 */
	public function _auto_generate_invoice_for_contract($contract_id, $actor_user_id = null)
	{
		$contract = $this->db->get_where('campaign_kol_contracts', ['id' => $contract_id])->row();
		if (!$contract) return false;

		$ck = $this->db->get_where('campaign_kols', ['id' => $contract->campaign_kol_id])->row();
		if (!$ck) return false;

		// Check if active invoice already exists for this deal
		$existing = $this->db->where('campaign_kol_id', $ck->id)
			->where_not_in('status', ['CANCELLED'])
			->get('invoices')
			->row();
		if ($existing) {
			return $existing->id;
		}

		$kol = $this->db->get_where('kols', ['id' => $ck->kol_id])->row();
		$subtotal = (float)($ck->deal_amount ?? 0);

		// Calculate Tax: PPh 21 (2.5% if NPWP exists, 3% if no NPWP)
		$tax_type = 'NONE';
		$tax_rate_percent = 0.00;
		$tax_amount = 0.00;

		if ($subtotal > 0) {
			if (!empty($kol->npwp_number)) {
				$tax_type = 'PPH21';
				$tax_rate_percent = 2.50;
				$tax_amount = round($subtotal * 0.025, 2);
			} else {
				$tax_type = 'PPH21';
				$tax_rate_percent = 3.00;
				$tax_amount = round($subtotal * 0.03, 2);
			}
		}

		$grand_total = max(0, $subtotal - $tax_amount);

		$today = date('Ymd');
		$this->db->like('invoice_no', 'INV-' . $today);
		$this->db->order_by('id', 'DESC');
		$this->db->limit(1);
		$last_invoice = $this->db->get('invoices')->row();
		if ($last_invoice) {
			$last_no = explode('-', $last_invoice->invoice_no);
			$next_no = sprintf('%04d', intval(end($last_no)) + 1);
		} else {
			$next_no = '0001';
		}
		$invoice_no = 'INV-' . $today . '-' . $next_no;

		$user_id = !empty($actor_user_id) ? $actor_user_id : (method_exists($this->aauth, 'get_user_id') ? $this->aauth->get_user_id() : 1);
		if (empty($user_id)) $user_id = 1;

		$invoice_data = [
			'campaign_kol_id'  => $ck->id,
			'invoice_no'       => $invoice_no,
			'subtotal'         => $subtotal,
			'tax_type'         => $tax_type,
			'tax_rate_percent' => $tax_rate_percent,
			'tax_amount'       => $tax_amount,
			'grand_total'      => $grand_total,
			'due_date'         => date('Y-m-d', strtotime('+14 days')),
			'active_deal_key'  => $ck->id,
			'status'           => 'SUBMITTED',
			'created_by'       => $user_id,
			'created_at'       => date('Y-m-d H:i:s'),
		];

		$this->db->insert('invoices', $invoice_data);
		$invoice_id = $this->db->insert_id();

		if ($invoice_id) {
			$notes = 'Invoice resmi diterbitkan oleh KOL (' . ($kol ? $kol->name : 'KOL') . ') melalui Portal KOL setelah MoU ' . $contract->contract_no . ' aktif. Subtotal: Rp ' . number_format($subtotal, 0, ',', '.') . ', PPh 21: Rp ' . number_format($tax_amount, 0, ',', '.') . ', Net: Rp ' . number_format($grand_total, 0, ',', '.');
			$this->db->insert('invoice_histories', [
				'invoice_id'    => $invoice_id,
				'action'        => 'SUBMITTED',
				'notes'         => $notes,
				'actor_user_id' => $user_id,
				'created_at'    => date('Y-m-d H:i:s')
			]);
			return $invoice_id;
		}

		return false;
	}

	/**
	 * Secure PDF Streaming Endpoint
	 */
	public function download_pdf($id)
	{
		$contract = $this->model_campaign_kol_contracts->find($id);
		if (!$contract || empty($contract->contract_file)) {
			show_404();
		}

		$file_path = $this->_resolve_file_path($contract->contract_file);
		if (!$file_path || !file_exists($file_path)) {
			show_404();
		}

		header('Content-Type: application/pdf');
		header('Content-Disposition: inline; filename="' . basename($contract->contract_file) . '"');
		header('Content-Transfer-Encoding: binary');
		header('Accept-Ranges: bytes');
		readfile($file_path);
		exit;
	}

	/**
	 * Resolves file path across local and shared sibling directories
	 */
	private function _resolve_file_path($filename)
	{
		if (empty($filename)) return null;

		$paths = [
			FCPATH . 'uploads/campaign_kol_contracts/' . $filename,
			dirname(FCPATH) . '/campaign/uploads/campaign_kol_contracts/' . $filename,
			dirname(FCPATH) . '/datahub/uploads/campaign_kol_contracts/' . $filename,
			// fallback nama folder lama (kalau pernah dideploy dengan nama tersebut)
			dirname(FCPATH) . '/KOL-System/uploads/campaign_kol_contracts/' . $filename,
			dirname(FCPATH) . '/KOL-System-card-generator-plugin/uploads/campaign_kol_contracts/' . $filename,
		];

		foreach ($paths as $path) {
			if (file_exists($path)) {
				return $path;
			}
		}

		return $paths[0];
	}

}
/* End of file campaign_kol_contracts.php */
/* Location: ./application/controllers/administrator/Campaign Kol Contracts.php */