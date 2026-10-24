<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
|--------------------------------------------------------------------------
| Apify Configuration
|--------------------------------------------------------------------------
|
| Configuration for the Apify scraping service (Sprint 4).
| Used by application/controllers/Apify_service.php
|
*/

// Apify API token — override via cc_options ('apify_api_token') or set here directly.
// cc_options value takes precedence if set.
$config['apify_api_token']   = '';

// Apify REST API base URL (v2)
$config['apify_base_url']    = 'https://api.apify.com/v2';

// Maximum retry attempts per posting before giving up
$config['apify_max_retries'] = 3;

// cURL timeout in seconds for the synchronous API call
$config['apify_timeout']     = 240;

// Mapping: platforms.code => Apify actor ID
// Add new platform scrapers here as needed.
$config['apify_actors'] = [
    'TIKTOK'    => 'clockworks~tiktok-scraper',
    'INSTAGRAM' => 'apify~instagram-scraper',
    'YOUTUBE'   => 'streamers~youtube-scraper',
    'TWITTER'   => 'apidojo~tweet-scraper',
    'X'         => 'apidojo~tweet-scraper',
    'FACEBOOK'  => 'apify~facebook-posts-scraper',
];

// Mapping: platforms.code => Apify PROFILE actor ID
// Used for scraping overall KOL stats (followers, avg_views, avg_er)
$config['apify_profile_actors'] = [
    'TIKTOK'    => 'clockworks~tiktok-profile-scraper',
    'INSTAGRAM' => 'apify~instagram-profile-scraper',
    'YOUTUBE'   => 'streamers~youtube-channel-scraper',
    'TWITTER'   => 'apidojo~tweet-scraper',
    'X'         => 'apidojo~tweet-scraper',
    'FACEBOOK'  => 'apify~facebook-pages-scraper',
];

// Field mapping: Apify JSON response key => posting_metrics column
// TikTok response fields from clockworks~tiktok-scraper
$config['apify_field_map'] = [
    'TIKTOK' => [
        'views'    => 'playCount',
        'likes'    => 'diggCount',
        'comments' => 'commentCount',
        'shares'   => 'shareCount',
        'saves'    => 'collectCount',
    ],
    'INSTAGRAM' => [
        'views'    => 'videoViewCount',
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
