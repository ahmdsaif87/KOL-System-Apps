<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Waha_client
 * 
 * Client library for interacting with WhatsApp HTTP API (WAHA).
 * Reference: https://waha.devlike.pro
 */
class Waha_client
{
	protected $ci;
	protected $base_url;
	protected $api_key;
	protected $default_session;
	protected $connect_timeout;
	protected $timeout;

	public function __construct($params = [])
	{
		$this->ci = &get_instance();
		$this->ci->config->load('waha', TRUE);

		// Load config with priority: 1) cc_options DB, 2) config/waha.php, 3) defaults
		$this->base_url = $this->_get_option('waha_base_url', $this->ci->config->item('waha_base_url', 'waha') ?: 'http://localhost:3000');
		$this->api_key = $this->_get_option('waha_api_key', $this->ci->config->item('waha_api_key', 'waha') ?: '');
		$this->default_session = $this->ci->config->item('waha_default_session', 'waha') ?: 'default';
		$this->connect_timeout = (int)($this->ci->config->item('waha_connect_timeout', 'waha') ?: 2);
		$this->timeout = (int)($this->ci->config->item('waha_timeout', 'waha') ?: 6);
	}

	/**
	 * Helper to get dynamic option from cc_options table
	 */
	private function _get_option($key, $default = '')
	{
		try {
			if ($this->ci->db->table_exists('cc_options')) {
				$row = $this->ci->db->where('option_name', $key)->get('cc_options')->row();
				if ($row && !empty($row->option_value)) {
					return $row->option_value;
				}
			}
		} catch (Exception $e) {
			// Ignore DB errors during early bootstrap
		}
		return $default;
	}

	/**
	 * Format any phone number into WAHA WhatsApp JID (e.g. 628123456789@c.us)
	 * 
	 * @param string $phone
	 * @return string
	 */
	public function format_jid($phone)
	{
		if (empty($phone)) {
			return '';
		}

		// If already full JID
		if (strpos($phone, '@c.us') !== false || strpos($phone, '@g.us') !== false) {
			return trim($phone);
		}

		$digits = preg_replace('/[^0-9]/', '', $phone);

		if (strlen($digits) < 9) {
			return '';
		}

		if (substr($digits, 0, 2) === '62') {
			// Already has 62 country code
		} elseif (substr($digits, 0, 1) === '0') {
			$digits = '62' . substr($digits, 1);
		} elseif (substr($digits, 0, 1) === '8') {
			$digits = '62' . $digits;
		}

		return $digits . '@c.us';
	}

	/**
	 * Clean and normalize phone number digits only (e.g. 628123456789)
	 * 
	 * @param string $phone
	 * @return string
	 */
	public function clean_phone($phone)
	{
		$jid = $this->format_jid($phone);
		return str_replace(['@c.us', '@g.us'], '', $jid);
	}

	/**
	 * Check if WAHA microservice server is reachable (Health Probe)
	 * 
	 * @return array
	 */
	public function check_health()
	{
		$res = $this->request('/api/server/version', 'GET');
		if ($res['success']) {
			return [
				'online'  => true,
				'version' => $res['data']['version'] ?? 'WAHA',
				'data'    => $res['data']
			];
		}

		// Alternative check: /api/sessions
		$res2 = $this->request('/api/sessions', 'GET');
		if ($res2['success']) {
			return [
				'online'  => true,
				'version' => 'WAHA',
				'data'    => $res2['data']
			];
		}

		return [
			'online'  => false,
			'message' => $res['error'] ?? 'WAHA microservice unreachable on ' . $this->base_url,
			'data'    => null
		];
	}

	/**
	 * Get session info and status
	 * 
	 * @param string|null $session
	 * @return array
	 */
	public function get_session_status($session = null)
	{
		$session = $session ?: $this->default_session;
		$res = $this->request('/api/sessions/' . rawurlencode($session), 'GET');

		if ($res['success'] && !empty($res['data'])) {
			$status = $res['data']['status'] ?? 'UNKNOWN';
			return [
				'success' => true,
				'session' => $session,
				'status'  => strtoupper($status),
				'me'      => $res['data']['me'] ?? null,
				'data'    => $res['data']
			];
		}

		return [
			'success' => false,
			'session' => $session,
			'status'  => 'OFFLINE',
			'error'   => $res['error'] ?? 'Gagal mengambil status session'
		];
	}

	/**
	 * Start a WAHA session
	 * 
	 * @param string|null $session
	 * @return array
	 */
	public function start_session($session = null)
	{
		$session = $session ?: $this->default_session;
		return $this->request('/api/sessions', 'POST', [
			'name' => $session
		]);
	}

	/**
	 * Stop a WAHA session
	 * 
	 * @param string|null $session
	 * @return array
	 */
	public function stop_session($session = null)
	{
		$session = $session ?: $this->default_session;
		return $this->request('/api/sessions/' . rawurlencode($session) . '/stop', 'POST');
	}

	/**
	 * Logout a WAHA session (clears auth credentials on WAHA)
	 * 
	 * @param string|null $session
	 * @return array
	 */
	public function logout_session($session = null)
	{
		$session = $session ?: $this->default_session;
		return $this->request('/api/sessions/' . rawurlencode($session) . '/logout', 'POST');
	}

