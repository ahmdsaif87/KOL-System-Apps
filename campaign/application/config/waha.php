<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| WAHA (WhatsApp HTTP API) Configuration
|--------------------------------------------------------------------------
|
| Configuration for WhatsApp integration via WAHA microservice.
| Reference: https://waha.devlike.pro / WhatsApp HTTP API
|
*/

// WAHA / Node.js WhatsApp Gateway Base URL (port 3001 for native Baileys service)
// Can be overridden dynamically from `cc_options` with key 'waha_base_url'
$config['waha_base_url'] = 'http://localhost:3001';

// WAHA API Key (optional header X-Api-Key)
// Can be overridden from `cc_options` with key 'waha_api_key'
$config['waha_api_key'] = '';

// Default active session name
$config['waha_default_session'] = 'default';

// cURL connection timeout in seconds
$config['waha_connect_timeout'] = 2;

// cURL total timeout in seconds
$config['waha_timeout'] = 6;

// Secret token for incoming webhook verification (optional)
$config['waha_webhook_secret'] = '';
