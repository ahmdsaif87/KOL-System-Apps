<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Variants Controller
*| --------------------------------------------------------------------------
*| Variants site
*|
*/
class Variants extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_variants');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Variantss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('variants_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['variantss'] = $this->model_variants->get($filter, $field, $this->limit_page, $offset);
		$this->data['variants_counts'] = $this->model_variants->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/variants/index/',
			'total_rows'   => $this->model_variants->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Variant Konsep Konten List');
		$this->render('backend/standart/administrator/variants/variants_list', $this->data);
	}
	
	/**
	* Add new variantss
	*
	*/
	public function add()
	{
		$this->is_allowed('variants_add');

		$this->template->title('Variant Konsep Konten New');
		$this->render('backend/standart/administrator/variants/variants_add', $this->data);
	}

	/**
	* Add New Variantss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('variants_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('name', 'Name Konsep', 'trim|max_length[100]|required');
		$this->form_validation->set_rules('code', 'Code Konsep', 'trim|max_length[50]|required');
		$this->form_validation->set_rules('description', 'Description', 'trim|max_length[500]');
		$this->form_validation->set_rules('is_active', 'Is Active', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'name' => $this->input->post('name'),
				'code' => $this->input->post('code'),
				'description' => $this->input->post('description'),
				'is_active' => $this->input->post('is_active'),
			];

			
			$save_variants = $this->model_variants->store($save_data);
            

			if ($save_variants) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_variants;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/variants/edit/' . $save_variants, 'Edit Variants'),
						anchor('administrator/variants', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/variants/edit/' . $save_variants, 'Edit Variants')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/variants');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/variants');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Variantss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('variants_update');

		$this->data['variants'] = $this->model_variants->find($id);

		$this->template->title('Variant Konsep Konten Update');
		$this->render('backend/standart/administrator/variants/variants_update', $this->data);
	}

	/**
	* Update Variantss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('variants_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('name', 'Name Konsep', 'trim|max_length[100]|required');
		$this->form_validation->set_rules('code', 'Code Konsep', 'trim|max_length[50]|required');
		$this->form_validation->set_rules('description', 'Description', 'trim|max_length[500]');
		$this->form_validation->set_rules('is_active', 'Is Active', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'name' => $this->input->post('name'),
				'code' => $this->input->post('code'),
				'description' => $this->input->post('description'),
				'is_active' => $this->input->post('is_active'),
			];

			
			$save_variants = $this->model_variants->change($id, $save_data);

			if ($save_variants) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/variants', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/variants');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/variants');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Variantss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('variants_delete');

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
            set_message(cclang('has_been_deleted', 'variants'), 'success');
        } else {
            set_message(cclang('error_delete', 'variants'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Variantss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('variants_view');

		$this->data['variants'] = $this->model_variants->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Variant Konsep Konten Detail');
		$this->render('backend/standart/administrator/variants/variants_view', $this->data);
	}
	
	/**
	* delete Variantss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$variants = $this->model_variants->find($id);

		
		
		return $this->model_variants->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('variants_export');

		$this->model_variants->export('variants', 'variants');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('variants_export');

		$this->model_variants->pdf('variants', 'variants');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('variants_export');

		$table = $title = 'variants';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_variants->find($id);
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


/* End of file variants.php */
/* Location: ./application/controllers/administrator/Variants.php */