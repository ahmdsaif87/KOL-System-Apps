<?php
defined('BASEPATH') OR exit('No direct script access allowed');


/**
*| --------------------------------------------------------------------------
*| Drafts Controller
*| --------------------------------------------------------------------------
*| Drafts site
*|
*/
class Drafts extends Admin	
{
	
	public function __construct()
	{
		parent::__construct();

		$this->load->model('model_drafts');
		$this->lang->load('web_lang', $this->current_lang);
	}

	/**
	* show all Draftss
	*
	* @var $offset String
	*/
	public function index($offset = 0)
	{
		$this->is_allowed('drafts_list');

		if (!$this->input->get('raw')) {
			redirect('administrator/tasks?view=review_draft');
			return;
		}

		$filter = $this->input->get('q');
		$field 	= $this->input->get('f');

		$this->data['draftss'] = $this->model_drafts->get($filter, $field, $this->limit_page, $offset);
		$this->data['drafts_counts'] = $this->model_drafts->count_all($filter, $field);

		$config = [
			'base_url'     => 'administrator/drafts/index/',
			'total_rows'   => $this->model_drafts->count_all($filter, $field),
			'per_page'     => $this->limit_page,
			'uri_segment'  => 4,
		];

		$this->data['pagination'] = $this->pagination($config);

		$this->template->title('Drafts List');
		$this->render('backend/standart/administrator/drafts/drafts_list', $this->data);
	}
	
	/**
	* Add new draftss
	*
	*/
	public function add()
	{
		$this->is_allowed('drafts_add');

		$this->template->title('Drafts New');
		$this->render('backend/standart/administrator/drafts/drafts_add', $this->data);
	}

