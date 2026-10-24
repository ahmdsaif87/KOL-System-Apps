<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Kol Rate Cards Controller
*| --------------------------------------------------------------------------
*| Kol Rate Cards site
*|
*/
class Kol_rate_cards extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_kol_rate_cards');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Kol Rate Cardss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('kol_rate_cards_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['kol_rate_cardss'] = $this->model_kol_rate_cards->get($filter, $field, $this->limit_page, $offset);
		$this->data['kol_rate_cards_counts'] = $this->model_kol_rate_cards->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/kol_rate_cards/index/',
			'total_rows'   => $this->model_kol_rate_cards->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Kol Rate Cards List');
		$this->render('backend/standart/administrator/kol_rate_cards/kol_rate_cards_list', $this->data);
	}
	
	/**
	* Add new kol_rate_cardss
	*
	*/
	public function add()
	{
		$this->is_allowed('kol_rate_cards_add');

		$this->template->title('Kol Rate Cards New');
		$this->render('backend/standart/administrator/kol_rate_cards/kol_rate_cards_add', $this->data);
	}

	/**
	* Add New Kol Rate Cardss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('kol_rate_cards_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('kol_platform_id', 'Platform KOL', 'trim|required');
		$this->form_validation->set_rules('content_type', 'Type Content', 'trim|max_length[100]|required|in_list[' . implode(',', array_keys(kol_rate_content_types())) . ']');
		$this->form_validation->set_rules('rate', 'Budget', 'trim|max_length[15]|required');
		$this->form_validation->set_rules('currency_code', 'Code Currency', 'trim|max_length[3]|required');
		$this->form_validation->set_rules('is_active', 'Status', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'kol_platform_id' => $this->input->post('kol_platform_id'),
				'content_type' => normalize_content_type($this->input->post('content_type')),
				'rate' => $this->input->post('rate'),
				'currency_code' => $this->input->post('currency_code'),
				'notes' => $this->input->post('notes'),
				'is_active' => $this->input->post('is_active'),
			];

			
			$save_kol_rate_cards = $this->model_kol_rate_cards->store($save_data);
            

			if ($save_kol_rate_cards) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_kol_rate_cards;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/kol_rate_cards/edit/' . $save_kol_rate_cards, 'Edit Kol Rate Cards'),
						anchor('administrator/kol_rate_cards', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/kol_rate_cards/edit/' . $save_kol_rate_cards, 'Edit Kol Rate Cards')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kol_rate_cards');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kol_rate_cards');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Kol Rate Cardss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('kol_rate_cards_update');

		$this->data['kol_rate_cards'] = $this->model_kol_rate_cards->find($id);

		$this->template->title('Kol Rate Cards Update');
		$this->render('backend/standart/administrator/kol_rate_cards/kol_rate_cards_update', $this->data);
	}

	/**
	* Update Kol Rate Cardss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('kol_rate_cards_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('kol_platform_id', 'Platform KOL', 'trim|required');
		$this->form_validation->set_rules('content_type', 'Type Content', 'trim|max_length[100]|required');
		$this->form_validation->set_rules('rate', 'Budget', 'trim|max_length[15]|required');
		$this->form_validation->set_rules('currency_code', 'Code Currency', 'trim|max_length[3]|required');
		$this->form_validation->set_rules('is_active', 'Status', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'kol_platform_id' => $this->input->post('kol_platform_id'),
				'content_type' => normalize_content_type($this->input->post('content_type')),
				'rate' => $this->input->post('rate'),
				'currency_code' => $this->input->post('currency_code'),
				'notes' => $this->input->post('notes'),
				'is_active' => $this->input->post('is_active'),
			];

			
			$save_kol_rate_cards = $this->model_kol_rate_cards->change($id, $save_data);

			if ($save_kol_rate_cards) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/kol_rate_cards', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kol_rate_cards');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kol_rate_cards');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Kol Rate Cardss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('kol_rate_cards_delete');

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
            set_message(cclang('has_been_deleted', 'kol_rate_cards'), 'success');
        } else {
            set_message(cclang('error_delete', 'kol_rate_cards'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Kol Rate Cardss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('kol_rate_cards_view');

		$this->data['kol_rate_cards'] = $this->model_kol_rate_cards->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Kol Rate Cards Detail');
		$this->render('backend/standart/administrator/kol_rate_cards/kol_rate_cards_view', $this->data);
	}
	
	/**
	* delete Kol Rate Cardss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$kol_rate_cards = $this->model_kol_rate_cards->find($id);

		
		
		return $this->model_kol_rate_cards->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('kol_rate_cards_export');

		$this->model_kol_rate_cards->export('kol_rate_cards', 'kol_rate_cards');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('kol_rate_cards_export');

		$this->model_kol_rate_cards->pdf('kol_rate_cards', 'kol_rate_cards');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('kol_rate_cards_export');

		$table = $title = 'kol_rate_cards';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_kol_rate_cards->find($id);
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


/* End of file kol_rate_cards.php */
/* Location: ./application/controllers/administrator/Kol Rate Cards.php */