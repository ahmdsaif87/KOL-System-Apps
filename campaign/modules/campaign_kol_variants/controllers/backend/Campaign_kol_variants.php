<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Campaign Kol Variants Controller
*| --------------------------------------------------------------------------
*| Campaign Kol Variants site
*|
*/
class Campaign_kol_variants extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_campaign_kol_variants');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Campaign Kol Variantss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('campaign_kol_variants_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['campaign_kol_variantss'] = $this->model_campaign_kol_variants->get($filter, $field, $this->limit_page, $offset);
		$this->data['campaign_kol_variants_counts'] = $this->model_campaign_kol_variants->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/campaign_kol_variants/index/',
			'total_rows'   => $this->model_campaign_kol_variants->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Campaign Kol Variants List');
		$this->render('backend/standart/administrator/campaign_kol_variants/campaign_kol_variants_list', $this->data);
	}
	
	/**
	* Add new campaign_kol_variantss
	*
	*/
	public function add()
	{
		$this->is_allowed('campaign_kol_variants_add');

		$this->template->title('Campaign Kol Variants New');
		$this->render('backend/standart/administrator/campaign_kol_variants/campaign_kol_variants_add', $this->data);
	}

	/**
	* Add New Campaign Kol Variantss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('campaign_kol_variants_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('campaign_kol_id', 'Assigned KOL', 'trim|required');
		$this->form_validation->set_rules('variant_id', 'Konsep Kreatif Konten', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'campaign_kol_id' => $this->input->post('campaign_kol_id'),
				'variant_id' => $this->input->post('variant_id'),
			];

			
			$save_campaign_kol_variants = $this->model_campaign_kol_variants->store($save_data);
            

			if ($save_campaign_kol_variants) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_campaign_kol_variants;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/campaign_kol_variants/edit/' . $save_campaign_kol_variants, 'Edit Campaign Kol Variants'),
						anchor('administrator/campaign_kol_variants', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/campaign_kol_variants/edit/' . $save_campaign_kol_variants, 'Edit Campaign Kol Variants')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/campaign_kol_variants');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/campaign_kol_variants');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Campaign Kol Variantss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('campaign_kol_variants_update');

		$this->data['campaign_kol_variants'] = $this->model_campaign_kol_variants->find($id);

		$this->template->title('Campaign Kol Variants Update');
		$this->render('backend/standart/administrator/campaign_kol_variants/campaign_kol_variants_update', $this->data);
	}

	/**
	* Update Campaign Kol Variantss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('campaign_kol_variants_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('campaign_kol_id', 'Assigned KOL', 'trim|required');
		$this->form_validation->set_rules('variant_id', 'Konsep Kreatif Konten', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'campaign_kol_id' => $this->input->post('campaign_kol_id'),
				'variant_id' => $this->input->post('variant_id'),
			];

			
			$save_campaign_kol_variants = $this->model_campaign_kol_variants->change($id, $save_data);

			if ($save_campaign_kol_variants) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/campaign_kol_variants', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/campaign_kol_variants');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/campaign_kol_variants');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Campaign Kol Variantss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('campaign_kol_variants_delete');

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
            set_message(cclang('has_been_deleted', 'campaign_kol_variants'), 'success');
        } else {
            set_message(cclang('error_delete', 'campaign_kol_variants'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Campaign Kol Variantss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('campaign_kol_variants_view');

		$this->data['campaign_kol_variants'] = $this->model_campaign_kol_variants->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Campaign Kol Variants Detail');
		$this->render('backend/standart/administrator/campaign_kol_variants/campaign_kol_variants_view', $this->data);
	}
	
	/**
	* delete Campaign Kol Variantss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$campaign_kol_variants = $this->model_campaign_kol_variants->find($id);

		
		
		return $this->model_campaign_kol_variants->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('campaign_kol_variants_export');

		$this->model_campaign_kol_variants->export('campaign_kol_variants', 'campaign_kol_variants');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('campaign_kol_variants_export');

		$this->model_campaign_kol_variants->pdf('campaign_kol_variants', 'campaign_kol_variants');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('campaign_kol_variants_export');

		$table = $title = 'campaign_kol_variants';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_campaign_kol_variants->find($id);
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


/* End of file campaign_kol_variants.php */
/* Location: ./application/controllers/administrator/Campaign Kol Variants.php */