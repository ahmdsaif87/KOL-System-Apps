<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_open_campaigns extends MY_Model
{
    protected $primary_key = 'id';
    protected $table_name = 'campaigns';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active Open Post campaigns available for KOL browsing
     *
     * @param int|null $kol_id
     * @return array
     */
    public function get_open_campaigns($kol_id = null)
    {
        $today = date('Y-m-d');

        $this->db->select('c.*, u.full_name as pic_name')
            ->from('campaigns c')
            ->join('aauth_users u', 'u.id = c.pic_user_id', 'left')
            ->where('c.sourcing_model', 'OPEN_POST')
            ->where('c.status', 'OPEN_REGISTRATION')
            ->group_start()
                ->where('c.application_deadline >=', $today)
                ->or_where('c.application_deadline IS NULL')
            ->group_end()
            ->order_by('c.created_at', 'DESC');

        $campaigns = $this->db->get()->result();

        if (empty($campaigns)) {
            return [];
        }

        $campaign_ids = array_map(function($c) { return $c->id; }, $campaigns);

        // 1. Fetch Junction Data
        $allowed_tiers_raw = $this->db->select('cat.campaign_id, t.name as tier_name')
            ->from('campaign_allowed_tiers cat')
            ->join('tiers t', 't.id = cat.tier_id', 'inner')
            ->where_in('cat.campaign_id', $campaign_ids)
            ->get()->result();

        $tiers_by_camp = [];
        foreach ($allowed_tiers_raw as $t) {
            $tiers_by_camp[$t->campaign_id][] = $t->tier_name;
        }

        $allowed_cats_raw = $this->db->select('cac.campaign_id, cat.name as category_name')
            ->from('campaign_allowed_categories cac')
            ->join('categories cat', 'cat.id = cac.category_id', 'inner')
            ->where_in('cac.campaign_id', $campaign_ids)
            ->get()->result();

        $cats_by_camp = [];
        foreach ($allowed_cats_raw as $c) {
            $cats_by_camp[$c->campaign_id][] = $c->category_name;
        }

        $req_plats_raw = $this->db->select('crp.campaign_id, p.name as platform_name, p.code as platform_code')
            ->from('campaign_required_platforms crp')
            ->join('platforms p', 'p.id = crp.platform_id', 'inner')
            ->where_in('crp.campaign_id', $campaign_ids)
            ->get()->result();

        $plats_by_camp = [];
        foreach ($req_plats_raw as $p) {
            $plats_by_camp[$p->campaign_id][] = $p;
        }

        // 2. Fetch Products
        $products_raw = $this->db->select('cp.campaign_id, prod.name as product_name, prod.sku')
            ->from('campaign_products cp')
            ->join('products prod', 'prod.id = cp.product_id', 'inner')
            ->where_in('cp.campaign_id', $campaign_ids)
            ->get()->result();

        $prods_by_camp = [];
        foreach ($products_raw as $pr) {
            $prods_by_camp[$pr->campaign_id][] = $pr;
        }

        // 3. Accepted Count & Slots
        $accepted_apps = $this->db->select('campaign_id, COUNT(id) as accepted_count')
            ->from('campaign_applications')
            ->where_in('campaign_id', $campaign_ids)
            ->where('status', 'ACCEPTED')
            ->group_by('campaign_id')
            ->get()->result();

        $accepted_by_camp = [];
        foreach ($accepted_apps as $aa) {
            $accepted_by_camp[$aa->campaign_id] = (int)$aa->accepted_count;
        }

        // 4. Existing Applications for this KOL
        $my_apps_by_camp = [];
        if ($kol_id) {
            $my_apps = $this->db->select('campaign_id, id as application_id, status as application_status, applied_at')
                ->from('campaign_applications')
                ->where('kol_id', (int)$kol_id)
                ->where_in('campaign_id', $campaign_ids)
                ->get()->result();

            foreach ($my_apps as $ma) {
                $my_apps_by_camp[$ma->campaign_id] = $ma;
            }
        }

        // Assemble
        foreach ($campaigns as $camp) {
            $camp->allowed_tiers = isset($tiers_by_camp[$camp->id]) ? $tiers_by_camp[$camp->id] : [];
            $camp->allowed_categories = isset($cats_by_camp[$camp->id]) ? $cats_by_camp[$camp->id] : [];
            $camp->required_platforms = isset($plats_by_camp[$camp->id]) ? $plats_by_camp[$camp->id] : [];
            $camp->products = isset($prods_by_camp[$camp->id]) ? $prods_by_camp[$camp->id] : [];
            $camp->accepted_count = isset($accepted_by_camp[$camp->id]) ? $accepted_by_camp[$camp->id] : 0;
            $camp->has_applied = isset($my_apps_by_camp[$camp->id]);
            $camp->my_application = isset($my_apps_by_camp[$camp->id]) ? $my_apps_by_camp[$camp->id] : null;

            $camp->is_slots_full = (!empty($camp->max_slots) && $camp->max_slots > 0) ? ($camp->accepted_count >= (int)$camp->max_slots) : false;
        }

        return $campaigns;
    }

    /**
     * Get single campaign detail with all requirements
     *
     * @param int $campaign_id
     * @return object|null
     */
    public function get_campaign_detail($campaign_id)
    {
        $campaign = $this->db->select('c.*, u.full_name as pic_name, u.email as pic_email')
            ->from('campaigns c')
            ->join('aauth_users u', 'u.id = c.pic_user_id', 'left')
            ->where('c.id', (int)$campaign_id)
            ->get()->row();

        if (!$campaign) {
            return null;
        }

        // Products
        $campaign->products = $this->db->select('prod.*')
            ->from('campaign_products cp')
            ->join('products prod', 'prod.id = cp.product_id', 'inner')
            ->where('cp.campaign_id', $campaign->id)
            ->get()->result();

        // Junction requirements
        $campaign->allowed_tiers = $this->db->select('t.id, t.name')
            ->from('campaign_allowed_tiers cat')
            ->join('tiers t', 't.id = cat.tier_id', 'inner')
            ->where('cat.campaign_id', $campaign->id)
            ->get()->result();

        $campaign->allowed_categories = $this->db->select('cat.id, cat.name')
            ->from('campaign_allowed_categories cac')
            ->join('categories cat', 'cat.id = cac.category_id', 'inner')
            ->where('cac.campaign_id', $campaign->id)
            ->get()->result();

        $campaign->required_platforms = $this->db->select('p.id, p.name, p.code')
            ->from('campaign_required_platforms crp')
            ->join('platforms p', 'p.id = crp.platform_id', 'inner')
            ->where('crp.campaign_id', $campaign->id)
            ->get()->result();

        // Slot counts
        $accepted = $this->db->from('campaign_applications')
            ->where('campaign_id', $campaign->id)
            ->where('status', 'ACCEPTED')
            ->count_all_results();
        $campaign->accepted_count = $accepted;
        $campaign->is_slots_full = (!empty($campaign->max_slots) && $campaign->max_slots > 0) ? ($accepted >= (int)$campaign->max_slots) : false;

        return $campaign;
    }

    /**
     * Get all applications submitted by a specific KOL
     *
     * @param int $kol_id
     * @return array
     */
    public function get_my_applications($kol_id)
    {
        return $this->db->select('ca.*, c.name as campaign_name, c.campaign_type, c.brand_name, c.budget, c.currency_code, c.start_date, c.end_date, c.status as campaign_status, u.full_name as reviewer_name')
            ->from('campaign_applications ca')
            ->join('campaigns c', 'c.id = ca.campaign_id', 'inner')
            ->join('aauth_users u', 'u.id = ca.reviewed_by', 'left')
            ->where('ca.kol_id', (int)$kol_id)
            ->order_by('ca.applied_at', 'DESC')
            ->get()->result();
    }
}
