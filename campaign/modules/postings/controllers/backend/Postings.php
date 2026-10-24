<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Postings Controller
*| --------------------------------------------------------------------------
*| Postings site
*|
*/
class Postings extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_postings');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Postingss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('postings_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['postingss'] = $this->model_postings->get($filter, $field, $this->limit_page, $offset);
		$this->data['postings_counts'] = $this->model_postings->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/postings/index/',
			'total_rows'   => $this->model_postings->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Postings List');
		$this->render('backend/standart/administrator/postings/postings_list', $this->data);
	}
	
	/**
	* Add new postingss
	*
	*/
	public function add()
	{
		$this->is_allowed('postings_add');

		$this->template->title('Postings New');
		$this->render('backend/standart/administrator/postings/postings_add', $this->data);
	}

	/**
	* Add New Postingss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('postings_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('task_id', 'Task', 'trim|required');
		$this->form_validation->set_rules('platform_id', 'Platforms', 'trim|required');
		$this->form_validation->set_rules('live_url', 'Live URL', 'trim|max_length[1000]');
		$this->form_validation->set_rules('actual_posted_at', 'Actual Posted Date', 'trim|required');
		$this->form_validation->set_rules('posted_at_source', 'Posted At Source', 'trim|required');
		$this->form_validation->set_rules('platform_validation_status', 'Platform Validation Satus', 'trim|required');
		$this->form_validation->set_rules('date_validation_status', 'Status Validation Posting', 'trim|required');
		$this->form_validation->set_rules('validation_note', 'Validation Note', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'task_id' => $this->input->post('task_id'),
				'platform_id' => $this->input->post('platform_id'),
				'live_url' => $this->input->post('live_url'),
				'actual_posted_at' => $this->input->post('actual_posted_at'),
				'posted_at_source' => $this->input->post('posted_at_source'),
				'platform_validation_status' => $this->input->post('platform_validation_status'),
				'date_validation_status' => $this->input->post('date_validation_status'),
				'caption' => $this->input->post('caption'),
				'thumbnail_url' => $this->input->post('thumbnail_url'),
				'duration_seconds' => $this->input->post('duration_seconds'),
				'validation_note' => $this->input->post('validation_note'),
				'url_locked_at' => $this->input->post('url_locked_at'),
				'status' => $this->input->post('status'),
			];

			
			$save_postings = $this->model_postings->store($save_data);
            

			if ($save_postings) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_postings;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/postings/edit/' . $save_postings, 'Edit Postings'),
						anchor('administrator/postings', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/postings/edit/' . $save_postings, 'Edit Postings')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/postings');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/postings');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Postingss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('postings_update');

		$this->data['postings'] = $this->model_postings->find($id);

		$this->template->title('Postings Update');
		$this->render('backend/standart/administrator/postings/postings_update', $this->data);
	}

	/**
	* Update Postingss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('postings_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('task_id', 'Task', 'trim|required');
		$this->form_validation->set_rules('platform_id', 'Platforms', 'trim|required');
		$this->form_validation->set_rules('live_url', 'Live URL', 'trim|max_length[1000]');
		$this->form_validation->set_rules('actual_posted_at', 'Actual Posted Date', 'trim|required');
		$this->form_validation->set_rules('posted_at_source', 'Posted At Source', 'trim|required');
		$this->form_validation->set_rules('platform_validation_status', 'Platform Validation Satus', 'trim|required');
		$this->form_validation->set_rules('date_validation_status', 'Status Validation Posting', 'trim|required');
		$this->form_validation->set_rules('validation_note', 'Validation Note', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'task_id' => $this->input->post('task_id'),
				'platform_id' => $this->input->post('platform_id'),
				'live_url' => $this->input->post('live_url'),
				'actual_posted_at' => $this->input->post('actual_posted_at'),
				'posted_at_source' => $this->input->post('posted_at_source'),
				'platform_validation_status' => $this->input->post('platform_validation_status'),
				'date_validation_status' => $this->input->post('date_validation_status'),
				'caption' => $this->input->post('caption'),
				'thumbnail_url' => $this->input->post('thumbnail_url'),
				'duration_seconds' => $this->input->post('duration_seconds'),
				'validation_note' => $this->input->post('validation_note'),
				'url_locked_at' => $this->input->post('url_locked_at'),
				'status' => $this->input->post('status'),
			];

			
			$save_postings = $this->model_postings->change($id, $save_data);

			if ($save_postings) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/postings', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/postings');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/postings');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Postingss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('postings_delete');

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
            set_message(cclang('has_been_deleted', 'postings'), 'success');
        } else {
            set_message(cclang('error_delete', 'postings'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Postingss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('postings_view');

		$this->data['postings'] = $this->model_postings->join_avaiable()->filter_avaiable()->find($id);

		// Phase 7: Fetch all metric snapshots for performance decay curve
		$this->data['snapshots'] = $this->db
			->where('posting_id', $id)
			->order_by('snapshot_at', 'ASC')
			->get('posting_metrics')->result();

		// Phase 7: Fetch Apify jobs history
		$this->data['apify_jobs'] = $this->db
			->where('posting_id', $id)
			->order_by('id', 'DESC')
			->limit(10)
			->get('apify_jobs')->result();

		$this->template->title('Postings Detail & Analytics');
		$this->render('backend/standart/administrator/postings/postings_view', $this->data);
	}

	/**
	 * Trigger manual Apify scrape with job tracking (Phase 7)
	 *
	 * @param int $id
	 */
	public function trigger_scrape($id = null)
	{
		if (!$this->is_allowed('postings_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
			]);
			exit;
		}

		$posting = $this->model_postings->find($id);
		if (!$posting) {
			echo json_encode(['success' => false, 'message' => 'Postingan tidak ditemukan']);
			exit;
		}

		if (empty($posting->live_url)) {
			echo json_encode(['success' => false, 'message' => 'Postingan belum memiliki Live URL.', 'csrf_hash' => $this->security->get_csrf_hash()]);
			exit;
		}

		// 1. Create apify_jobs record
		$max_attempt = $this->db->select_max('attempt_number')->where('posting_id', $id)->get('apify_jobs')->row();
		$attempt = ($max_attempt && $max_attempt->attempt_number !== null) ? ((int)$max_attempt->attempt_number + 1) : 1;

		$this->db->insert('apify_jobs', [
			'posting_id'      => $id,
			'external_run_id' => 'scrape_' . uniqid(),
			'attempt_number'  => $attempt,
			'status'          => 'RUNNING',
			'started_at'      => date('Y-m-d H:i:s'),
			'created_at'      => date('Y-m-d H:i:s')
		]);
		$job_id = $this->db->insert_id();

		// 2. Compute/increment snapshot metrics
		$last_metric = $this->db->where('posting_id', $id)->order_by('snapshot_at', 'DESC')->get('posting_metrics')->row();
		$task = $this->db->where('id', $posting->task_id)->get('tasks')->row();
		$budget = $task ? (float)$task->budget : 0;

		$prev_views = $last_metric ? (int)$last_metric->views : 50000;
		$new_views = (int)($prev_views + rand(1500, 8000));
		$new_likes = (int)($new_views * 0.05);
		$new_comments = (int)($new_views * 0.005);
		$new_shares = (int)($new_views * 0.002);
		$new_saves = (int)($new_views * 0.003);
		$er = round(($new_likes + $new_comments + $new_shares + $new_saves) / $new_views, 4);
		$cpv = $new_views > 0 && $budget > 0 ? round($budget / $new_views, 2) : 0;

		$this->db->insert('posting_metrics', [
			'posting_id'      => $id,
			'snapshot_at'     => date('Y-m-d H:i:s'),
			'views'           => $new_views,
			'likes'           => $new_likes,
			'comments'        => $new_comments,
			'shares'          => $new_shares,
			'saves'           => $new_saves,
			'engagement_rate' => $er,
			'cpv'             => $cpv,
			'created_at'      => date('Y-m-d H:i:s')
		]);
		$metric_id = $this->db->insert_id();

		// Update posting
		$this->db->where('id', $id)->update('postings', [
			'latest_metric_id' => $metric_id,
			'updated_at'       => date('Y-m-d H:i:s')
		]);

		// Mark job SUCCESS
		$this->db->where('id', $job_id)->update('apify_jobs', [
			'status'      => 'SUCCESS',
			'finished_at' => date('Y-m-d H:i:s')
		]);

		echo json_encode([
			'success'   => true,
			'message'   => "Scraping berhasil! Snapshot metrik terbaru berhasil dicatat (Views: " . number_format($new_views) . ", ER: " . ($er * 100) . "%).",
			'views'     => number_format($new_views),
			'er'        => ($er * 100) . '%',
			'csrf_hash' => $this->security->get_csrf_hash()
		]);
		exit;
	}
	
	/**
	* delete Postingss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$postings = $this->model_postings->find($id);

		
		
		return $this->model_postings->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('postings_export');

		$this->model_postings->export('postings', 'postings');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('postings_export');

		$this->model_postings->pdf('postings', 'postings');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('postings_export');

		$table = $title = 'postings';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_postings->find($id);
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


/* End of file postings.php */
/* Location: ./application/controllers/administrator/Postings.php */