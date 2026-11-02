<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Sentiment_client
 *
 * HTTP client library for the KOL Sentiment Analysis Microservice.
 * Performs IndoBERT-based sentiment analysis on TikTok, Instagram, and
 * YouTube post comments via Hugging Face Spaces (Gradio 5 API).
 *
 * Space: https://huggingface.co/spaces/ahmdsaif/kol-sentiment-api
 * Direct Host: https://ahmdsaif-kol-sentiment-api.hf.space
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

        $default_url = 'https://ahmdsaif-kol-sentiment-api.hf.space';
        $this->base_url        = rtrim((get_option('sentiment_base_url') ?: $this->ci->config->item('sentiment_base_url', 'sentiment')) ?: $default_url, '/');
        $this->bearer_token    = (get_option('sentiment_bearer_token') ?: $this->ci->config->item('sentiment_bearer_token', 'sentiment'));
        $this->connect_timeout = (int) ($this->ci->config->item('sentiment_connect_timeout', 'sentiment') ?: 15);
        $this->timeout         = (int) ($this->ci->config->item('sentiment_timeout', 'sentiment') ?: 120);
        $this->comment_limit   = (int) ($this->ci->config->item('sentiment_comment_limit', 'sentiment') ?: 500);
    }

    // -------------------------------------------------------------------------
    // Public API
    // -------------------------------------------------------------------------

    /**
     * Check if the Hugging Face microservice is healthy and ready.
     *
     * @return array ['healthy' => bool, 'model_loaded' => bool, 'error' => string|null]
     */
    public function check_health()
    {
        // 1. Check Gradio 5 API Info endpoint
        $res = $this->_request('/gradio_api/info', 'GET');

        if ($res['success']) {
            $data = $res['data'];
            $has_predict = is_array($data) && isset($data['named_endpoints']['/predict']);
            if ($has_predict) {
                return [
                    'healthy'      => true,
                    'model_loaded' => true,
                    'error'        => null,
                ];
            }
        }

        // 2. Fallback: Check direct FastAPI /health endpoint if mounted
        $res_health = $this->_request('/health', 'GET');
        if ($res_health['success'] && is_array($res_health['data']) && isset($res_health['data']['status'])) {
            return [
                'healthy'      => $res_health['data']['status'] === 'healthy',
                'model_loaded' => !empty($res_health['data']['model_loaded']),
                'error'        => null,
            ];
        }

        $err = !empty($res['error']) ? $res['error'] : ($res_health['error'] ?? 'Hugging Face Space sedang cold start atau offline. Silakan coba 1-2 menit lagi.');

        return [
            'healthy'      => false,
            'model_loaded' => false,
            'error'        => $err,
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
            $host = preg_replace('/^www\./', '', $host);
            if (isset($this->domain_map[$host])) {
                return $this->domain_map[$host];
            }
        }

        return null;
    }

    /**
     * Trigger sentiment analysis for a given post URL via Hugging Face Spaces.
     *
     * @param string $platform  Lowercase platform enum: 'tiktok', 'instagram', 'youtube'
     * @param string $url       Full post URL
     * @param int    $limit     Max comments to analyze (default: config value)
     * @return array [
     *   'success'    => bool,
     *   'http_code'  => int,
     *   'data'       => array|null,  // Structured Sentiment Data on success
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

        $comment_limit = (int) ($limit ?: $this->comment_limit);
        if ($comment_limit < 10) {
            $comment_limit = 10;
        }
        if ($comment_limit > 500) {
            $comment_limit = 500;
        }

        // Primary: Execute via Gradio 5 API (/gradio_api/call/predict + SSE)
        $res = $this->_call_gradio_predict($platform, $url, $comment_limit);
        if ($res['success']) {
            return $res;
        }

        // Fallback: Check if direct REST endpoint /api/v1/analyze is available
        $payload = [
            'platform' => $platform,
            'url'      => $url,
            'limit'    => $comment_limit,
        ];
        $direct_res = $this->_request('/api/v1/analyze', 'POST', $payload);
        if ($direct_res['success'] && is_array($direct_res['data'])) {
            $body = $direct_res['data'];
            if (isset($body['data'])) {
                $body = $body['data'];
            }
            if (isset($body['sentiment_summary'])) {
                return [
                    'success'   => true,
                    'http_code' => 200,
                    'data'      => $body,
                    'error'     => null,
                ];
            }
        }

        return $res;
    }

    // -------------------------------------------------------------------------
    // Gradio 5 API Execution & Response Parsing
    // -------------------------------------------------------------------------

    /**
     * Call Gradio 5 prediction endpoint via event-driven SSE protocol.
     *
     * @param string $platform
     * @param string $url
     * @param int    $limit
     * @return array
     */
    protected function _call_gradio_predict($platform, $url, $limit)
    {
        // Step 1: POST /gradio_api/call/predict
        $post_url = $this->base_url . '/gradio_api/call/predict';
        $payload  = json_encode(['data' => [$platform, $url, (int) $limit]]);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $post_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
        ];
        if (!empty($this->bearer_token)) {
            $headers[] = 'Authorization: Bearer ' . $this->bearer_token;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);
        curl_close($ch);

        if ($curl_err) {
            return [
                'success'   => false,
                'http_code' => 0,
                'data'      => null,
                'error'     => 'Koneksi ke Hugging Face gagal: ' . $curl_err,
            ];
        }

        $decoded  = json_decode($response, true);
        $event_id = is_array($decoded) ? ($decoded['event_id'] ?? null) : null;

        if (!$event_id || $http_code < 200 || $http_code >= 300) {
            return [
                'success'   => false,
                'http_code' => $http_code,
                'data'      => null,
                'error'     => 'Gagal mendaftarkan antrean Gradio di Hugging Face (HTTP ' . $http_code . '): ' . ($decoded['message'] ?? $response),
            ];
        }

        // Step 2: GET /gradio_api/call/predict/{event_id} (Wait for SSE stream completion)
        $get_url = $this->base_url . '/gradio_api/call/predict/' . $event_id;
        $ch_get  = curl_init();
        curl_setopt($ch_get, CURLOPT_URL, $get_url);
        curl_setopt($ch_get, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_get, CURLOPT_CONNECTTIMEOUT, $this->connect_timeout);
        curl_setopt($ch_get, CURLOPT_TIMEOUT, $this->timeout);
        curl_setopt($ch_get, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch_get, CURLOPT_SSL_VERIFYHOST, false);

        $get_headers = ['Accept: text/event-stream'];
        if (!empty($this->bearer_token)) {
            $get_headers[] = 'Authorization: Bearer ' . $this->bearer_token;
        }
        curl_setopt($ch_get, CURLOPT_HTTPHEADER, $get_headers);

        $stream_response = curl_exec($ch_get);
        $get_code        = curl_getinfo($ch_get, CURLINFO_HTTP_CODE);
        $get_err         = curl_error($ch_get);
        curl_close($ch_get);

        if ($get_err) {
            return [
                'success'   => false,
                'http_code' => 0,
                'data'      => null,
                'error'     => 'Timeout atau kegagalan membaca respons Hugging Face: ' . $get_err,
            ];
        }

        if ($get_code < 200 || $get_code >= 300) {
            return [
                'success'   => false,
                'http_code' => $get_code,
                'data'      => null,
                'error'     => 'Layanan Hugging Face mengembalikan status HTTP ' . $get_code,
            ];
        }

        // Step 3: Extract SSE payload
        $data_payload = $this->_extract_sse_data($stream_response);
        if ($data_payload === null) {
            return [
                'success'   => false,
                'http_code' => $get_code,
                'data'      => null,
                'error'     => 'Respons stream dari Hugging Face tidak terbaca atau kosong.',
            ];
        }

        // If payload is already a structured array
        if (is_array($data_payload) && isset($data_payload['sentiment_summary'])) {
            return [
                'success'   => true,
                'http_code' => 200,
                'data'      => $data_payload,
                'error'     => null,
            ];
        }

        // Unwrap first element if array of string
        $raw_text = is_array($data_payload) ? ($data_payload[0] ?? '') : $data_payload;
        if (!is_string($raw_text)) {
            $raw_text = json_encode($data_payload);
        }

        // Step 4: Parse summary text into standard sentiment structure
        $parsed = $this->_parse_gradio_summary($raw_text);
        if (!$parsed['success']) {
            return [
                'success'   => false,
                'http_code' => 422,
                'data'      => null,
                'error'     => $parsed['error'],
            ];
        }

        return [
            'success'   => true,
            'http_code' => 200,
            'data'      => $parsed['data'],
            'error'     => null,
        ];
    }

    /**
     * Extract JSON data payload from a Gradio Server-Sent Events (SSE) stream.
     *
     * @param string $stream
     * @return mixed
     */
    protected function _extract_sse_data($stream)
    {
        // 1. Match event: complete followed by data: [...]
        if (preg_match('/event:\s*complete\s*\ndata:\s*(\[.*?\])(?=\n\n|\n\z|\z)/s', $stream, $matches)) {
            $decoded = json_decode($matches[1], true);
            if ($decoded !== null) {
                return $decoded;
            }
        }

        // 2. Fallback: match any data: [...] block in the stream
        if (preg_match_all('/data:\s*(\[.*?\])/s', $stream, $matches)) {
            $last_match = end($matches[1]);
            $decoded = json_decode($last_match, true);
            if ($decoded !== null) {
                return $decoded;
            }
        }

        // 3. Fallback: direct JSON response
        $direct = json_decode($stream, true);
        if (is_array($direct)) {
            return $direct;
        }

        return null;
    }

    /**
     * Parse formatted text returned by Gradio interface into structured array.
     *
     * @param string $text
     * @return array ['success' => bool, 'data' => array|null, 'error' => string|null]
     */
    protected function _parse_gradio_summary($text)
    {
        $clean = trim($text);

        // Check for error prefixes returned by the microservice
        if (strpos($clean, 'Terjadi kesalahan:') === 0 || 
            strpos($clean, 'Silakan masukkan URL') === 0 || 
            strpos($clean, 'Tidak ada komentar') === 0) {
            return [
                'success' => false,
                'data'    => null,
                'error'   => $clean,
            ];
        }

        // 1. Total comments
        $total_comments = 0;
        if (preg_match('/TOTAL KOMENTAR DIBERSIHKAN:\s*(\d+)/i', $clean, $m)) {
            $total_comments = (int) $m[1];
        }

        // 2. Sentiment summary (positive, neutral, negative)
        $positive = 0;
        $neutral  = 0;
        $negative = 0;
        if (preg_match('/Positif\s*:\s*(\d+)/i', $clean, $m)) {
            $positive = (int) $m[1];
        }
        if (preg_match('/Netral\s*:\s*(\d+)/i', $clean, $m)) {
            $neutral = (int) $m[1];
        }
        if (preg_match('/Negatif\s*:\s*(\d+)/i', $clean, $m)) {
            $negative = (int) $m[1];
        }

        // 3. Main topics discussed
        $main_topics = null;
        if (preg_match('/TOPIK UTAMA \(MAIN TOPICS\):\s*\n(.*?)(?=\n\s*KATA KUNCI TERBANYAK:|\z)/s', $clean, $m)) {
            $main_topics = trim($m[1]) ?: null;
        }

        // 4. Frequent keywords
        $keywords = [];
        if (preg_match('/KATA KUNCI TERBANYAK:\s*\n(.*?)(?=\n\s*HIGHLIGHT KOMENTAR POSITIF:|\z)/s', $clean, $m)) {
            $kw_str = trim($m[1]);
            if ($kw_str !== '') {
                $raw_kws = explode(',', $kw_str);
                foreach ($raw_kws as $kw) {
                    $trimmed = trim($kw);
                    if ($trimmed !== '') {
                        $keywords[] = $trimmed;
                    }
                }
            }
        }

        // 5. Highlight positive comments
        $highlights_pos = [];
        if (preg_match('/HIGHLIGHT KOMENTAR POSITIF:\s*\n(.*?)(?=\n\s*HIGHLIGHT KOMENTAR NEGATIF:|\z)/s', $clean, $m)) {
            $lines = explode("\n", trim($m[1]));
            foreach ($lines as $line) {
                $line = preg_replace('/^\s*\+\s*/', '', trim($line));
                if ($line !== '') {
                    $highlights_pos[] = $line;
                }
            }
        }

        // 6. Highlight negative comments
        $highlights_neg = [];
        if (preg_match('/HIGHLIGHT KOMENTAR NEGATIF:\s*\n(.*)$/s', $clean, $m)) {
            $lines = explode("\n", trim($m[1]));
            foreach ($lines as $line) {
                $line = preg_replace('/^\s*-\s*/', '', trim($line));
                if ($line !== '') {
                    $highlights_neg[] = $line;
                }
            }
        }

        return [
            'success' => true,
            'data'    => [
                'total_comments_scraped' => $total_comments,
                'sentiment_summary'      => [
                    'positive' => $positive,
                    'neutral'  => $neutral,
                    'negative' => $negative,
                ],
                'main_topics_discussed'  => $main_topics,
                'frequent_keywords'      => $keywords,
                'highlight_comments'     => [
                    'positive' => $highlights_pos,
                    'neutral'  => [],
                    'negative' => $highlights_neg,
                ],
            ],
            'error'   => null,
        ];
    }

    // -------------------------------------------------------------------------
    // Internal Generic cURL Request Executor
    // -------------------------------------------------------------------------

    /**
     * Execute an HTTP request against the microservice.
     *
     * @param string     $endpoint  e.g. '/gradio_api/info' or '/api/v1/analyze'
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
        ];
        if (!empty($this->bearer_token)) {
            $headers[] = 'Authorization: Bearer ' . $this->bearer_token;
        }

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
            404 => 'Endpoint atau video tidak ditemukan. Periksa kembali URL target.',
            422 => 'Payload tidak valid: ' . ($svc_message ?: 'Periksa kembali parameter platform dan URL.'),
            502 => 'Hugging Face gagal berkomunikasi dengan scraper atau layanan IndoBERT. Coba beberapa saat lagi.',
            503 => 'Layanan Hugging Face sedang booting (cold start). Silakan coba 1-2 menit lagi.',
            504 => 'Gateway Timeout dari server Hugging Face saat menganalisis komentar.',
        ];

        if (isset($known[$http_code])) {
            return $known[$http_code];
        }

        return 'HTTP ' . $http_code . ($svc_message ? ': ' . $svc_message : ' — Terjadi kesalahan pada microservice Hugging Face.');
    }
}
