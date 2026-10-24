<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Apify Jobs Controller
*| --------------------------------------------------------------------------
*| Apify Jobs site
*|
*/
class Apify_jobs extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_apify_jobs');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Apify Jobss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('apify_jobs_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['apify_jobss'] = $this->model_apify_jobs->get($filter, $field, $this->limit_page, $offset);
		$this->data['apify_jobs_counts'] = $this->model_apify_jobs->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/apify_jobs/index/',
			'total_rows'   => $this->model_apify_jobs->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Apify Jobs List');
		$this->render('backend/standart/administrator/apify_jobs/apify_jobs_list', $this->data);
	}
	
	
	
	/**
	* delete Apify Jobss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('apify_jobs_delete');

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
            set_message(cclang('has_been_deleted', 'apify_jobs'), 'success');
        } else {
            set_message(cclang('error_delete', 'apify_jobs'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Apify Jobss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('apify_jobs_view');

		$this->data['apify_jobs'] = $this->model_apify_jobs->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Apify Jobs Detail');
		$this->render('backend/standart/administrator/apify_jobs/apify_jobs_view', $this->data);
	}
	
	/**
	* delete Apify Jobss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$apify_jobs = $this->model_apify_jobs->find($id);

		
		
		return $this->model_apify_jobs->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('apify_jobs_export');

		$this->model_apify_jobs->export('apify_jobs', 'apify_jobs');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('apify_jobs_export');

		$this->model_apify_jobs->pdf('apify_jobs', 'apify_jobs');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('apify_jobs_export');

		$table = $title = 'apify_jobs';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_apify_jobs->find($id);
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


/* End of file apify_jobs.php */
/* Location: ./application/controllers/administrator/Apify Jobs.php */