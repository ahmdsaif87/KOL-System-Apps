<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Qualification Evaluator Library
 * Evaluates whether a KOL meets an Open Post campaign's qualification requirements.
 */
class Qualification_evaluator
{
    protected $ci;

    public function __construct()
    {
        $this->ci =& get_instance();
        $this->ci->load->database();
    }

    /**
     * Evaluate qualification for a given KOL and Campaign.
     *
     * @param int $kol_id
     * @param int $campaign_id
     * @return array
     */
    public function evaluate($kol_id, $campaign_id)
    {
        $kol_id = (int)$kol_id;
        $campaign_id = (int)$campaign_id;

        // 1. Fetch Campaign Details
        $campaign = $this->ci->db->select('id, name, status, sourcing_model, application_deadline, max_slots, min_followers, min_er, start_date, end_date')
            ->from('campaigns')
            ->where('id', $campaign_id)
            ->get()
            ->row();

        if (!$campaign) {
            return [
                'is_qualified' => false,
                'can_apply'    => false,
                'error'        => 'Campaign not found',
                'criteria'     => [],
                'summary'      => 'Campaign not found'
            ];
        }

        // 2. Fetch Campaign Qualification Requirements
        $allowed_tiers = $this->ci->db->select('t.id, t.name')
            ->from('campaign_allowed_tiers cat')
            ->join('tiers t', 't.id = cat.tier_id', 'inner')
            ->where('cat.campaign_id', $campaign_id)
            ->get()
            ->result();

        $allowed_categories = $this->ci->db->select('c.id, c.name')
            ->from('campaign_allowed_categories cac')
            ->join('categories c', 'c.id = cac.category_id', 'inner')
            ->where('cac.campaign_id', $campaign_id)
            ->get()
            ->result();

        $required_platforms = $this->ci->db->select('p.id, p.name, p.code')
            ->from('campaign_required_platforms crp')
            ->join('platforms p', 'p.id = crp.platform_id', 'inner')
            ->where('crp.campaign_id', $campaign_id)
            ->get()
            ->result();

        // 3. Fetch KOL Details
        $kol = $this->ci->db->select('k.id, k.name, k.tier_id, k.status, k.is_blacklisted, t.name as tier_name')
            ->from('kols k')
            ->join('tiers t', 't.id = k.tier_id', 'left')
            ->where('k.id', $kol_id)
            ->get()
            ->row();

        if (!$kol) {
            return [
                'is_qualified' => false,
                'can_apply'    => false,
                'error'        => 'KOL profile not found',
                'criteria'     => [],
                'summary'      => 'KOL profile not found'
            ];
        }

        // KOL Categories
        $kol_categories = $this->ci->db->select('c.id, c.name')
            ->from('kol_categories kc')
            ->join('categories c', 'c.id = kc.category_id', 'inner')
            ->where('kc.kol_id', $kol_id)
            ->get()
            ->result();

        $kol_cat_ids = array_map(function($c) { return (int)$c->id; }, $kol_categories);
        $kol_cat_names = array_map(function($c) { return $c->name; }, $kol_categories);

        // KOL Platforms & Metrics
        $kol_platforms = $this->ci->db->select('kp.platform_id, kp.followers, kp.avg_er, kp.avg_views, p.name as platform_name, p.code as platform_code')
            ->from('kol_platforms kp')
            ->join('platforms p', 'p.id = kp.platform_id', 'inner')
            ->where('kp.kol_id', $kol_id)
            ->where('kp.is_active', 1)
            ->get()
            ->result();

        $kol_plat_ids = array_map(function($p) { return (int)$p->platform_id; }, $kol_platforms);
        $kol_plat_names = array_map(function($p) { return $p->platform_name; }, $kol_platforms);

        // Calculate max followers & max ER across KOL's platforms
        $max_followers = 0;
        $max_er = 0.00;
        foreach ($kol_platforms as $kp) {
            if ((int)$kp->followers > $max_followers) {
                $max_followers = (int)$kp->followers;
            }
            if ((float)$kp->avg_er > $max_er) {
                $max_er = (float)$kp->avg_er;
            }
        }

        // Slot Count Check
        $accepted_count = $this->ci->db->from('campaign_applications')
            ->where('campaign_id', $campaign_id)
            ->where('status', 'ACCEPTED')
            ->count_all_results();

        $assigned_count = $this->ci->db->from('campaign_kols')
            ->where('campaign_id', $campaign_id)
            ->where_in('status', ['ASSIGNED', 'ACTIVE', 'COMPLETED'])
            ->count_all_results();

        // Effective slots taken (avoid double-counting if an accepted app is already in campaign_kols)
        $total_slots_taken = max($accepted_count, $assigned_count);
        $slots_full = (!empty($campaign->max_slots) && $campaign->max_slots > 0) ? ($total_slots_taken >= (int)$campaign->max_slots) : false;

        // Deadline Check
        $today = date('Y-m-d');
        $deadline_passed = (!empty($campaign->application_deadline)) ? ($campaign->application_deadline < $today) : false;

        // Existing application check
        $existing_app = $this->ci->db->get_where('campaign_applications', [
            'campaign_id' => $campaign_id,
            'kol_id'      => $kol_id
        ])->row();

        $criteria = [];
        $is_qualified = true;

        // Criteria 1: Account Status & Blacklist
        $passed_status = ($kol->status === 'ACTIVE' && (int)$kol->is_blacklisted === 0);
        if (!$passed_status) $is_qualified = false;
        $criteria[] = [
            'key'      => 'account_status',
            'label'    => 'Account Status',
            'required' => 'Active, Not Blacklisted',
            'actual'   => ($kol->is_blacklisted ? 'Blacklisted' : $kol->status),
            'passed'   => $passed_status,
            'note'     => $passed_status ? 'Profile in good standing' : 'Account must be Active and not blacklisted'
        ];

        // Criteria 2: Tier Requirement
        if (!empty($allowed_tiers)) {
            $allowed_tier_ids = array_map(function($t) { return (int)$t->id; }, $allowed_tiers);
            $allowed_tier_names = array_map(function($t) { return $t->name; }, $allowed_tiers);
            $passed_tier = in_array((int)$kol->tier_id, $allowed_tier_ids);
            if (!$passed_tier) $is_qualified = false;

            $criteria[] = [
                'key'      => 'tier',
                'label'    => 'KOL Tier',
                'required' => implode(', ', $allowed_tier_names),
                'actual'   => $kol->tier_name ?: 'None',
                'passed'   => $passed_tier,
                'note'     => $passed_tier ? 'Tier matches criteria' : 'Tier does not match allowed tiers'
            ];
        }

        // Criteria 3: Category Requirement
        if (!empty($allowed_categories)) {
            $allowed_cat_ids = array_map(function($c) { return (int)$c->id; }, $allowed_categories);
            $allowed_cat_names = array_map(function($c) { return $c->name; }, $allowed_categories);
            $matching_cats = array_intersect($kol_cat_ids, $allowed_cat_ids);
            $passed_cat = count($matching_cats) > 0;
            if (!$passed_cat) $is_qualified = false;

            $criteria[] = [
                'key'      => 'category',
                'label'    => 'Niche / Category',
                'required' => implode(', ', $allowed_cat_names),
                'actual'   => !empty($kol_cat_names) ? implode(', ', $kol_cat_names) : 'None',
                'passed'   => $passed_cat,
                'note'     => $passed_cat ? 'Category aligns with campaign' : 'No matching category'
            ];
        }

        // Criteria 4: Platform Requirement
        if (!empty($required_platforms)) {
            $req_plat_ids = array_map(function($p) { return (int)$p->id; }, $required_platforms);
            $req_plat_names = array_map(function($p) { return $p->name; }, $required_platforms);
            
            // KOL must have at least one or all required platforms?
            // In campaign requirements, having at least one active profile on the specified platforms allows them to post
            $matching_plats = array_intersect($kol_plat_ids, $req_plat_ids);
            $passed_plat = count($matching_plats) > 0;
            if (!$passed_plat) $is_qualified = false;

            $criteria[] = [
                'key'      => 'platform',
                'label'    => 'Required Platform',
                'required' => implode(', ', $req_plat_names),
                'actual'   => !empty($kol_plat_names) ? implode(', ', $kol_plat_names) : 'None',
                'passed'   => $passed_plat,
                'note'     => $passed_plat ? 'Platform profile verified' : 'Missing active profile on required platform(s)'
            ];
        }

        // Criteria 5: Minimum Followers
        if (!empty($campaign->min_followers) && (int)$campaign->min_followers > 0) {
            $passed_followers = ($max_followers >= (int)$campaign->min_followers);
            if (!$passed_followers) $is_qualified = false;

            $criteria[] = [
                'key'      => 'min_followers',
                'label'    => 'Minimum Followers',
                'required' => number_format((int)$campaign->min_followers) . '+',
                'actual'   => number_format($max_followers),
                'passed'   => $passed_followers,
                'note'     => $passed_followers ? 'Follower threshold met' : 'Follower count below minimum requirement'
            ];
        }

        // Criteria 6: Minimum ER
        if (!empty($campaign->min_er) && (float)$campaign->min_er > 0) {
            $passed_er = ($max_er >= (float)$campaign->min_er);
            if (!$passed_er) $is_qualified = false;

            $criteria[] = [
                'key'      => 'min_er',
                'label'    => 'Minimum Engagement Rate',
                'required' => number_format((float)$campaign->min_er, 2) . '%+',
                'actual'   => number_format($max_er, 2) . '%',
                'passed'   => $passed_er,
                'note'     => $passed_er ? 'ER threshold met' : 'Engagement rate below requirement'
            ];
        }

        // Operational checks
        $can_apply = true;
        $cannot_apply_reason = '';

        if ($existing_app) {
            $can_apply = false;
            $cannot_apply_reason = 'You have already applied for this campaign (Status: ' . $existing_app->status . ').';
        } elseif ($deadline_passed) {
            $can_apply = false;
            $cannot_apply_reason = 'The application deadline has passed (' . date('d M Y', strtotime($campaign->application_deadline)) . ').';
        } elseif ($slots_full) {
            $can_apply = false;
            $cannot_apply_reason = 'Campaign quota has been filled (' . $total_slots_taken . '/' . $campaign->max_slots . ' slots).';
        } elseif (!in_array($campaign->status, ['ACTIVE', 'OPEN_REGISTRATION'])) {
            $can_apply = false;
            $cannot_apply_reason = 'Campaign is currently ' . $campaign->status . ' and not accepting applications.';
        }

        $total_criteria = count($criteria);
        $passed_criteria = count(array_filter($criteria, function($c) { return $c['passed']; }));

        return [
            'is_qualified'        => $is_qualified,
            'can_apply'           => $can_apply,
            'cannot_apply_reason' => $cannot_apply_reason,
            'existing_application'=> $existing_app ? [
                'id'                  => $existing_app->id,
                'status'              => $existing_app->status,
                'applied_at'          => $existing_app->applied_at,
                'proposed_rate'       => $existing_app->proposed_rate,
                'pitch_message'       => $existing_app->pitch_message,
                'rejection_reason'    => $existing_app->rejection_reason,
            ] : null,
            'criteria'            => $criteria,
            'summary'             => $passed_criteria . '/' . $total_criteria . ' criteria met',
            'slots'               => [
                'max'       => (int)$campaign->max_slots,
                'taken'     => $total_slots_taken,
                'remaining' => (!empty($campaign->max_slots) && $campaign->max_slots > 0) ? max(0, (int)$campaign->max_slots - $total_slots_taken) : null,
                'is_full'   => $slots_full
            ],
            'deadline'            => [
                'date'      => $campaign->application_deadline,
                'is_passed' => $deadline_passed
            ]
        ];
    }
}
