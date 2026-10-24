<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_dashboard_metrics extends MY_Model {

    public function get_total_kols()
    {
        $this->db->where('status', 'ACTIVE');
        return $this->db->count_all_results('kols');
    }

    public function get_kols_by_tier()
    {
        $this->db->select('tiers.name as tier_name, COUNT(kols.id) as total');
        $this->db->from('kols');
        $this->db->join('tiers', 'tiers.id = kols.tier_id', 'left');
        $this->db->where('kols.status', 'ACTIVE');
        $this->db->group_by('tiers.id');
        return $this->db->get()->result();
    }

    public function get_avg_er()
    {
        // Get average ER from the latest snapshots of all active KOLs
        $query = $this->db->query("
            SELECT AVG(average_er_last_30) as overall_avg_er
            FROM kol_profile_snapshots
            WHERE id IN (
                SELECT MAX(id)
                FROM kol_profile_snapshots
                GROUP BY kol_id, platform_id
            )
        ");
        $res = $query->row();
        return $res ? $res->overall_avg_er : 0;
    }

    public function get_recent_scrapes($limit = 5)
    {
        $this->db->select('kol_profile_snapshots.*, kols.name as kol_name, platforms.name as platform_name, platforms.icon_url');
        $this->db->from('kol_profile_snapshots');
        $this->db->join('kols', 'kols.id = kol_profile_snapshots.kol_id');
        $this->db->join('platforms', 'platforms.id = kol_profile_snapshots.platform_id');
        $this->db->order_by('kol_profile_snapshots.snapshot_date', 'DESC');
        $this->db->limit($limit);
        return $this->db->get()->result();
    }

    public function get_stale_kols()
    {
        // Find KOL platforms where last_synced_at is null or older than 7 days
        $seven_days_ago = date('Y-m-d H:i:s', strtotime('-7 days'));
        
        $this->db->select('kol_platforms.*, kols.name as kol_name, kols.avatar_url, platforms.name as platform_name, platforms.icon_url');
        $this->db->from('kol_platforms');
        $this->db->join('kols', 'kols.id = kol_platforms.kol_id');
        $this->db->join('platforms', 'platforms.id = kol_platforms.platform_id');
        $this->db->where('kol_platforms.is_active', 1);
        $this->db->where('kols.status', 'ACTIVE');
        
        $this->db->group_start();
        $this->db->where('kol_platforms.last_synced_at', NULL);
        $this->db->or_where('kol_platforms.last_synced_at <', $seven_days_ago);
        $this->db->group_end();
        
        $this->db->order_by('kol_platforms.last_synced_at', 'ASC');
        $this->db->limit(50); // Show top 50 stale profiles for paginated view
        return $this->db->get()->result();
    }

    public function get_total_reach()
    {
        $this->db->select_sum('followers', 'total_reach');
        $this->db->where('is_active', 1);
        $res = $this->db->get('kol_platforms')->row();
        return $res && $res->total_reach ? (float)$res->total_reach : 0;
    }

    public function get_campaign_stats()
    {
        $query = $this->db->query("
            SELECT 
                COUNT(CASE WHEN status IN ('ACTIVE', 'OPEN_REGISTRATION') THEN 1 END) as active_campaigns,
                COALESCE(SUM(CASE WHEN status IN ('ACTIVE', 'OPEN_REGISTRATION') THEN budget ELSE 0 END), 0) as active_budget
            FROM campaigns
        ");
        return $query->row();
    }

    public function get_content_stats()
    {
        $query = $this->db->query("
            SELECT 
                COUNT(DISTINCT p.id) as total_posts,
                COALESCE(SUM(pm.views), 0) as total_views,
                COALESCE(AVG(pm.engagement_rate), 0) as avg_er
            FROM postings p
            LEFT JOIN posting_metrics pm ON pm.id = p.latest_metric_id
            WHERE p.status = 'ACTIVE'
        ");
        return $query->row();
    }

    public function get_action_items()
    {
        $query = $this->db->query("
            SELECT 
                (SELECT COUNT(*) FROM tasks WHERE status IN ('WAITING_DRAFT', 'IN_REVIEW_DRAFT', 'REVISION_DRAFT')) as pending_drafts,
                (SELECT COUNT(*) FROM tasks WHERE estimated_posting_date < CURDATE() AND status NOT IN ('POSTED', 'DONE')) as overdue_tasks,
                (SELECT COUNT(*) FROM kol_shipments WHERE status = 'PENDING') as pending_shipments,
                (SELECT COUNT(*) FROM briefs WHERE status IN ('PENDING_REVIEW', 'REVISION')) as pending_briefs
        ");
        $res = $query->row();
        $total_actions = ($res->pending_drafts ?? 0) + ($res->overdue_tasks ?? 0) + ($res->pending_shipments ?? 0) + ($res->pending_briefs ?? 0);
        return [
            'pending_drafts' => (int)($res->pending_drafts ?? 0),
            'overdue_tasks' => (int)($res->overdue_tasks ?? 0),
            'pending_shipments' => (int)($res->pending_shipments ?? 0),
            'pending_briefs' => (int)($res->pending_briefs ?? 0),
            'total_actions' => (int)$total_actions
        ];
    }

    public function get_platform_distribution()
    {
        $this->db->select('platforms.name as platform_name, COUNT(kol_platforms.id) as total');
        $this->db->from('platforms');
        $this->db->join('kol_platforms', 'kol_platforms.platform_id = platforms.id AND kol_platforms.is_active = 1', 'left');
        $this->db->group_by('platforms.id');
        $this->db->order_by('total', 'DESC');
        return $this->db->get()->result();
    }

    public function get_top_postings($limit = 5)
    {
        $query = $this->db->query("
            SELECT 
                p.id, p.live_url, p.actual_posted_at,
                k.name as kol_name, k.avatar_url,
                pl.name as platform_name,
                COALESCE(pm.views, 0) as views,
                pm.engagement_rate,
                pm.cpv
            FROM postings p
            JOIN tasks t ON t.id = p.task_id
            JOIN campaign_kols ck ON ck.id = t.campaign_kol_id
            JOIN kols k ON k.id = ck.kol_id
            JOIN platforms pl ON pl.id = p.platform_id
            LEFT JOIN posting_metrics pm ON pm.id = p.latest_metric_id
            WHERE p.status = 'ACTIVE'
            ORDER BY COALESCE(pm.views, 0) DESC
            LIMIT ?
        ", [(int)$limit]);
        return $query->result();
    }
}

