<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Briefs Controller
*| --------------------------------------------------------------------------
*| Briefs site
*|
*/
class Briefs extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_briefs');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Briefss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('briefs_list');

		if (!$this->input->get('raw')) {
			redirect('administrator/tasks?view=sow');
			return;
		}

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['briefss'] = $this->model_briefs->get($filter, $field, $this->limit_page, $offset);
		$this->data['briefs_counts'] = $this->model_briefs->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/briefs/index/',
			'total_rows'   => $this->model_briefs->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Briefs List');
		$this->render('backend/standart/administrator/briefs/briefs_list', $this->data);
	}
	
	/**
	* Add new briefss
	*
	*/
	public function add()
	{
		$this->is_allowed('briefs_add');

		$this->template->title('Briefs New');
		$this->render('backend/standart/administrator/briefs/briefs_add', $this->data);
	}

	/**
	* Add New Briefss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('briefs_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('task_id', 'Task', 'trim|required');
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		$this->form_validation->set_rules('content', 'Content', 'trim|required');
		$this->form_validation->set_rules('status', 'Status', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'task_id' => $this->input->post('task_id'),
				'title' => $this->input->post('title'),
				'content' => $this->input->post('content'),
				'status' => $this->input->post('status'),
			];

			
			$save_briefs = $this->model_briefs->store($save_data);
            

			if ($save_briefs) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_briefs;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/briefs/edit/' . $save_briefs, 'Edit Briefs'),
						anchor('administrator/briefs', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/briefs/edit/' . $save_briefs, 'Edit Briefs')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/briefs');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/briefs');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Briefss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('briefs_update');

		$this->data['briefs'] = $this->model_briefs->find($id);

		$this->template->title('Briefs Update');
		$this->render('backend/standart/administrator/briefs/briefs_update', $this->data);
	}

	/**
	* Update Briefss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('briefs_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('task_id', 'Task', 'trim|required');
		$this->form_validation->set_rules('title', 'Title', 'trim|required');
		$this->form_validation->set_rules('content', 'Content', 'trim|required');
		$this->form_validation->set_rules('status', 'Status', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'task_id' => $this->input->post('task_id'),
				'title' => $this->input->post('title'),
				'content' => $this->input->post('content'),
				'status' => $this->input->post('status'),
			];

			
			$save_briefs = $this->model_briefs->change($id, $save_data);

			if ($save_briefs) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/briefs', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/briefs');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/briefs');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Briefss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('briefs_delete');

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
            set_message(cclang('has_been_deleted', 'briefs'), 'success');
        } else {
            set_message(cclang('error_delete', 'briefs'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Briefss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('briefs_view');

		$this->data['briefs'] = $this->model_briefs->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Briefs Detail');
		$this->render('backend/standart/administrator/briefs/briefs_view', $this->data);
	}
	
	/**
	* delete Briefss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$briefs = $this->model_briefs->find($id);

		
		
		return $this->model_briefs->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('briefs_export');

		$this->model_briefs->export('briefs', 'briefs');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('briefs_export');

		$this->model_briefs->pdf('briefs', 'briefs');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('briefs_export');

		$table = $title = 'briefs';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_briefs->find($id);
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
	* Submit brief for Leader review (Staff action)
	*/
	public function submit_review($id)
	{
		$this->is_allowed('briefs_update');

		$this->db->where('id', $id)->update('briefs', [
			'status' => 'PENDING_REVIEW',
			'submitted_at' => date('Y-m-d H:i:s')
		]);

		set_message('Brief submitted for Leader review.', 'success');
		redirect('administrator/briefs');
	}

	/**
	* Approve brief (Leader action) -> also sets task to APPROVED
	*/
	public function approve($id)
	{
		$this->is_allowed('briefs_update');
		$user_id = get_user_data('id');

		$brief = $this->db->get_where('briefs', ['id' => $id])->row();
		if (!$brief) {
			show_404();
		}

		$this->db->where('id', $id)->update('briefs', [
			'status' => 'APPROVED'
		]);

		$this->db->insert('brief_approval_histories', [
			'brief_id' => $id,
			'status' => 'APPROVED',
			'note' => 'Brief approved by Leader',
			'acted_by' => $user_id,
			'created_at' => date('Y-m-d H:i:s')
		]);

		// Update corresponding task to WAITING_DRAFT
		if ($brief->task_id) {
			$this->db->where('id', $brief->task_id)->update('tasks', [
				'status'     => 'WAITING_DRAFT',
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$this->db->insert('task_status_histories', [
				'task_id'    => $brief->task_id,
				'status'     => 'WAITING_DRAFT',
				'changed_by' => $user_id,
				'note'       => 'Brief disetujui Leader. Task maju ke tahap WAITING_DRAFT.',
				'created_at' => date('Y-m-d H:i:s')
			]);
		}

		set_message('Brief dan Task disetujui. Siap menuju tahap draft.', 'success');
		redirect('administrator/tasks?view=sow');
	}

	/**
	* Request brief revision (Leader action) -> snapshots current brief content
	*/
	public function revision($id)
	{
		$this->is_allowed('briefs_update');
		$user_id = get_user_data('id');
		$note = $this->input->post('note') ? $this->input->post('note') : ($this->input->get('note') ? $this->input->get('note') : 'Revision needed');

		$brief = $this->db->get_where('briefs', ['id' => $id])->row();
		if (!$brief) {
			show_404();
		}

		// Snapshot current content before revision
		$last_version_row = $this->db->select_max('version')->get_where('brief_content_histories', ['brief_id' => $id])->row();
		$next_version = ($last_version_row && $last_version_row->version) ? ((int)$last_version_row->version + 1) : 1;

		$this->db->insert('brief_content_histories', [
			'brief_id'    => $id,
			'version'     => $next_version,
			'title'       => $brief->title,
			'content'     => $brief->content,
			'snapshot_at' => date('Y-m-d H:i:s'),
			'created_by'  => $user_id
		]);

		// Update brief status and increment revision_count
		$this->db->where('id', $id)->update('briefs', [
			'status'         => 'REVISION',
			'revision_count' => $brief->revision_count + 1,
			'updated_at'     => date('Y-m-d H:i:s')
		]);

		// Update corresponding task to REVISION
		if ($brief->task_id) {
			$this->db->where('id', $brief->task_id)->update('tasks', [
				'status'     => 'REVISION',
				'brief_note' => $note,
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$this->db->insert('task_status_histories', [
				'task_id'    => $brief->task_id,
				'status'     => 'REVISION',
				'changed_by' => $user_id,
				'note'       => 'Brief dikembalikan untuk revisi: ' . $note,
				'created_at' => date('Y-m-d H:i:s')
			]);
		}

		// Log to brief_approval_histories
		$this->db->insert('brief_approval_histories', [
			'brief_id'   => $id,
			'status'     => 'REVISION',
			'note'       => $note,
			'acted_by'   => $user_id,
			'created_at' => date('Y-m-d H:i:s')
		]);

		set_message('Brief dikembalikan untuk revisi.', 'warning');
		redirect('administrator/tasks?view=sow');
	}
}


/* End of file briefs.php */
/* Location: ./application/controllers/administrator/Briefs.php */