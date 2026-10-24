<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Contract Templates Controller
*| --------------------------------------------------------------------------
*| Contract Templates site
*|
*/
class Contract_templates extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_contract_templates');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Contract Templatess
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('contract_templates_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['contract_templatess'] = $this->model_contract_templates->get($filter, $field, $this->limit_page, $offset);
		$this->data['contract_templates_counts'] = $this->model_contract_templates->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/contract_templates/index/',
			'total_rows'   => $this->model_contract_templates->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Contract Templates List');
		$this->render('backend/standart/administrator/contract_templates/contract_templates_list', $this->data);
	}
	
	/**
	* Add new contract_templatess
	*
	*/
	public function add()
	{
		$this->is_allowed('contract_templates_add');

		$this->template->title('Contract Templates New');
		$this->render('backend/standart/administrator/contract_templates/contract_templates_add', $this->data);
	}

	/**
	* Add New Contract Templatess
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('contract_templates_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		$this->form_validation->set_rules('content', 'Content', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'title' => $this->input->post('title'),
				'content' => $this->input->post('content'),
			];

			
			$save_contract_templates = $this->model_contract_templates->store($save_data);
            

			if ($save_contract_templates) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_contract_templates;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/contract_templates/edit/' . $save_contract_templates, 'Edit Contract Templates'),
						anchor('administrator/contract_templates', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/contract_templates/edit/' . $save_contract_templates, 'Edit Contract Templates')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/contract_templates');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/contract_templates');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Contract Templatess
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('contract_templates_update');

		$this->data['contract_templates'] = $this->model_contract_templates->find($id);

		$this->template->title('Contract Templates Update');
		$this->render('backend/standart/administrator/contract_templates/contract_templates_update', $this->data);
	}

	/**
	* Update Contract Templatess
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('contract_templates_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		$this->form_validation->set_rules('content', 'Content', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'title' => $this->input->post('title'),
				'content' => $this->input->post('content'),
			];

			
			$save_contract_templates = $this->model_contract_templates->change($id, $save_data);

			if ($save_contract_templates) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/contract_templates', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/contract_templates');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/contract_templates');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Contract Templatess
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('contract_templates_delete');

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
            set_message(cclang('has_been_deleted', 'contract_templates'), 'success');
        } else {
            set_message(cclang('error_delete', 'contract_templates'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Contract Templatess
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('contract_templates_view');

		$this->data['contract_templates'] = $this->model_contract_templates->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Contract Templates Detail');
		$this->render('backend/standart/administrator/contract_templates/contract_templates_view', $this->data);
	}
	
	/**
	* delete Contract Templatess
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$contract_templates = $this->model_contract_templates->find($id);

		
		
		return $this->model_contract_templates->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('contract_templates_export');

		$this->model_contract_templates->export('contract_templates', 'contract_templates');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('contract_templates_export');

		$this->model_contract_templates->pdf('contract_templates', 'contract_templates');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('contract_templates_export');

		$table = $title = 'contract_templates';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_contract_templates->find($id);
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


/* End of file contract_templates.php */
/* Location: ./application/controllers/administrator/Contract Templates.php */