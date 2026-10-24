<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Kol Platforms Controller
*| --------------------------------------------------------------------------
*| Kol Platforms site
*|
*/
class Kol_platforms extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_kol_platforms');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Kol Platformss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('kol_platforms_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['kol_platformss'] = $this->model_kol_platforms->get($filter, $field, $this->limit_page, $offset);
		$this->data['kol_platforms_counts'] = $this->model_kol_platforms->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/kol_platforms/index/',
			'total_rows'   => $this->model_kol_platforms->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Kol Platforms List');
		$this->render('backend/standart/administrator/kol_platforms/kol_platforms_list', $this->data);
	}
	
	/**
	* Add new kol_platformss
	*
	*/
	public function add()
	{
		$this->is_allowed('kol_platforms_add');

		$this->template->title('Kol Platforms New');
		$this->render('backend/standart/administrator/kol_platforms/kol_platforms_add', $this->data);
	}

	/**
	* Add New Kol Platformss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('kol_platforms_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('kol_id', 'KOL', 'trim|required');
		$this->form_validation->set_rules('platform_id', 'Platforms', 'trim|required');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('profile_url', 'URL Profile', 'trim|required|max_length[500]');
		$this->form_validation->set_rules('followers', 'Followers', 'trim|required');
		$this->form_validation->set_rules('is_verified', 'Verified Status', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Status', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'kol_id' => $this->input->post('kol_id'),
				'platform_id' => $this->input->post('platform_id'),
				'username' => $this->input->post('username'),
				'profile_url' => $this->input->post('profile_url'),
				'followers' => $this->input->post('followers'),
				'avg_views' => $this->input->post('avg_views'),
				'avg_er' => $this->input->post('avg_er'),
				'is_verified' => $this->input->post('is_verified'),
				'is_active' => $this->input->post('is_active'),
			];

			
			$save_kol_platforms = $this->model_kol_platforms->store($save_data);
            

			if ($save_kol_platforms) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_kol_platforms;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/kol_platforms/edit/' . $save_kol_platforms, 'Edit Kol Platforms'),
						anchor('administrator/kol_platforms', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/kol_platforms/edit/' . $save_kol_platforms, 'Edit Kol Platforms')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kol_platforms');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kol_platforms');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Kol Platformss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('kol_platforms_update');

		$this->data['kol_platforms'] = $this->model_kol_platforms->find($id);

		$this->template->title('Kol Platforms Update');
		$this->render('backend/standart/administrator/kol_platforms/kol_platforms_update', $this->data);
	}

	/**
	* Update Kol Platformss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('kol_platforms_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('kol_id', 'KOL', 'trim|required');
		$this->form_validation->set_rules('platform_id', 'Platforms', 'trim|required');
		$this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[150]');
		$this->form_validation->set_rules('profile_url', 'URL Profile', 'trim|required|max_length[500]');
		$this->form_validation->set_rules('followers', 'Followers', 'trim|required');
		$this->form_validation->set_rules('is_verified', 'Verified Status', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Status', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'kol_id' => $this->input->post('kol_id'),
				'platform_id' => $this->input->post('platform_id'),
				'username' => $this->input->post('username'),
				'profile_url' => $this->input->post('profile_url'),
				'followers' => $this->input->post('followers'),
				'avg_views' => $this->input->post('avg_views'),
				'avg_er' => $this->input->post('avg_er'),
				'is_verified' => $this->input->post('is_verified'),
				'is_active' => $this->input->post('is_active'),
			];

			
			$save_kol_platforms = $this->model_kol_platforms->change($id, $save_data);

			if ($save_kol_platforms) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/kol_platforms', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kol_platforms');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kol_platforms');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* Sync KOL Profile Stats
	*
	* @var $id String
	*/
	public function sync($id = null)
	{
		$this->is_allowed('kol_platforms_update');

		$this->load->helper('url');

		$kol_platforms = $this->model_kol_platforms->find($id);

		if (!$kol_platforms) {
			set_message('KOL Platform not found', 'warning');
			redirect_back();
		}

		$result = $this->model_kol_platforms->sync_profile($id);

		if ($result['success']) {
			set_message($result['message'], 'success');
		} else {
			set_message($result['message'], 'warning');
		}

		redirect_back();
	}

	/**
	* delete Kol Platformss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('kol_platforms_delete');

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
            set_message(cclang('has_been_deleted', 'kol_platforms'), 'success');
        } else {
            set_message(cclang('error_delete', 'kol_platforms'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Kol Platformss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('kol_platforms_view');

		$this->data['kol_platforms'] = $this->model_kol_platforms->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Kol Platforms Detail');
		$this->render('backend/standart/administrator/kol_platforms/kol_platforms_view', $this->data);
	}
	
	/**
	* delete Kol Platformss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$kol_platforms = $this->model_kol_platforms->find($id);

		
		
		return $this->model_kol_platforms->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('kol_platforms_export');

		$this->model_kol_platforms->export('kol_platforms', 'kol_platforms');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('kol_platforms_export');

		$this->model_kol_platforms->pdf('kol_platforms', 'kol_platforms');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('kol_platforms_export');

		$table = $title = 'kol_platforms';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_kol_platforms->find($id);
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
}


/* End of file kol_platforms.php */
/* Location: ./application/controllers/administrator/Kol Platforms.php */