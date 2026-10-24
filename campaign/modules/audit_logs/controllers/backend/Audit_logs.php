<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Audit Logs Controller
*| --------------------------------------------------------------------------
*| Audit Logs site
*|
*/
class Audit_logs extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_audit_logs');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Audit Logss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('audit_logs_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['audit_logss'] = $this->model_audit_logs->get($filter, $field, $this->limit_page, $offset);
		$this->data['audit_logs_counts'] = $this->model_audit_logs->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/audit_logs/index/',
			'total_rows'   => $this->model_audit_logs->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Audit Logs List');
		$this->render('backend/standart/administrator/audit_logs/audit_logs_list', $this->data);
	}
	
	
	
	/**
	* delete Audit Logss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('audit_logs_delete');

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
            set_message(cclang('has_been_deleted', 'audit_logs'), 'success');
        } else {
            set_message(cclang('error_delete', 'audit_logs'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Audit Logss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('audit_logs_view');

		$this->data['audit_logs'] = $this->model_audit_logs->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Audit Logs Detail');
		$this->render('backend/standart/administrator/audit_logs/audit_logs_view', $this->data);
	}
	
	/**
	* delete Audit Logss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$audit_logs = $this->model_audit_logs->find($id);

		
		
		return $this->model_audit_logs->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('audit_logs_export');

		$this->model_audit_logs->export('audit_logs', 'audit_logs');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('audit_logs_export');

		$this->model_audit_logs->pdf('audit_logs', 'audit_logs');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('audit_logs_export');

		$table = $title = 'audit_logs';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_audit_logs->find($id);
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


/* End of file audit_logs.php */
/* Location: ./application/controllers/administrator/Audit Logs.php */