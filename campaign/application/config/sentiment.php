<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Sentiment Analysis Microservice Config
|--------------------------------------------------------------------------
| Configuration for the KOL Sentiment Analysis Microservice (IndoBERT).
| Model: Aardiiiiy/indoberttweet-base-indonesian-sentiment-analysis
|
*/

// Base URL Hugging Face Spaces Gradio API (default).
// Dapat juga di-override via DB cc_options ('sentiment_base_url').
$config['sentiment_base_url'] = 'https://ahmdsaif-kol-sentiment-api.hf.space';
// Bearer token untuk Authorization header (diisi jika Space Private, misal token hf_...)
$config['sentiment_bearer_token'] = '';

// cURL connect timeout in seconds (connection establishment only)
$config['sentiment_connect_timeout'] = 15;

// cURL total request timeout in seconds (scraping + ZeroGPU inference may take 30–90s)
$config['sentiment_timeout'] = 120;

// Maximum number of comments to scrape & analyze per request (10–500)
$config['sentiment_comment_limit'] = 500;

// Minimum minutes between re-analyses for the same task (cooldown)
$config['sentiment_cooldown_minutes'] = 5;
