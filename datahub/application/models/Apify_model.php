<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Apify_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        // Since we are now using a single DB, we can just use $this->db
        // If we were using dual DBs, we'd use $this->kol_db
    }

    /**
     * Get profiles that are due for scraping.
     * Due condition: last_synced_at is NULL OR older than 7 days.
     */
    public function get_due_profiles($limit = 10)
    {
        $this->db->select('kp.id as kol_platform_id, kp.kol_id, kp.platform_id, kp.username, p.code as platform_code');
        $this->db->from('kol_platforms kp');
        $this->db->join('platforms p', 'p.id = kp.platform_id');
        
        $this->db->where("kp.is_active", 1);
        $this->db->where("p.code IN ('TIKTOK', 'INSTAGRAM', 'YOUTUBE')");
        $this->db->where("(kp.last_synced_at IS NULL OR kp.last_synced_at < DATE_SUB(NOW(), INTERVAL 7 DAY))");
        
        $this->db->limit($limit);
        
        return $this->db->get()->result();
    }

    /**
     * Insert a snapshot and update the kol_platforms table with the latest stats.
     */
    public function save_metrics($kol_platform_id, $kol_id, $platform_id, $followers, $avg_views, $avg_er, $total_posts = 0)
    {
        $this->db->trans_start();

        // 1. Insert Snapshot
        $snapshot = [
            'kol_id' => $kol_id,
            'platform_id' => $platform_id,
            'followers_count' => $followers,
            'average_views_last_30' => $avg_views,
            'average_er_last_30' => $avg_er,
            'total_posts' => $total_posts,
            'snapshot_date' => date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        $this->db->insert('kol_profile_snapshots', $snapshot);

        // 2. Update kol_platforms
        $update = [
            'followers' => $followers,
            'avg_views' => $avg_views,
            'avg_er' => $avg_er,
            'last_synced_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $this->db->where('id', $kol_platform_id);
        $this->db->update('kol_platforms', $update);

        // 3. Update KOL Tier based on total followers
        $this->db->select_sum('followers');
        $this->db->where('kol_id', $kol_id);
        $this->db->where('is_active', 1);
        $result = $this->db->get('kol_platforms')->row();
        $total_followers = $result ? (int)$result->followers : 0;

        $tier = $this->db->where('min_followers <=', $total_followers)
                         ->group_start()
                         ->where('max_followers >=', $total_followers)
                         ->or_where('max_followers', 0) // Just in case 0 means infinity
                         ->or_where('max_followers IS NULL')
                         ->group_end()
                         ->where('is_active', 1)
                         ->order_by('min_followers', 'DESC')
                         ->limit(1)
                         ->get('tiers')
                         ->row();
                         
        if ($tier) {
            $this->db->where('id', $kol_id)->update('kols', ['tier_id' => $tier->id]);
        }

        $this->db->trans_complete();

        return $this->db->trans_status();
    }
}
