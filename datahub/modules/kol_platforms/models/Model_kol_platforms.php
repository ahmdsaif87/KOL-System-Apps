<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_kol_platforms extends MY_Model {

    private $primary_key    = 'id';
    private $table_name     = 'kol_platforms';
    private $field_search   = ['kol_id', 'platform_id', 'username', 'profile_url', 'followers', 'avg_views', 'avg_er', 'created_at', 'updated_at', 'is_verified', 'is_active', 'last_synced_at'];

    public function __construct()
    {
        $config = array(
            'primary_key'   => $this->primary_key,
            'table_name'    => $this->table_name,
            'field_search'  => $this->field_search,
         );

        parent::__construct($config);
    }

    public function count_all($q = null, $field = null)
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "kol_platforms.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "kol_platforms.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "kol_platforms.".$field . " LIKE '%" . $q . "%' )";
        }

        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $query = $this->db->get($this->table_name);

        return $query->num_rows();
    }

    public function get($q = null, $field = null, $limit = 0, $offset = 0, $select_field = [])
    {
        $iterasi = 1;
        $num = count($this->field_search);
        $where = NULL;
        $q = $this->scurity($q);
        $field = $this->scurity($field);

        if (empty($field)) {
            foreach ($this->field_search as $field) {
                if ($iterasi == 1) {
                    $where .= "kol_platforms.".$field . " LIKE '%" . $q . "%' ";
                } else {
                    $where .= "OR " . "kol_platforms.".$field . " LIKE '%" . $q . "%' ";
                }
                $iterasi++;
            }

            $where = '('.$where.')';
        } else {
            $where .= "(" . "kol_platforms.".$field . " LIKE '%" . $q . "%' )";
        }

        if (is_array($select_field) AND count($select_field)) {
            $this->db->select($select_field);
        }
        
        $this->join_avaiable()->filter_avaiable();
        $this->db->where($where);
        $this->db->limit($limit, $offset);
                $this->db->order_by('kol_platforms.'.$this->primary_key, "DESC");
                $query = $this->db->get($this->table_name);

        return $query->result();
    }

    public function join_avaiable() {
        $this->db->join('kols', 'kols.id = kol_platforms.kol_id', 'LEFT');
        $this->db->join('platforms', 'platforms.id = kol_platforms.platform_id', 'LEFT');
        
        $this->db->select('kol_platforms.*,kols.name as kols_name,platforms.name as platforms_name');


        return $this;
    }

    public function filter_avaiable() {

        if (!$this->aauth->is_admin()) {
            }

        return $this;
    }

    /**
     * Sync a KOL platform profile using Apify to update followers, avg_views, and avg_er.
     * Uses the 10-30 most recent videos returned by the profile scraper to calculate averages.
     *
     * @param int $id The kol_platforms ID
     * @return array|bool Array with success status and message, or false on hard failure
     */
    public function sync_profile($id)
    {
        // Get the kol_platform details
        $this->db->select('kp.*, p.code as platform_code');
        $this->db->from('kol_platforms kp');
        $this->db->join('platforms p', 'p.id = kp.platform_id', 'left');
        $this->db->where('kp.id', $id);
        $platform = $this->db->get()->row();

        if (!$platform) {
            return ['success' => false, 'message' => 'KOL Platform not found.'];
        }

        if (empty($platform->username)) {
            return ['success' => false, 'message' => 'Username is empty. Cannot sync.'];
        }

        $result = $this->sync_profile_raw($platform->platform_code, $platform->username);

        if ($result['success']) {
            $this->db->update('kol_platforms', [
                'followers' => $result['data']['followers'],
                'avg_views' => $result['data']['avg_views'],
                'avg_er'    => $result['data']['avg_er'],
                'last_synced_at' => date('Y-m-d H:i:s')
            ], ['id' => $id]);
        }

        return $result;
    }

    /**
     * Call Apify to scrape profile stats directly based on platform code and username.
     * 
     * @param string $platform_code (e.g. 'TIKTOK', 'INSTAGRAM')
     * @param string $username The social media handle
     * @return array
     */
    public function sync_profile_raw($platform_code, $username)
    {
        $this->load->config('apify', TRUE);
        $cfg = $this->config->item('apify') ?: [];
        
        $api_token = '';
        if (function_exists('get_option')) {
            $api_token = get_option('apify_api_token');
        }
        if (empty($api_token)) {
            $api_token = $cfg['apify_api_token'] ?? '';
        }

        if (empty($api_token)) {
            return ['success' => false, 'message' => 'Apify API token is missing. Please configure it in Settings.'];
        }

        $base_url = $cfg['apify_base_url'] ?? 'https://api.apify.com/v2';
        $profile_actors = $cfg['apify_profile_actors'] ?? [];
        $timeout = 25; // 25s per platform to avoid hanging Apache worker threads

        $platform_code = strtoupper(trim($platform_code));
        $actor_id = $profile_actors[$platform_code] ?? null;

        if (!$actor_id) {
            return ['success' => false, 'message' => "No Apify Profile Actor configured for platform: {$platform_code}"];
        }

        $url = rtrim($base_url, '/') 
             . '/actors/' . urlencode($actor_id) 
             . '/run-sync-get-dataset-items'
             . '?token=' . urlencode($api_token);

        $clean_un = ltrim(trim($username), '@');
        if ($platform_code === 'YOUTUBE') {
            $yt_url = (strpos($username, 'http') === 0) 
                ? $username 
                : ('https://www.youtube.com/' . (strpos($username, '@') === 0 ? $username : '@' . $clean_un));
            $payload_arr = [
                'startUrls'        => [['url' => $yt_url]],
                'channelUrls'      => [$yt_url],
                'maxResults'       => 20,
                'maxResultsShorts' => 10,
                'resultsPerPage'   => 20
            ];
        } elseif ($platform_code === 'TWITTER' || $platform_code === 'X') {
            $payload_arr = [
                'searchMode'     => 'live',
                'twitterHandles' => [$clean_un],
                'usernames'      => [$clean_un],
                'handles'        => [$clean_un],
                'searchTerms'    => ["from:{$clean_un}"],
                'maxItems'       => 20,
                'maxTweets'      => 20,
                'tweetsDesired'  => 20,
                'resultsPerPage' => 20
            ];
        } elseif ($platform_code === 'FACEBOOK') {
            $fb_url = (strpos($username, 'http') === 0) 
                ? $username 
                : ('https://www.facebook.com/' . $clean_un);
            $payload_arr = [
                'startUrls'      => [['url' => $fb_url]],
                'usernames'      => [$clean_un],
                'resultsPerPage' => 20
            ];
        } else {
            $payload_arr = [
                'usernames'      => [$clean_un],
                'profiles'       => [$clean_un],
                'resultsPerPage' => 30
            ];
        }

        $payload = json_encode($payload_arr);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => $timeout,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response  = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curl_err  = curl_error($ch);
        curl_close($ch);

        if ($response === false || !empty($curl_err)) {
            return ['success' => false, 'message' => "cURL error: {$curl_err}"];
        }

        if ($http_code < 200 || $http_code >= 300) {
            $snippet = mb_substr($response, 0, 200);
            $err_msg = "Apify API HTTP {$http_code}: {$snippet}";
            if ($http_code === 402 || stripos($response, 'usage-limit-exceeded') !== false || stripos($response, 'usage limit') !== false) {
                $err_msg = "Kredit / Kuota Apify telah habis (HTTP 402: Monthly usage limit reached). Silakan top up saldo kredit di console.apify.com.";
            } elseif ($http_code === 401) {
                $err_msg = "Autentikasi Apify gagal (HTTP 401: Unauthorized). Token API Apify tidak valid atau kedaluwarsa.";
            }
            return ['success' => false, 'message' => $err_msg];
        }

        $data = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['success' => false, 'message' => 'JSON decode error from Apify response.'];
        }

        if (empty($data) || !is_array($data)) {
            return ['success' => false, 'message' => 'Empty or unexpected response from Apify.'];
        }

        $total_views = 0;
        $total_engagements = 0;
        $video_count = 0;
        $followers = 0;

        $all_posts = [];
        
        foreach ($data as $item) {
            if ($followers === 0) {
                if (isset($item['authorMeta']['fans'])) {
                    $followers = (int)$item['authorMeta']['fans'];
                } elseif (isset($item['author']['followers'])) {
                    $followers = (int)$item['author']['followers'];
                } elseif (isset($item['author']['followers_count'])) {
                    $followers = (int)$item['author']['followers_count'];
                } elseif (isset($item['user']['followers_count'])) {
                    $followers = (int)$item['user']['followers_count'];
                } elseif (isset($item['user']['followersCount'])) {
                    $followers = (int)$item['user']['followersCount'];
                } elseif (isset($item['user']['followers'])) {
                    $followers = (int)$item['user']['followers'];
                } elseif (isset($item['followersCount'])) {
                    $followers = (int)$item['followersCount'];
                } elseif (isset($item['followers_count'])) {
                    $followers = (int)$item['followers_count'];
                } elseif (isset($item['followers'])) {
                    $followers = (int)$item['followers'];
                } elseif (isset($item['normal_followers_count'])) {
                    $followers = (int)$item['normal_followers_count'];
                } elseif (isset($item['numberOfSubscribers'])) {
                    $followers = (int)$item['numberOfSubscribers'];
                } elseif (isset($item['subscriberCount'])) {
                    $followers = (int)$item['subscriberCount'];
                } elseif (isset($item['subscribers'])) {
                    $followers = (int)$item['subscribers'];
                } elseif (isset($item['subscribersCount'])) {
                    $followers = (int)$item['subscribersCount'];
                } elseif (isset($item['channelSubscribers'])) {
                    $followers = (int)$item['channelSubscribers'];
                }
            }

            // Direct post/tweet item detection
            if (isset($item['text']) || isset($item['full_text']) || isset($item['tweet_id']) || isset($item['caption']) || isset($item['playCount']) || isset($item['likesCount']) || isset($item['views']) || isset($item['diggCount']) || isset($item['viewCount']) || isset($item['likes']) || isset($item['like_count']) || isset($item['favorite_count']) || isset($item['impressions']) || isset($item['retweet_count'])) {
                $all_posts[] = $item;
            }

            if (isset($item['latestPosts']) && is_array($item['latestPosts'])) {
                foreach ($item['latestPosts'] as $post) {
                    $all_posts[] = $post;
                }
            }

            if (isset($item['latestVideos']) && is_array($item['latestVideos'])) {
                foreach ($item['latestVideos'] as $post) {
                    $all_posts[] = $post;
                }
            }

            if (isset($item['tweets']) && is_array($item['tweets'])) {
                foreach ($item['tweets'] as $post) {
                    $all_posts[] = $post;
                }
            }

            if (isset($item['user']['pinned_tweet']) && is_array($item['user']['pinned_tweet'])) {
                $all_posts[] = $item['user']['pinned_tweet'];
            }
        }

        $posts_with_views = 0;
        $post_count = 0;
        $sponsored_posts = 0;

        foreach ($all_posts as $post) {
            $views = (int)(
                $post['viewCount'] 
                ?? $post['views'] 
                ?? $post['viewsCount'] 
                ?? $post['impressions'] 
                ?? $post['impressionCount'] 
                ?? $post['views_count'] 
                ?? $post['videoViewCount'] 
                ?? $post['playCount'] 
                ?? $post['videoPlayCount'] 
                ?? 0
            );

            $likes = (int)(
                $post['likes'] 
                ?? $post['likeCount'] 
                ?? $post['likesCount'] 
                ?? $post['favorite_count'] 
                ?? $post['favoriteCount'] 
                ?? $post['like_count'] 
                ?? $post['numberOfLikes'] 
                ?? $post['diggCount'] 
                ?? 0
            );

            $comments = (int)(
                $post['commentsCount'] 
                ?? $post['commentCount'] 
                ?? $post['replyCount'] 
                ?? $post['reply_count'] 
                ?? $post['comments'] 
                ?? $post['comment_count'] 
                ?? $post['numberOfComments'] 
                ?? 0
            );

            $shares = (int)(
                $post['retweetCount'] 
                ?? $post['retweet_count'] 
                ?? $post['shareCount'] 
                ?? $post['sharesCount'] 
                ?? $post['shares'] 
                ?? 0
            );

            $saves = (int)(
                $post['bookmarkCount'] 
                ?? $post['bookmark_count'] 
                ?? $post['collectCount'] 
                ?? $post['savesCount'] 
                ?? $post['saves'] 
                ?? 0
            );

            $eng = ($likes + $comments + $shares + $saves);
            $total_engagements += $eng;

            if ($views > 0) {
                $total_views += $views;
                $posts_with_views++;
            }

            if ($views > 0 || $eng > 0 || isset($post['text']) || isset($post['full_text']) || isset($post['id'])) {
                $post_count++;
            }

            // --- Sponsored / Endorsement Detection ---
            $is_sponsored = false;

            // 1. Native platform metadata flags
            if (!empty($post['isPaidPartnership']) || !empty($post['isSponsored']) || 
                !empty($post['is_paid_partnership']) || !empty($post['is_sponsored']) ||
                !empty($post['isAd']) || !empty($post['is_ad']) ||
                !empty($post['paid_promotion']) || !empty($post['paidPromotion']) ||
                !empty($post['sponsorTags']) || !empty($post['sponsor_tags'])) {
                $is_sponsored = true;
            }

            // 2. Caption keyword detection
            if (!$is_sponsored) {
                $caption_text = mb_strtolower(
                    $post['caption'] 
                    ?? $post['text'] 
                    ?? $post['full_text'] 
                    ?? $post['description'] 
                    ?? $post['title'] 
                    ?? ''
                );
                if (!empty($caption_text)) {
                    $sponsored_keywords = [
                        '#ad', '#sponsored', '#berbayar', '#partnership',
                        '#collab', 'paid partnership', '#iklan', '#endorse',
                        '#affiliate', '#promo', '#racuntiktok', '#racunshopee'
                    ];
                    foreach ($sponsored_keywords as $kw) {
                        if (strpos($caption_text, $kw) !== false) {
                            $is_sponsored = true;
                            break;
                        }
                    }
                }
            }

            if ($is_sponsored) {
                $sponsored_posts++;
            }
        }

        $avg_views = 0;
        $avg_er = 0;

        if ($posts_with_views > 0) {
            $avg_views = round($total_views / $posts_with_views);
        }

        if ($post_count > 0) {
            if ($total_views > 0 && $total_engagements > 0) {
                // Video / View-based Engagement Rate
                $avg_er = $total_engagements / $total_views;
            } elseif ($total_engagements > 0 && $followers > 0) {
                // Twitter / Text Post Follower-based Engagement Rate: avg engagements per post / followers
                $avg_er = ($total_engagements / $post_count) / $followers;
            } elseif ($total_views > 0 && $followers > 0) {
                // View-to-Subscriber Ratio fallback
                $avg_er = $avg_views / $followers;
            }
        }

        $endorse_ratio = ($post_count > 0)
            ? round(($sponsored_posts / $post_count) * 100, 1)
            : 0;

        $result_data = [
            'followers'        => $followers,
            'avg_views'        => $avg_views,
            'avg_er'           => round($avg_er, 6),
            'endorse_ratio'    => $endorse_ratio,
            'sponsored_posts'  => $sponsored_posts,
            'post_count'       => $post_count
        ];

        $unit_label = ($platform_code === 'TWITTER' || $platform_code === 'X') ? 'tweets' : (($platform_code === 'YOUTUBE' || $platform_code === 'TIKTOK') ? 'videos' : 'posts');

        if ($post_count === 0) {
            return ['success' => true, 'message' => "Profile synced. Followers updated to {$followers}. No {$unit_label} found to calculate ER.", 'data' => $result_data];
        }

        $er_percent = round($avg_er * 100, 2);
        return [
            'success' => true, 
            'message' => "Profile synced successfully! Followers: {$followers}, Avg Views: {$avg_views}, Avg ER: {$er_percent}%, Endorse Ratio: {$endorse_ratio}% (Based on {$post_count} {$unit_label}, {$sponsored_posts} sponsored)", 
            'data' => $result_data
        ];
    }

}

/* End of file Model_kol_platforms.php */
/* Location: ./application/models/Model_kol_platforms.php */