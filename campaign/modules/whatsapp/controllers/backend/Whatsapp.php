<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Whatsapp Controller
 * Dedicated WhatsApp Communication Console with WAHA Integration
 */
class Whatsapp extends Admin
{
	public function __construct()
	{
		parent::__construct();
		$this->lang->load('web_lang', $this->current_lang);
		$this->load->model('whatsapp/model_whatsapp');
		$this->load->library('waha_client');
	}

	/**
	 * Main console entry
	 * 
	 * @param int|null $kol_id
	 */
	public function index($kol_id = null)
	{
		$this->chat($kol_id);
	}

	/**
	 * Dedicated chat view for a specific KOL or default first KOL
	 * 
	 * @param int|null $kol_id
	 */
	public function chat($kol_id = null)
	{
		// Fetch all registered active KOLs with tier and latest platform info
		$kols = $this->db->select('
				kols.*, 
				tiers.name as tier_name,
				(SELECT COUNT(ck.id) FROM campaign_kols ck WHERE ck.kol_id = kols.id AND ck.status IN ("ASSIGNED","ACTIVE")) as active_campaign_count,
				(SELECT c.name FROM campaign_kols ck JOIN campaigns c ON c.id = ck.campaign_id WHERE ck.kol_id = kols.id ORDER BY ck.id DESC LIMIT 1) as latest_campaign_name,
				(SELECT kp.username FROM kol_platforms kp WHERE kp.kol_id = kols.id AND kp.is_active = 1 LIMIT 1) as primary_handle,
				(SELECT p.name FROM kol_platforms kp JOIN platforms p ON p.id = kp.platform_id WHERE kp.kol_id = kols.id AND kp.is_active = 1 LIMIT 1) as primary_platform,
				(SELECT wm.message_body FROM whatsapp_messages wm WHERE wm.kol_id = kols.id ORDER BY wm.id DESC LIMIT 1) as last_chat_snippet,
				(SELECT wm.sent_at FROM whatsapp_messages wm WHERE wm.kol_id = kols.id ORDER BY wm.id DESC LIMIT 1) as last_chat_time
			')
			->from('kols')
			->join('tiers', 'tiers.id = kols.tier_id', 'left')
			->where('kols.status', 'ACTIVE')
			->order_by('kols.id', 'ASC')
			->get()
			->result();

		// Determine active selected KOL
		$selected_kol = null;
		if (!empty($kol_id)) {
			foreach ($kols as $k) {
				if ($k->id == $kol_id) {
					$selected_kol = $k;
					break;
				}
			}

			// If kol_id was passed but not found among ACTIVE KOLs (e.g. pending or rejected), redirect
			if (!$selected_kol) {
				set_message('KOL belum disetujui (pending approval) atau berstatus non-aktif sehingga tidak dapat dihubungi via WhatsApp.', 'error');
				redirect('administrator/whatsapp');
			}
		}


		// Context details for the selected KOL (Active Campaign, Tasks, Shipping)
		$campaign_context = null;
		$tasks_context = [];
		$shipment_context = null;

		if ($selected_kol) {
			$ck_id_param = $this->input->get('campaign_kol_id');
			$task_id_param = $this->input->get('task_id');

			// Get campaign KOL record
			$this->db->select('campaign_kols.*, campaigns.name as campaign_name, campaigns.start_date, campaigns.end_date, campaigns.budget as campaign_budget')
				->from('campaign_kols')
				->join('campaigns', 'campaigns.id = campaign_kols.campaign_id', 'inner')
				->where('campaign_kols.kol_id', $selected_kol->id);

			if (!empty($ck_id_param)) {
				$this->db->where('campaign_kols.id', (int)$ck_id_param);
			}

			$campaign_context = $this->db->order_by('campaign_kols.id', 'DESC')->limit(1)->get()->row();

			if ($campaign_context) {
				// Get associated tasks (VTs)
				$tasks_context = $this->db->select('tasks.*, platforms.name as platform_name, variants.name as variant_name, briefs.content as brief_text, briefs.file_path as brief_file')
					->from('tasks')
					->join('platforms', 'platforms.id = tasks.platform_id', 'left')
					->join('variants', 'variants.id = tasks.variant_id', 'left')
					->join('briefs', 'briefs.task_id = tasks.id', 'left')
					->where('tasks.campaign_kol_id', $campaign_context->id)
					->order_by('tasks.task_number', 'ASC')
					->get()
					->result();

				// Get shipment tracking
				$shipment_context = $this->db->where('campaign_kol_id', $campaign_context->id)
					->order_by('id', 'DESC')
					->limit(1)
					->get('kol_shipments')
					->row();
			}

			// Load real messages from database
			$messages = $this->model_whatsapp->get_messages_by_kol($selected_kol->id, 100);
		} else {
			$messages = [];
		}

		// Prepare quick templates with dynamic variables
		$templates = $this->_generate_templates($selected_kol, $campaign_context, $tasks_context, $shipment_context);

		// Probe WAHA status
		$session_status = $this->waha_client->get_session_status();
		$waha_status = $session_status['status'] ?? 'OFFLINE';

		// Update session table status
		$this->model_whatsapp->update_session('default', [
			'status'         => $waha_status,
			'last_active_at' => date('Y-m-d H:i:s')
		]);

		// Mark incoming messages as READ for the currently active KOL chat
		if ($selected_kol) {
			$this->model_whatsapp->mark_as_read($selected_kol->id);
		}

		$unread_by_kol = $this->model_whatsapp->get_unread_counts_by_kol();
		$total_unread  = $this->model_whatsapp->get_total_unread_count();

		$all_tiers = $this->db->select('id, name')->order_by('name', 'ASC')->get('tiers')->result();
		$all_categories = $this->db->select('id, name')->order_by('name', 'ASC')->get('categories')->result();
		$all_campaigns = $this->db->select('id, name')->order_by('name', 'ASC')->get('campaigns')->result();

		$data = [
			'kols'             => $kols,
			'selected_kol'     => $selected_kol,
			'campaign_context' => $campaign_context,
			'tasks_context'    => $tasks_context,
			'shipment_context' => $shipment_context,
			'messages'         => $messages,
			'templates'        => $templates,
			'waha_status'      => $waha_status,
			'waha_session'     => $session_status,
			'unread_by_kol'    => $unread_by_kol,
			'total_unread'     => $total_unread,
			'all_tiers'        => $all_tiers,
			'all_categories'   => $all_categories,
			'all_campaigns'    => $all_campaigns
		];

		$this->template->title('Komunikasi WhatsApp KOL');
		$this->render('backend/standart/administrator/whatsapp/whatsapp_chat', $data);
	}

	/**
	 * AJAX endpoint to send a message to the KOL
	 */
	public function ajax_send_message()
	{
		$this->output->enable_profiler(FALSE);

		// CSRF token renewal
		$csrf_token = $this->security->get_csrf_token_name();
		$csrf_hash  = $this->security->get_csrf_hash();

		$kol_id          = (int)$this->input->post('kol_id');
		$message         = trim($this->input->post('message'));
		$campaign_kol_id = $this->input->post('campaign_kol_id') ? (int)$this->input->post('campaign_kol_id') : null;

		if (empty($kol_id) || empty($message)) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'Pesan dan KOL tujuan tidak boleh kosong.',
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		$kol = $this->db->where('id', $kol_id)->get('kols')->row();
		if (!$kol) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'Data KOL tidak ditemukan.',
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		if ($kol->status !== 'ACTIVE') {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'KOL berstatus ' . $kol->status . ' (belum disetujui / non-aktif) tidak dapat dikirimi pesan WhatsApp.',
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		// Normalize phone
		$phone = $this->waha_client->clean_phone($kol->phone ?? '');
		if (empty($phone) || strlen($phone) < 9) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'Nomor telepon KOL tidak valid atau kurang dari 9 digit.',
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		// Current sender info
		$user = $this->aauth->get_user();
		$user_id = $user ? $user->id : null;
		$sender_name = $user ? $user->full_name : 'Staff Operasional';

		// Dispatch via WAHA Client Library
		$waha_res = $this->waha_client->send_text($phone, $message);

		if (empty($waha_res['success'])) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'Gagal mengirim pesan via WAHA: ' . ($waha_res['error'] ?? 'Koneksi WAHA bermasalah.'),
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		$now_dt = date('Y-m-d H:i:s');
		$waha_msg_id = $waha_res['waha_message_id'] ?? ('msg_' . time() . '_' . mt_rand(100, 999));

		// Save to database
		$insert_data = [
			'waha_message_id' => $waha_msg_id,
			'session_name'    => 'default',
			'kol_id'          => $kol->id,
			'campaign_kol_id' => $campaign_kol_id,
			'direction'       => 'OUTBOUND',
			'sender_jid'      => 'system@c.us',
			'receiver_jid'    => $this->waha_client->format_jid($phone),
			'sender_user_id'  => $user_id,
			'sender_name'     => $sender_name,
			'message_type'    => 'text',
			'message_body'    => $message,
			'status'          => 'SENT',
			'sent_at'         => $now_dt,
			'created_at'      => $now_dt
		];

		$saved_id = $this->model_whatsapp->save_message($insert_data);

		$new_message = [
			'id'              => $saved_id,
			'waha_message_id' => $waha_msg_id,
			'direction'       => 'OUTBOUND',
			'sender_name'     => $sender_name,
			'text'            => $message,
			'time'            => date('H:i', strtotime($now_dt)),
			'date'            => date('d M Y', strtotime($now_dt)),
			'status'          => 'SENT',
			'created_at'      => $now_dt
		];

		return $this->output->set_content_type('application/json')->set_output(json_encode([
			'success'      => true,
			'message_data' => $new_message,
			'phone'        => $phone,
			'waha_status'  => 'SENT',
			'csrf_name'    => $csrf_token,
			'csrf_hash'    => $csrf_hash,
		]));
	}

	/**
	 * AJAX endpoint to send a media file (Image/Document/PDF) to the KOL
	 */
	public function ajax_send_media()
	{
		$this->output->enable_profiler(FALSE);

		$csrf_token = $this->security->get_csrf_token_name();
		$csrf_hash  = $this->security->get_csrf_hash();

		$kol_id          = (int)$this->input->post('kol_id');
		$caption         = trim($this->input->post('caption') ?: '');
		$campaign_kol_id = $this->input->post('campaign_kol_id') ? (int)$this->input->post('campaign_kol_id') : null;

		if (empty($kol_id)) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'KOL tujuan tidak boleh kosong.',
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		if (empty($_FILES['media_file']['name'])) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'Pilih file media (gambar atau dokumen) yang ingin dikirim.',
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		$kol = $this->db->where('id', $kol_id)->get('kols')->row();
		if (!$kol) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'Data KOL tidak ditemukan.',
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		if ($kol->status !== 'ACTIVE') {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'KOL berstatus ' . $kol->status . ' (belum disetujui / non-aktif) tidak dapat dikirimi pesan WhatsApp.',
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		$phone = $this->waha_client->clean_phone($kol->phone ?? '');
		if (empty($phone) || strlen($phone) < 9) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'Nomor telepon KOL tidak valid atau kurang dari 9 digit.',
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		// Handle File Upload using CodeIgniter Upload Library
		$upload_dir = FCPATH . 'uploads/whatsapp/';
		if (!is_dir($upload_dir)) {
			@mkdir($upload_dir, 0777, true);
		}

		$config['upload_path']   = $upload_dir;
		$config['allowed_types'] = 'jpg|jpeg|png|webp|gif|pdf|doc|docx|xls|xlsx|ppt|pptx|txt|zip';
		$config['max_size']      = 15360; // 15MB
		$config['encrypt_name']  = TRUE;
		$config['encrypt_name']  = FALSE;

		$this->load->library('upload', $config);

		if (!$this->upload->do_upload('media_file')) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'Gagal mengunggah file: ' . strip_tags($this->upload->display_errors()),
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		$upload_data = $this->upload->data();
		$local_file_path = $upload_data['full_path'];
		$original_filename = $upload_data['orig_name'] ?: $upload_data['file_name'];
		$file_mime = $upload_data['file_type'] ?: 'application/octet-stream';
		$public_url = base_url('uploads/whatsapp/' . $upload_data['file_name']);

		$is_image = (strpos($file_mime, 'image/') === 0);
		$msg_type = $is_image ? 'image' : 'document';

		// Dispatch via WAHA Client Library
		$file_payload = [
			'path'     => $local_file_path,
			'url'      => $public_url,
			'filename' => $original_filename,
			'mimetype' => $file_mime
		];

		$waha_res = $this->waha_client->send_file($phone, $file_payload, $caption, $original_filename);

		if (empty($waha_res['success'])) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'Gagal mengirim file via WhatsApp: ' . ($waha_res['error'] ?? 'Koneksi WAHA bermasalah.'),
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		$user = $this->aauth->get_user();
		$user_id = $user ? $user->id : null;
		$sender_name = $user ? $user->full_name : 'Staff Operasional';
		$now_dt = date('Y-m-d H:i:s');
		$waha_msg_id = $waha_res['data']['id'] ?? ('msg_' . time() . '_' . mt_rand(100, 999));

		// Save to database
		$insert_data = [
			'waha_message_id' => $waha_msg_id,
			'session_name'    => 'default',
			'kol_id'          => $kol->id,
			'campaign_kol_id' => $campaign_kol_id,
			'direction'       => 'OUTBOUND',
			'sender_jid'      => 'system@c.us',
			'receiver_jid'    => $this->waha_client->format_jid($phone),
			'sender_user_id'  => $user_id,
			'sender_name'     => $sender_name,
			'message_type'    => $msg_type,
			'message_body'    => $caption ?: $original_filename,
			'message_body'    => $caption ?: '',
			'media_url'       => $public_url,
			'media_mime'      => $file_mime,
			'status'          => 'SENT',
			'sent_at'         => $now_dt,
			'created_at'      => $now_dt
		];

		$saved_id = $this->model_whatsapp->save_message($insert_data);

		$new_message = [
			'id'              => $saved_id,
			'waha_message_id' => $waha_msg_id,
			'direction'       => 'OUTBOUND',
			'sender_name'     => $sender_name,
			'text'            => $caption ?: '',
			'media_url'       => $public_url,
			'media_mime'      => $file_mime,
			'filename'        => $original_filename,
			'time'            => date('H:i', strtotime($now_dt)),
			'date'            => date('d M Y', strtotime($now_dt)),
			'status'          => 'SENT',
			'created_at'      => $now_dt
		];

		return $this->output->set_content_type('application/json')->set_output(json_encode([
			'success'      => true,
			'message_data' => $new_message,
			'phone'        => $phone,
			'waha_status'  => 'SENT',
			'csrf_name'    => $csrf_token,
			'csrf_hash'    => $csrf_hash,
		]));
	}

	/**
	 * AJAX endpoint for live polling new messages without full reload
	 */
	public function ajax_poll_messages()
	{
		$this->output->enable_profiler(FALSE);
		$kol_id  = (int)$this->input->get('kol_id');
		$last_id = (int)$this->input->get('last_id');

		if (empty($kol_id)) {
			return $this->output->set_content_type('application/json')->set_output(json_encode(['success' => false, 'messages' => []]));
		}

		$new_messages = $this->model_whatsapp->get_new_messages($kol_id, $last_id);

		// If user is currently polling this KOL chat, mark new inbound messages as read
		if (!empty($new_messages)) {
			$this->model_whatsapp->mark_as_read($kol_id);
		}

		return $this->output->set_content_type('application/json')->set_output(json_encode([
			'success'      => true,
			'kol_id'       => $kol_id,
			'messages'     => $new_messages,
			'count'        => count($new_messages),
			'total_unread' => $this->model_whatsapp->get_total_unread_count(),
			'by_kol'       => $this->model_whatsapp->get_unread_counts_by_kol()
		]));
	}

	/**
	 * AJAX endpoint to get global unread counts for navbar badge and KOL list badges
	 */
	public function ajax_unread_counts()
	{
		$this->output->enable_profiler(FALSE);
		$total_unread = $this->model_whatsapp->get_total_unread_count();
		$by_kol = $this->model_whatsapp->get_unread_counts_by_kol();

		return $this->output->set_content_type('application/json')->set_output(json_encode([
			'success'      => true,
			'total_unread' => $total_unread,
			'by_kol'       => $by_kol
		]));
	}

	/**
	 * AJAX endpoint to check WAHA session status and fetch QR code if pairing required
	 */
	public function ajax_session_status()
	{
		$this->output->enable_profiler(FALSE);
		$session_name = $this->input->get('session') ?: 'default';
		$status_res = $this->waha_client->get_session_status($session_name);
		$status = $status_res['status'] ?? 'OFFLINE';

		$qr_url = null;
		if ($status === 'SCAN_QR_CODE') {
			$qr_res = $this->waha_client->get_qr($session_name);
			if (!empty($qr_res['data'])) {
				$d = $qr_res['data'];
				if (is_array($d)) {
					if (!empty($d['data']['url'])) {
						$qr_url = $d['data']['url'];
					} elseif (!empty($d['url'])) {
						$qr_url = $d['url'];
					}
				} elseif (is_string($d) && strpos($d, 'data:image') === 0) {
					$qr_url = $d;
				}
			}
		}

		return $this->output->set_content_type('application/json')->set_output(json_encode([
			'success' => true,
			'session' => $session_name,
			'status'  => $status,
			'qr_url'  => $qr_url,
			'qr'      => [
				'url'  => $qr_url,
				'data' => $qr_url
			],
			'data'    => [
				'url'  => $qr_url
			],
			'me'      => $status_res['me'] ?? null
		]));
	}

	/**
	 * AJAX endpoint to start/restart WAHA session
	 */
	public function ajax_start_session()
	{
		$this->output->enable_profiler(FALSE);
		$csrf_token = $this->security->get_csrf_token_name();
		$csrf_hash  = $this->security->get_csrf_hash();

		$session_name = $this->input->post('session') ?: 'default';
		$start_res = $this->waha_client->start_session($session_name);

		return $this->output->set_content_type('application/json')->set_output(json_encode([
			'success'   => $start_res['success'] ?? false,
			'session'   => $session_name,
			'response'  => $start_res,
			'csrf_name' => $csrf_token,
			'csrf_hash' => $csrf_hash,
		]));
	}

	/**
	 * Generate contextual message templates with dynamic tags
	 */
	private function _generate_templates($kol, $campaign, $tasks, $shipment)
	{
		$kol_name      = $kol ? $kol->name : 'Kak';
		$campaign_name = $campaign ? $campaign->campaign_name : '[Nama Campaign]';
		$vt_title      = (!empty($tasks) && isset($tasks[0])) ? $tasks[0]->title : 'VT #1 Review Produk';
		$variant_name  = (!empty($tasks) && isset($tasks[0]->variant_name)) ? $tasks[0]->variant_name : 'Varian Utama';
		$posting_date  = (!empty($tasks) && isset($tasks[0]->estimated_posting_date)) ? date('d M Y', strtotime($tasks[0]->estimated_posting_date)) : date('d M Y', strtotime('+3 days'));
		$tracking_no   = ($shipment && !empty($shipment->tracking_no)) ? $shipment->tracking_no : '[Nomor Resi]';
		$courier       = ($shipment && !empty($shipment->courier)) ? $shipment->courier : '[Jasa Kurir]';

		// Retrieve templates from DB
		$db_templates = $this->model_whatsapp->get_active_templates();

		$output = [];
		if (!empty($db_templates)) {
			foreach ($db_templates as $t) {
				$text = $t->content;
				$text = str_replace(['{kol_name}', '[Nama KOL]'], $kol_name, $text);
				$text = str_replace(['{campaign_name}', '[Nama Campaign]'], $campaign_name, $text);
				$text = str_replace(['{vt_title}', '[Judul VT]'], $vt_title, $text);
				$text = str_replace('{variant_name}', $variant_name, $text);
				$text = str_replace(['{posting_date}', '[Tgl Posting]'], $posting_date, $text);
				$text = str_replace('{tracking_no}', $tracking_no, $text);
				$text = str_replace('{courier}', $courier, $text);

				$output[] = [
					'id'       => $t->template_code,
					'category' => $t->category,
					'title'    => $t->title,
					'text'     => $text
				];
			}
		}

		// Fallback defaults if DB templates empty
		if (empty($output)) {
			$output = [
				[
					'id'       => 'TPL_DEAL',
					'category' => 'Dealing & Offer',
					'title'    => 'Undangan Kolaborasi Campaign',
					'text'     => "Halo Kak {$kol_name}, salam hangat dari tim Brand! Kami ingin mengajak kolaborasi untuk campaign \"{$campaign_name}\". Apakah slot kolaborasi Kakak tersedia di bulan ini? Kami siap diskusikan brief dan rate card Kakak.",
				],
				[
					'id'       => 'TPL_BRIEF',
					'category' => 'Brief Konten',
					'title'    => 'Kirim Dokumen Brief VT',
					'text'     => "Halo Kak {$kol_name}, berikut kami lampirkan brief panduan konten untuk \"{$vt_title}\" (Varian: {$variant_name}). Mohon dipelajari panduan angle, key message, dan hashtag wajibnya ya Kak. Jika ada hal yang ingin ditanyakan, jangan ragu untuk kontak kami.",
				],
				[
					'id'       => 'TPL_SHIPMENT',
					'category' => 'Logistik Sample',
					'title'    => 'Konfirmasi Resi Pengiriman Sampel',
					'text'     => "Halo Kak {$kol_name}, paket sampel produk untuk campaign \"{$campaign_name}\" sudah kami kirimkan via {$courier} dengan nomor resi: {$tracking_no}. Mohon konfirmasi jika paketnya sudah sampai ya Kak.",
				],
				[
					'id'       => 'TPL_REMINDER_DRAFT',
					'category' => 'Pengingat Draft',
					'title'    => 'Pengingat Pengumpulan Draft Video (H-1)',
					'text'     => "Halo Kak {$kol_name}, mengingatkan kembali bahwa jadwal pengumpulan draft video \"{$vt_title}\" adalah besok. Apakah proses shooting sudah selesai atau ada kendala Kak? Ditunggu link draft-nya ya.",
				],
				[
					'id'       => 'TPL_REMINDER_POST',
					'category' => 'Pengingat Posting',
					'title'    => 'Pengingat Jadwal Live Posting (Hari-H)',
					'text'     => "Halo Kak {$kol_name}, hari ini adalah jadwal live posting untuk video \"{$vt_title}\" ({$posting_date}). Mohon kabari jika videonya sudah tayang beserta link postingannya ya Kak. Terima kasih banyak!",
				],
			];
		}

		return $output;
	}

	/**
	 * AJAX endpoint to logout/disconnect WAHA session
	 */
	public function ajax_logout_waha()
	{
		if (!$this->input->is_ajax_request()) {
			return show_404();
		}

		$waha_res = $this->waha_client->logout_session();

		return $this->output->set_content_type('application/json')->set_output(json_encode([
			'success'   => $waha_res['success'],
			'message'   => $waha_res['success'] ? 'WhatsApp berhasil di-logout.' : 'Gagal melogout WhatsApp.',
			'waha_res'  => $waha_res,
			'csrf_name' => $this->security->get_csrf_token_name(),
			'csrf_hash' => $this->security->get_csrf_hash()
		]));
	}

	/**
	 * AJAX endpoint to fetch all active KOLs for the Broadcast recipient picker
	 */
	public function ajax_get_broadcast_kols()
	{
		$this->output->enable_profiler(FALSE);

		$kols = $this->db->select('
				kols.id,
				kols.name,
				kols.phone,
				kols.avatar_url,
				kols.gender,
				kols.address,
				tiers.name as tier_name,
				tiers.id as tier_id,
				(SELECT c.name FROM campaign_kols ck JOIN campaigns c ON c.id = ck.campaign_id WHERE ck.kol_id = kols.id AND ck.status IN ("ASSIGNED","ACTIVE") ORDER BY ck.id DESC LIMIT 1) as active_campaign_name,
				(SELECT ck.id FROM campaign_kols ck WHERE ck.kol_id = kols.id AND ck.status IN ("ASSIGNED","ACTIVE") ORDER BY ck.id DESC LIMIT 1) as active_campaign_kol_id,
				(SELECT t.title FROM tasks t JOIN campaign_kols ck ON ck.id = t.campaign_kol_id WHERE ck.kol_id = kols.id ORDER BY t.id DESC LIMIT 1) as latest_task_title,
				(SELECT t.estimated_posting_date FROM tasks t JOIN campaign_kols ck ON ck.id = t.campaign_kol_id WHERE ck.kol_id = kols.id ORDER BY t.id DESC LIMIT 1) as latest_task_date
			')
			->from('kols')
			->join('tiers', 'tiers.id = kols.tier_id', 'left')
			->where('kols.status', 'ACTIVE')
			->order_by('kols.name', 'ASC')
			->get()
			->result();

		$kol_ids = array_map(function($k) { return $k->id; }, $kols);
		$categories_by_kol = [];
		if (!empty($kol_ids)) {
			$cats = $this->db->select('kc.kol_id, c.id as category_id, c.name as category_name')
				->from('kol_categories kc')
				->join('categories c', 'c.id = kc.category_id')
				->where_in('kc.kol_id', $kol_ids)
				->get()
				->result();
			foreach ($cats as $cat) {
				$categories_by_kol[$cat->kol_id][] = [
					'id'   => $cat->category_id,
					'name' => $cat->category_name
				];
			}
		}

		$all_tiers = $this->db->select('id, name')->order_by('name', 'ASC')->get('tiers')->result();
		$all_categories = $this->db->select('id, name')->order_by('name', 'ASC')->get('categories')->result();
		$all_campaigns = $this->db->select('id, name')->order_by('name', 'ASC')->get('campaigns')->result();

		$data = [];
		foreach ($kols as $k) {
			$clean = $this->waha_client->clean_phone($k->phone ?? '');
			$has_valid = (!empty($clean) && strlen($clean) >= 9);
			$k_cats = $categories_by_kol[$k->id] ?? [];
			$cat_names = array_map(function($c) { return $c['name']; }, $k_cats);

			$data[] = [
				'id'                     => (int)$k->id,
				'name'                   => $k->name,
				'phone'                  => $k->phone ?: '',
				'clean_phone'            => $clean,
				'has_valid_phone'        => $has_valid,
				'avatar_url'             => $k->avatar_url ?: '',
				'gender'                 => $k->gender ?: '',
				'city'                   => $k->address ?: '',
				'tier_id'                => (int)$k->tier_id,
				'tier_name'              => $k->tier_name ?: 'Standard',
				'categories'             => $k_cats,
				'category_names'         => implode(', ', $cat_names),
				'category_ids'           => array_map(function($c) { return (int)$c['id']; }, $k_cats),
				'active_campaign_name'   => $k->active_campaign_name ?: '',
				'active_campaign_kol_id' => $k->active_campaign_kol_id ? (int)$k->active_campaign_kol_id : null,
				'latest_task_title'      => $k->latest_task_title ?: '',
				'latest_task_date'       => (!empty($k->latest_task_date)) ? date('d M Y', strtotime($k->latest_task_date)) : ''
			];
		}

		return $this->output->set_content_type('application/json')->set_output(json_encode([
			'success'    => true,
			'kols'       => $data,
			'tiers'      => $all_tiers,
			'categories' => $all_categories,
			'campaigns'  => $all_campaigns
		]));
	}

	/**
	 * AJAX endpoint to send an individual broadcast message item to a single KOL
	 */
	public function ajax_send_broadcast_item()
	{
		$this->output->enable_profiler(FALSE);

		$csrf_token = $this->security->get_csrf_token_name();
		$csrf_hash  = $this->security->get_csrf_hash();

		$kol_id          = (int)$this->input->post('kol_id');
		$message_text    = trim($this->input->post('message_text'));
		$campaign_name   = trim($this->input->post('campaign_name'));
		$campaign_id     = $this->input->post('campaign_id') ? (int)$this->input->post('campaign_id') : null;

		if (empty($kol_id) || empty($message_text)) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'KOL dan teks pesan tidak boleh kosong.',
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		$kol = $this->db->where('id', $kol_id)->get('kols')->row();
		if (!$kol) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'Data KOL tidak ditemukan.',
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		if ($kol->status !== 'ACTIVE') {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'KOL berstatus ' . $kol->status . ' (belum disetujui / non-aktif).',
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		$phone = $this->waha_client->clean_phone($kol->phone ?? '');
		if (empty($phone) || strlen($phone) < 9) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'Nomor telepon tidak valid atau kurang dari 9 digit.',
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		// Retrieve active context for placeholder replacement
		$this->db->select('ck.id as campaign_kol_id, c.id as campaign_id, c.name as campaign_name')
			->from('campaign_kols ck')
			->join('campaigns c', 'c.id = ck.campaign_id')
			->where('ck.kol_id', $kol->id);

		if (!empty($campaign_id)) {
			$this->db->where('c.id', $campaign_id);
		}

		$campaign_context = $this->db->order_by('ck.id', 'DESC')->limit(1)->get()->row();
		$campaign_kol_id = $campaign_context ? $campaign_context->campaign_kol_id : null;
		$resolved_campaign_name = !empty($campaign_name) ? $campaign_name : ($campaign_context ? $campaign_context->campaign_name : 'Brand Campaign');

		// Retrieve latest task if exists
		$task_context = null;
		if ($campaign_kol_id) {
			$task_context = $this->db->where('campaign_kol_id', $campaign_kol_id)->order_by('task_number', 'ASC')->limit(1)->get('tasks')->row();
		}
		$vt_title     = ($task_context && !empty($task_context->title)) ? $task_context->title : 'VT #1 Review Produk';
		$posting_date = ($task_context && !empty($task_context->estimated_posting_date)) ? date('d M Y', strtotime($task_context->estimated_posting_date)) : date('d M Y', strtotime('+3 days'));

		// Dynamic variable replacements
		$personalized = $message_text;
		$personalized = str_replace(['{kol_name}', '[Nama KOL]'], $kol->name, $personalized);
		$personalized = str_replace(['{campaign_name}', '[Nama Campaign]'], $resolved_campaign_name, $personalized);
		$personalized = str_replace(['{vt_title}', '[Judul VT]'], $vt_title, $personalized);
		$personalized = str_replace(['{posting_date}', '[Tgl Posting]'], $posting_date, $personalized);

		// Dispatch via WAHA Client Library
		$waha_res = $this->waha_client->send_text($phone, $personalized);

		if (empty($waha_res['success'])) {
			return $this->output->set_content_type('application/json')->set_output(json_encode([
				'success'   => false,
				'message'   => 'Gagal kirim via WAHA: ' . ($waha_res['error'] ?? 'WAHA Offline / Gangguan Koneksi.'),
				'csrf_name' => $csrf_token,
				'csrf_hash' => $csrf_hash,
			]));
		}

		$user = $this->aauth->get_user();
		$user_id = $user ? $user->id : null;
		$sender_name = $user ? $user->full_name : 'Staff Operasional';
		$now_dt = date('Y-m-d H:i:s');
		$waha_msg_id = $waha_res['waha_message_id'] ?? ('msg_' . time() . '_' . mt_rand(100, 999));

		// Save to database in whatsapp_messages
		$insert_data = [
			'waha_message_id' => $waha_msg_id,
			'session_name'    => 'default',
			'kol_id'          => $kol->id,
			'campaign_kol_id' => $campaign_kol_id,
			'direction'       => 'OUTBOUND',
			'sender_jid'      => 'system@c.us',
			'receiver_jid'    => $this->waha_client->format_jid($phone),
			'sender_user_id'  => $user_id,
			'sender_name'     => $sender_name,
			'message_type'    => 'text',
			'message_body'    => $personalized,
			'status'          => 'SENT',
			'sent_at'         => $now_dt,
			'created_at'      => $now_dt
		];

		$saved_id = $this->model_whatsapp->save_message($insert_data);

		return $this->output->set_content_type('application/json')->set_output(json_encode([
			'success'      => true,
			'message'      => 'Terkirim ke ' . $kol->name,
			'kol_id'       => $kol->id,
			'kol_name'     => $kol->name,
			'phone'        => $phone,
			'sent_at'      => date('H:i:s'),
			'message_id'   => $saved_id,
			'csrf_name'    => $csrf_token,
			'csrf_hash'    => $csrf_hash,
		]));
	}
}
