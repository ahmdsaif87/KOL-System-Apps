<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * KeyAPI (keyapi.ai) TikTok Shop Analytics Configuration
 *
 * Replaces Kalodata & FastMoss.
 * Base URL: https://api.keyapi.ai
 * Auth: Bearer Token
 */
$config['keyapi_base_url']        = 'https://api.keyapi.ai';
// KeyAPI API Key — SET LEWAT DB: cc_options ('keyapi_api_key') lewat admin
// Keyapi Config, ATAU isi di sini di server (JANGAN commit kunci asli ke repo).
$config['keyapi_api_key']         = ''; // placeholder — diisi di server
$config['keyapi_cache_days']      = 7;
$config['keyapi_max_products']    = 20;
$config['keyapi_usd_to_idr_rate'] = 16150;
