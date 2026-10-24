<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Posting Metrics Controller
*| --------------------------------------------------------------------------
*| Posting Metrics site
*|
*/
class Posting_metrics extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_posting_metrics');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Posting Metricss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('posting_metrics_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['posting_metricss'] = $this->model_posting_metrics->get($filter, $field, $this->limit_page, $offset);
		$this->data['posting_metrics_counts'] = $this->model_posting_metrics->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/posting_metrics/index/',
			'total_rows'   => $this->model_posting_metrics->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Posting Metrics List');
		$this->render('backend/standart/administrator/posting_metrics/posting_metrics_list', $this->data);
	}
	
	
	
	/**
	* delete Posting Metricss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('posting_metrics_delete');

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
            set_message(cclang('has_been_deleted', 'posting_metrics'), 'success');
        } else {
            set_message(cclang('error_delete', 'posting_metrics'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Posting Metricss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('posting_metrics_view');

		$this->data['posting_metrics'] = $this->model_posting_metrics->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Posting Metrics Detail');
		$this->render('backend/standart/administrator/posting_metrics/posting_metrics_view', $this->data);
	}
	
	/**
	* delete Posting Metricss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$posting_metrics = $this->model_posting_metrics->find($id);

		
		
		return $this->model_posting_metrics->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('posting_metrics_export');

		$this->model_posting_metrics->export('posting_metrics', 'posting_metrics');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('posting_metrics_export');

		$this->model_posting_metrics->pdf('posting_metrics', 'posting_metrics');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('posting_metrics_export');

		$table = $title = 'posting_metrics';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_posting_metrics->find($id);
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


/* End of file posting_metrics.php */
/* Location: ./application/controllers/administrator/Posting Metrics.php */