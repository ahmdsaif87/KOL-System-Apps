<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Kol Profile Snapshots Controller
*| --------------------------------------------------------------------------
*| Kol Profile Snapshots site
*|
*/
class Kol_profile_snapshots extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_kol_profile_snapshots');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Kol Profile Snapshotss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('kol_profile_snapshots_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['kol_profile_snapshotss'] = $this->model_kol_profile_snapshots->get($filter, $field, $this->limit_page, $offset);
		$this->data['kol_profile_snapshots_counts'] = $this->model_kol_profile_snapshots->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/kol_profile_snapshots/index/',
			'total_rows'   => $this->model_kol_profile_snapshots->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Kol Profile Snapshots List');
		$this->render('backend/standart/administrator/kol_profile_snapshots/kol_profile_snapshots_list', $this->data);
	}
	
	/**
	* Add new kol_profile_snapshotss
	*
	*/
	public function add()
	{
		redirect('administrator/kol_profile_snapshots');
	}

	/**
	* Add New Kol Profile Snapshotss
	*
	* @return JSON
	*/
	public function add_save()
	{
		redirect('administrator/kol_profile_snapshots');
	}
	
		/**
	* Update view Kol Profile Snapshotss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('kol_profile_snapshots_update');

		$this->data['kol_profile_snapshots'] = $this->model_kol_profile_snapshots->find($id);

		$this->template->title('Kol Profile Snapshots Update');
		$this->render('backend/standart/administrator/kol_profile_snapshots/kol_profile_snapshots_update', $this->data);
	}

	/**
	* Update Kol Profile Snapshotss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('kol_profile_snapshots_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('kol_id', 'Kol Id', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('platform_id', 'Platform Id', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('followers_count', 'Followers Count', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('average_views_last_30', 'Average Views Last 30', 'trim|required|max_length[15]');
		$this->form_validation->set_rules('average_er_last_30', 'Average Er Last 30', 'trim|required|max_length[10]');
		$this->form_validation->set_rules('total_posts', 'Total Posts', 'trim|required|max_length[20]');
		$this->form_validation->set_rules('snapshot_date', 'Snapshot Date', 'trim|required');
		$this->form_validation->set_rules('created_at', 'Created At', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'kol_id' => $this->input->post('kol_id'),
				'platform_id' => $this->input->post('platform_id'),
				'followers_count' => $this->input->post('followers_count'),
				'average_views_last_30' => $this->input->post('average_views_last_30'),
				'average_er_last_30' => $this->input->post('average_er_last_30'),
				'total_posts' => $this->input->post('total_posts'),
				'snapshot_date' => $this->input->post('snapshot_date'),
				'created_at' => $this->input->post('created_at'),
			];

			
			$save_kol_profile_snapshots = $this->model_kol_profile_snapshots->change($id, $save_data);

			if ($save_kol_profile_snapshots) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/kol_profile_snapshots', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/kol_profile_snapshots');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/kol_profile_snapshots');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Kol Profile Snapshotss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('kol_profile_snapshots_delete');

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
            set_message(cclang('has_been_deleted', 'kol_profile_snapshots'), 'success');
        } else {
            set_message(cclang('error_delete', 'kol_profile_snapshots'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Kol Profile Snapshotss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('kol_profile_snapshots_view');

		$this->data['kol_profile_snapshots'] = $this->model_kol_profile_snapshots->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Kol Profile Snapshots Detail');
		$this->render('backend/standart/administrator/kol_profile_snapshots/kol_profile_snapshots_view', $this->data);
	}
	
	/**
	* delete Kol Profile Snapshotss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$kol_profile_snapshots = $this->model_kol_profile_snapshots->find($id);

		
		
		return $this->model_kol_profile_snapshots->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('kol_profile_snapshots_export');

		$this->model_kol_profile_snapshots->export('kol_profile_snapshots', 'kol_profile_snapshots');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('kol_profile_snapshots_export');

		$this->model_kol_profile_snapshots->pdf('kol_profile_snapshots', 'kol_profile_snapshots');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('kol_profile_snapshots_export');

		$table = $title = 'kol_profile_snapshots';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_kol_profile_snapshots->find($id);
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


/* End of file kol_profile_snapshots.php */
/* Location: ./application/controllers/administrator/Kol Profile Snapshots.php */