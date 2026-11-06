<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Sentiment Analysis Microservice Config
|--------------------------------------------------------------------------
| Configuration for the KOL Sentiment Analysis Microservice (IndoBERT).
| Model: Aardiiiiy/indoberttweet-base-indonesian-sentiment-analysis
| Direct Host: https://ahmdsaif-kol-sentiment-api.hf.space
|
*/

// Base URL & token diatur via DB cc_options (sentiment_base_url / sentiment_bearer_token).
// Nilai di sini adalah fallback default jika opsi database belum diisi.
$config['sentiment_base_url'] = 'https://ahmdsaif-kol-sentiment-api.hf.space';

// Bearer token untuk Authorization header (diisi jika Space Private atau memerlukan auth)
$config['sentiment_bearer_token'] = 'kol-system';

// cURL connect timeout in seconds (connection establishment & TLS handshake)
$config['sentiment_connect_timeout'] = 30;

// cURL total request timeout in seconds (scraping + ZeroGPU / IndoBERT inference may take 30–90s)
$config['sentiment_timeout'] = 120;

// Maximum number of comments to scrape & analyze per request (10–500)
$config['sentiment_comment_limit'] = 500;

// Minimum minutes between re-analyses for the same task (cooldown)
$config['sentiment_cooldown_minutes'] = 1;
