<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_performance_card extends CI_Model {

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get KOL profile with tier name and category names.
     */
    public function get_kol_with_details($kol_id)
    {
        $this->db->select('
            k.*,
            t.name as tier_name
        ');
        $this->db->from('kols k');
        $this->db->join('tiers t', 't.id = k.tier_id', 'left');
        $this->db->where('k.id', $kol_id);

        $kol = $this->db->get()->row();

        if (!$kol) return null;

        // Get categories
        $this->db->select('c.name');
        $this->db->from('kol_categories kc');
        $this->db->join('categories c', 'c.id = kc.category_id');
        $this->db->where('kc.kol_id', $kol_id);
        $categories = $this->db->get()->result();

        $kol->categories = array_map(function($c) { return $c->name; }, $categories);

        return $kol;
    }

    /**
     * Get all platform stats for a KOL (current data from kol_platforms).
     */
    public function get_platform_stats($kol_id)
    {
        $this->db->select('
            kp.id as kol_platform_id,
            kp.username,
            kp.profile_url,
            kp.followers,
            kp.avg_views,
            kp.avg_er,
            kp.last_synced_at,
            p.id as platform_id,
            p.name as platform_name,
            p.code as platform_code
        ');
        $this->db->from('kol_platforms kp');
        $this->db->join('platforms p', 'p.id = kp.platform_id');
        $this->db->where('kp.kol_id', $kol_id);
        $this->db->where('kp.is_active', 1);
        $this->db->order_by('p.name', 'ASC');

        return $this->db->get()->result();
    }

    /**
     * Get snapshot history for trend chart (last N months, per platform).
     * Returns array grouped by platform_code => [{month, avg_views, avg_er, followers}]
     */
    public function get_snapshot_history($kol_id, $months = 6)
    {
        $since = date('Y-m-d', strtotime("-{$months} months"));

        $this->db->select('
            p.code as platform_code,
            p.name as platform_name,
            DATE_FORMAT(s.snapshot_date, "%Y-%m") as month,
            AVG(s.average_views_last_30) as avg_views,
            AVG(s.average_er_last_30) as avg_er,
            MAX(s.followers_count) as followers
        ');
        $this->db->from('kol_profile_snapshots s');
        $this->db->join('platforms p', 'p.id = s.platform_id');
        $this->db->where('s.kol_id', $kol_id);
        $this->db->where('s.snapshot_date >=', $since);
        $this->db->group_by(['p.code', 'p.name', 'DATE_FORMAT(s.snapshot_date, "%Y-%m")']);
        $this->db->order_by('month', 'ASC');

        $rows = $this->db->get()->result();

        // Group by platform
        $grouped = [];
        foreach ($rows as $row) {
            $code = $row->platform_code;
            if (!isset($grouped[$code])) {
                $grouped[$code] = [
                    'name' => $row->platform_name,
                    'data' => []
                ];
            }
            $grouped[$code]['data'][] = [
                'month'     => $row->month,
                'avg_views' => round((float)$row->avg_views, 2),
                'avg_er'    => round((float)$row->avg_er * 100, 2),
                'followers' => (int)$row->followers,
            ];
        }

        return $grouped;
    }

    /**
     * Get rate card matrix: active platforms with manual rates per canonical type.
     * Only platforms the KOL actually uses (followers > 0 or has rates).
     * Falls back to kols.price_* summary when kol_rate_cards rows are absent.
     *
     * @return array ['types' => [type => desc], 'platforms' => [...]]
     */
    public function get_rate_matrix($kol_id)
    {
        $types = function_exists('kol_rate_content_types') ? kol_rate_content_types() : [];

        $platforms = $this->db->select('
                kp.id as kol_platform_id,
                kp.username,
                kp.followers,
                p.name as platform_name,
                p.code as platform_code
            ')
            ->from('kol_platforms kp')
            ->join('platforms p', 'p.id = kp.platform_id', 'LEFT')
            ->where('kp.kol_id', $kol_id)
            ->where('kp.is_active', 1)
            ->order_by('kp.followers', 'DESC')
            ->order_by('p.name', 'ASC')
            ->get()->result();

        $plat_ids = array_map(function($p) { return $p->kol_platform_id; }, $platforms);
        $rate_map = [];
        if (!empty($plat_ids)) {
            $rows = $this->db->where_in('kol_platform_id', $plat_ids)
                ->where('is_active', 1)
                ->where('currency_code', 'IDR')
                ->get('kol_rate_cards')->result();
            foreach ($rows as $r) {
                $ct = function_exists('normalize_content_type') ? normalize_content_type($r->content_type) : $r->content_type;
                $rate_map[$r->kol_platform_id][$ct] = (float)$r->rate;
            }
        }

        // Fallback to legacy kols.price_* summary when no detail rows exist at all
        if (empty($rate_map)) {
            $kol = $this->db->select('price_vt, price_live, price_long_form, price_unboxing')
                ->from('kols')->where('id', $kol_id)->get()->row();
            if ($kol) {
                $flat = [
                    'Short Form Video' => $kol->price_vt,
                    'Live Streaming'   => $kol->price_live,
                    'Long Form Video'  => $kol->price_long_form,
                ];
                if (($kol->price_unboxing !== null && (float)$kol->price_unboxing > 0)
                    && !($kol->price_long_form !== null && (float)$kol->price_long_form > 0)) {
                    $flat['Long Form Video'] = $kol->price_unboxing;
                }
                $has_any = false;
                foreach ($flat as $v) {
                    if ($v !== null && (float)$v > 0) { $has_any = true; break; }
                }
                if ($has_any) {
                    $platforms = [(object)[
                        'kol_platform_id' => 0,
                        'username'        => null,
                        'followers'       => null,
                        'platform_name'   => 'Semua Platform',
                        'platform_code'   => null,
                        'is_summary'      => true,
                    ]];
                    foreach ($flat as $t => $v) {
                        if ($v !== null && (float)$v > 0) {
                            $rate_map[0][$t] = (float)$v;
                        }
                    }
                }
            }
        } else {
            // Show only platforms actually used (followers or rates present)
            $platforms = array_values(array_filter($platforms, function($p) use ($rate_map) {
                return (int)$p->followers > 0 || !empty($rate_map[$p->kol_platform_id]);
            }));
        }

        return ['types' => $types, 'platforms' => $platforms, 'rates' => $rate_map];
    }

    /**
     * Get the latest snapshot per platform for a KOL.
     */
    public function get_latest_snapshots($kol_id)
    {
        $sql = "
            SELECT s.*, p.name as platform_name, p.code as platform_code
            FROM kol_profile_snapshots s
            INNER JOIN platforms p ON p.id = s.platform_id
            WHERE s.kol_id = ?
            AND s.id = (
                SELECT MAX(s2.id) 
                FROM kol_profile_snapshots s2 
                WHERE s2.kol_id = s.kol_id AND s2.platform_id = s.platform_id
            )
            ORDER BY p.name ASC
        ";

        return $this->db->query($sql, [$kol_id])->result();
    }
}
