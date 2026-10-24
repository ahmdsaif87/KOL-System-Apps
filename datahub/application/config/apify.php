<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Apify Configuration
|--------------------------------------------------------------------------
|
| Configuration for the Apify scraping service.
| Defaulted to cost-optimized high-efficiency actors (API Dojo & Streamers)
| Saving ~75-80% on scraping overhead.
|
*/

// Apify API token — override via cc_options ('apify_api_token') or set here directly.
// cc_options value takes precedence if set.
// Apify API token — SET LEWAT DB: cc_options ('apify_api_token'), ATAU isi di
// sini di server (JANGAN commit token asli ke repo).
$config['apify_api_token']   = ''; // placeholder — diisi di server

// Apify REST API base URL (v2)
$config['apify_base_url']    = 'https://api.apify.com/v2';

// Maximum retry attempts per posting before giving up
$config['apify_max_retries'] = 3;

// cURL timeout in seconds for the synchronous API call
$config['apify_timeout']     = 240;

// Mapping: platforms.code => Apify actor ID
// Defaulted to cost-optimized high-efficiency actors
$config['apify_actors'] = [
    'TIKTOK'    => 'apidojo~tiktok-profile-scraper-api',
    'INSTAGRAM' => 'apidojo~instagram-scraper-api',
    'YOUTUBE'   => 'streamers~youtube-channel-scraper',
    'TWITTER'   => 'apidojo~tweet-scraper',
    'X'         => 'apidojo~tweet-scraper',
    'FACEBOOK'  => 'apify~facebook-posts-scraper',
];

// Mapping: platforms.code => Apify PROFILE actor ID
// Used for scraping overall KOL stats (followers, avg_views, avg_er)
$config['apify_profile_actors'] = [
    'TIKTOK'    => 'apidojo~tiktok-profile-scraper-api',
    'INSTAGRAM' => 'apidojo~instagram-scraper-api',
    'YOUTUBE'   => 'streamers~youtube-channel-scraper',
    'TWITTER'   => 'apidojo~tweet-scraper',
    'X'         => 'apidojo~tweet-scraper',
    'FACEBOOK'  => 'apify~facebook-pages-scraper',
];

// Field mapping: Apify JSON response key => posting_metrics column
$config['apify_field_map'] = [
    'TIKTOK' => [
        'views'    => 'views',
        'likes'    => 'likes',
        'comments' => 'comments',
        'shares'   => 'shares',
        'saves'    => 'bookmarks',
    ],
    'INSTAGRAM' => [
        'views'    => 'playCount',
        'likes'    => 'likesCount',
        'comments' => 'commentsCount',
        'shares'   => 'sharesCount',
        'saves'    => 'savesCount',
    ],
    'YOUTUBE' => [
        'views'    => 'viewCount',
        'likes'    => 'likes',
        'comments' => 'commentsCount',
        'shares'   => 'shareCount',
        'saves'    => 'savesCount',
    ],
    'TWITTER' => [
        'views'    => 'viewCount',
        'likes'    => 'likeCount',
        'comments' => 'replyCount',
        'shares'   => 'retweetCount',
        'saves'    => 'bookmarkCount',
    ],
    'X' => [
        'views'    => 'viewCount',
        'likes'    => 'likeCount',
        'comments' => 'replyCount',
        'shares'   => 'retweetCount',
        'saves'    => 'bookmarkCount',
    ],
    'FACEBOOK' => [
        'views'    => 'views',
        'likes'    => 'likes',
        'comments' => 'comments',
        'shares'   => 'shares',
        'saves'    => 'saves',
    ],
];
