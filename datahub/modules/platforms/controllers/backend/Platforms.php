<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Platforms Controller
*| --------------------------------------------------------------------------
*| Platforms site
*|
*/
class Platforms extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_platforms');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Platformss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('platforms_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['platformss'] = $this->model_platforms->get($filter, $field, $this->limit_page, $offset);
		$this->data['platforms_counts'] = $this->model_platforms->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/platforms/index/',
			'total_rows'   => $this->model_platforms->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Platforms Sosial Media List');
		$this->render('backend/standart/administrator/platforms/platforms_list', $this->data);
	}
	
	/**
	* Add new platformss
	*
	*/
	public function add()
	{
		$this->is_allowed('platforms_add');

		$this->template->title('Platforms Sosial Media New');
		$this->render('backend/standart/administrator/platforms/platforms_add', $this->data);
	}

	/**
	* Add New Platformss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('platforms_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('code', 'Code', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('icon_url', 'Icon URL', 'trim|max_length[500]');
		$this->form_validation->set_rules('color_hex', 'Color HEX', 'trim|max_length[7]');
		$this->form_validation->set_rules('is_active', 'Status Active', 'trim|required|max_length[1]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'name' => $this->input->post('name'),
				'code' => $this->input->post('code'),
				'icon_url' => $this->input->post('icon_url'),
				'color_hex' => $this->input->post('color_hex'),
				'is_active' => $this->input->post('is_active'),
			];

			
			$save_platforms = $this->model_platforms->store($save_data);
            

			if ($save_platforms) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_platforms;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/platforms/edit/' . $save_platforms, 'Edit Platforms'),
						anchor('administrator/platforms', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/platforms/edit/' . $save_platforms, 'Edit Platforms')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/platforms');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/platforms');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Platformss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('platforms_update');

		$this->data['platforms'] = $this->model_platforms->find($id);

		$this->template->title('Platforms Sosial Media Update');
		$this->render('backend/standart/administrator/platforms/platforms_update', $this->data);
	}

	/**
	* Update Platformss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('platforms_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('code', 'Code', 'trim|required|max_length[30]');
		$this->form_validation->set_rules('icon_url', 'Icon URL', 'trim|max_length[500]');
		$this->form_validation->set_rules('color_hex', 'Color HEX', 'trim|max_length[7]');
		$this->form_validation->set_rules('is_active', 'Status Active', 'trim|required|max_length[1]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'name' => $this->input->post('name'),
				'code' => $this->input->post('code'),
				'icon_url' => $this->input->post('icon_url'),
				'color_hex' => $this->input->post('color_hex'),
				'is_active' => $this->input->post('is_active'),
			];

			
			$save_platforms = $this->model_platforms->change($id, $save_data);

			if ($save_platforms) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/platforms', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/platforms');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/platforms');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Platformss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('platforms_delete');

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
            set_message(cclang('has_been_deleted', 'platforms'), 'success');
        } else {
            set_message(cclang('error_delete', 'platforms'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Platformss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('platforms_view');

		$this->data['platforms'] = $this->model_platforms->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Platforms Sosial Media Detail');
		$this->render('backend/standart/administrator/platforms/platforms_view', $this->data);
	}
	
	/**
	* delete Platformss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$platforms = $this->model_platforms->find($id);

		
		
		return $this->model_platforms->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('platforms_export');

		$this->model_platforms->export('platforms', 'platforms');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('platforms_export');

		$this->model_platforms->pdf('platforms', 'platforms');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('platforms_export');

		$table = $title = 'platforms';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_platforms->find($id);
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


/* End of file platforms.php */
/* Location: ./application/controllers/administrator/Platforms.php */