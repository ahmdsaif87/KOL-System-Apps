<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Task Budget Histories Controller
*| --------------------------------------------------------------------------
*| Task Budget Histories site
*|
*/
class Task_budget_histories extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_task_budget_histories');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Task Budget Histories
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('task_budget_histories_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['task_budget_historiess'] = $this->model_task_budget_histories->get($filter, $field, $this->limit_page, $offset);
		$this->data['task_budget_histories_counts'] = $this->model_task_budget_histories->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/task_budget_histories/index/',
			'total_rows'   => $this->model_task_budget_histories->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Task Budget Histories List');
		$this->render('backend/standart/administrator/task_budget_histories/task_budget_histories_list', $this->data);
	}
	
	
	
	/**
	* delete Task Budget Histories
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('task_budget_histories_delete');

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
            set_message(cclang('has_been_deleted', 'task_budget_histories'), 'success');
        } else {
            set_message(cclang('error_delete', 'task_budget_histories'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Task Budget Histories
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('task_budget_histories_view');

		$this->data['task_budget_histories'] = $this->model_task_budget_histories->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Task Budget Histories Detail');
		$this->render('backend/standart/administrator/task_budget_histories/task_budget_histories_view', $this->data);
	}
	
	/**
	* delete Task Budget Histories
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$task_budget_histories = $this->model_task_budget_histories->find($id);

		
		
		return $this->model_task_budget_histories->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('task_budget_histories_export');

		$this->model_task_budget_histories->export('task_budget_histories', 'task_budget_histories');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('task_budget_histories_export');

		$this->model_task_budget_histories->pdf('task_budget_histories', 'task_budget_histories');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('task_budget_histories_export');

		$table = $title = 'task_budget_histories';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_task_budget_histories->find($id);
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


/* End of file Task_budget_histories.php */
/* Location: ./application/controllers/administrator/Task_budget_histories.php */