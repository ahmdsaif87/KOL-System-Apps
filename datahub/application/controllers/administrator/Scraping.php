<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scraping extends Admin {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Apify_model');
    }

    public function bulk()
    {
        $this->is_allowed('kols_update');

        $kol_ids = $this->input->post('kol_ids');
        
        if (empty($kol_ids) || !is_array($kol_ids)) {
            echo json_encode(['success' => false, 'message' => 'No KOLs selected']);
            return;
        }

        $success_count = 0;
        $failed_count = 0;

        // Force load the apify profile service config
        $this->load->config('apify');
        $api_token = $this->config->item('apify_token');
        $actor_id = $this->config->item('apify_actor_id');

        foreach ($kol_ids as $kol_id) {
            // Get all active platforms for this KOL
            $this->db->where('kol_id', $kol_id);
            $this->db->where('is_active', 1);
            $platforms = $this->db->get('kol_platforms')->result();

            foreach ($platforms as $platform) {
                // Determine platform string for Apify
                $platform_name = '';
                $p_info = $this->db->get_where('platforms', ['id' => $platform->platform_id])->row();
                if ($p_info) {
                    $platform_name = strtolower($p_info->code); // tiktok, instagram, youtube
                }

                if (!$platform_name) continue;

                // Fire Apify API (Synchronous for now, or asynchronous if possible)
                // Since this is a demo, we will simulate the scrape if API fails, or just call it directly.
                
                $url = "https://api.apify.com/v2/acts/{$actor_id}/run-sync-get-dataset-items?token={$api_token}";
                $payload = json_encode([
                    "profiles" => [$platform->username],
                    "platform" => $platform_name,
                    "resultsLimit" => 30
                ]);

                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
                curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);

                if ($http_code == 200 || $http_code == 201) {
                    $data = json_decode($response, true);
                    
                    if (!empty($data)) {
                        $followers = 0;
                        $total_views = 0;
                        $total_engagements = 0;
                        $post_count = 0;

                        foreach ($data as $post) {
                            if ($post_count == 0 && isset($post['authorMeta']['fans'])) {
                                $followers = $post['authorMeta']['fans'];
                            }
                            $views = isset($post['playCount']) ? $post['playCount'] : 0;
                            $likes = isset($post['diggCount']) ? $post['diggCount'] : 0;
                            $comments = isset($post['commentCount']) ? $post['commentCount'] : 0;
                            $shares = isset($post['shareCount']) ? $post['shareCount'] : 0;
                            
                            $total_views += $views;
                            $total_engagements += ($likes + $comments + $shares);
                            $post_count++;
                        }

                        $avg_views = $post_count > 0 ? $total_views / $post_count : 0;
                        $avg_er = $followers > 0 ? ($total_engagements / $post_count) / $followers : 0;

                        // Save snapshot
                        $this->Apify_model->save_snapshot(
                            $kol_id,
                            $platform->platform_id,
                            $followers,
                            $avg_views,
                            $avg_er
                        );
                        $success_count++;
                    } else {
                        $failed_count++;
                    }
                } else {
                    $failed_count++;
                }
            }
        }

        echo json_encode([
            'success' => true, 
            'message' => "Scraping completed. Success: {$success_count} platforms, Failed: {$failed_count} platforms."
        ]);
    }
}
