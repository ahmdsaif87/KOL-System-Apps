<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Model_whatsapp
 * 
 * Data access and business logic for WhatsApp messaging, sessions, and templates.
 */
class Model_whatsapp extends MY_Model
{
	protected $primary_key = 'id';
	protected $table_name  = 'whatsapp_messages';

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Retrieve message history for a specific KOL
	 * 
	 * @param int $kol_id
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function get_messages_by_kol($kol_id, $limit = 100, $offset = 0)
	{
		if (empty($kol_id)) {
			return [];
		}

		$rows = $this->db->select('
				wm.*,
				u.full_name as staff_user_name
			')
			->from('whatsapp_messages wm')
			->join('aauth_users u', 'u.id = wm.sender_user_id', 'left')
			->where('wm.kol_id', (int)$kol_id)
			->order_by('wm.id', 'ASC')
			->limit($limit, $offset)
			->get()
			->result();

		$messages = [];
		foreach ($rows as $r) {
			$messages[] = $this->_format_message_row($r);
		}

		return $messages;
	}

	/**
	 * Retrieve new messages for polling
	 * 
	 * @param int $kol_id
	 * @param int $last_id
	 * @return array
	 */
	public function get_new_messages($kol_id, $last_id = 0)
	{
		if (empty($kol_id)) {
			return [];
		}

		$rows = $this->db->select('
				wm.*,
				u.full_name as staff_user_name
			')
			->from('whatsapp_messages wm')
			->join('aauth_users u', 'u.id = wm.sender_user_id', 'left')
			->where('wm.kol_id', (int)$kol_id)
			->where('wm.id >', (int)$last_id)
			->order_by('wm.id', 'ASC')
			->get()
			->result();

		$messages = [];
		foreach ($rows as $r) {
			$messages[] = $this->_format_message_row($r);
		}

		return $messages;
	}

	/**
	 * Get total count of unread incoming messages
	 * 
	 * @return int
	 */
	public function get_total_unread_count()
	{
		return (int)$this->db->from('whatsapp_messages')
			->join('kols', 'kols.id = whatsapp_messages.kol_id', 'inner')
			->where('whatsapp_messages.direction', 'INBOUND')
			->where('whatsapp_messages.status !=', 'READ')
			->where('kols.status', 'ACTIVE')
			->count_all_results();
	}

	/**
	 * Get count of unread incoming messages grouped by kol_id
	 * 
	 * @return array [kol_id => count]
	 */
	public function get_unread_counts_by_kol()
	{
		$rows = $this->db->select('whatsapp_messages.kol_id, COUNT(*) as unread_count')
			->from('whatsapp_messages')
			->join('kols', 'kols.id = whatsapp_messages.kol_id', 'inner')
			->where('whatsapp_messages.direction', 'INBOUND')
			->where('whatsapp_messages.status !=', 'READ')
			->where('kols.status', 'ACTIVE')
			->group_by('whatsapp_messages.kol_id')
			->get()
			->result();

		$counts = [];
		foreach ($rows as $r) {
			$counts[(int)$r->kol_id] = (int)$r->unread_count;
		}
		return $counts;
	}

	/**
	 * Mark all incoming messages for a specific KOL as READ
	 * 
	 * @param int $kol_id
	 * @return bool
	 */
	public function mark_as_read($kol_id)
	{
		if (empty($kol_id)) {
			return false;
		}

		$now = date('Y-m-d H:i:s');
		return $this->db->where('kol_id', (int)$kol_id)
			->where('direction', 'INBOUND')
			->where('status !=', 'READ')
			->update('whatsapp_messages', [
				'status'  => 'READ',
				'read_at' => $now
			]);
	}

	/**
	 * Format message row for JSON/View presentation
	 */
	private function _format_message_row($r)
	{
		$time_str = !empty($r->sent_at) ? date('H:i', strtotime($r->sent_at)) : date('H:i', strtotime($r->created_at));
		$date_str = !empty($r->sent_at) ? date('d M Y', strtotime($r->sent_at)) : date('d M Y', strtotime($r->created_at));

		$sender = $r->sender_name;
		if (empty($sender)) {
			$sender = ($r->direction === 'OUTBOUND') ? ($r->staff_user_name ?: 'KOL Specialist') : 'KOL';
		}

		return [
			'id'              => (int)$r->id,
			'waha_message_id' => $r->waha_message_id,
			'direction'       => $r->direction,
			'sender_name'     => $sender,
			'text'            => $r->message_body,
			'media_url'       => $r->media_url,
			'media_mime'      => $r->media_mime,
			'status'          => $r->status,
			'time'            => $time_str,
			'date'            => $date_str,
			'created_at'      => $r->created_at
		];
	}

	/**
	 * Save incoming or outgoing message to database
	 * 
	 * @param array $data
	 * @return int Inserted ID
	 */
	public function save_message($data)
	{
		$now = date('Y-m-d H:i:s');
		if (empty($data['created_at'])) {
			$data['created_at'] = $now;
		}
		if (empty($data['sent_at'])) {
			$data['sent_at'] = $now;
		}

		$this->db->insert('whatsapp_messages', $data);
		return $this->db->insert_id();
	}

	/**
	 * Update message ack status (e.g. from WAHA webhook)
	 * 
	 * @param string $waha_message_id
	 * @param string $status PENDING|SENT|DELIVERED|READ|FAILED
	 * @param string|null $timestamp
	 * @return bool
	 */
	public function update_message_status($waha_message_id, $status, $timestamp = null)
	{
		if (empty($waha_message_id)) {
			return false;
		}

		$update = ['status' => strtoupper($status)];
		$dt = !empty($timestamp) ? date('Y-m-d H:i:s', is_numeric($timestamp) ? $timestamp : strtotime($timestamp)) : date('Y-m-d H:i:s');

		if (strtoupper($status) === 'DELIVERED') {
			$update['delivered_at'] = $dt;
		} elseif (strtoupper($status) === 'READ') {
			$update['read_at'] = $dt;
		}

		$this->db->where('waha_message_id', $waha_message_id);
		return $this->db->update('whatsapp_messages', $update);
	}

	/**
	 * Intelligent matching of a phone number / JID to a registered KOL
	 * 
	 * @param string $phone_or_jid
	 * @return object|null
	 */
	public function find_kol_by_phone($phone_or_jid)
	{
		if (empty($phone_or_jid)) {
			return null;
		}

		// Strip JID domains and device suffixes like :37
		$clean = preg_replace('/@[a-zA-Z0-9\.\_\-]+$/', '', trim($phone_or_jid));
		$clean = preg_replace('/:[0-9]+$/', '', $clean);
		$digits = preg_replace('/[^0-9]/', '', $clean);

		if (empty($digits) || strlen($digits) < 7) {
			return null;
		}

		// Generate variations: 628xxx, 08xxx, 8xxx
		$variations = [$digits];

		if (substr($digits, 0, 2) === '62') {
			$without_62 = substr($digits, 2);
			$variations[] = '0' . $without_62;
			$variations[] = $without_62;
			$variations[] = '+62' . $without_62;
		} elseif (substr($digits, 0, 1) === '0') {
			$without_0 = substr($digits, 1);
			$variations[] = '62' . $without_0;
			$variations[] = $without_0;
			$variations[] = '+62' . $without_0;
		} elseif (substr($digits, 0, 1) === '8') {
			$variations[] = '62' . $digits;
			$variations[] = '0' . $digits;
			$variations[] = '+62' . $digits;
		}

		$this->db->group_start();
		foreach ($variations as $v) {
			$this->db->or_like('phone', $v);
		}
		$this->db->group_end();

		return $this->db->where('status', 'ACTIVE')->limit(1)->get('kols')->row();
	}

	/**
	 * Retrieve all active templates from database
	 * 
	 * @return array
	 */
	public function get_active_templates()
	{
		return $this->db->where('is_active', 1)
			->order_by('id', 'ASC')
			->get('whatsapp_templates')
			->result();
	}

	/**
	 * Get session details from database
	 * 
	 * @param string $session_name
	 * @return object|null
	 */
	public function get_session($session_name = 'default')
	{
		return $this->db->where('session_name', $session_name)->get('whatsapp_sessions')->row();
	}

	/**
	 * Update or insert session status into database
	 * 
	 * @param string $session_name
	 * @param array $data
	 * @return bool
	 */
	public function update_session($session_name, $data)
	{
		$existing = $this->get_session($session_name);
		$data['updated_at'] = date('Y-m-d H:i:s');

		if ($existing) {
			$this->db->where('session_name', $session_name);
			return $this->db->update('whatsapp_sessions', $data);
		} else {
			$data['session_name'] = $session_name;
			$data['created_at']   = date('Y-m-d H:i:s');
			return $this->db->insert('whatsapp_sessions', $data);
		}
	}
}

