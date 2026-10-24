<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Apify_profile_service extends MX_Controller {

    public function __construct()
    {
        parent::__construct();
        // Ensure this is only run from CLI
        if (!is_cli()) {
            show_error('CLI access only', 403);
        }
        $this->load->model('Apify_model');
        $this->load->config('apify');
    }

    public function run()
    {
        echo "Starting Apify Profile Scraper Service...\n";

        $profiles = $this->Apify_model->get_due_profiles(10);
        if (empty($profiles)) {
            echo "No profiles due for scraping.\n";
            return;
        }

        echo "Found " . count($profiles) . " profiles to scrape.\n";

        foreach ($profiles as $p) {
            echo "Scraping {$p->platform_code} profile: {$p->username}...\n";
            $this->_scrape_profile($p);
            sleep(2); // Rate limiting
        }

        echo "Scraping complete.\n";
    }

    private function _scrape_profile($profile)
    {
        $platform = strtoupper($profile->platform_code);
        $actors = $this->config->item('apify_profile_actors');
        
        $actor_option_key = 'apify_profile_actor_' . strtolower($platform);
        $actor = get_option($actor_option_key) ?: ($actors[$platform] ?? null);
        
        if (empty($actor)) {
            echo "Error: No profile actor configured for $platform\n";
            return;
        }

        $token = get_option('apify_api_token') ?: $this->config->item('apify_api_token');
        $base_url = get_option('apify_base_url') ?: ($this->config->item('apify_base_url') ?: 'https://api.apify.com/v2');
        $resultsLimit = (int)(get_option('apify_results_limit') ?: 30);

        $clean_username = ltrim(trim($profile->username), '@');

        // Prepare payload based on platform and actor type
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
        }

        // 1. Trigger the Actor Sync (waits for dataset)
        $url = "{$base_url}/acts/{$actor}/runs?token={$token}&waitSecs=120";
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 240);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($http_code !== 201 && $http_code !== 200) {
            echo "Error: Apify run failed with code $http_code\n";
            echo $response . "\n";
            return;
        }

        $runData = json_decode($response, true);
        if (!isset($runData['data']['defaultDatasetId'])) {
            echo "Error: Could not retrieve dataset ID\n";
            return;
        }

        $datasetId = $runData['data']['defaultDatasetId'];

        // 2. Fetch Dataset Items
        $datasetUrl = "{$base_url}/datasets/{$datasetId}/items?token={$token}";
        $itemsJson = @file_get_contents($datasetUrl);
        if ($itemsJson === false) {
            echo "Error: Failed to download dataset\n";
            return;
        }
        $items = json_decode($itemsJson, true);

        if (empty($items)) {
            echo "Error: Dataset is empty\n";
            return;
        }

        $this->_calculate_and_save($profile, $items, $platform);
    }

    private function _calculate_and_save($profile, $items, $platform)
    {
        $followers = 0;
        $total_views = 0;
        $total_engagements = 0;
        $post_count = 0;

        foreach ($items as $item) {
            // Extract followers until non-zero
            if ($followers === 0) {
                if ($platform === 'TIKTOK') {
                    if (isset($item['profile']['followerCount'])) {
                        $followers = (int) $item['profile']['followerCount'];
                    } elseif (isset($item['authorMeta']['fans'])) {
                        $followers = (int) $item['authorMeta']['fans'];
                    } elseif (isset($item['followerCount'])) {
                        $followers = (int) $item['followerCount'];
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
                    $post_count++;
                }
                continue;
            }

            // Extract views
            $views = 0;
            if ($platform === 'TIKTOK') {
                $views = (int) ($item['views'] ?? ($item['playCount'] ?? 0));
            } elseif ($platform === 'INSTAGRAM') {
                $views = (int) ($item['playCount'] ?? ($item['videoViewCount'] ?? ($item['video_view_count'] ?? 0)));
            } elseif ($platform === 'YOUTUBE') {
                $views = (int) ($item['viewCount'] ?? 0);
            }
            $total_views += $views;

            // Extract engagements
            $engagements = 0;
            if ($platform === 'TIKTOK') {
                $engagements += isset($item['likes']) ? (int)$item['likes'] : (isset($item['diggCount']) ? (int)$item['diggCount'] : 0);
                $engagements += isset($item['comments']) ? (int)$item['comments'] : (isset($item['commentCount']) ? (int)$item['commentCount'] : 0);
                $engagements += isset($item['shares']) ? (int)$item['shares'] : (isset($item['shareCount']) ? (int)$item['shareCount'] : 0);
                $engagements += isset($item['bookmarks']) ? (int)$item['bookmarks'] : (isset($item['collectCount']) ? (int)$item['collectCount'] : 0);
            } elseif ($platform === 'INSTAGRAM') {
                $engagements += isset($item['likesCount']) ? (int)$item['likesCount'] : (isset($item['likeCount']) ? (int)$item['likeCount'] : (isset($item['likes_count']) ? (int)$item['likes_count'] : 0));
                $engagements += isset($item['commentsCount']) ? (int)$item['commentsCount'] : (isset($item['commentCount']) ? (int)$item['commentCount'] : (isset($item['comments_count']) ? (int)$item['comments_count'] : 0));
            } elseif ($platform === 'YOUTUBE') {
                $engagements += isset($item['likes']) ? (int)$item['likes'] : 0;
                $engagements += isset($item['commentsCount']) ? (int)$item['commentsCount'] : (isset($item['comments']) ? (int)$item['comments'] : 0);
            }
            $total_engagements += $engagements;
            
            $post_count++;
        }

        $avg_views = $post_count > 0 ? $total_views / $post_count : 0;
        $avg_er = $total_views > 0 ? $total_engagements / $total_views : 0;

        echo "  - Followers: $followers\n";
        echo "  - Avg Views (Last 30): " . number_format($avg_views, 2) . "\n";
        echo "  - ER (Last 30): " . number_format($avg_er * 100, 2) . "%\n";

        $success = $this->Apify_model->save_metrics(
            $profile->kol_platform_id,
            $profile->kol_id,
            $profile->platform_id,
            $followers,
            $avg_views,
            $avg_er,
            $post_count
        );

        if ($success) {
            echo "  > Saved successfully.\n";
        } else {
            echo "  > Failed to save.\n";
        }
    }

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
