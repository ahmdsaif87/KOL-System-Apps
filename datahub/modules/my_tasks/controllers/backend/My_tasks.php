<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class My_tasks extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper('url');
		$this->lang->load('web_lang', $this->current_lang);
	}

	private function _check_contract_active($campaign_kol_id)
	{
		if (!$campaign_kol_id) return true;
		$this->db->where('campaign_kol_id', $campaign_kol_id);
		$this->db->where('status', 'ACTIVE');
		$active_contract = $this->db->get('campaign_kol_contracts')->row();
		return $active_contract ? true : false;
	}

	/**
	 * KOL Personal Task List Dashboard
	 */
	public function index($offset = 0)
	{
		if (!$this->aauth->is_loggedin()) {
			redirect('administrator/login', 'refresh');
		}

		$kol_id = get_current_user_kol_id();

		// If user has no linked KOL and is not admin, show unlinked notice
		if (!$kol_id && !$this->aauth->is_admin()) {
			$this->data['unlinked_kol'] = true;
			$this->template->title('My Tasks');
			$this->render('backend/standart/administrator/my_tasks/my_tasks_list', $this->data);
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
			$task->shipments = [];
			$task->latest_shipment = null;
			$task->needs_product = false;
			$task->product_received = false;
			$task->can_upload_draft = true;
			if ($this->db->table_exists('kol_shipments')) {
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

		$this->template->title('My Tasks');
		$this->render('backend/standart/administrator/my_tasks/my_tasks_list', $this->data);
	}

	/**
	 * Action: KOL Submits Draft Video
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

		// Gate: bila dealing ini ada pengiriman produk, KOL wajib konfirmasi terima dulu
		if ($this->db->table_exists('kol_shipments')) {
			$shipment = $this->db->where('campaign_kol_id', $task->campaign_kol_id)->order_by('id', 'DESC')->get('kol_shipments')->row();
			if ($shipment && (empty($shipment->kol_received_at) || $shipment->status !== 'DELIVERED')) {
				echo json_encode(['success' => false, 'message' => 'Produk belum diterima. Konfirmasi penerimaan produk terlebih dahulu sebelum upload draft.', 'csrf_hash' => $this->security->get_csrf_hash()]);
				return;
			}
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
	 * Action: KOL Submits Live Post URL
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
		$task = $this->db->select('tasks.*, campaign_kols.kol_id, platforms.code as platform_code, platforms.name as platform_name')
			->from('tasks')
			->join('campaign_kols', 'campaign_kols.id = tasks.campaign_kol_id', 'INNER')
			->join('platforms', 'platforms.id = tasks.platform_id', 'LEFT')
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