	/**
	 * Get QR code data for session pairing
	 * 
	 * @param string|null $session
	 * @return array
	 */
	public function get_qr($session = null)
	{
		$session = $session ?: $this->default_session;

		// WAHA provides /api/{session}/auth/qr or /api/sessions/{session}/auth/qr
		$res = $this->request('/api/' . rawurlencode($session) . '/auth/qr', 'GET');
		if ($res['success']) {
			return $res;
		}

		return $this->request('/api/sessions/' . rawurlencode($session) . '/auth/qr', 'GET');
	}

	/**
	 * Send WhatsApp text message
	 * 
	 * @param string $phone
	 * @param string $text
	 * @param string|null $session
	 * @return array
	 */
	public function send_text($phone, $text, $session = null)
	{
		$session = $session ?: $this->default_session;
		$chat_id = $this->format_jid($phone);

		if (empty($chat_id)) {
			return [
				'success' => false,
				'error'   => 'Nomor telepon tujuan tidak valid atau kurang dari 9 digit.'
			];
		}

		$payload = [
			'session' => $session,
			'chatId'  => $chat_id,
			'text'    => $text
		];

		$res = $this->request('/api/sendText', 'POST', $payload);

		if ($res['success']) {
			return [
				'success'         => true,
				'waha_message_id' => $res['data']['id'] ?? ('waha_' . time()),
				'data'            => $res['data']
			];
		}

		return [
			'success' => false,
			'error'   => $res['error'] ?? 'Pengiriman WAHA gagal. Pastikan microservice WAHA aktif.'
		];
	}

	/**
	 * Send WhatsApp media file (PDF, Image, Video)
	 * 
	 * @param string $phone
	 * @param string $file_url
	 * @param string $caption
	 * @param string $filename
	 * @param string|null $session
	 * @return array
	 */
	public function send_file($phone, $file_info, $caption = '', $filename = '', $session = null)
	{
		$session = $session ?: $this->default_session;
		$chat_id = $this->format_jid($phone);

		if (empty($chat_id)) {
			return [
				'success' => false,
				'error'   => 'Nomor telepon tujuan tidak valid atau kurang dari 9 digit.'
			];
		}

		$file_payload = [];
		if (is_array($file_info)) {
			$file_payload = $file_info;
		} elseif (is_string($file_info)) {
			if (file_exists($file_info)) {
				$file_payload['path'] = realpath($file_info);
				$file_payload['filename'] = $filename ?: basename($file_info);
				$file_payload['mimetype'] = function_exists('mime_content_type') ? @mime_content_type($file_info) : null;
			} else {
				$file_payload['url'] = $file_info;
				$file_payload['filename'] = $filename ?: basename($file_info);
			}
		}

		if (empty($file_payload['filename']) && !empty($filename)) {
			$file_payload['filename'] = $filename;
		}

		$payload = [
			'session' => $session,
			'chatId'  => $chat_id,
			'file'    => $file_payload,
			'caption' => $caption
		];

		return $this->request('/api/sendFile', 'POST', $payload);
	}

	/**
	 * Execute HTTP cURL request to WAHA
	 * 
	 * @param string $endpoint
	 * @param string $method
	 * @param array|null $data
	 * @return array
	 */
	protected function request($endpoint, $method = 'GET', $data = null)
	{
		$url = rtrim($this->base_url, '/') . '/' . ltrim($endpoint, '/');

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout);
		curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

		$headers = [
			'Accept: application/json',
		];

		if (!empty($this->api_key)) {
			$headers[] = 'X-Api-Key: ' . $this->api_key;
		}

		if (strtoupper($method) === 'POST') {
			curl_setopt($ch, CURLOPT_POST, true);
			if ($data !== null) {
				$json_payload = is_string($data) ? $data : json_encode($data);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
				$headers[] = 'Content-Type: application/json';
			}
		} elseif (strtoupper($method) !== 'GET') {
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
			if ($data !== null) {
				$json_payload = is_string($data) ? $data : json_encode($data);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
				$headers[] = 'Content-Type: application/json';
			}
		}

		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

		$response = curl_exec($ch);
		$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$curl_error = curl_error($ch);
		curl_close($ch);

		if ($curl_error) {
			return [
				'success'     => false,
				'http_code'   => $http_code,
				'error'       => 'cURL Error: ' . $curl_error,
				'data'        => null
			];
		}

		$decoded = json_decode($response, true);

		if ($http_code >= 200 && $http_code < 300) {
			return [
				'success'   => true,
				'http_code' => $http_code,
				'data'      => $decoded !== null ? $decoded : $response,
				'error'     => null
			];
		}

		$error_msg = 'HTTP ' . $http_code;
		if (is_array($decoded) && !empty($decoded['message'])) {
			$error_msg .= ': ' . (is_array($decoded['message']) ? json_encode($decoded['message']) : $decoded['message']);
		} elseif (is_array($decoded) && !empty($decoded['error'])) {
			$error_msg .= ': ' . (is_array($decoded['error']) ? json_encode($decoded['error']) : $decoded['error']);
		}

		return [
			'success'   => false,
			'http_code' => $http_code,
			'error'     => $error_msg,
			'data'      => $decoded
		];
	}
}