	/**
	* Add New Draftss
	*
	* @return JSON
	*/
	public function add_save()
	{
		if (!$this->is_allowed('drafts_add', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}

		$this->form_validation->set_rules('task_id', 'Task Id', 'trim|required');
		$this->form_validation->set_rules('version', 'Version', 'trim|required');
		$this->form_validation->set_rules('draft_url', 'Draft URL', 'trim|max_length[1000]|required');
		$this->form_validation->set_rules('caption', 'Caption', 'trim|required');
		$this->form_validation->set_rules('hashtag', 'Hashtag', 'trim|required');
		$this->form_validation->set_rules('notes', 'Notes', 'trim|required');
		$this->form_validation->set_rules('status', 'Status', 'trim|required');
		

		if ($this->form_validation->run()) {
		
			$save_data = [
				'task_id' => $this->input->post('task_id'),
				'version' => $this->input->post('version'),
				'draft_url' => $this->input->post('draft_url'),
				'caption' => $this->input->post('caption'),
				'hashtag' => $this->input->post('hashtag'),
				'notes' => $this->input->post('notes'),
				'status' => $this->input->post('status'),
			];

			
			$save_drafts = $this->model_drafts->store($save_data);
            

			if ($save_drafts) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $save_drafts;
					$this->data['message'] = cclang('success_save_data_stay', [
						anchor('administrator/drafts/edit/' . $save_drafts, 'Edit Drafts'),
						anchor('administrator/drafts', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_save_data_redirect', [
						anchor('administrator/drafts/edit/' . $save_drafts, 'Edit Drafts')
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/drafts');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/drafts');
				}
			}

		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
		/**
	* Update view Draftss
	*
	* @var $id String
	*/
	public function edit($id)
	{
		$this->is_allowed('drafts_update');

		$this->data['drafts'] = $this->model_drafts->find($id);

		$this->template->title('Drafts Update');
		$this->render('backend/standart/administrator/drafts/drafts_update', $this->data);
	}

	/**
	* Update Draftss
	*
	* @var $id String
	*/
	public function edit_save($id)
	{
		if (!$this->is_allowed('drafts_update', false)) {
			echo json_encode([
				'success' => false,
				'message' => cclang('sorry_you_do_not_have_permission_to_access')
				]);
			exit;
		}
		
		$this->form_validation->set_rules('task_id', 'Task Id', 'trim|required');
		$this->form_validation->set_rules('version', 'Version', 'trim|required');
		$this->form_validation->set_rules('draft_url', 'Draft URL', 'trim|max_length[1000]|required');
		$this->form_validation->set_rules('caption', 'Caption', 'trim|required');
		$this->form_validation->set_rules('hashtag', 'Hashtag', 'trim|required');
		$this->form_validation->set_rules('notes', 'Notes', 'trim|required');
		$this->form_validation->set_rules('status', 'Status', 'trim|required');
		
		if ($this->form_validation->run()) {
		
			$save_data = [
				'task_id' => $this->input->post('task_id'),
				'version' => $this->input->post('version'),
				'draft_url' => $this->input->post('draft_url'),
				'caption' => $this->input->post('caption'),
				'hashtag' => $this->input->post('hashtag'),
				'notes' => $this->input->post('notes'),
				'status' => $this->input->post('status'),
			];

			
			$save_drafts = $this->model_drafts->change($id, $save_data);

			if ($save_drafts) {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = true;
					$this->data['id'] 	   = $id;
					$this->data['message'] = cclang('success_update_data_stay', [
						anchor('administrator/drafts', ' Go back to list')
					]);
				} else {
					set_message(
						cclang('success_update_data_redirect', [
					]), 'success');

            		$this->data['success'] = true;
					$this->data['redirect'] = base_url('administrator/drafts');
				}
			} else {
				if ($this->input->post('save_type') == 'stay') {
					$this->data['success'] = false;
					$this->data['message'] = cclang('data_not_change');
				} else {
            		$this->data['success'] = false;
            		$this->data['message'] = cclang('data_not_change');
					$this->data['redirect'] = base_url('administrator/drafts');
				}
			}
		} else {
			$this->data['success'] = false;
			$this->data['message'] = validation_errors();
		}

		echo json_encode($this->data);
	}
	
	/**
	* delete Draftss
	*
	* @var $id String
	*/
	public function delete($id = null)
	{
		$this->is_allowed('drafts_delete');

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
            set_message(cclang('has_been_deleted', 'drafts'), 'success');
        } else {
            set_message(cclang('error_delete', 'drafts'), 'error');
        }

		redirect_back();
	}

		/**
	* View view Draftss
	*
	* @var $id String
	*/
	public function view($id)
	{
		$this->is_allowed('drafts_view');

		$this->data['drafts'] = $this->model_drafts->join_avaiable()->filter_avaiable()->find($id);

		$this->template->title('Drafts Detail');
		$this->render('backend/standart/administrator/drafts/drafts_view', $this->data);
	}
	
	/**
	* delete Draftss
	*
	* @var $id String
	*/
	private function _remove($id)
	{
		$drafts = $this->model_drafts->find($id);

		
		
		return $this->model_drafts->remove($id);
	}
	
	
	/**
	* Export to excel
	*
	* @return Files Excel .xls
	*/
	public function export()
	{
		$this->is_allowed('drafts_export');

		$this->model_drafts->export('drafts', 'drafts');
	}

	/**
	* Export to PDF
	*
	* @return Files PDF .pdf
	*/
	public function export_pdf()
	{
		$this->is_allowed('drafts_export');

		$this->model_drafts->pdf('drafts', 'drafts');
	}


	public function single_pdf($id = null)
	{
		$this->is_allowed('drafts_export');

		$table = $title = 'drafts';
		$this->load->library('HtmlPdf');
      
        $config = array(
            'orientation' => 'p',
            'format' => 'a4',
            'marges' => array(5, 5, 5, 5)
        );

        $this->pdf = new HtmlPdf($config);
        $this->pdf->setDefaultFont('stsongstdlight'); 

        $result = $this->db->get($table);
       
        $data = $this->model_drafts->find($id);
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
	* Submit draft for Leader review (Staff action)
	*/
	public function submit($id)
	{
		$this->is_allowed('drafts_update');
		$user_id = get_user_data('id') ?: 1;

		$draft = $this->model_drafts->find($id);
		if (!$draft) {
			show_404();
		}

		$this->db->where('id', $id)->update('drafts', [
			'status'     => 'SUBMITTED',
			'updated_at' => date('Y-m-d H:i:s')
		]);

		$this->db->insert('draft_review_histories', [
			'draft_id'   => $id,
			'action'     => 'SUBMITTED',
			'note'       => 'Draft v' . $draft->version . ' disubmit untuk review',
			'acted_by'   => $user_id,
			'created_at' => date('Y-m-d H:i:s')
		]);

		if ($draft->task_id) {
			$this->db->where('id', $draft->task_id)->update('tasks', [
				'status'     => 'IN_REVIEW_DRAFT',
				'draft_url'  => $draft->draft_url,
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$this->db->insert('task_status_histories', [
				'task_id'    => $draft->task_id,
				'status'     => 'IN_REVIEW_DRAFT',
				'changed_by' => $user_id,
				'note'       => 'Draft v' . $draft->version . ' disubmit untuk review.',
				'created_at' => date('Y-m-d H:i:s')
			]);
		}

		set_message('Draft berhasil dikirim untuk review.', 'success');
		redirect('administrator/tasks?view=review_draft');
	}

	/**
	* Approve draft (Leader action)
	*/
	public function approve($id)
	{
		$this->is_allowed('drafts_update');
		$user_id = get_user_data('id') ?: 1;

		$draft = $this->model_drafts->find($id);
		if (!$draft) {
			show_404();
		}

		$this->db->where('id', $id)->update('drafts', [
			'status'     => 'APPROVED',
			'updated_at' => date('Y-m-d H:i:s')
		]);

		$this->db->insert('draft_review_histories', [
			'draft_id'   => $id,
			'action'     => 'APPROVED',
			'note'       => 'Draft disetujui Leader',
			'acted_by'   => $user_id,
			'created_at' => date('Y-m-d H:i:s')
		]);

		if ($draft->task_id) {
			$this->db->where('id', $draft->task_id)->update('tasks', [
				'status'     => 'APPROVED_DRAFT',
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$this->db->insert('task_status_histories', [
				'task_id'    => $draft->task_id,
				'status'     => 'APPROVED_DRAFT',
				'changed_by' => $user_id,
				'note'       => 'Draft disetujui Leader. Siap untuk Live Posting.',
				'created_at' => date('Y-m-d H:i:s')
			]);
		}

		set_message('Draft disetujui.', 'success');
		redirect('administrator/tasks?view=review_draft');
	}

	/**
	* Request draft revision (Leader action)
	*/
	public function revision($id)
	{
		$this->is_allowed('drafts_update');
		$user_id = get_user_data('id') ?: 1;
		$note = $this->input->post('note') ? $this->input->post('note') : ($this->input->get('note') ? $this->input->get('note') : 'Revision needed');

		$draft = $this->model_drafts->find($id);
		if (!$draft) {
			show_404();
		}

		$this->db->where('id', $id)->update('drafts', [
			'status'     => 'REVISION',
			'notes'      => $note,
			'updated_at' => date('Y-m-d H:i:s')
		]);

		$this->db->insert('draft_review_histories', [
			'draft_id'   => $id,
			'action'     => 'REVISION',
			'note'       => $note,
			'acted_by'   => $user_id,
			'created_at' => date('Y-m-d H:i:s')
		]);

		if ($draft->task_id) {
			$this->db->where('id', $draft->task_id)->update('tasks', [
				'status'     => 'REVISION_DRAFT',
				'draft_note' => $note,
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$this->db->insert('task_status_histories', [
				'task_id'    => $draft->task_id,
				'status'     => 'REVISION_DRAFT',
				'changed_by' => $user_id,
				'note'       => 'Draft dikembalikan untuk revisi: ' . $note,
				'created_at' => date('Y-m-d H:i:s')
			]);
		}

		set_message('Draft dikembalikan untuk revisi.', 'warning');
		redirect('administrator/tasks?view=review_draft');
	}
}


/* End of file drafts.php */
/* Location: ./application/controllers/administrator/Drafts.php */