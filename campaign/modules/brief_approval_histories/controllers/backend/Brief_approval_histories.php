<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Brief Approval Histories Controller
*| --------------------------------------------------------------------------
*| Brief Approval Histories site
*|
*/
class Brief_approval_histories extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_brief_approval_histories');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Brief Approval Historiess
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('brief_approval_histories_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['brief_approval_historiess'] = $this->model_brief_approval_histories->get($filter, $field, $this->limit_page, $offset);
		$this->data['brief_approval_histories_counts'] = $this->model_brief_approval_histories->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/brief_approval_histories/index/',
			'total_rows'   => $this->model_brief_approval_histories->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Brief Approval Histories List');
		$this->render('backend/standart/administrator/brief_approval_histories/brief_approval_histories_list', $this->data);
	}
	
	
	
	/**
	* delete Brief Approval Historiess
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('brief_approval_histories_delete');

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
            set_message(cclang('has_been_deleted', 'brief_approval_histories'), 'success');
        } else {
            set_message(cclang('error_delete', 'brief_approval_histories'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Brief Approval Historiess
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('brief_approval_histories_view');

		$this->data['brief_approval_histories'] = $this->model_brief_approval_histories->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Brief Approval Histories Detail');
		$this->render('backend/standart/administrator/brief_approval_histories/brief_approval_histories_view', $this->data);
	}
	
	/**
	* delete Brief Approval Historiess
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$brief_approval_histories = $this->model_brief_approval_histories->find($id);

		
		
		return $this->model_brief_approval_histories->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('brief_approval_histories_export');

		$this->model_brief_approval_histories->export('brief_approval_histories', 'brief_approval_histories');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('brief_approval_histories_export');

		$this->model_brief_approval_histories->pdf('brief_approval_histories', 'brief_approval_histories');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('brief_approval_histories_export');

		$table = $title = 'brief_approval_histories';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_brief_approval_histories->find($id);
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


/* End of file brief_approval_histories.php */
/* Location: ./application/controllers/administrator/Brief Approval Histories.php */