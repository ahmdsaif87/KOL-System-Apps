<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Campaign Articles REST API Base URL
|--------------------------------------------------------------------------
| Mengikuti endpoint Campaign secara dinamis:
| - Lokal: base_url('campaign') => http://localhost:8087/campaign
| - Hosting: base_url('campaign') => https://kolab.metaforacorp.com/campaign
| Bisa di-override lewat environment variable LANDING_API_BASE jika diperlukan.
*/
$api_base = getenv('LANDING_API_BASE');

if ($api_base !== false && $api_base !== '') {
    $config['articles_api_base'] = rtrim($api_base, '/');
} elseif (function_exists('base_url')) {
    $config['articles_api_base'] = rtrim(base_url('campaign'), '/');
} else {
    $is_https = (! empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https')
        || (isset($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] === 'on')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    $scheme = $is_https ? 'https' : 'http';
    $host = ! empty($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost:8087';
    $config['articles_api_base'] = $scheme . '://' . $host . '/campaign';
}

$config['articles_api_connect_timeout'] = 2;
$config['articles_api_timeout'] = 5;
$config['articles_api_max_sitemap_pages'] = 40;
