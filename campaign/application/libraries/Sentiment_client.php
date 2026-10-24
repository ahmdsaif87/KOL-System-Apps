<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Sentiment_client
 *
 * HTTP client library for the KOL Sentiment Analysis Microservice.
 * Performs IndoBERT-based sentiment analysis on TikTok, Instagram, and
 * YouTube post comments via ScrapeCreators API.
 *
 * Microservice: https://precaudal-intraspinal-russel.ngrok-free.dev/
 * Model: Aardiiiiy/indoberttweet-base-indonesian-sentiment-analysis
 */
class Sentiment_client
{
    /** @var CI_Controller */
    protected $ci;

    protected $base_url;
    protected $bearer_token;
    protected $connect_timeout;
    protected $timeout;
    protected $comment_limit;

    /**
     * Platform code normalization map.
     * Maps DB platform codes (uppercase) → API enum values (lowercase).
     */
    protected $platform_map = [
        'TIKTOK'    => 'tiktok',
        'INSTAGRAM' => 'instagram',
        'YOUTUBE'   => 'youtube',
        'IG'        => 'instagram',
        'YT'        => 'youtube',
        'TT'        => 'tiktok',
    ];

    /**
     * URL domain → platform fallback map.
     */
    protected $domain_map = [
        'tiktok.com'    => 'tiktok',
        'instagram.com' => 'instagram',
        'youtube.com'   => 'youtube',
        'youtu.be'      => 'youtube',
    ];

    public function __construct()
    {
        $this->ci = &get_instance();
        $this->ci->config->load('sentiment', TRUE);

        $this->base_url        = rtrim((get_option('sentiment_base_url') ?: $this->ci->config->item('sentiment_base_url', 'sentiment')) ?: '', '/');
        $this->bearer_token    = (get_option('sentiment_bearer_token') ?: $this->ci->config->item('sentiment_bearer_token', 'sentiment'));
        $this->connect_timeout = (int) ($this->ci->config->item('sentiment_connect_timeout', 'sentiment') ?: 10);
        $this->timeout         = (int) ($this->ci->config->item('sentiment_timeout', 'sentiment') ?: 60);
        $this->comment_limit   = (int) ($this->ci->config->item('sentiment_comment_limit', 'sentiment') ?: 500);
    }

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Check if the microservice is healthy and model is loaded.
     *
     * @return array ['healthy' => bool, 'model_loaded' => bool, 'error' => string|null]
     */
    public function check_health()
    {
        $res = $this->_request('/health', 'GET');

        if (!$res['success']) {
            return [
                'healthy'      => false,
                'model_loaded' => false,
                'error'        => $res['error'],
            ];
        }

        $data = $res['data'];

        return [
            'healthy'      => isset($data['status']) && $data['status'] === 'healthy',
            'model_loaded' => isset($data['model_loaded']) && $data['model_loaded'] === true,
            'error'        => null,
        ];
    }

    /**
     * Normalize a platform code (from DB) or URL domain into the API enum value.
     * Returns lowercase 'tiktok', 'instagram', 'youtube', or null if unresolvable.
     *
     * @param string|null $platform_code  Platform code from DB (e.g. 'TIKTOK')
     * @param string|null $url            Post URL for domain fallback
     * @return string|null
     */
    public function resolve_platform($platform_code = null, $url = null)
    {
        // 1. Try exact/alias match from DB platform code
        if (!empty($platform_code)) {
            $upper = strtoupper(trim($platform_code));
            if (isset($this->platform_map[$upper])) {
                return $this->platform_map[$upper];
            }
        }

        // 2. Fallback: deduce from URL domain
        if (!empty($url)) {
            $host = strtolower(parse_url($url, PHP_URL_HOST) ?: '');
            // Strip 'www.' prefix
            $host = preg_replace('/^www\./', '', $host);
            if (isset($this->domain_map[$host])) {
                return $this->domain_map[$host];
            }
        }

        return null;
    }

