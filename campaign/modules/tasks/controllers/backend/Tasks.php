<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Tasks Controller
*| --------------------------------------------------------------------------
*| Tasks site
*|
*/
class Tasks extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_tasks');
		$this->load->model('tasks/model_task_sentiments');
		$this->load->library('sentiment_client');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Taskss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('tasks_list');

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$view = $this->input->get('view');
		$statuses = [];
		if ($view === 'monitoring') {
			$statuses = []; // No filter, show all tasks for overall monitoring
		} elseif ($view === 'review_draft') {
			$statuses = ['WAITING_DRAFT', 'IN_REVIEW_DRAFT', 'REVISION_DRAFT'];
		} elseif ($view === 'live_posting') {
			$statuses = ['APPROVED_DRAFT', 'POSTED', 'DONE'];
		} elseif ($view === 'sow' || $view === 'brief') {
			$statuses = ['DRAFT', 'PENDING_REVIEW', 'REVISION'];
		}
		
		// Auto-scrape dipindah ke cron/manual (lihat sync_posting_metrics).
		// Jangan panggil _auto_scrape_due_postings() di GET agar list tidak timeout / double-queue.

		$sort_by = $this->input->get('sort');
		$sort_dir = $this->input->get('dir') ?: 'desc';

		if (!empty($statuses)) {
			$this->db->where_in('tasks.status', $statuses);
		}
		$taskss = $this->model_tasks->get($filter, $field, $this->limit_page, $offset, [], $sort_by, $sort_dir);
		
		if (!empty($statuses)) {
			$this->db->where_in('tasks.status', $statuses);
		}
		$this->data['tasks_counts'] = $this->model_tasks->count_all($filter, $field);
		
		$this->data['taskss'] = $taskss;
		$this->data['current_view'] = $view;

		$config = [
			'base_url'     => 'administrator/tasks/index/',
			'total_rows'   => $this->data['tasks_counts'],
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
			'reuse_query_string' => true,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Tasks List');
		$this->render('backend/standart/administrator/tasks/tasks_list', $this->data);
	}
	
	/**
	* Add new taskss
	*
	*/
	public function add()
	{
		$this->is_allowed('tasks_add');

		$this->template->title('Tasks New');
		$this->render('backend/standart/administrator/tasks/tasks_add', $this->data);
	}

	/**
	* Add New Taskss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('tasks_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		$this->form_validation->set_rules('campaign_kol_id', 'Assigned KOL ', 'trim|required');
		$this->form_validation->set_rules('variant_id', 'Konsep Kreatif Konten', 'trim|required');
		$this->form_validation->set_rules('platform_id', 'Platform', 'trim|required');
		$this->form_validation->set_rules('task_number', 'Number Task', 'trim|required');
		$this->form_validation->set_rules('budget', 'Budget', 'trim|max_length[15]|required');
		$this->form_validation->set_rules('currency_code', 'Code Currency', 'trim|max_length[3]|required');
		$this->form_validation->set_rules('estimated_posting_date', 'Estimation Posting Date', 'trim|required');
		$this->form_validation->set_rules('content_type', 'Type Contents', 'trim|max_length[100]|in_list[' . implode(',', array_keys(kol_rate_content_types())) . ']');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'title' => $this->input->post('title'),
				'campaign_kol_id' => $this->input->post('campaign_kol_id'),
				'variant_id' => $this->input->post('variant_id'),
				'platform_id' => $this->input->post('platform_id'),
				'task_number' => $this->input->post('task_number'),
				'budget' => $this->input->post('budget'),
				'content_type' => normalize_content_type($this->input->post('content_type')),
				'notes' => $this->input->post('notes'),
				'currency_code' => $this->input->post('currency_code'),
				'estimated_posting_date' => $this->input->post('estimated_posting_date'),
			];

			
			$save_tasks = $this->model_tasks->store($save_data);
            
			if ($save_tasks) {
				// Auto-create linked briefs record (DRAFT, no dummy — staff uploads via Upload Brief modal)
				$this->db->insert('briefs', [
					'task_id'        => $save_tasks,
					'title'          => $this->input->post('title') ?: ('Brief Task #' . $save_tasks),
					'content'        => $this->input->post('notes') ?: '',
					'file_path'      => null,
					'revision_count' => 0,
					'status'         => 'DRAFT',
					'created_by'     => get_user_data('id') ?: 1,
					'created_at'     => date('Y-m-d H:i:s'),
					'updated_at'     => date('Y-m-d H:i:s')
				]);
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_tasks;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/tasks/edit/' . $save_tasks, 'Edit Tasks'),
						anchor('administrator/tasks', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/tasks/edit/' . $save_tasks, 'Edit Tasks')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tasks');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tasks');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Taskss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('tasks_update');

		$this->data['tasks'] = $this->model_tasks->find($id);

		$this->template->title('Tasks Update');
		$this->render('backend/standart/administrator/tasks/tasks_update', $this->data);
	}

	/**
	* Update Taskss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('tasks_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		$this->form_validation->set_rules('campaign_kol_id', 'Assigned KOL ', 'trim|required');
		$this->form_validation->set_rules('variant_id', 'Konsep Kreatif Konten', 'trim|required');
		$this->form_validation->set_rules('platform_id', 'Platform', 'trim|required');
		$this->form_validation->set_rules('task_number', 'Number Task', 'trim|required');
		$this->form_validation->set_rules('budget', 'Budget', 'trim|max_length[15]|required');
		$this->form_validation->set_rules('currency_code', 'Code Currency', 'trim|max_length[3]|required');
		$this->form_validation->set_rules('estimated_posting_date', 'Estimation Posting Date', 'trim|required');
		$this->form_validation->set_rules('content_type', 'Type Contents', 'trim|max_length[100]');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'title' => $this->input->post('title'),
				'campaign_kol_id' => $this->input->post('campaign_kol_id'),
				'variant_id' => $this->input->post('variant_id'),
				'platform_id' => $this->input->post('platform_id'),
				'task_number' => $this->input->post('task_number'),
				'budget' => $this->input->post('budget'),
				'content_type' => normalize_content_type($this->input->post('content_type')),
				'notes' => $this->input->post('notes'),
				'currency_code' => $this->input->post('currency_code'),
				'estimated_posting_date' => $this->input->post('estimated_posting_date'),
			];

			
			$save_tasks = $this->model_tasks->change($id, $save_data);

			if ($save_tasks) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/tasks', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/tasks');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/tasks');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Taskss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('tasks_delete');

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
            set_message(cclang('has_been_deleted', 'tasks'), 'success');
        } else {
            set_message(cclang('error_delete', 'tasks'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Taskss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('tasks_view');

		$task = $this->model_tasks->join_avaiable()->filter_avaiable()->find($id);
		$this->data['tasks'] = $task;

		// Fetch posting and scraping metrics
		$posting = $this->db->get_where('postings', ['task_id' => $id])->row();
		$this->data['posting'] = $posting;

		$latest_metrics = null;
		$metric_histories = [];
		if ($posting) {
			if (!empty($posting->latest_metric_id)) {
				$latest_metrics = $this->db->get_where('posting_metrics', ['id' => $posting->latest_metric_id])->row();
			} else {
				$latest_metrics = $this->db->where('posting_id', $posting->id)->order_by('id', 'DESC')->get('posting_metrics')->row();
			}
			$metric_histories = $this->db->where('posting_id', $posting->id)->order_by('id', 'DESC')->get('posting_metrics')->result();
		}
		$this->data['latest_metrics'] = $latest_metrics;
		$this->data['metric_histories'] = $metric_histories;

		// Fetch status histories
		$this->data['status_histories'] = $this->db->select('task_status_histories.*, aauth_users.username, COALESCE(NULLIF(aauth_users.full_name, ""), aauth_users.username) as user_full_name')
			->join('aauth_users', 'aauth_users.id = task_status_histories.changed_by', 'left')
			->where('task_status_histories.task_id', $id)
			->order_by('task_status_histories.id', 'DESC')
			->get('task_status_histories')
			->result();

		// Fetch linked brief and approval histories
		$brief = $this->db->where('task_id', $id)->order_by('id', 'DESC')->get('briefs')->row();
		$this->data['brief'] = $brief;
		$this->data['brief_approvals'] = $brief ? $this->db->select('brief_approval_histories.*, aauth_users.username, COALESCE(NULLIF(aauth_users.full_name, ""), aauth_users.username) as user_full_name')
			->join('aauth_users', 'aauth_users.id = brief_approval_histories.acted_by', 'left')
			->where('brief_id', $brief->id)
			->order_by('brief_approval_histories.id', 'DESC')
			->get('brief_approval_histories')
			->result() : [];

		// Fetch linked drafts and review histories
		$drafts_list = $this->db->where('task_id', $id)->order_by('version', 'DESC')->get('drafts')->result();
		$this->data['drafts_list'] = $drafts_list;
		$draft_ids = !empty($drafts_list) ? array_map(function($d){ return $d->id; }, $drafts_list) : [];
		$this->data['draft_reviews'] = !empty($draft_ids) ? $this->db->select('draft_review_histories.*, aauth_users.username, COALESCE(NULLIF(aauth_users.full_name, ""), aauth_users.username) as user_full_name, drafts.version as draft_version')
			->join('drafts', 'drafts.id = draft_review_histories.draft_id', 'left')
			->join('aauth_users', 'aauth_users.id = draft_review_histories.acted_by', 'left')
			->where_in('draft_review_histories.draft_id', $draft_ids)
			->order_by('draft_review_histories.id', 'DESC')
			->get('draft_review_histories')
			->result() : [];

		// Fetch sentiment analysis record (if any)
		$this->data['sentiment'] = $this->model_task_sentiments->get_by_task_id($id);

		$this->template->title('Tasks Detail');
		$this->render('backend/standart/administrator/tasks/tasks_view', $this->data);
	}
	
	/**
	* delete Taskss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$tasks = $this->model_tasks->find($id);

		
		
		return $this->model_tasks->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('tasks_export');

		$this->model_tasks->export('tasks', 'tasks');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('tasks_export');

		$this->model_tasks->pdf('tasks', 'tasks');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('tasks_export');

		$table = $title = 'tasks';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_tasks->find($id);
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

	/**
	* Change Task Status (Quick Action)
	*/
	public function set_status($id, $status = null)
	{
		$this->is_allowed('tasks_update');
		$task = $this->model_tasks->find($id);
		if (!$task) {
			set_message('Task tidak ditemukan.', 'error');
			redirect('administrator/tasks');
		}

		$new_status = $status ? strtoupper($status) : strtoupper($this->input->post('status') ?: $this->input->get('status'));
		$allowed_statuses = ['DRAFT', 'PENDING_REVIEW', 'REVISION', 'WAITING_DRAFT', 'IN_REVIEW_DRAFT', 'REVISION_DRAFT', 'APPROVED_DRAFT', 'POSTED', 'DONE'];

		if (!in_array($new_status, $allowed_statuses)) {
			set_message('Status tidak valid: ' . htmlspecialchars($new_status), 'error');
			redirect('administrator/tasks');
		}

		// Contract Blocker Logic
		if (!$this->_check_contract_active($task->campaign_kol_id)) {
			set_message('Tidak dapat mengubah status task. Kontrak untuk KOL ini belum ditandatangani (ACTIVE).', 'error');
			$ref = $this->input->get('ref');
			if ($ref) {
				redirect($ref);
			} else {
				redirect('administrator/tasks');
			}
			return;
		}

		$note = $this->input->post('note') ?: ($this->input->get('note') ?: 'Status diubah ke ' . $new_status);
		$user_id = get_user_data('id');

		$update_data = [
			'status'     => $new_status,
			'updated_at' => date('Y-m-d H:i:s')
		];

		// If posting link provided
		$live_url = $this->input->post('live_url') ?: $this->input->get('live_url');
		if ($live_url && $new_status == 'POSTED') {
			$update_data['live_url'] = $live_url;
			$update_data['notes'] = ($task->notes ? $task->notes . "\n" : '') . 'Live URL: ' . $live_url;
			
			// Check if posting already exists for this task
			$existing_posting = $this->db->get_where('postings', ['task_id' => $id])->row();
			if (!$existing_posting) {
				// Insert to postings
				$this->db->insert('postings', [
					'task_id' => $id,
					'platform_id' => $task->platform_id,
					'live_url' => $live_url,
					'posted_at_source' => 'MANUAL',
					'status' => 'ACTIVE',
					'submitted_by' => $user_id,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				]);
				$posting_id = $this->db->insert_id();

				// Insert to apify_jobs for scraping
				$this->db->insert('apify_jobs', [
					'posting_id' => $posting_id,
					'status' => 'QUEUED',
					'created_at' => date('Y-m-d H:i:s')
				]);
			}
		}

		$this->db->where('id', $id)->update('tasks', $update_data);

		// Log history
		$this->db->insert('task_status_histories', [
			'task_id'    => $id,
			'status'     => $new_status,
			'changed_by' => $user_id,
			'note'       => $note,
			'created_at' => date('Y-m-d H:i:s')
		]);

		// If brief exists, keep brief status in sync
		if ($new_status == 'APPROVED') {
			$this->db->where('task_id', $id)->update('briefs', ['status' => 'APPROVED']);
		}

		// Update completed task count on campaign_kols
		if ($task->campaign_kol_id) {
			$done_count = $this->db->where([
				'campaign_kol_id' => $task->campaign_kol_id,
				'status'          => 'DONE'
			])->count_all_results('tasks');

			$this->db->where('id', $task->campaign_kol_id)->update('campaign_kols', [
				'task_completed_count' => $done_count
			]);
		}

		set_message('Status Task #' . $task->task_number . ' (' . htmlspecialchars($task->title) . ') berhasil diubah ke ' . $new_status . '.', 'success');
		
		$ref = $this->input->get('ref');
		if ($ref) {
			redirect($ref);
		} else {
			redirect('administrator/tasks');
		}
	}

	/**
	* Submit Draft Video (Change task status to IN_REVIEW_DRAFT)
	*/
	/**
	 * Migrasi sekali-jalan: tasks.notes "Live URL: ..." -> postings.live_url + tasks.live_url.
	 * Idempotent: hanya memproses task yang belum punya postings tapi notes-nya mengandung URL.
	 */
	public function migrate_notes_live_url()
	{
		$this->is_allowed('tasks_update');
		$tasks = $this->db->select('id, platform_id, notes, live_url')
			->like('notes', 'Live URL:', 'both')
			->get('tasks')->result();
		$created = 0; $synced = 0; $skipped = 0;
		foreach ($tasks as $row) {
			if (!preg_match('/Live URL:\s*(https?:\/\/[^\s\n\r]+)/i', (string)$row->notes, $m)) { $skipped++; continue; }
			$url = trim($m[1]);
			$exists = $this->db->get_where('postings', ['task_id' => $row->id])->row();
			if (!$exists) {
				$this->db->insert('postings', [
					'task_id'          => $row->id,
					'platform_id'      => $row->platform_id,
					'live_url'         => $url,
					'posted_at_source' => 'MIGRATION',
					'status'           => 'ACTIVE',
					'submitted_by'     => $this->aauth->get_user_id(),
					'posted_at'        => date('Y-m-d H:i:s'),
					'scrape_day3_done' => 0,
					'scrape_day7_done' => 0,
					'created_at'       => date('Y-m-d H:i:s'),
					'updated_at'       => date('Y-m-d H:i:s')
				]);
				$posting_id = $this->db->insert_id();
				$this->db->insert('apify_jobs', ['posting_id' => $posting_id, 'status' => 'QUEUED', 'created_at' => date('Y-m-d H:i:s')]);
				$created++;
			}
			if (empty($row->live_url)) {
				$this->db->where('id', $row->id)->update('tasks', ['live_url' => $url, 'updated_at' => date('Y-m-d H:i:s')]);
				$synced++;
			}
		}
		if ($this->input->is_ajax_request()) {
			echo json_encode(['success' => true, 'created' => $created, 'synced' => $synced, 'skipped' => $skipped, 'message' => "Migrasi selesai: {$created} postings dibuat, {$synced} tasks.live_url disinkron."]);
			return;
		}
		set_message("Migrasi selesai: {$created} postings dibuat, {$synced} tasks.live_url disinkron.", 'success');
		redirect_back();
	}

	public function submit_draft($id)
	{
		$this->is_allowed('tasks_update');
		$user_id = get_user_data('id') ?: 1;
		
		$draft_url = trim($this->input->post('draft_url'));
		if (empty($draft_url)) {
			echo json_encode(['success' => false, 'message' => 'URL draft wajib diisi.']);
			return;
		}

		$task = $this->model_tasks->find($id);
		if (!$task) {
			echo json_encode(['success' => false, 'message' => 'Task tidak ditemukan.']);
			return;
		}

		if (!$this->_check_contract_active($task->campaign_kol_id)) {
			echo json_encode(['success' => false, 'message' => 'Tidak dapat mengubah status task. Kontrak untuk KOL ini belum ditandatangani (ACTIVE).']);
			return;
		}

		// Get max version for this task
		$this->db->select_max('version');
		$this->db->where('task_id', $id);
		$query = $this->db->get('drafts');
		$res = $query->row();
		$next_version = ($res && $res->version) ? ((int)$res->version + 1) : 1;

		// Insert to drafts table
		$this->db->insert('drafts', [
			'task_id'    => $id,
			'version'    => $next_version,
			'draft_url'  => $draft_url,
			'caption'    => $this->input->post('caption') ?: null,
			'hashtag'    => $this->input->post('hashtag') ?: null,
			'notes'      => $this->input->post('notes') ?: null,
			'status'     => 'SUBMITTED',
			'created_by' => $user_id,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);
		$draft_id = $this->db->insert_id();

		// Record in draft_review_histories
		$this->db->insert('draft_review_histories', [
			'draft_id'   => $draft_id,
			'action'     => 'SUBMITTED',
			'note'       => 'Draft v' . $next_version . ' disubmit untuk review.',
			'acted_by'   => $user_id,
			'created_at' => date('Y-m-d H:i:s')
		]);

		// Update task status to IN_REVIEW_DRAFT
		$this->db->where('id', $id)->update('tasks', [
			'status'     => 'IN_REVIEW_DRAFT',
			'draft_url'  => $draft_url,
			'updated_at' => date('Y-m-d H:i:s')
		]);

		// Log history
		$this->db->insert('task_status_histories', [
			'task_id'    => $id,
			'status'     => 'IN_REVIEW_DRAFT',
			'changed_by' => $user_id,
			'note'       => 'Draft video (v' . $next_version . ') disubmit via link: ' . $draft_url,
			'created_at' => date('Y-m-d H:i:s')
		]);

		echo json_encode(['success' => true]);
	}

	/**
	 * ACTION: Lengkapi Jadwal & Upload SOW (Staff)
	 */
	public function set_schedule_sow($id)
	{
		$this->is_allowed('tasks_update');
		$user_id = get_user_data('id') ?: 1;
		$task = $this->model_tasks->find($id);
		if (!$task) {
			set_message('Task tidak ditemukan.', 'error');
			redirect_back();
			return;
		}

		if (!$this->_check_contract_active($task->campaign_kol_id)) {
			set_message('Tidak dapat mengubah status task. Kontrak untuk KOL ini belum ditandatangani (ACTIVE).', 'error');
			redirect_back();
			return;
		}

		$estimated_date = $this->input->post('estimated_posting_date');
		$brief_note = $this->input->post('brief_note');
		
		$update_data = [
			'status'     => 'PENDING_REVIEW',
			'updated_at' => date('Y-m-d H:i:s')
		];
		if ($estimated_date) {
			$update_data['estimated_posting_date'] = $estimated_date;
		}
		if ($brief_note !== null) {
			$update_data['brief_note'] = $brief_note;
		}

		// Handle file upload if exists
		$file_uploaded = null;
		if (!empty($_FILES['brief_file']['name'])) {
			$config['upload_path']   = './uploads/tasks/';
			$config['allowed_types'] = 'pdf|doc|docx|jpg|png|jpeg';
			$config['max_size']      = 5000;
			$config['encrypt_name']  = true;

			if (!is_dir($config['upload_path'])) {
				mkdir($config['upload_path'], 0777, true);
			}

			$this->load->library('upload', $config);
			if ($this->upload->do_upload('brief_file')) {
				$uploadData = $this->upload->data();
				$update_data['brief_file'] = $uploadData['file_name'];
				$file_uploaded = $uploadData['file_name'];
			} else {
				set_message($this->upload->display_errors(), 'error');
				redirect_back();
				return;
			}
		}

		$this->db->where('id', $id)->update('tasks', $update_data);

		// Synchronize with `briefs` table
		$existing_brief = $this->db->where('task_id', $id)->order_by('id', 'DESC')->get('briefs')->row();
		$brief_content = $brief_note ? $brief_note : ($task->notes ? $task->notes : ($task->title ? $task->title : 'Brief Task #' . $id));
		$file_path = $file_uploaded ? $file_uploaded : ($existing_brief ? $existing_brief->file_path : $task->brief_file);

		if ($existing_brief) {
			$this->db->where('id', $existing_brief->id)->update('briefs', [
				'title'        => $task->title ?: ('Brief Task #' . $id),
				'content'      => $brief_content,
				'file_path'    => $file_path,
				'status'       => 'PENDING_REVIEW',
				'submitted_at' => date('Y-m-d H:i:s'),
				'updated_at'   => date('Y-m-d H:i:s')
			]);
		} else {
			$this->db->insert('briefs', [
				'task_id'        => $id,
				'title'          => $task->title ?: ('Brief Task #' . $id),
				'content'        => $brief_content,
				'file_path'      => $file_path,
				'revision_count' => 0,
				'status'         => 'PENDING_REVIEW',
				'submitted_at'   => date('Y-m-d H:i:s'),
				'created_by'     => $user_id,
				'created_at'     => date('Y-m-d H:i:s'),
				'updated_at'     => date('Y-m-d H:i:s')
			]);
		}

		// Log status history
		$this->db->insert('task_status_histories', [
			'task_id'    => $id,
			'status'     => 'PENDING_REVIEW',
			'changed_by' => $user_id,
			'note'       => 'Jadwal & SOW disimpan. Menunggu review Leader.',
			'created_at' => date('Y-m-d H:i:s')
		]);

		set_message('Jadwal dan SOW berhasil disimpan. Menunggu review Leader.', 'success');
		redirect_back();
	}

	/**
	 * ACTION: Upload Brief per-Task (Staff)
	 * File dan teks keduanya opsional, tapi minimal salah satu wajib ada.
	 */
	public function upload_brief($id)
	{
		$this->is_allowed('tasks_update');
		$user_id = get_user_data('id') ?: 1;
		$task = $this->model_tasks->find($id);
		if (!$task) {
			set_message('Task tidak ditemukan.', 'error');
			redirect_back();
			return;
		}

		if (!$this->_check_contract_active($task->campaign_kol_id)) {
			set_message('Tidak dapat mengupload brief. Kontrak untuk KOL ini belum ditandatangani (ACTIVE).', 'error');
			redirect_back();
			return;
		}

		if (!in_array($task->status, ['DRAFT', 'REVISION'])) {
			set_message('Brief hanya dapat diupload pada status DRAFT atau REVISION. Brief yang sudah diajukan (PENDING_REVIEW) menunggu keputusan Leader.', 'error');
			redirect_back();
			return;
		}

		$brief_title = trim((string)$this->input->post('brief_title'));
		$brief_content = trim((string)$this->input->post('brief_content'));
		$has_file = !empty($_FILES['brief_file']['name']);

		if ($brief_content === '' && !$has_file) {
			set_message('Isi teks brief atau file brief minimal salah satu wajib diisi.', 'error');
			redirect_back();
			return;
		}

		$file_uploaded = null;
		if ($has_file) {
			$config['upload_path']   = './uploads/tasks/';
			$config['allowed_types'] = 'pdf|doc|docx|jpg|jpeg|png';
			$config['max_size']      = 5000;
			$config['encrypt_name']  = true;

			if (!is_dir($config['upload_path'])) {
				mkdir($config['upload_path'], 0777, true);
			}

			$this->load->library('upload', $config);
			if ($this->upload->do_upload('brief_file')) {
				$uploadData = $this->upload->data();
				$file_uploaded = $uploadData['file_name'];
				// Hapus file lama bila dioverwrite
				if (!empty($task->brief_file) && $task->brief_file !== $file_uploaded) {
					@unlink(FCPATH . 'uploads/tasks/' . $task->brief_file);
				}
			} else {
				set_message(strip_tags($this->upload->display_errors()), 'error');
				redirect_back();
				return;
			}
		}

		$update_data = [
			'status'     => 'PENDING_REVIEW',
			'updated_at' => date('Y-m-d H:i:s')
		];
		if ($brief_content !== '') {
			$update_data['brief_note'] = $brief_content;
		}
		if ($file_uploaded) {
			$update_data['brief_file'] = $file_uploaded;
		}
		$this->db->where('id', $id)->update('tasks', $update_data);

		$existing_brief = $this->db->where('task_id', $id)->order_by('id', 'DESC')->get('briefs')->row();
		$final_title = $brief_title !== '' ? $brief_title : ($task->title ?: ('Brief Task #' . $id));
		$final_content = $brief_content !== '' ? $brief_content : ($existing_brief ? $existing_brief->content : ($task->notes ?: ''));
		if ($final_content === '' || $final_content === null) {
			$final_content = $final_title;
		}
		$final_file = $file_uploaded ? $file_uploaded : ($existing_brief ? $existing_brief->file_path : $task->brief_file);

		if ($existing_brief) {
			$this->db->where('id', $existing_brief->id)->update('briefs', [
				'title'        => $final_title,
				'content'      => $final_content,
				'file_path'    => $final_file,
				'status'       => 'PENDING_REVIEW',
				'submitted_at' => date('Y-m-d H:i:s'),
				'updated_at'   => date('Y-m-d H:i:s')
			]);
		} else {
			$this->db->insert('briefs', [
				'task_id'        => $id,
				'title'          => $final_title,
				'content'        => $final_content,
				'file_path'      => $final_file,
				'revision_count' => 0,
				'status'         => 'PENDING_REVIEW',
				'submitted_at'   => date('Y-m-d H:i:s'),
				'created_by'     => $user_id,
				'created_at'     => date('Y-m-d H:i:s'),
				'updated_at'     => date('Y-m-d H:i:s')
			]);
		}

		$this->db->insert('task_status_histories', [
			'task_id'    => $id,
			'status'     => 'PENDING_REVIEW',
			'changed_by' => $user_id,
			'note'       => 'Brief diupload Staff. Menunggu review Leader.',
			'created_at' => date('Y-m-d H:i:s')
		]);

		set_message('Brief berhasil diupload. Menunggu review Leader.', 'success');
		redirect_back();
	}

	/**
	 * ACTION: Approve/Reject SOW (Leader)
	 */
	public function review_sow($id)
	{
		$this->is_allowed('briefs_approve');
		$user_id = get_user_data('id') ?: 1;
		$action = $this->input->post('action'); // 'approve' or 'reject'
		
		$task = $this->model_tasks->find($id);
		if (!$task) {
			set_message('Task tidak ditemukan.', 'error');
			redirect_back();
			return;
		}

		if (!$this->_check_contract_active($task->campaign_kol_id)) {
			set_message('Tidak dapat mengubah status task. Kontrak untuk KOL ini belum ditandatangani (ACTIVE).', 'error');
			redirect_back();
			return;
		}

		$existing_brief = $this->db->where('task_id', $id)->order_by('id', 'DESC')->get('briefs')->row();
		if (!$existing_brief) {
			$this->db->insert('briefs', [
				'task_id'        => $id,
				'title'          => $task->title ?: ('Brief Task #' . $id),
				'content'        => $task->notes ?: ('Brief Task #' . $id),
				'file_path'      => $task->brief_file,
				'revision_count' => 0,
				'status'         => ($action === 'approve' ? 'APPROVED' : 'REVISION'),
				'created_by'     => $user_id,
				'created_at'     => date('Y-m-d H:i:s'),
				'updated_at'     => date('Y-m-d H:i:s')
			]);
			$brief_id = $this->db->insert_id();
		} else {
			$brief_id = $existing_brief->id;
		}

		if ($action === 'approve') {
			$this->db->where('id', $id)->update('tasks', [
				'status'     => 'WAITING_DRAFT',
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$this->db->where('id', $brief_id)->update('briefs', [
				'status'     => 'APPROVED',
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$this->db->insert('brief_approval_histories', [
				'brief_id'   => $brief_id,
				'status'     => 'APPROVED',
				'note'       => 'Brief/SOW disetujui Leader.',
				'acted_by'   => $user_id,
				'created_at' => date('Y-m-d H:i:s')
			]);

			$this->db->insert('task_status_histories', [
				'task_id'    => $id,
				'status'     => 'WAITING_DRAFT',
				'changed_by' => $user_id,
				'note'       => 'Brief/SOW disetujui Leader. Siap menuju tahap draft.',
				'created_at' => date('Y-m-d H:i:s')
			]);

			set_message('SOW disetujui. Task maju ke tahap Draft.', 'success');
		} else {
			$note = $this->input->post('brief_note');
			$this->db->where('id', $id)->update('tasks', [
				'status'     => 'REVISION',
				'brief_note' => $note,
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$this->db->where('id', $brief_id)->set('revision_count', 'revision_count+1', FALSE)->update('briefs', [
				'status'     => 'REVISION',
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$this->db->insert('brief_approval_histories', [
				'brief_id'   => $brief_id,
				'status'     => 'REVISION',
				'note'       => $note ?: 'SOW dikembalikan untuk revisi',
				'acted_by'   => $user_id,
				'created_at' => date('Y-m-d H:i:s')
			]);

			$this->db->insert('task_status_histories', [
				'task_id'    => $id,
				'status'     => 'REVISION',
				'changed_by' => $user_id,
				'note'       => 'SOW dikembalikan untuk revisi: ' . ($note ?: '-'),
				'created_at' => date('Y-m-d H:i:s')
			]);

			set_message('SOW dikembalikan untuk revisi.', 'warning');
		}
		redirect_back();
	}

	/**
	 * ACTION: Upload Draft Video (Staff)
	 */
	public function upload_draft($id)
	{
		$this->is_allowed('tasks_update');
		$user_id = get_user_data('id') ?: 1;
		$draft_url = trim($this->input->post('draft_url'));
		
		if (empty($draft_url)) {
			set_message('URL draft wajib diisi.', 'error');
			redirect_back();
			return;
		}

		$task = $this->model_tasks->find($id);
		if (!$task) {
			set_message('Task tidak ditemukan.', 'error');
			redirect_back();
			return;
		}

		if (!$this->_check_contract_active($task->campaign_kol_id)) {
			set_message('Tidak dapat mengupload draft. Kontrak untuk KOL ini belum ditandatangani (ACTIVE).', 'error');
			redirect_back();
			return;
		}

		if (!in_array($task->status, ['WAITING_DRAFT', 'REVISION_DRAFT'])) {
			set_message('Draft hanya dapat diupload setelah brief disetujui (status WAITING_DRAFT).', 'error');
			redirect_back();
			return;
		}

		// Get max version for this task
		$this->db->select_max('version');
		$this->db->where('task_id', $id);
		$res = $this->db->get('drafts')->row();
		$next_version = ($res && $res->version) ? ((int)$res->version + 1) : 1;

		// Insert into drafts table
		$this->db->insert('drafts', [
			'task_id'    => $id,
			'version'    => $next_version,
			'draft_url'  => $draft_url,
			'status'     => 'SUBMITTED',
			'created_by' => $user_id,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);
		$draft_id = $this->db->insert_id();

		// Log into draft_review_histories
		$this->db->insert('draft_review_histories', [
			'draft_id'   => $draft_id,
			'action'     => 'SUBMITTED',
			'note'       => 'Draft video v' . $next_version . ' diunggah staff.',
			'acted_by'   => $user_id,
			'created_at' => date('Y-m-d H:i:s')
		]);

		// Update task
		$this->db->where('id', $id)->update('tasks', [
			'status'     => 'IN_REVIEW_DRAFT',
			'draft_url'  => $draft_url,
			'updated_at' => date('Y-m-d H:i:s')
		]);

		// Log task history
		$this->db->insert('task_status_histories', [
			'task_id'    => $id,
			'status'     => 'IN_REVIEW_DRAFT',
			'changed_by' => $user_id,
			'note'       => 'Draft video v' . $next_version . ' diunggah: ' . $draft_url,
			'created_at' => date('Y-m-d H:i:s')
		]);

		set_message('Draft berhasil dikirim. Menunggu review.', 'success');
		redirect_back();
	}

	/**
	 * ACTION: Approve Draft (Leader)
	 */
	public function approve_draft($id)
	{
		$this->is_allowed('briefs_approve');
		$user_id = get_user_data('id') ?: 1;

		$task = $this->model_tasks->find($id);
		if ($task && !in_array($task->status, ['IN_REVIEW_DRAFT'])) {
			if ($this->input->is_ajax_request()) {
				echo json_encode(['success' => false, 'message' => 'Draft hanya bisa diapprove dari status IN_REVIEW_DRAFT.']);
				return;
			}
			set_message('Draft hanya bisa diapprove dari status IN_REVIEW_DRAFT.', 'error');
			redirect_back();
			return;
		}
		if (!$task) {
			if ($this->input->is_ajax_request()) {
				echo json_encode(['success' => false, 'message' => 'Task tidak ditemukan.']);
				return;
			}
			set_message('Task tidak ditemukan.', 'error');
			redirect_back();
			return;
		}

		// Update task status to APPROVED_DRAFT
		$this->db->where('id', $id)->update('tasks', [
			'status'     => 'APPROVED_DRAFT',
			'updated_at' => date('Y-m-d H:i:s')
		]);

		// Find latest draft for this task
		$draft = $this->db->where('task_id', $id)->order_by('version', 'DESC')->get('drafts')->row();
		if (!$draft) {
			$this->db->insert('drafts', [
				'task_id'    => $id,
				'version'    => 1,
				'draft_url'  => $task->draft_url ?: '',
				'status'     => 'APPROVED',
				'created_by' => $user_id,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);
			$draft_id = $this->db->insert_id();
		} else {
			$draft_id = $draft->id;
			$this->db->where('id', $draft_id)->update('drafts', [
				'status'     => 'APPROVED',
				'updated_at' => date('Y-m-d H:i:s')
			]);
		}

		// Log to draft_review_histories
		$this->db->insert('draft_review_histories', [
			'draft_id'   => $draft_id,
			'action'     => 'APPROVED',
			'note'       => 'Draft disetujui Leader. Siap untuk Live Posting.',
			'acted_by'   => $user_id,
			'created_at' => date('Y-m-d H:i:s')
		]);

		// Log to task_status_histories
		$this->db->insert('task_status_histories', [
			'task_id'    => $id,
			'status'     => 'APPROVED_DRAFT',
			'changed_by' => $user_id,
			'note'       => 'Draft disetujui oleh Leader.',
			'created_at' => date('Y-m-d H:i:s')
		]);

		if ($this->input->is_ajax_request()) {
			echo json_encode(['success' => true, 'message' => 'Draft disetujui.']);
			return;
		}

		set_message('Draft disetujui. Siap untuk Live Posting.', 'success');
		redirect_back();
	}

	/**
	 * ACTION: Request Revision Draft (Leader)
	 */
	public function request_revision_draft($id)
	{
		$this->is_allowed('briefs_approve');
		$user_id = get_user_data('id') ?: 1;
		$note = trim($this->input->post('draft_note') ?: $this->input->post('note'));

		$task = $this->model_tasks->find($id);
		if (!$task) {
			if ($this->input->is_ajax_request()) {
				echo json_encode(['success' => false, 'message' => 'Task tidak ditemukan.']);
				return;
			}
			set_message('Task tidak ditemukan.', 'error');
			redirect_back();
			return;
		}
		if ($task->status !== 'IN_REVIEW_DRAFT') {
			if ($this->input->is_ajax_request()) {
				echo json_encode(['success' => false, 'message' => 'Revisi hanya bisa dari status IN_REVIEW_DRAFT.']);
				return;
			}
			set_message('Revisi hanya bisa dari status IN_REVIEW_DRAFT.', 'error');
			redirect_back();
			return;
		}

		// Update task status to REVISION_DRAFT
		$this->db->where('id', $id)->update('tasks', [
			'status'     => 'REVISION_DRAFT',
			'draft_note' => $note,
			'updated_at' => date('Y-m-d H:i:s')
		]);

		// Find latest draft for this task
		$draft = $this->db->where('task_id', $id)->order_by('version', 'DESC')->get('drafts')->row();
		if (!$draft) {
			$this->db->insert('drafts', [
				'task_id'    => $id,
				'version'    => 1,
				'draft_url'  => $task->draft_url ?: '',
				'notes'      => $note,
				'status'     => 'REVISION',
				'created_by' => $user_id,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);
			$draft_id = $this->db->insert_id();
		} else {
			$draft_id = $draft->id;
			$this->db->where('id', $draft_id)->update('drafts', [
				'status'     => 'REVISION',
				'notes'      => $note,
				'updated_at' => date('Y-m-d H:i:s')
			]);
		}

		// Log to draft_review_histories
		$this->db->insert('draft_review_histories', [
			'draft_id'   => $draft_id,
			'action'     => 'REVISION',
			'note'       => $note ?: 'Revisi diminta oleh Leader',
			'acted_by'   => $user_id,
			'created_at' => date('Y-m-d H:i:s')
		]);

		// Log to task_status_histories
		$this->db->insert('task_status_histories', [
			'task_id'    => $id,
			'status'     => 'REVISION_DRAFT',
			'changed_by' => $user_id,
			'note'       => 'Draft dikembalikan untuk revisi: ' . ($note ?: '-'),
			'created_at' => date('Y-m-d H:i:s')
		]);

		if ($this->input->is_ajax_request()) {
			echo json_encode(['success' => true, 'message' => 'Revisi draft berhasil diminta.']);
			return;
		}

		set_message('Draft dikembalikan untuk revisi.', 'warning');
		redirect_back();
	}

	/**
	 * ACTION: Approve/Reject Draft (Leader) - Wrapper
	 */
	public function review_draft($id)
	{
		$action = $this->input->post('action');
		if ($action === 'approve') {
			$this->approve_draft($id);
		} else {
			$this->request_revision_draft($id);
		}
	}

	/**
	 * ACTION: Approve Draft + Langsung POSTED (Leader)
	 * Dipakai saat KOL menempel link live di kolom draft.
	 * Alur: IN_REVIEW_DRAFT -> APPROVED_DRAFT -> POSTED dalam 1 klik.
	 * Gate produk/shopee tidak diubah: hanya bisa dari IN_REVIEW_DRAFT
	 * (artinya sudah lolos gate upload draft: shipment DELIVERED).
	 */
	public function approve_and_post($id)
	{
		$this->is_allowed('briefs_approve');
		$user_id = get_user_data('id') ?: 1;
		$direct_posted = $this->input->post('direct_posted');
		$live_url = trim((string)$this->input->post('direct_live_url'));

		// Tanpa centang = approve biasa
		if (empty($direct_posted)) {
			$this->approve_draft($id);
			return;
		}

		$task = $this->db->select('tasks.*, platforms.name as platform_name, platforms.code as platform_code, campaigns.status as campaign_status')
			->join('platforms', 'platforms.id = tasks.platform_id', 'left')
			->join('campaign_kols', 'campaign_kols.id = tasks.campaign_kol_id', 'left')
			->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'left')
			->where('tasks.id', $id)
			->get('tasks')
			->row();

		if (!$task) {
			set_message('Task tidak ditemukan.', 'error');
			redirect_back();
			return;
		}

		if ($task->status !== 'IN_REVIEW_DRAFT') {
			set_message('Direct POSTED hanya bisa dari status IN_REVIEW_DRAFT.', 'error');
			redirect_back();
			return;
		}

		// Gate produk tunggal: sudah lolos saat upload draft, di sini tinggal re-check.
		if (!$this->_is_product_ready($task->campaign_kol_id)) {
			set_message('Produk belum tercatat DELIVERED. Direct POSTED dibatalkan, silakan approve biasa.', 'error');
			redirect_back();
			return;
		}

		if (!$this->_check_contract_active($task->campaign_kol_id)) {
			set_message('Kontrak deal ini belum ACTIVE. Direct POSTED dibatalkan.', 'error');
			redirect_back();
			return;
		}

		if ($live_url === '') {
			set_message('Centang Langsung POSTED wajib diisi link live-nya.', 'error');
			redirect_back();
			return;
		}

		if (!in_array($task->campaign_status, ['ACTIVE', 'COMPLETED'])) {
			set_message('Campaign belum ACTIVE. Direct POSTED dibatalkan, silakan approve biasa dulu.', 'error');
			redirect_back();
			return;
		}

		$platform_code = strtoupper(trim($task->platform_code ?: ''));
		$platform_name = $task->platform_name ?: ($platform_code ?: 'Platform yang sesuai');
		$is_valid = true;
		if ($platform_code === 'INSTAGRAM' && !preg_match('/(instagram\.com)/i', $live_url)) {
			$is_valid = false;
		} elseif ($platform_code === 'TIKTOK' && !preg_match('/(tiktok\.com)/i', $live_url)) {
			$is_valid = false;
		} elseif ($platform_code === 'YOUTUBE' && !preg_match('/(youtube\.com|youtu\.be)/i', $live_url)) {
			$is_valid = false;
		} elseif (in_array($platform_code, ['TWITTER', 'X']) && !preg_match('/(twitter\.com|x\.com)/i', $live_url)) {
			$is_valid = false;
		} elseif ($platform_code === 'FACEBOOK' && !preg_match('/(facebook\.com|fb\.watch)/i', $live_url)) {
			$is_valid = false;
		}
		if (!$is_valid) {
			set_message("Link tidak valid! Platform task ini adalah <strong>{$platform_name}</strong>.", 'error');
			redirect_back();
			return;
		}

		$this->db->trans_start();

		// 1. Approve (sama seperti approve_draft)
		$this->db->where('id', $id)->update('tasks', [
			'status'     => 'APPROVED_DRAFT',
			'updated_at' => date('Y-m-d H:i:s')
		]);
		$draft = $this->db->where('task_id', $id)->order_by('version', 'DESC')->get('drafts')->row();
		if (!$draft) {
			$this->db->insert('drafts', [
				'task_id'    => $id,
				'version'    => 1,
				'draft_url'  => $task->draft_url ?: '',
				'status'     => 'APPROVED',
				'created_by' => $user_id,
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);
			$draft_id = $this->db->insert_id();
		} else {
			$draft_id = $draft->id;
			$this->db->where('id', $draft_id)->update('drafts', [
				'status'     => 'APPROVED',
				'updated_at' => date('Y-m-d H:i:s')
			]);
		}
		$this->db->insert('draft_review_histories', [
			'draft_id'   => $draft_id,
			'action'     => 'APPROVED',
			'note'       => 'Draft disetujui Leader via Direct POSTED. Siap Live.',
			'acted_by'   => $user_id,
			'created_at' => date('Y-m-d H:i:s')
		]);
		$this->db->insert('task_status_histories', [
			'task_id'    => $id,
			'status'     => 'APPROVED_DRAFT',
			'changed_by' => $user_id,
			'note'       => 'Draft disetujui via Direct POSTED oleh Leader.',
			'created_at' => date('Y-m-d H:i:s')
		]);

		// 2. POSTED (sama seperti set_live_url)
		$this->db->where('id', $id)->update('tasks', [
			'status'     => 'POSTED',
			'live_url'   => $live_url,
			'updated_at' => date('Y-m-d H:i:s')
		]);
		$existing_posting = $this->db->get_where('postings', ['task_id' => $id])->row();
		if (!$existing_posting) {
			$this->db->insert('postings', [
				'task_id'          => $id,
				'platform_id'      => $task->platform_id,
				'live_url'         => $live_url,
				'posted_at_source' => 'MANUAL',
				'status'           => 'ACTIVE',
				'submitted_by'     => $this->aauth->get_user_id(),
				'posted_at'        => date('Y-m-d H:i:s'),
				'scrape_day3_done' => 0,
				'scrape_day7_done' => 0,
				'created_at'       => date('Y-m-d H:i:s'),
				'updated_at'       => date('Y-m-d H:i:s')
			]);
			$posting_id = $this->db->insert_id();
			$this->db->insert('apify_jobs', [
				'posting_id' => $posting_id,
				'status'     => 'QUEUED',
				'created_at' => date('Y-m-d H:i:s')
			]);
		} else {
			$this->db->where('id', $existing_posting->id)->update('postings', [
				'live_url'   => $live_url,
				'posted_at'  => $existing_posting->posted_at ?: date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);
		}
		if ($task->campaign_kol_id) {
			$done_count = $this->db->where('campaign_kol_id', $task->campaign_kol_id)
				->where_in('status', ['POSTED', 'DONE'])
				->count_all_results('tasks');
			$this->db->where('id', $task->campaign_kol_id)->update('campaign_kols', [
				'task_completed_count' => $done_count
			]);
		}
		$this->db->insert('task_status_histories', [
			'task_id'    => $id,
			'status'     => 'POSTED',
			'changed_by' => $user_id,
			'note'       => 'Direct POSTED dari Review Draft: ' . $live_url,
			'created_at' => date('Y-m-d H:i:s')
		]);

		$this->db->trans_complete();
		if ($this->db->trans_status() === FALSE) {
			set_message('Gagal memproses Direct POSTED.', 'error');
			redirect_back();
			return;
		}

		set_message('Draft disetujui dan langsung POSTED. Siap di-sync!', 'success');
		redirect_back();
	}

	/**
	 * ACTION: Input Live URL (Staff)
	 */
	public function set_live_url($id)
	{
		$this->is_allowed('tasks_update');
		$live_url = trim($this->input->post('live_url'));
		
		$task = $this->db->select('tasks.*, platforms.name as platform_name, platforms.code as platform_code, campaigns.status as campaign_status')
			->join('platforms', 'platforms.id = tasks.platform_id', 'left')
			->join('campaign_kols', 'campaign_kols.id = tasks.campaign_kol_id', 'left')
			->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'left')
			->where('tasks.id', $id)
			->get('tasks')
			->row();

		if (!$task) {
			set_message('Task tidak ditemukan.', 'error');
			redirect_back();
			return;
		}

		if (!in_array($task->status, ['APPROVED_DRAFT', 'POSTED'])) {
			set_message('Live URL hanya bisa diinput dari status APPROVED_DRAFT (atau update saat POSTED).', 'error');
			redirect_back();
			return;
		}

		if (!$this->_check_contract_active($task->campaign_kol_id)) {
			set_message('Kontrak deal ini belum ACTIVE. Live URL ditolak.', 'error');
			redirect_back();
			return;
		}

		if (!$this->_is_product_ready($task->campaign_kol_id)) {
			set_message('Produk belum tercatat DELIVERED. Live URL ditolak.', 'error');
			redirect_back();
			return;
		}

		if (!in_array($task->campaign_status, ['ACTIVE', 'COMPLETED'])) {
			set_message('Campaign belum ACTIVE. Live URL hanya dapat diinput setelah status Campaign menjadi ACTIVE.', 'error');
			redirect_back();
			return;
		}

		$platform_code = strtoupper(trim($task->platform_code ?: ''));
		$platform_name = $task->platform_name ?: ($platform_code ?: 'Platform yang sesuai');

		// Strict platform validation
		$is_valid = true;
		if ($platform_code === 'INSTAGRAM' && !preg_match('/(instagram\.com)/i', $live_url)) {
			$is_valid = false;
		} elseif ($platform_code === 'TIKTOK' && !preg_match('/(tiktok\.com)/i', $live_url)) {
			$is_valid = false;
		} elseif ($platform_code === 'YOUTUBE' && !preg_match('/(youtube\.com|youtu\.be)/i', $live_url)) {
			$is_valid = false;
		} elseif (in_array($platform_code, ['TWITTER', 'X']) && !preg_match('/(twitter\.com|x\.com)/i', $live_url)) {
			$is_valid = false;
		} elseif ($platform_code === 'FACEBOOK' && !preg_match('/(facebook\.com|fb\.watch)/i', $live_url)) {
			$is_valid = false;
		}

		if (!$is_valid) {
			set_message("Link tidak valid! Platform task ini adalah <strong>{$platform_name}</strong>. Link yang Anda masukkan tidak sesuai dengan platform tersebut.", 'error');
			redirect_back();
			return;
		}

		$this->db->where('id', $id)->update('tasks', [
			'status' => 'POSTED',
			'live_url' => $live_url,
			'updated_at' => date('Y-m-d H:i:s')
		]);

		// Ensure posting record exists
		$existing_posting = $this->db->get_where('postings', ['task_id' => $id])->row();
		if (!$existing_posting) {
			$this->db->insert('postings', [
				'task_id'          => $id,
				'platform_id'      => $task->platform_id,
				'live_url'         => $live_url,
				'posted_at_source' => 'MANUAL',
				'status'           => 'ACTIVE',
				'submitted_by'     => $this->aauth->get_user_id(),
				'posted_at'        => date('Y-m-d H:i:s'),
				'scrape_day3_done' => 0,
				'scrape_day7_done' => 0,
				'created_at'       => date('Y-m-d H:i:s'),
				'updated_at'       => date('Y-m-d H:i:s')
			]);
			$posting_id = $this->db->insert_id();

			$this->db->insert('apify_jobs', [
				'posting_id' => $posting_id,
				'status'     => 'QUEUED',
				'created_at' => date('Y-m-d H:i:s')
			]);
		} else {
			$this->db->where('id', $existing_posting->id)->update('postings', [
				'live_url'   => $live_url,
				'posted_at'  => $existing_posting->posted_at ?: date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);
		}

		// Optional: Update completed task count on campaign_kols
		if ($task->campaign_kol_id) {
			$done_count = $this->db->where('campaign_kol_id', $task->campaign_kol_id)
				->where_in('status', ['POSTED', 'DONE'])
				->count_all_results('tasks');

			$this->db->where('id', $task->campaign_kol_id)->update('campaign_kols', [
				'task_completed_count' => $done_count
			]);
		}

		set_message('Live URL berhasil disimpan. Task siap di-sync!', 'success');
		redirect_back();
	}

	/**
	 * Sync & Scrape Live URL Video Metrics via Apify
	 */
	/**
	 * Automatically Scrape Postings that reached Day+3 or Day+7 (Run automatically in background)
	 *
	 * @param int $limit Maximum number of postings to process per batch
	 * @return array Summary of processed, successes, and errors
	 */
	public function _auto_scrape_due_postings($limit = 5)
	{
		$postings = $this->db->select('postings.*, tasks.id as task_id, DATEDIFF(NOW(), COALESCE(postings.posted_at, postings.created_at)) as days_passed')
			->join('tasks', 'tasks.id = postings.task_id', 'inner')
			->where('postings.live_url !=', '')
			->where('postings.live_url IS NOT NULL', null, false)
			->where('(
				((postings.scrape_day3_done = 0 OR postings.scrape_day3_done IS NULL) AND DATEDIFF(NOW(), COALESCE(postings.posted_at, postings.created_at)) >= 3)
				OR
				((postings.scrape_day7_done = 0 OR postings.scrape_day7_done IS NULL) AND DATEDIFF(NOW(), COALESCE(postings.posted_at, postings.created_at)) >= 7)
			)', null, false)
			->limit($limit)
			->get('postings')
			->result();

		$processed = 0;
		$success_count = 0;
		$errors = [];

		foreach ($postings as $p) {
			$processed++;
			$res = $this->_perform_scrape_task($p->task_id);
			if ($res['success']) {
				$success_count++;
				$update = [];
				if ($p->days_passed >= 7) {
					$update['scrape_day3_done'] = 1;
					$update['scrape_day7_done'] = 1;
				} elseif ($p->days_passed >= 3) {
					$update['scrape_day3_done'] = 1;
				}
				if (!empty($update)) {
					$this->db->where('id', $p->id)->update('postings', $update);
				}
			} else {
				$errors[] = "Task #{$p->task_id}: " . $res['message'];
			}
		}

		return [
			'processed'     => $processed,
			'success_count' => $success_count,
			'errors'        => $errors
		];
	}

	/**
	 * CLI / Background Cron Endpoint for Automatic Scraping
	 * Usage: php index.php administrator/tasks cron_auto_scrape
	 */
	public function cron_auto_scrape()
	{
		$res = $this->_auto_scrape_due_postings(50);
		if (is_cli()) {
			echo "Auto-Scrape Cron Completed: {$res['success_count']} of {$res['processed']} postings scraped.\n";
		} else {
			header('Content-Type: application/json');
			echo json_encode([
				'success'  => true,
				'message'  => "Auto-Scrape completed: {$res['success_count']} of {$res['processed']} postings scraped.",
				'data'     => $res,
				'csrfHash' => $this->security->get_csrf_hash()
			]);
		}
	}

	/**
	 * Sync & Scrape Live URL Video Metrics via Apify
	 */
	public function sync_posting_metrics($id = null)
	{
		header('Content-Type: application/json');
		if (!$this->is_allowed('tasks_update', false) && !$this->is_allowed('tasks_view', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access'),
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$task_id = (int)($id ?: $this->input->post('id'));
		if (!$task_id) {
			echo json_encode([
				'success' => false,
				'message' => 'Task ID tidak valid.',
				'csrfHash' => $this->security->get_csrf_hash()
			]);
			return;
		}

		$res = $this->_perform_scrape_task($task_id);
		$res['csrfHash'] = $this->security->get_csrf_hash();
		echo json_encode($res);
	}

	/**
	 * Core Scraping Routine for a single Task ID
	 */
	protected function _perform_scrape_task($task_id)
	{
		$task = $this->db->select('tasks.*, platforms.name as platform_name, platforms.code as platform_code, postings.id as posting_id, postings.live_url as posting_live_url')
			->join('platforms', 'platforms.id = tasks.platform_id', 'left')
			->join('postings', 'postings.task_id = tasks.id', 'left')
			->where('tasks.id', $task_id)
			->get('tasks')
			->row();

		if (!$task) {
			return ['success' => false, 'message' => 'Task tidak ditemukan.'];
		}

		$live_url = trim($task->posting_live_url ?: ($task->live_url ?? ''));
		if (empty($live_url) && !empty($task->notes)) {
			if (preg_match('/Live URL:\s*(https?:\/\/[^\s\n\r]+)/i', $task->notes, $matches)) {
				$live_url = trim($matches[1]);
			}
		}

		if (empty($live_url)) {
			return ['success' => false, 'message' => 'Live URL belum diisi untuk task ini.'];
		}

		$platform_code = strtoupper(trim($task->platform_code ?: 'TIKTOK'));
		$platform_name = $task->platform_name ?: ($platform_code ?: 'Platform');

		// Strict Platform Validation
		$is_valid = true;
		if ($platform_code === 'INSTAGRAM' && !preg_match('/(instagram\.com)/i', $live_url)) {
			$is_valid = false;
		} elseif ($platform_code === 'TIKTOK' && !preg_match('/(tiktok\.com)/i', $live_url)) {
			$is_valid = false;
		} elseif ($platform_code === 'YOUTUBE' && !preg_match('/(youtube\.com|youtu\.be)/i', $live_url)) {
			$is_valid = false;
		} elseif (in_array($platform_code, ['TWITTER', 'X']) && !preg_match('/(twitter\.com|x\.com)/i', $live_url)) {
			$is_valid = false;
		} elseif ($platform_code === 'FACEBOOK' && !preg_match('/(facebook\.com|fb\.watch)/i', $live_url)) {
			$is_valid = false;
		}

		if (!$is_valid) {
			return ['success' => false, 'message' => "Link tidak valid! Platform task ini adalah {$platform_name}."];
		}

		// Ensure posting record exists
		$posting_id = $task->posting_id;
		if (empty($posting_id)) {
			$this->db->insert('postings', [
				'task_id'          => $task_id,
				'platform_id'      => $task->platform_id,
				'live_url'         => $live_url,
				'posted_at_source' => 'MANUAL',
				'status'           => 'ACTIVE',
				'submitted_by'     => $this->aauth->get_user_id(),
				'posted_at'        => date('Y-m-d H:i:s'),
				'scrape_day3_done' => 0,
				'scrape_day7_done' => 0,
				'created_at'       => date('Y-m-d H:i:s'),
				'updated_at'       => date('Y-m-d H:i:s')
			]);
			$posting_id = $this->db->insert_id();
		} else {
			$this->db->where('id', $posting_id)->update('postings', ['live_url' => $live_url, 'updated_at' => date('Y-m-d H:i:s')]);
		}

		// Load Apify config
		$this->config->load('apify', TRUE);
		$cfg = $this->config->item('apify') ?: [];

		$api_token = '';
		if (function_exists('get_option')) {
			$api_token = get_option('apify_api_token');
		}
		if (empty($api_token)) {
			$api_token = $cfg['apify_api_token'] ?? '';
		}

		if (empty($api_token)) {
			return ['success' => false, 'message' => 'Apify API token belum dikonfigurasi.'];
		}

		$actors = $cfg['apify_actors'] ?? [];
		$actor_id = $actors[$platform_code] ?? null;

		if (!$actor_id) {
			return ['success' => false, 'message' => "Belum ada Apify Actor untuk platform: {$platform_code}"];
		}

		$base_url = $cfg['apify_base_url'] ?? 'https://api.apify.com/v2';
		$timeout  = $cfg['apify_timeout'] ?? 120;
		$field_map = $cfg['apify_field_map'][$platform_code] ?? [];

		$url = rtrim($base_url, '/') . '/actors/' . urlencode($actor_id) . '/run-sync-get-dataset-items?token=' . urlencode($api_token);

		if ($platform_code === 'INSTAGRAM') {
			$payload_arr = [
				'directUrls'   => [$live_url],
				'resultsType'  => 'posts',
				'resultsLimit' => 1
			];
		} elseif ($platform_code === 'TIKTOK') {
			$payload_arr = [
				'postURLs'       => [$live_url],
				'startUrls'      => [['url' => $live_url]],
				'resultsPerPage' => 1
			];
		} elseif ($platform_code === 'YOUTUBE') {
			$payload_arr = [
				'startUrls'  => [['url' => $live_url]],
				'maxResults' => 1
			];
		} elseif ($platform_code === 'TWITTER' || $platform_code === 'X') {
			$payload_arr = [
				'startUrls' => [['url' => $live_url]],
				'maxItems'  => 1
			];
		} elseif ($platform_code === 'FACEBOOK') {
			$payload_arr = [
				'startUrls'    => [['url' => $live_url]],
				'resultsLimit' => 1
			];
		} else {
			$payload_arr = [
				'directUrls' => [$live_url],
				'startUrls'  => [['url' => $live_url]]
			];
		}

		$ch = curl_init();
		curl_setopt_array($ch, [
			CURLOPT_URL            => $url,
			CURLOPT_POST           => true,
			CURLOPT_POSTFIELDS     => json_encode($payload_arr),
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => $timeout,
			CURLOPT_HTTPHEADER     => [
				'Content-Type: application/json',
				'Accept: application/json',
			],
			CURLOPT_SSL_VERIFYPEER => true,
		]);

		$response  = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curl_err  = curl_error($ch);
		curl_close($ch);

		if ($response === false || !empty($curl_err)) {
			return ['success' => false, 'message' => "cURL error: {$curl_err}"];
		}

		if ($http_code < 200 || $http_code >= 300) {
			$snippet = mb_substr($response, 0, 200);
			return ['success' => false, 'message' => "Apify HTTP {$http_code}: {$snippet}"];
		}

		$data = json_decode($response, true);
		if (empty($data) || !is_array($data) || !isset($data[0])) {
			return ['success' => false, 'message' => "Format data dari Apify tidak sesuai atau kosong."];
		}

		$scraped = $data[0];

		$views    = (int)($scraped[$field_map['views'] ?? 'playCount'] ?? $scraped['views'] ?? $scraped['viewCount'] ?? $scraped['playCount'] ?? $scraped['videoViewCount'] ?? 0);
		$likes    = (int)($scraped[$field_map['likes'] ?? 'diggCount'] ?? $scraped['likes'] ?? $scraped['likeCount'] ?? $scraped['likesCount'] ?? $scraped['diggCount'] ?? $scraped['favorite_count'] ?? 0);
		$comments = (int)($scraped[$field_map['comments'] ?? 'commentCount'] ?? $scraped['comments'] ?? $scraped['commentCount'] ?? $scraped['commentsCount'] ?? $scraped['replyCount'] ?? 0);
		$shares   = (int)($scraped[$field_map['shares'] ?? 'shareCount'] ?? $scraped['shares'] ?? $scraped['shareCount'] ?? $scraped['sharesCount'] ?? $scraped['retweetCount'] ?? 0);
		$saves    = (int)($scraped[$field_map['saves'] ?? 'collectCount'] ?? $scraped['saves'] ?? $scraped['saveCount'] ?? $scraped['collectCount'] ?? $scraped['savesCount'] ?? $scraped['bookmarkCount'] ?? 0);

		$followers   = (int)($scraped['authorMeta']['fans'] ?? $scraped['followers'] ?? $scraped['followersCount'] ?? 0);
		$total_likes = (int)($scraped['authorMeta']['heart'] ?? $scraped['totalLikes'] ?? 0);
		$impressions = (int)($scraped['impressions'] ?? 0);
		$reach       = (int)($scraped['reach'] ?? 0);

		$engagements = $likes + $comments + $shares + $saves;
		$engagement_rate = ($views > 0) ? round($engagements / $views, 6) : 0;
		$budget = (float)preg_replace('/[^0-9]/', '', (string)($task->budget ?? 0));
		$cpv = ($views > 0 && $budget > 0) ? round($budget / $views, 2) : null;
		$cpe = ($engagements > 0 && $budget > 0) ? round($budget / $engagements, 2) : null;
		$view_rate = ($followers > 0) ? round($views / $followers, 6) : null;

		$now = date('Y-m-d H:i:s');
		$metric_data = [
			'posting_id'      => $posting_id,
			'snapshot_at'     => $now,
			'views'           => $views,
			'likes'           => $likes,
			'comments'        => $comments,
			'shares'          => $shares,
			'saves'           => $saves,
			'followers'       => $followers,
			'total_likes'     => $total_likes,
			'impressions'     => $impressions,
			'reach'           => $reach,
			'engagement_rate' => $engagement_rate,
			'cpv'             => $cpv,
			'view_rate'       => $view_rate,
			'created_at'      => $now,
		];

		$this->db->insert('posting_metrics', $metric_data);
		$metric_id = $this->db->insert_id();

		$this->db->where('id', $posting_id)->update('postings', [
			'latest_metric_id' => $metric_id,
			'updated_at'       => $now
		]);

		// Update CPV, CPE and status on tasks table
		$task_update = [
			'cpv'        => $cpv,
			'cpe'        => $cpe,
			'updated_at' => $now
		];
		if (in_array($task->status, ['POSTED', 'APPROVED_DRAFT'])) {
			$task_update['status'] = 'DONE';
		}
		$this->db->where('id', $task_id)->update('tasks', $task_update);

		if (in_array($task->status, ['POSTED', 'APPROVED_DRAFT'])) {
			$this->db->insert('task_status_histories', [
				'task_id'    => $task_id,
				'status'     => 'DONE',
				'changed_by' => $this->aauth->get_user_id() ?: 1,
				'note'       => 'Scraped via Apify. Views: ' . number_format($views) . ', ER: ' . round($engagement_rate * 100, 2) . '%',
				'created_at' => $now
			]);
		}

		// Recalculate collaboration score for campaign_kol & lifetime score for KOL
		if ($task->campaign_kol_id) {
			// 1. Collaboration Score for this dealing
			$sql = "
				SELECT AVG(pm.engagement_rate) AS avg_er
				FROM   posting_metrics pm
				JOIN   postings p ON p.id = pm.posting_id
				JOIN   tasks t    ON t.id = p.task_id
				WHERE  t.campaign_kol_id = ?
			";
			$ck_row = $this->db->query($sql, [$task->campaign_kol_id])->row();
			$score = ($ck_row && $ck_row->avg_er !== null) ? round((float)$ck_row->avg_er * 100, 2) : 0;

			$this->db->where('id', $task->campaign_kol_id)->update('campaign_kols', [
				'collaboration_score' => $score
			]);

			// 2. Lifetime Score for the Master KOL profile
			$ck = $this->db->select('kol_id')->where('id', $task->campaign_kol_id)->get('campaign_kols')->row();
			if ($ck && $ck->kol_id) {
				$kol_id = $ck->kol_id;

				// AVG ER across all tasks
				$er_sql = "
					SELECT AVG(pm.engagement_rate) AS avg_er
					FROM   posting_metrics pm
					JOIN   postings p       ON p.id  = pm.posting_id
					JOIN   tasks t          ON t.id  = p.task_id
					JOIN   campaign_kols ck ON ck.id = t.campaign_kol_id
					WHERE  ck.kol_id = ?
				";
				$er_row = $this->db->query($er_sql, [$kol_id])->row();
				$avg_er = ($er_row && $er_row->avg_er !== null) ? (float) $er_row->avg_er : 0;
				$norm_er = min($avg_er * 100, 100);

				// CPV Efficiency
				$cpv_sql = "
					SELECT AVG(pm.cpv) AS avg_cpv
					FROM   posting_metrics pm
					JOIN   postings p       ON p.id  = pm.posting_id
					JOIN   tasks t          ON t.id  = p.task_id
					JOIN   campaign_kols ck ON ck.id = t.campaign_kol_id
					WHERE  ck.kol_id = ?
					  AND  pm.cpv IS NOT NULL
				";
				$cpv_row = $this->db->query($cpv_sql, [$kol_id])->row();
				$avg_cpv = ($cpv_row && $cpv_row->avg_cpv !== null) ? (float) $cpv_row->avg_cpv : 0;
				$cpv_efficiency = max(100 - min(($avg_cpv / 1000) * 100, 100), 0);

				// Task Completion Rate
				$task_sql = "
					SELECT
						COUNT(*) AS total,
						SUM(CASE WHEN t.status IN ('POSTED', 'DONE') THEN 1 ELSE 0 END) AS completed
					FROM   tasks t
					JOIN   campaign_kols ck ON ck.id = t.campaign_kol_id
					WHERE  ck.kol_id  = ?
					  AND  t.status  != 'CANCELLED'
				";
				$task_row = $this->db->query($task_sql, [$kol_id])->row();
				$total     = ($task_row) ? (int) $task_row->total     : 0;
				$completed = ($task_row) ? (int) $task_row->completed : 0;
				$completion_rate = ($total > 0) ? ($completed / $total) * 100 : 0;

				// Weighted Composite Formula (40% ER + 30% CPV + 30% Completion)
				$lifetime_score = round(
					(0.40 * $norm_er) + (0.30 * $cpv_efficiency) + (0.30 * $completion_rate),
					2
				);

				// Insert score_histories
				$this->db->insert('score_histories', [
					'kol_id'          => $kol_id,
					'campaign_kol_id' => null,
					'score_type'      => 'LIFETIME',
					'score'           => $lifetime_score,
					'calculated_at'   => date('Y-m-d H:i:s'),
				]);

				// Update kols.lifetime_score
				$this->db->where('id', $kol_id)->update('kols', [
					'lifetime_score' => $lifetime_score
				]);
			}
		}

		$er_pct = round($engagement_rate * 100, 2);
		return [
			'success'    => true,
			'message'    => "Scraping berhasil! Views: " . number_format($views) . ", Likes: " . number_format($likes) . ", Comments: " . number_format($comments) . ", ER: {$er_pct}%",
			'views'      => $views,
			'likes'      => $likes,
			'comments'   => $comments,
			'shares'     => $shares,
			'er'         => "{$er_pct}%",
			'new_status' => $new_status
		];
	}

	// =========================================================================
	// SENTIMENT ANALYSIS INTEGRATION
	// =========================================================================

	/**
	 * AJAX endpoint: Trigger IndoBERT sentiment analysis for a task's live post.
	 * POST administrator/tasks/analyze_sentiment/{task_id}
	 *
	 * Guards: tasks_view permission, CSRF, cooldown (5 min), health pre-flight.
	 * Returns JSON with updated CSRF token for subsequent requests.
	 *
	 * @param int $id Task ID
	 */
	public function analyze_sentiment($id)
	{
		if (!$this->is_allowed('tasks_view', false)) {
			echo json_encode([
				'status'  => false,
				'message' => 'Akses tidak diizinkan.'
			]);
			return;
		}

		header('Content-Type: application/json');

		try {
			// 1. Verify the task exists and retrieve platform/URL context
			$task = $this->db
				->select('tasks.id, tasks.live_url, tasks.platform_id, tasks.status, platforms.code as platform_code')
				->join('platforms', 'platforms.id = tasks.platform_id', 'left')
				->where('tasks.id', (int) $id)
				->get('tasks')
				->row();

			if (!$task) {
				echo json_encode(['status' => false, 'message' => 'Task tidak ditemukan.']);
				return;
			}

			// 2. Resolve live post URL (postings table has priority over tasks.live_url)
			$posting = $this->db->get_where('postings', ['task_id' => (int) $id])->row();
			$live_url = !empty($posting->live_url) ? $posting->live_url : $task->live_url;

			if (empty($live_url)) {
				echo json_encode(['status' => false, 'message' => 'Task ini belum memiliki Live URL. Masukkan Live URL terlebih dahulu sebelum menganalisis sentimen.']);
				return;
			}

			// 3. Resolve platform code (DB code -> lowercase API enum, fallback to URL domain)
			$platform = $this->sentiment_client->resolve_platform($task->platform_code, $live_url);

			if (empty($platform)) {
				echo json_encode(['status' => false, 'message' => 'Platform task ini (' . htmlspecialchars($task->platform_code ?: 'tidak diketahui') . ') tidak didukung oleh microservice sentimen. Platform yang didukung: TikTok, Instagram, YouTube.']);
				return;
			}

			// 4. Cooldown check — prevent re-analysis within 5 minutes
			$cooldown_cfg = (int) ($this->config->item('sentiment_cooldown_minutes', 'sentiment') ?: 5);
			$cooldown = $this->model_task_sentiments->get_cooldown_status($id, $cooldown_cfg);

			if ($cooldown['active']) {
				$secs = $cooldown['seconds_remaining'];
				$mins_left = ceil($secs / 60);
				echo json_encode([
					'status'  => false,
					'message' => "Analisis baru saja dilakukan " . $cooldown['minutes_ago'] . " menit yang lalu. Harap tunggu {$mins_left} menit lagi sebelum menganalisis ulang."
				]);
				return;
			}

			// 5. Health check pre-flight — verify microservice is ready before burning a scraping call
			$health = $this->sentiment_client->check_health();

			if (!$health['healthy'] || !$health['model_loaded']) {
				$err = $health['error'] ?? 'Model IndoBERT belum siap.';
				echo json_encode(['status' => false, 'message' => 'Microservice sentimen tidak siap: ' . $err . ' Coba beberapa saat lagi.']);
				return;
			}

			// 6. Call the analysis microservice with user-specified comment limit
			$limit_cfg = (int) ($this->config->item('sentiment_comment_limit', 'sentiment') ?: 500);
			$limit_post = (int) $this->input->post('limit');
			$limit = ($limit_post >= 50 && $limit_post <= 1000) ? $limit_post : $limit_cfg;

			$result = $this->sentiment_client->analyze($platform, $live_url, $limit);

			if (!$result['success']) {
				echo json_encode(['status' => false, 'message' => 'Analisis gagal: ' . $result['error']]);
				return;
			}

			$api_data = $result['data'];

			// 7. Persist results to database
			$sentiment_summary  = $api_data['sentiment_summary']  ?? [];
			$highlight_comments = $api_data['highlight_comments'] ?? ['positive' => [], 'neutral' => [], 'negative' => []];
			$frequent_keywords  = $api_data['frequent_keywords']  ?? [];

			$this->model_task_sentiments->save_or_update($id, [
				'platform'           => $platform,
				'post_url'           => $live_url,
				'total_comments'     => (int) ($api_data['total_comments_scraped'] ?? 0),
				'positive_count'     => (int) ($sentiment_summary['positive'] ?? 0),
				'neutral_count'      => (int) ($sentiment_summary['neutral']  ?? 0),
				'negative_count'     => (int) ($sentiment_summary['negative'] ?? 0),
				'highlight_comments' => json_encode($highlight_comments, JSON_UNESCAPED_UNICODE),
				'frequent_keywords'  => json_encode(is_array($frequent_keywords) ? $frequent_keywords : [], JSON_UNESCAPED_UNICODE),
				'main_topics'        => $api_data['main_topics_discussed'] ?? null,
				'analyzed_by'        => $this->aauth->get_user_id(),
			]);

			// 8. Return success with refreshed CSRF token and full sentiment data
			$fresh_sentiment = $this->model_task_sentiments->get_by_task_id($id);

			echo json_encode([
				'status'          => true,
				'message'         => 'Analisis sentimen berhasil! ' . ($api_data['total_comments_scraped'] ?? 0) . ' komentar dianalisis.',
				'data'            => $fresh_sentiment,
				$this->security->get_csrf_token_name() => $this->security->get_csrf_hash(),
			]);

		} catch (Throwable $e) {
			// Log the full exception details per AGENT.md §9.3
			log_message('error', '[analyze_sentiment] Exception for task_id=' . $id . ': ' . $e->getMessage() . ' | DB Error: ' . json_encode($this->db->error()));

			echo json_encode([
				'status'  => false,
				'message' => 'Terjadi kesalahan server saat memproses analisis sentimen. Silakan coba lagi atau hubungi administrator.'
			]);
		}
	}
	private function _check_contract_active($campaign_kol_id)
	{
		if (!$campaign_kol_id) return false;
		$this->db->where('campaign_kol_id', $campaign_kol_id);
		$this->db->where('status', 'ACTIVE');
		$active_contract = $this->db->get('campaign_kol_contracts')->row();
		return $active_contract ? true : false;
	}

	/**
	 * Satu-satunya definisi "produk siap": tidak ada shipment SAMA SEKALI,
	 * atau minimal 1 shipment DELIVERED untuk deal ini.
	 * Dipakai staff list, approve_and_post, dan (via can_upload_draft) portal KOL.
	 */
	private function _is_product_ready($campaign_kol_id)
	{
		if (empty($campaign_kol_id)) return true;
		$has = $this->db->where('campaign_kol_id', $campaign_kol_id)->count_all_results('kol_shipments');
		if ($has < 1) return true;
		$delivered = $this->db->where('campaign_kol_id', $campaign_kol_id)->where('status', 'DELIVERED')->count_all_results('kol_shipments');
		return $delivered > 0;
	}

	/**
	 * KOL's Personal Task List ("Tugas Saya")
	 * Dedicated dashboard for logged-in KOL users
	 */
	public function my_tasks($offset = 0)
	{
		if (!$this->aauth->is_loggedin()) {
			redirect('administrator/login', 'refresh');
		}

		$kol_id = get_current_user_kol_id();

		// If user has no linked KOL and is not admin, show unlinked notice
		if (!$kol_id && !$this->aauth->is_admin()) {
			$this->data['unlinked_kol'] = true;
			$this->template->title('Tugas Saya');
			$this->render('backend/standart/administrator/tasks/kol_my_tasks', $this->data);
			return;
		}

		$tab = $this->input->get('tab') ?: 'all';
		$q   = trim((string)$this->input->get('q'));

		$this->db->select('tasks.*, 
			campaign_kols.deal_scope as master_sow, 
			campaign_kols.status as ck_status,
			campaign_kols.deal_amount,
			campaigns.name as campaign_title, 
			campaigns.status as campaign_status, 
			variants.name as variants_name, 
			platforms.name as platforms_name, 
			platforms.code as platform_code, 
			platforms.icon_url as platform_icon,
			briefs.file_path as brief_file, 
			COALESCE(briefs.content, tasks.brief_note) as brief_note, 
			postings.live_url as live_url, 
			postings.status as posting_status')
			->from('tasks')
			->join('campaign_kols', 'campaign_kols.id = tasks.campaign_kol_id', 'INNER')
			->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'LEFT')
			->join('variants', 'variants.id = tasks.variant_id', 'LEFT')
			->join('platforms', 'platforms.id = tasks.platform_id', 'LEFT')
			->join('briefs', 'briefs.task_id = tasks.id', 'LEFT')
			->join('postings', 'postings.task_id = tasks.id', 'LEFT');

		if ($kol_id) {
			$this->db->where('campaign_kols.kol_id', $kol_id);
		}

		if (!empty($q)) {
			$this->db->group_start();
			$this->db->like('tasks.title', $q);
			$this->db->or_like('campaigns.name', $q);
			$this->db->group_end();
		}

		if ($tab === 'needs_draft') {
			$this->db->where_in('tasks.status', ['WAITING_DRAFT', 'REVISION_DRAFT']);
		} elseif ($tab === 'in_review') {
			$this->db->where_in('tasks.status', ['IN_REVIEW_DRAFT', 'PENDING_REVIEW']);
		} elseif ($tab === 'ready_to_post') {
			$this->db->where('tasks.status', 'APPROVED_DRAFT');
		} elseif ($tab === 'live_done') {
			$this->db->where_in('tasks.status', ['POSTED', 'DONE']);
		}

		$this->db->order_by('tasks.estimated_posting_date', 'ASC');
		$tasks = $this->db->get()->result();

		// Attach draft history to each task
		foreach ($tasks as &$task) {
			$task->drafts = $this->db
				->where('task_id', $task->id)
				->order_by('version', 'DESC')
				->get('drafts')->result();
			$task->latest_draft = !empty($task->drafts) ? $task->drafts[0] : null;

			// Check contract status
			$task->is_contract_active = ($task->ck_status === 'ACTIVE' || $this->_check_contract_active($task->campaign_kol_id));

			// Shipment receipt info (KOL POV): latest shipment + whether any shipment exists
			$task->shipments = $this->db
				->where('campaign_kol_id', $task->campaign_kol_id)
				->order_by('id', 'DESC')
				->get('kol_shipments')->result();
			$task->latest_shipment = !empty($task->shipments) ? $task->shipments[0] : null;
			$task->needs_product = !empty($task->shipments);
			$task->product_received = $task->latest_shipment
				&& $task->latest_shipment->status === 'DELIVERED'
				&& !empty($task->latest_shipment->kol_received_at);
			// Boleh kerjakan draft bila: tidak ada pengiriman SAMA SEKALI, atau produk sudah diterima KOL
			$task->can_upload_draft = !$task->needs_product || $task->product_received;
		}

		// Count summary badges
		$count_summary = [
			'all'          => 0,
			'needs_draft'  => 0,
			'in_review'    => 0,
			'ready_to_post'=> 0,
			'live_done'    => 0
		];

		if ($kol_id) {
			$counts_query = $this->db->select('tasks.status, COUNT(*) as cnt')
				->from('tasks')
				->join('campaign_kols', 'campaign_kols.id = tasks.campaign_kol_id', 'INNER')
				->where('campaign_kols.kol_id', $kol_id)
				->group_by('tasks.status')
				->get()->result();

			foreach ($counts_query as $c) {
				$count_summary['all'] += (int)$c->cnt;
				if (in_array($c->status, ['WAITING_DRAFT', 'REVISION_DRAFT'])) {
					$count_summary['needs_draft'] += (int)$c->cnt;
				} elseif (in_array($c->status, ['IN_REVIEW_DRAFT', 'PENDING_REVIEW'])) {
					$count_summary['in_review'] += (int)$c->cnt;
				} elseif ($c->status === 'APPROVED_DRAFT') {
					$count_summary['ready_to_post'] += (int)$c->cnt;
				} elseif (in_array($c->status, ['POSTED', 'DONE'])) {
					$count_summary['live_done'] += (int)$c->cnt;
				}
			}
		}

		$this->data['tasks']         = $tasks;
		$this->data['tab']           = $tab;
		$this->data['q']             = $q;
		$this->data['count_summary'] = $count_summary;
		$this->data['kol_id']        = $kol_id;
		$this->data['unlinked_kol']  = false;

		$this->template->title('Tugas Saya');
		$this->render('backend/standart/administrator/tasks/kol_my_tasks', $this->data);
	}

	/**
	 * Action: KOL Confirms Product Receipt ("Produk Diterima")
	 * Allowed when latest shipment is SHIPPED or DELIVERED.
	 * Confirming flips a SHIPPED shipment to DELIVERED (KOL POV receipt).
	 */
	public function kol_confirm_receipt()
	{
		if (!$this->aauth->is_loggedin()) {
			echo json_encode(['success' => false, 'message' => 'Sesi login telah berakhir.']);
			return;
		}

		$kol_id = get_current_user_kol_id();
		$shipment_id = (int)$this->input->post('shipment_id');

		if ($shipment_id <= 0) {
			echo json_encode(['success' => false, 'message' => 'Data pengiriman tidak valid.', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		$shipment = $this->db->select('kol_shipments.*, campaign_kols.kol_id')
			->from('kol_shipments')
			->join('campaign_kols', 'campaign_kols.id = kol_shipments.campaign_kol_id', 'INNER')
			->where('kol_shipments.id', $shipment_id)
			->get()->row();

		if (!$shipment) {
			echo json_encode(['success' => false, 'message' => 'Data pengiriman tidak ditemukan.', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		if (!$this->aauth->is_admin() && (int)$shipment->kol_id !== (int)$kol_id) {
			echo json_encode(['success' => false, 'message' => 'Akses ditolak: pengiriman ini bukan milik akun Anda.', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		if (!empty($shipment->kol_received_at)) {
			echo json_encode(['success' => false, 'message' => 'Produk sudah dikonfirmasi diterima sebelumnya.', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		if (!in_array($shipment->status, ['SHIPPED', 'DELIVERED'])) {
			echo json_encode(['success' => false, 'message' => 'Produk belum dikirim. Konfirmasi hanya bisa dilakukan setelah produk dikirim (SHIPPED).', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		$user_id = get_user_data('id') ?: 1;
		$now = date('Y-m-d H:i:s');
		$update = [
			'kol_received_at' => $now,
			'kol_received_by' => $user_id,
			'updated_at' => $now
		];
		if ($shipment->status !== 'DELIVERED') {
			$update['status'] = 'DELIVERED';
			$update['delivered_at'] = $now;
		}
		$this->db->where('id', $shipment_id)->update('kol_shipments', $update);

		echo json_encode([
			'success' => true,
			'message' => 'Penerimaan produk dikonfirmasi! Anda sekarang dapat mengerjakan draft.',
			'csrf_hash' => $this->security->get_csrf_hash()
		]);
	}

	/**
	 * Action: KOL Submits Draft Video (From Logged-in "Tugas Saya")
	 */
	public function kol_submit_draft($task_id)
	{
		if (!$this->aauth->is_loggedin()) {
			echo json_encode(['success' => false, 'message' => 'Sesi login telah berakhir.']);
			return;
		}

		$kol_id = get_current_user_kol_id();
		$task_id = (int)$task_id;
		$draft_url = trim($this->input->post('draft_url') ?? '');
		$caption = trim($this->input->post('caption') ?? '');
		$hashtag = trim($this->input->post('hashtag') ?? '');
		$notes = trim($this->input->post('notes') ?? '');

		if (empty($draft_url)) {
			echo json_encode(['success' => false, 'message' => 'Link draft video wajib diisi (Google Drive / Video Link).', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		// Security: Validate that this task strictly belongs to the logged-in KOL
		$task = $this->db->select('tasks.*, campaign_kols.kol_id')
			->from('tasks')
			->join('campaign_kols', 'campaign_kols.id = tasks.campaign_kol_id', 'INNER')
			->where('tasks.id', $task_id)
			->get()->row();

		if (!$task) {
			echo json_encode(['success' => false, 'message' => 'Task tidak ditemukan.', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		if (!$this->aauth->is_admin() && (int)$task->kol_id !== (int)$kol_id) {
			echo json_encode(['success' => false, 'message' => 'Akses ditolak: Task ini bukan milik akun Anda.', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		if (!in_array($task->status, ['WAITING_DRAFT', 'REVISION_DRAFT'])) {
			echo json_encode(['success' => false, 'message' => 'Draft hanya dapat diupload pada tahap pengumpulan draft.', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		// Gate: bila dealing ini ada pengiriman produk, KOL wajib konfirmasi terima dulu
		$shipment = $this->db->where('campaign_kol_id', $task->campaign_kol_id)->order_by('id', 'DESC')->get('kol_shipments')->row();
		if ($shipment && (empty($shipment->kol_received_at) || $shipment->status !== 'DELIVERED')) {
			echo json_encode(['success' => false, 'message' => 'Produk belum diterima. Konfirmasi penerimaan produk terlebih dahulu sebelum upload draft.', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		$user_id = get_user_data('id') ?: 1;

		// Determine next draft version
		$max_ver = $this->db->select_max('version')->where('task_id', $task_id)->get('drafts')->row()->version;
		$next_version = ($max_ver ? (int)$max_ver : 0) + 1;

		// Insert into drafts
		$this->db->insert('drafts', [
			'task_id'    => $task_id,
			'version'    => $next_version,
			'draft_url'  => $draft_url,
			'caption'    => $caption ?: null,
			'hashtag'    => $hashtag ?: null,
			'notes'      => $notes ?: null,
			'status'     => 'SUBMITTED',
			'created_by' => $user_id,
			'created_at' => date('Y-m-d H:i:s'),
			'updated_at' => date('Y-m-d H:i:s')
		]);
		$draft_id = $this->db->insert_id();

		// Record in draft_review_histories
		$this->db->insert('draft_review_histories', [
			'draft_id'   => $draft_id,
			'action'     => 'SUBMITTED',
			'note'       => 'Draft v' . $next_version . ' diajukan oleh KOL: ' . ($notes ?: 'Tanpa catatan tambahan'),
			'acted_by'   => $user_id,
			'created_at' => date('Y-m-d H:i:s')
		]);

		// Update task status to IN_REVIEW_DRAFT
		$this->db->where('id', $task_id)->update('tasks', [
			'status'     => 'IN_REVIEW_DRAFT',
			'draft_url'  => $draft_url,
			'updated_at' => date('Y-m-d H:i:s')
		]);

		// Log status history
		$this->db->insert('task_status_histories', [
			'task_id'    => $task_id,
			'status'     => 'IN_REVIEW_DRAFT',
			'changed_by' => $user_id,
			'note'       => 'Draft v' . $next_version . ' diajukan oleh KOL via panel Tugas Saya',
			'created_at' => date('Y-m-d H:i:s')
		]);

		echo json_encode([
			'success'   => true,
			'message'   => "Draft versi {$next_version} berhasil dikirimkan! Tim brand akan segera meninjau.",
			'csrf_hash' => $this->security->get_csrf_hash()
		]);
	}

	/**
	 * Action: KOL Submits Live Post URL (From Logged-in "Tugas Saya")
	 */
	public function kol_submit_live_url($task_id)
	{
		if (!$this->aauth->is_loggedin()) {
			echo json_encode(['success' => false, 'message' => 'Sesi login telah berakhir.']);
			return;
		}

		$kol_id = get_current_user_kol_id();
		$task_id = (int)$task_id;
		$live_url = trim($this->input->post('live_url') ?? '');

		if (empty($live_url)) {
			echo json_encode(['success' => false, 'message' => 'Link postingan live wajib diisi.', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		// Security: Validate that this task strictly belongs to the logged-in KOL
		$task = $this->db->select('tasks.*, campaign_kols.kol_id, campaigns.status as campaign_status, platforms.code as platform_code, platforms.name as platform_name')
			->from('tasks')
			->join('campaign_kols', 'campaign_kols.id = tasks.campaign_kol_id', 'INNER')
			->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'LEFT')
			->join('platforms', 'platforms.id = tasks.platform_id', 'LEFT')
			->where('tasks.id', $task_id)
			->get()->row();

		if (!$task) {
			echo json_encode(['success' => false, 'message' => 'Task tidak ditemukan.', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		if (!in_array($task->campaign_status, ['ACTIVE', 'COMPLETED'])) {
			echo json_encode(['success' => false, 'message' => 'Campaign belum ACTIVE. Live link hanya dapat disubmit setelah campaign ACTIVE.', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		if (!in_array($task->status, ['APPROVED_DRAFT', 'POSTED', 'DONE'])) {
			echo json_encode(['success' => false, 'message' => 'Live link hanya dapat disubmit setelah draft disetujui (APPROVED_DRAFT).', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		if (!$this->aauth->is_admin() && (int)$task->kol_id !== (int)$kol_id) {
			echo json_encode(['success' => false, 'message' => 'Akses ditolak: Task ini bukan milik akun Anda.', 'csrf_hash' => $this->security->get_csrf_hash()]);
			return;
		}

		$user_id = get_user_data('id') ?: 1;

		// Insert or update postings table
		$existing_posting = $this->db->where('task_id', $task_id)->get('postings')->row();
		if ($existing_posting) {
			$this->db->where('id', $existing_posting->id)->update('postings', [
				'live_url'         => $live_url,
				'actual_posted_at' => date('Y-m-d H:i:s'),
				'posted_at'        => date('Y-m-d H:i:s'),
				'posted_at_source' => 'MANUAL',
				'status'           => 'ACTIVE',
				'updated_at'       => date('Y-m-d H:i:s')
			]);
		} else {
			$this->db->insert('postings', [
				'task_id'          => $task_id,
				'platform_id'      => $task->platform_id,
				'live_url'         => $live_url,
				'actual_posted_at' => date('Y-m-d H:i:s'),
				'posted_at'        => date('Y-m-d H:i:s'),
				'posted_at_source' => 'MANUAL',
				'status'           => 'ACTIVE',
				'created_at'       => date('Y-m-d H:i:s'),
				'updated_at'       => date('Y-m-d H:i:s')
			]);
		}

		// Update task status to POSTED
		$this->db->where('id', $task_id)->update('tasks', [
			'status'     => 'POSTED',
			'live_url'   => $live_url,
			'updated_at' => date('Y-m-d H:i:s')
		]);

		$this->db->insert('task_status_histories', [
			'task_id'    => $task_id,
			'status'     => 'POSTED',
			'changed_by' => $user_id,
			'note'       => 'KOL mengonfirmasi live posting URL: ' . $live_url,
			'created_at' => date('Y-m-d H:i:s')
		]);

		echo json_encode([
			'success'   => true,
			'message'   => 'Link postingan live berhasil disimpan! Metrik performa akan dipantau.',
			'csrf_hash' => $this->security->get_csrf_hash()
		]);
	}
}

