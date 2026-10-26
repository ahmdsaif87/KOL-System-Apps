<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Autoload
|--------------------------------------------------------------------------
| Landing sepenuhnya mengonsumsi REST API (/campaign/articles/feed) via cURL,
| tidak memiliki koneksi maupun konfigurasi database langsung.
*/
$autoload['packages'] = array();
$autoload['libraries'] = array();
$autoload['drivers'] = array();
$autoload['helper'] = array('url', 'text');
$autoload['config'] = array();
$autoload['language'] = array();
$autoload['model'] = array();
