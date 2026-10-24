<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_scraping extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get paginated snapshot history for the Scraping Monitor.
     */
    public function get_snapshots($filter = [], $limit = 20, $offset = 0)
    {
        $this->db->select('
            kp.id,
            kp.kol_id,
            k.name as kol_name,
            p.name as platform_name,
            p.code as platform_code,
            kp.followers as followers_count,
            kp.avg_views as average_views_last_30,
            kp.avg_er as average_er_last_30,
            0 as total_posts,
            kp.last_synced_at as snapshot_date,
            kp.created_at,
            kp.username,
            kp.profile_url
        ');
        $this->db->from('kol_platforms kp');
        $this->db->join('kols k', 'k.id = kp.kol_id', 'left');
        $this->db->join('platforms p', 'p.id = kp.platform_id', 'left');
        $this->db->where('kp.is_active', 1);

        $this->_apply_filters($filter);

        $this->db->order_by('k.name', 'ASC');
        $this->db->limit($limit, $offset);

        return $this->db->get()->result();
    }

    /**
     * Count total snapshots for pagination.
     */
    public function count_snapshots($filter = [])
    {
        $this->db->from('kol_platforms kp');
        $this->db->join('kols k', 'k.id = kp.kol_id', 'left');
        $this->db->join('platforms p', 'p.id = kp.platform_id', 'left');
        $this->db->where('kp.is_active', 1);

        $this->_apply_filters($filter);

        return $this->db->count_all_results();
    }

    /**
     * Apply common filters to snapshot queries.
     */
    private function _apply_filters($filter)
    {
        if (!empty($filter['platform_code'])) {
            $this->db->where('p.code', strtoupper($filter['platform_code']));
        }
        if (!empty($filter['kol_name'])) {
            $this->db->like('k.name', $filter['kol_name']);
        }
        if (!empty($filter['date_from'])) {
            $this->db->where('kp.last_synced_at >=', $filter['date_from']);
        }
        if (!empty($filter['date_to'])) {
            $this->db->where('kp.last_synced_at <=', $filter['date_to'] . ' 23:59:59');
        }
    }

    /**
     * Get all active kol_platforms for a specific KOL.
     */
    public function get_platforms_for_kol($kol_id)
    {
        $this->db->select('kp.id as kol_platform_id, kp.kol_id, kp.platform_id, kp.username, kp.profile_url, p.code as platform_code');
        $this->db->from('kol_platforms kp');
        $this->db->join('platforms p', 'p.id = kp.platform_id');
        $this->db->where('kp.kol_id', $kol_id);
        $this->db->where('kp.is_active', 1);
        $this->db->where_in('p.code', ['TIKTOK', 'INSTAGRAM', 'YOUTUBE']);

        return $this->db->get()->result();
    }

    /**
     * Get all stale KOL IDs (last_synced_at > 7 days or NULL).
     */
    public function get_stale_kol_ids()
    {
        $this->db->select('DISTINCT(kp.kol_id) as kol_id');
        $this->db->from('kol_platforms kp');
        $this->db->join('platforms p', 'p.id = kp.platform_id');
        $this->db->where('kp.is_active', 1);
        $this->db->where_in('p.code', ['TIKTOK', 'INSTAGRAM', 'YOUTUBE']);
        $this->db->group_start();
        $this->db->where('kp.last_synced_at IS NULL');
        $this->db->or_where('kp.last_synced_at <', date('Y-m-d H:i:s', strtotime('-7 days')));
        $this->db->group_end();

        $result = $this->db->get()->result();
        return array_column(json_decode(json_encode($result), true), 'kol_id');
    }

    /**
     * Force-scrape a single kol_platform record via Apify.
     * Reuses the Apify cURL logic from Apify_profile_service.
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public function force_scrape($profile)
    {
        $max_retries = (strtoupper($profile->platform_code) === 'TIKTOK') ? 3 : 1;
        $attempt = 0;
        $result = ['success' => false, 'message' => 'Init'];
        
        while ($attempt < $max_retries) {
            $result = $this->_do_scrape($profile);
            if ($result['success']) {
                break;
            }
            $attempt++;
            if ($attempt < $max_retries) {
                sleep(3); // Wait before retry
            }
        }
        return $result;
    }

    private function _do_scrape($profile)
    {
        $this->config->load('apify', TRUE);
        $config = $this->config->item('apify') ?: [];

        $platform = strtoupper($profile->platform_code);
        $default_actors = isset($config['apify_actors']) ? $config['apify_actors'] : [];

        // Check if platform actor is configured in cc_options, fallback to config
        $actor_option_key = 'apify_actor_' . strtolower($platform);
        $actor = get_option($actor_option_key) ?: ($default_actors[$platform] ?? null);

        if (empty($actor)) {
            return ['success' => false, 'message' => "No actor configured for {$platform}"];
        }

        $token = get_option('apify_api_token') ?: ($config['apify_api_token'] ?? '');
        $base_url = get_option('apify_base_url') ?: ($config['apify_base_url'] ?? 'https://api.apify.com/v2');
        $timeout = (int)(get_option('apify_timeout') ?: ($config['apify_timeout'] ?? 240));
        $resultsLimit = (int)(get_option('apify_results_limit') ?: 30);

        // Sanitize clean handle & URL
        $clean_username = ltrim(trim($profile->username), '@');

        // Build payload per platform with multi-vendor support (API Dojo, Streamers, Apify default)
        $payload = [];
        if ($platform === 'TIKTOK') {
            if (strpos($actor, 'apidojo') !== false) {
                $payload = [
                    'profiles' => [$clean_username],
                    'maxItems' => $resultsLimit
                ];
            } else {
                $payload = [
                    'profiles' => [$profile->username],
                    'resultsPerPage' => $resultsLimit
                ];
            }
        } elseif ($platform === 'INSTAGRAM') {
            $url_val = !empty($profile->profile_url) ? $profile->profile_url : 'https://www.instagram.com/' . $clean_username . '/';
            if (strpos($actor, 'apidojo') !== false) {
                $payload = [
                    'startUrls' => [$url_val],
                    'resultsLimit' => $resultsLimit
                ];
            } else {
                $payload = [
                    'directUrls' => [$url_val],
                    'resultsType' => 'details'
                ];
            }
        } elseif ($platform === 'YOUTUBE') {
            $clean_handle = ltrim(trim($profile->username), '@');
            $url_val = !empty($profile->profile_url) ? trim($profile->profile_url) : 'https://www.youtube.com/@' . $clean_handle;
            if (!preg_match('/^https?:\/\//i', $url_val)) {
                $url_val = 'https://www.youtube.com/@' . ltrim($url_val, '@');
            }
            if (strpos($url_val, 'youtube.com') !== false && strpos($url_val, 'www.youtube.com') === false) {
                $url_val = str_replace('youtube.com', 'www.youtube.com', $url_val);
            }

            $payload = [
                'startUrls' => [['url' => $url_val]],
                'maxResults' => $resultsLimit
            ];
        } elseif ($platform === 'TWITTER' || $platform === 'X') {
            $payload = [
                'twitterHandles' => [$clean_username],
                'maxItems' => $resultsLimit
            ];
        } elseif ($platform === 'FACEBOOK') {
            $url_val = !empty($profile->profile_url) ? $profile->profile_url : 'https://facebook.com/' . $profile->username;
            $payload = [
                'startUrls' => [['url' => $url_val]],
                'maxPosts' => $resultsLimit
            ];
        }

        // Call Apify (Start the run)
        $url = "{$base_url}/acts/{$actor}/runs?token={$token}";

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, min($timeout, 60));

        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($ch);
        curl_close($ch);

        if ($http_code !== 201 && $http_code !== 200) {
            log_message('error', "Scraping failed for {$profile->username} on {$platform}. HTTP Code: {$http_code}. cURL Error: {$curl_error}. Response: {$response}");
            return ['success' => false, 'message' => "Apify returned HTTP {$http_code}: " . substr($response, 0, 100)];
        }

        $runData = json_decode($response, true);
        if (!isset($runData['data']['id'])) {
            log_message('error', "Scraping failed for {$profile->username}. No run ID. Raw response: {$response}");
            return ['success' => false, 'message' => 'No run ID in response'];
        }

        $runId = $runData['data']['id'];
        $datasetId = $runData['data']['defaultDatasetId'];

        // Wait for the run to finish (poll up to ~5 minutes)
        $status = 'RUNNING';
        $attempts = 0;
        while (in_array($status, ['READY', 'RUNNING', 'STARTING']) && $attempts < 30) {
            $waitUrl = "{$base_url}/actor-runs/{$runId}?token={$token}&waitForFinish=10";
            $ch = curl_init($waitUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $waitRes = curl_exec($ch);
            curl_close($ch);

            $waitData = json_decode($waitRes, true);
            if (isset($waitData['data']['status'])) {
                $status = $waitData['data']['status'];
            }
            $attempts++;
        }

        if ($status !== 'SUCCEEDED') {
            return ['success' => false, 'message' => "Apify run timed out or failed. Status: {$status}"];
        }

        $datasetUrl = "{$base_url}/datasets/{$datasetId}/items?token={$token}";
        $itemsJson = @file_get_contents($datasetUrl);
        
        if ($itemsJson === false) {
            return ['success' => false, 'message' => 'Failed to download dataset. URL: ' . $datasetUrl];
        }

        $items = json_decode($itemsJson, true);

        if (empty($items)) {
            return ['success' => false, 'message' => 'Dataset empty. Apify Run Status was: ' . $status];
        }

        // Calculate metrics
        $followers = 0;
        $total_views = 0;
        $total_engagements = 0;
        $post_count = 0;
        $sponsored_count = 0;
        $sponsored_keywords = [
            '#ad', '#sponsored', '#berbayar', '#partnership', '#collab', 'paid partnership', '#iklan', '#endorse',
            'checkout di', 'keranjang kuning', 'link di bio', 'spill barang', 'racun tiktok', 'racun shopee', 
            'promo', 'diskon', 'kode voucher', 'cashback', 'gratis ongkir', 
            'top up murah', 'topup', 'diamond', 'joki', 'kode affiliate', 'belinya di'
        ];

        foreach ($items as $item) {
            // 1. Follower Extraction (evaluated until non-zero)
            if ($followers === 0) {
                if ($platform === 'TIKTOK') {
                    if (isset($item['profile']['followerCount'])) {
                        $followers = (int) $item['profile']['followerCount'];
                    } elseif (isset($item['authorMeta']['fans'])) {
                        $followers = (int) $item['authorMeta']['fans'];
                    } elseif (isset($item['followerCount'])) {
                        $followers = (int) $item['followerCount'];
                    } elseif (isset($item['fans'])) {
                        $followers = (int) $item['fans'];
                    }
                } elseif ($platform === 'INSTAGRAM') {
                    if (isset($item['followerCount'])) {
                        $followers = (int) $item['followerCount'];
                    } elseif (isset($item['followersCount'])) {
                        $followers = (int) $item['followersCount'];
                    } elseif (isset($item['profile']['followerCount'])) {
                        $followers = (int) $item['profile']['followerCount'];
                    } elseif (isset($item['owner_followers'])) {
                        $followers = (int) $item['owner_followers'];
                    } elseif (isset($item['user']['follower_count'])) {
                        $followers = (int) $item['user']['follower_count'];
                    }
                } elseif ($platform === 'YOUTUBE') {
                    if (isset($item['subscribers'])) {
                        $followers = (int) $item['subscribers'];
                    } elseif (isset($item['subscriberCount'])) {
                        $followers = $this->_parse_compact_number($item['subscriberCount']);
                    } elseif (isset($item['channelFollowers'])) {
                        $followers = (int) $item['channelFollowers'];
                    } elseif (isset($item['subscribersCount'])) {
                        $followers = (int) $item['subscribersCount'];
                    } elseif (isset($item['numberOfSubscribers'])) {
                        $followers = (int) $item['numberOfSubscribers'];
                    }
                } elseif ($platform === 'TWITTER' || $platform === 'X') {
                    if (isset($item['user']['followers_count'])) {
                        $followers = (int) $item['user']['followers_count'];
                    } elseif (isset($item['followersCount'])) {
                        $followers = (int) $item['followersCount'];
                    }
                } elseif ($platform === 'FACEBOOK') {
                    if (isset($item['followers'])) {
                        $followers = (int) $item['followers'];
                    } elseif (isset($item['likes'])) {
                        $followers = (int) $item['likes'];
                    }
                }
            }

            // Instagram legacy nested latestPosts support
            if ($platform === 'INSTAGRAM' && !empty($item['latestPosts']) && is_array($item['latestPosts'])) {
                foreach ($item['latestPosts'] as $post) {
                    $post_views = $post['playCount'] ?? ($post['videoViewCount'] ?? ($post['video_view_count'] ?? 0));
                    $total_views += (int)$post_views;
                    $post_likes = $post['likesCount'] ?? ($post['likeCount'] ?? ($post['likes_count'] ?? 0));
                    $post_comments = $post['commentsCount'] ?? ($post['commentCount'] ?? ($post['comments_count'] ?? 0));
                    $total_engagements += ((int)$post_likes + (int)$post_comments);
                    
                    $caption = mb_strtolower($post['caption'] ?? '');
                    
                    $is_sponsored_native = !empty($post['isSponsored']) || !empty($post['sponsorTags']);
                    if ($is_sponsored_native) {
                        $sponsored_count++;
                    } else {
                        foreach ($sponsored_keywords as $kw) {
                            if (strpos($caption, $kw) !== false) {
                                $sponsored_count++;
                                break;
                            }
                        }
                    }
                    $post_count++;
                }
                continue;
            }

            // Standard post-level item metrics extraction (API Dojo / Streamers / Flat Datasets)
            $views = 0;
            if ($platform === 'TIKTOK') {
                $views = (int) ($item['views'] ?? ($item['playCount'] ?? 0));
            } elseif ($platform === 'INSTAGRAM') {
                $views = (int) ($item['playCount'] ?? ($item['videoViewCount'] ?? ($item['video_view_count'] ?? 0)));
            } elseif ($platform === 'YOUTUBE') {
                $views = (int) ($item['viewCount'] ?? 0);
            } elseif ($platform === 'TWITTER' || $platform === 'X') {
                $views = (int) ($item['viewCount'] ?? ($item['views'] ?? 0));
            } elseif ($platform === 'FACEBOOK') {
                $views = (int) ($item['views'] ?? 0);
            }
            $total_views += $views;

            $engagements = 0;
            if ($platform === 'TIKTOK') {
                $engagements += isset($item['likes']) ? (int)$item['likes'] : (isset($item['diggCount']) ? (int)$item['diggCount'] : 0);
                $engagements += isset($item['comments']) ? (int)$item['comments'] : (isset($item['commentCount']) ? (int)$item['commentCount'] : 0);
                $engagements += isset($item['shares']) ? (int)$item['shares'] : (isset($item['shareCount']) ? (int)$item['shareCount'] : 0);
                $engagements += isset($item['bookmarks']) ? (int)$item['bookmarks'] : (isset($item['collectCount']) ? (int)$item['collectCount'] : 0);
                
                $caption = mb_strtolower($item['title'] ?? ($item['text'] ?? ''));
                $is_sponsored_native = !empty($item['isAd']);
                if ($is_sponsored_native) {
                    $sponsored_count++;
                } else {
                    foreach ($sponsored_keywords as $kw) {
                        if (strpos($caption, $kw) !== false) {
                            $sponsored_count++;
                            break;
                        }
                    }
                }
            } elseif ($platform === 'INSTAGRAM') {
                $engagements += isset($item['likesCount']) ? (int)$item['likesCount'] : (isset($item['likeCount']) ? (int)$item['likeCount'] : (isset($item['likes_count']) ? (int)$item['likes_count'] : 0));
                $engagements += isset($item['commentsCount']) ? (int)$item['commentsCount'] : (isset($item['commentCount']) ? (int)$item['commentCount'] : (isset($item['comments_count']) ? (int)$item['comments_count'] : 0));
                
                $caption = mb_strtolower($item['caption'] ?? '');
                $is_sponsored_native = !empty($item['isSponsored']) || !empty($item['sponsorTags']);
                if ($is_sponsored_native) {
                    $sponsored_count++;
                } else {
                    foreach ($sponsored_keywords as $kw) {
                        if (strpos($caption, $kw) !== false) {
                            $sponsored_count++;
                            break;
                        }
                    }
                }
            } elseif ($platform === 'YOUTUBE') {
                $engagements += isset($item['likes']) ? (int)$item['likes'] : 0;
                $engagements += isset($item['commentsCount']) ? (int)$item['commentsCount'] : (isset($item['comments']) ? (int)$item['comments'] : 0);
                
                $caption = mb_strtolower(($item['videoTitle'] ?? ($item['title'] ?? '')) . ' ' . ($item['description'] ?? ($item['text'] ?? '')));
                foreach ($sponsored_keywords as $kw) {
                    if (strpos($caption, $kw) !== false) {
                        $sponsored_count++;
                        break;
                    }
                }
            } elseif ($platform === 'TWITTER' || $platform === 'X') {
                $engagements += isset($item['likeCount']) ? (int)$item['likeCount'] : (isset($item['likes']) ? (int)$item['likes'] : 0);
                $engagements += isset($item['replyCount']) ? (int)$item['replyCount'] : (isset($item['comments']) ? (int)$item['comments'] : 0);
                $engagements += isset($item['retweetCount']) ? (int)$item['retweetCount'] : (isset($item['retweets']) ? (int)$item['retweets'] : 0);
                $engagements += isset($item['bookmarkCount']) ? (int)$item['bookmarkCount'] : 0;
            } elseif ($platform === 'FACEBOOK') {
                $engagements += isset($item['likes']) ? (int)$item['likes'] : 0;
                $engagements += isset($item['comments']) ? (int)$item['comments'] : 0;
                $engagements += isset($item['shares']) ? (int)$item['shares'] : 0;
            }
            $total_engagements += $engagements;
            $post_count++;
        }

        $avg_views = $post_count > 0 ? $total_views / $post_count : 0;
        $avg_er = $total_views > 0 ? $total_engagements / $total_views : 0;
        $endorse_ratio = $post_count > 0 ? ($sponsored_count / $post_count) : 0;
        
        if ($followers === 0) {
            $msg = 'Failed to parse followers or profile empty/private. Raw JSON snippet: ' . substr($itemsJson, 0, 100);
            return ['success' => false, 'message' => $msg];
        }

        // Save snapshot + update kol_platforms
        $this->load->model('Apify_model');
        $saved = $this->Apify_model->save_metrics(
            $profile->kol_platform_id,
            $profile->kol_id,
            $profile->platform_id,
            $followers,
            $avg_views,
            $avg_er,
            $post_count
        );

        if ($saved) {
            return [
                'success' => true,
                'message' => 'OK',
                'data' => [
                    'followers' => $followers,
                    'avg_views' => round($avg_views, 2),
                    'avg_er' => round($avg_er, 4),
                    'endorse_ratio' => round($endorse_ratio * 100, 2),
                    'posts' => $post_count
                ]
            ];
        }

        return ['success' => false, 'message' => 'Failed to save metrics'];
    }

    /**
     * Parse compact count strings (e.g., '330M', '1.2K', '500') into integer.
     */
    private function _parse_compact_number($val)
    {
        if (is_numeric($val)) return (int)$val;
        $val = trim((string)$val);
        if ($val === '') return 0;
        $last = strtoupper(substr($val, -1));
        $num = (float)substr($val, 0, -1);
        switch ($last) {
            case 'B': return (int)($num * 1000000000);
            case 'M': return (int)($num * 1000000);
            case 'K': return (int)($num * 1000);
            default: return (int)$val;
        }
    }
}
