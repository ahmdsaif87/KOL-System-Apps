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

// Base URL & token diatur via DB cc_options (sentiment_base_url / sentiment_bearer_token),
// MIRIP dengan KeyAPI & Apify. Nilai di sini hanya fallback INERT (kosong).
$config['sentiment_base_url'] = '';
// Bearer token untuk Authorization header
$config['sentiment_bearer_token'] = '';

// cURL connect timeout in seconds (connection establishment only)
$config['sentiment_connect_timeout'] = 10;

// cURL total request timeout in seconds (scraping + inference may take 10–25s)
$config['sentiment_timeout'] = 60;

// Maximum number of comments to scrape & analyze per request (1–1000)
$config['sentiment_comment_limit'] = 500;

// Minimum minutes between re-analyses for the same task (cooldown)
$config['sentiment_cooldown_minutes'] = 5;