    /**
     * Trigger sentiment analysis for a given post URL.
     *
     * @param string $platform  Lowercase platform enum: 'tiktok', 'instagram', 'youtube'
     * @param string $url       Full post URL
     * @param int    $limit     Max comments to analyze (default: config value)
     * @return array [
     *   'success'    => bool,
     *   'http_code'  => int,
     *   'data'       => array|null,  // AnalyzeData on success
     *   'error'      => string|null
     * ]
     */
    public function analyze($platform, $url, $limit = null)
    {
        if (empty($platform) || empty($url)) {
            return [
                'success'   => false,
                'http_code' => 0,
                'data'      => null,
                'error'     => 'Platform dan URL tidak boleh kosong.',
            ];
        }

        $payload = [
            'platform' => $platform,
            'url'      => $url,
            'limit'    => (int) ($limit ?: $this->comment_limit),
        ];

        $res = $this->_request('/api/v1/analyze', 'POST', $payload);

        if (!$res['success']) {
            return $res;
        }

        // Unwrap the 'data' envelope from the microservice response
        $body = $res['data'];
        if (isset($body['data'])) {
            $res['data'] = $body['data'];
        }

        return $res;
    }

    // -------------------------------------------------------------------------
    // Internal cURL Request Executor
    // -------------------------------------------------------------------------

    /**
     * Execute an HTTP request against the microservice.
     *
     * @param string     $endpoint  e.g. '/health' or '/api/v1/analyze'
     * @param string     $method    'GET' or 'POST'
     * @param array|null $data      JSON-encodable payload for POST requests
     * @return array ['success' => bool, 'http_code' => int, 'data' => mixed, 'error' => string|null]
     */
    protected function _request($endpoint, $method = 'GET', $data = null)
    {
        $url = $this->base_url . '/' . ltrim($endpoint, '/');

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $headers = [
            'Accept: application/json',
            'Authorization: Bearer ' . $this->bearer_token,
            'ngrok-skip-browser-warning: true',
        ];

        if (strtoupper($method) === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            if ($data !== null) {
                $json_payload = json_encode($data);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $json_payload);
                $headers[] = 'Content-Type: application/json';
            }
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);
        curl_close($ch);

        // cURL transport error
        if ($curl_err) {
            return [
                'success'   => false,
                'http_code' => 0,
                'data'      => null,
                'error'     => 'cURL Error: ' . $curl_err,
            ];
        }

        $decoded = json_decode($response, true);

        // HTTP success
        if ($http_code >= 200 && $http_code < 300) {
            return [
                'success'   => true,
                'http_code' => $http_code,
                'data'      => $decoded !== null ? $decoded : $response,
                'error'     => null,
            ];
        }

        // HTTP error — decode meaningful messages per status code
        $error_msg = $this->_decode_error($http_code, $decoded);

        return [
            'success'   => false,
            'http_code' => $http_code,
            'data'      => $decoded,
            'error'     => $error_msg,
        ];
    }

    /**
     * Decode a human-readable error message from an HTTP error response.
     *
     * @param int        $http_code
     * @param array|null $decoded
     * @return string
     */
    protected function _decode_error($http_code, $decoded)
    {
        // Extract microservice-level message first
        $svc_message = null;
        if (is_array($decoded)) {
            if (!empty($decoded['message'])) {
                $svc_message = $decoded['message'];
            } elseif (!empty($decoded['detail'])) {
                if (is_string($decoded['detail'])) {
                    $svc_message = $decoded['detail'];
                } elseif (is_array($decoded['detail']) && !empty($decoded['detail'][0]['msg'])) {
                    $svc_message = $decoded['detail'][0]['msg'];
                }
            }
        }

        $known = [
            401 => 'Autentikasi gagal. Bearer token tidak valid.',
            404 => 'Video atau komentar tidak ditemukan. Pastikan URL sudah benar dan video masih tersedia.',
            422 => 'Payload tidak valid: ' . ($svc_message ?: 'Periksa kembali parameter platform dan URL.'),
            502 => 'Microservice gagal terhubung ke scraper atau layanan IndoBERT. Coba beberapa saat lagi.',
        ];

        if (isset($known[$http_code])) {
            return $known[$http_code];
        }

        return 'HTTP ' . $http_code . ($svc_message ? ': ' . $svc_message : ' — Terjadi kesalahan pada microservice.');
    }
}
