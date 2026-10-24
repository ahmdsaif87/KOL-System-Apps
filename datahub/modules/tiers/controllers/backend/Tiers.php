<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Tiers Controller
*| --------------------------------------------------------------------------
*| Tiers site
*|
*/
class Tiers extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_tiers');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Tierss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('tiers_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['tierss'] = $this->model_tiers->get($filter, $field, $this->limit_page, $offset);
		$this->data['tiers_counts'] = $this->model_tiers->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/tiers/index/',
			'total_rows'   => $this->model_tiers->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Tiers KOL List');
		$this->render('backend/standart/administrator/tiers/tiers_list', $this->data);
	}
	
	/**
	* Add new tierss
	*
	*/
	public function add()
	{
		$this->is_allowed('tiers_add');

		$this->template->title('Tiers KOL New');
		$this->render('backend/standart/administrator/tiers/tiers_add', $this->data);
	}

	/**
	* Add New Tierss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('tiers_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('description', 'Description', 'trim|required');
		$this->form_validation->set_rules('min_followers', 'Min Followers', 'trim|required');
		$this->form_validation->set_rules('max_followers', 'Max Followers', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Status Active', 'trim|required|max_length[1]');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'min_followers' => $this->input->post('min_followers'),
				'max_followers' => $this->input->post('max_followers'),
				'is_active' => $this->input->post('is_active'),
			];

			
			$save_tiers = $this->model_tiers->store($save_data);
            

			if ($save_tiers) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_tiers;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/tiers/edit/' . $save_tiers, 'Edit Tiers'),
						anchor('administrator/tiers', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/tiers/edit/' . $save_tiers, 'Edit Tiers')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tiers');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tiers');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Tierss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('tiers_update');

		$this->data['tiers'] = $this->model_tiers->find($id);

		$this->template->title('Tiers KOL Update');
		$this->render('backend/standart/administrator/tiers/tiers_update', $this->data);
	}

	/**
	* Update Tierss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('tiers_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[50]');
		$this->form_validation->set_rules('description', 'Description', 'trim|required');
		$this->form_validation->set_rules('min_followers', 'Min Followers', 'trim|required');
		$this->form_validation->set_rules('max_followers', 'Max Followers', 'trim|required');
		$this->form_validation->set_rules('is_active', 'Status Active', 'trim|required|max_length[1]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'name' => $this->input->post('name'),
				'description' => $this->input->post('description'),
				'min_followers' => $this->input->post('min_followers'),
				'max_followers' => $this->input->post('max_followers'),
				'is_active' => $this->input->post('is_active'),
			];

			
			$save_tiers = $this->model_tiers->change($id, $save_data);

			if ($save_tiers) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/tiers', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tiers');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tiers');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Tierss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('tiers_delete');

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
            set_message(cclang('has_been_deleted', 'tiers'), 'success');
        } else {
            set_message(cclang('error_delete', 'tiers'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Tierss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('tiers_view');

		$this->data['tiers'] = $this->model_tiers->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Tiers KOL Detail');
		$this->render('backend/standart/administrator/tiers/tiers_view', $this->data);
	}
	
	/**
	* delete Tierss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$tiers = $this->model_tiers->find($id);

		
		
		return $this->model_tiers->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('tiers_export');

		$this->model_tiers->export('tiers', 'tiers');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('tiers_export');

		$this->model_tiers->pdf('tiers', 'tiers');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('tiers_export');

		$table = $title = 'tiers';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_tiers->find($id);
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


/* End of file tiers.php */
/* Location: ./application/controllers/administrator/Tiers.php */