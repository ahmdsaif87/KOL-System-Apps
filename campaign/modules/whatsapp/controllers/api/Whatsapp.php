<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Whatsapp API Controller
 * 
 * Handles incoming webhooks and external API calls from WAHA (WhatsApp HTTP API).
 * Accessible via /api/whatsapp/webhook
 */
class Whatsapp extends MY_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model('whatsapp/model_whatsapp');
		$this->load->library('waha_client');

		// Webhooks are JSON APIs; disable profiler and render clean JSON
		$this->output->enable_profiler(FALSE);
	}

	/**
	 * Default ping endpoint (GET /api/whatsapp)
	 */
	public function index()
	{
		$this->ping();
	}

	/**
	 * Health check / ping endpoint (GET /api/whatsapp/ping)
	 */
	public function ping()
	{
		$health = $this->waha_client->check_health();
		$this->_json_response([
			'status'  => 'OK',
			'service' => 'KOL WhatsApp Webhook Gateway',
			'time'    => date('Y-m-d H:i:s'),
			'waha'    => $health
		]);
	}

	/**
	 * Main Webhook Receiver (POST /api/whatsapp/webhook)
	 */
	public function webhook()
	{
		// Support GET for quick browser test
		if ($this->input->method() !== 'post') {
			$this->_json_response([
				'status'  => 'OK',
				'message' => 'WhatsApp Webhook endpoint is active and listening for POST payloads.',
				'time'    => date('Y-m-d H:i:s')
			]);
			return;
		}

		$raw_input = file_get_contents('php://input');
		$payload   = json_decode($raw_input, true);

		if (empty($payload)) {
			$this->_json_response([
				'status'  => 'ERROR',
				'message' => 'Empty or invalid JSON payload.'
			], 400);
			return;
		}

		$event   = $payload['event'] ?? '';
		$session = $payload['session'] ?? 'default';
		$data    = $payload['payload'] ?? [];

		log_message('debug', "WAHA Webhook Event [{$event}] on session [{$session}]");

		switch ($event) {
			case 'message':
				$this->_handle_message_event($session, $data);
				break;

			case 'message.ack':
				$this->_handle_ack_event($session, $data);
				break;

			case 'session.status':
				$this->_handle_session_status_event($session, $data);
				break;

			default:
				// Other unhandled events (e.g. group updates, state)
				break;
		}

		$this->_json_response([
			'status'  => 'SUCCESS',
			'event'   => $event,
			'session' => $session
		]);
	}

	/**
	 * Handle incoming WhatsApp message event
	 */
	private function _handle_message_event($session, $data)
	{
		$waha_msg_id = $data['id'] ?? '';
		$from_me     = !empty($data['fromMe']);
		$from        = $data['from'] ?? '';
		$to          = $data['to'] ?? '';
		$body        = $data['body'] ?? '';
		$timestamp   = !empty($data['timestamp']) ? date('Y-m-d H:i:s', $data['timestamp']) : date('Y-m-d H:i:s');
		$has_media   = !empty($data['hasMedia']);
		$media_url   = $data['media']['url'] ?? null;
		$media_mime  = $data['media']['mimetype'] ?? null;

		if (!empty($media_url) && !preg_match('/^http/', $media_url)) {
			$media_url = base_url($media_url);
		}

		// Skip status broadcasts and group chats
		if (strpos($from, '@broadcast') !== false || strpos($from, '@newsletter') !== false || strpos($from, '@g.us') !== false) {
			return;
		}

		$direction = $from_me ? 'OUTBOUND' : 'INBOUND';

		// Match remote party to registered KOL
		$kol = $this->model_whatsapp->find_kol_by_phone($from);

		$kol_id = $kol ? $kol->id : null;
		$sender_name = null;

		if ($from_me) {
			$sender_name = 'Staff Brand';
		} else {
			$sender_name = $kol ? $kol->name : 'KOL (' . preg_replace('/@[a-zA-Z0-9\.\_\-]+$/', '', $from) . ')';
		}

		// Prevent duplicate insert if already recorded, or update if existing record was empty
		if (!empty($waha_msg_id)) {
			$existing = $this->db->where('waha_message_id', $waha_msg_id)->get('whatsapp_messages')->row();
			if ($existing) {
				if ((empty($existing->message_body) || empty($existing->kol_id)) && (!empty($body) || !empty($kol_id))) {
					$upd = [];
					if (!empty($body)) $upd['message_body'] = $body;
					if (!empty($kol_id)) $upd['kol_id'] = $kol_id;
					if (!empty($sender_name)) $upd['sender_name'] = $sender_name;
					if (!empty($upd)) {
						$this->db->where('id', $existing->id)->update('whatsapp_messages', $upd);
					}
				}
				return;
			}
		}

		// Find active campaign_kol relation if any
		$campaign_kol_id = null;
		if ($kol_id) {
			$ck = $this->db->where('kol_id', $kol_id)
				->where_in('status', ['ASSIGNED', 'ACTIVE'])
				->order_by('id', 'DESC')
				->limit(1)
				->get('campaign_kols')
				->row();
			if ($ck) {
				$campaign_kol_id = $ck->id;
			}
		}

		$insert_data = [
			'waha_message_id' => $waha_msg_id,
			'session_name'    => $session,
			'kol_id'          => $kol_id,
			'campaign_kol_id' => $campaign_kol_id,
			'direction'       => $direction,
			'sender_jid'      => $from_me ? 'system@c.us' : $from,
			'receiver_jid'    => $from_me ? $from : $to,
			'sender_name'     => $sender_name,
			'message_type'    => $has_media ? 'media' : 'text',
			'message_body'    => $body ?: '',
			'media_url'       => $media_url,
			'media_mime'      => $media_mime,
			'status'          => $from_me ? 'SENT' : 'DELIVERED',
			'sent_at'         => $timestamp,
			'delivered_at'    => $from_me ? null : $timestamp,
			'created_at'      => $timestamp
		];

		$this->model_whatsapp->save_message($insert_data);
	}

	/**
	 * Handle WhatsApp message ACK event (SENT / DELIVERED / READ)
	 */
	private function _handle_ack_event($session, $data)
	{
		$msg_id = $data['id'] ?? '';
		$ack    = $data['ack'] ?? null;

		if (empty($msg_id) || $ack === null) {
			return;
		}

		// WAHA ack codes: 1 = SENT (SERVER), 2 = DELIVERED (DEVICE), 3 = READ, 4 = PLAYED
		$status_map = [
			1 => 'SENT',
			2 => 'DELIVERED',
			3 => 'READ',
			4 => 'READ',
		];

		$status = $status_map[$ack] ?? null;
		if ($status) {
			$this->model_whatsapp->update_message_status($msg_id, $status);
		}
	}

	/**
	 * Handle WAHA session status event
	 */
	private function _handle_session_status_event($session, $data)
	{
		$status = $data['status'] ?? 'UNKNOWN';
		$this->model_whatsapp->update_session($session, [
			'status'         => strtoupper($status),
			'last_active_at' => date('Y-m-d H:i:s')
		]);
	}

	/**
	 * Send JSON response
	 */
	private function _json_response($data, $code = 200)
	{
		$this->output
			->set_status_header($code)
			->set_content_type('application/json', 'utf-8')
			->set_output(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
	}
}
