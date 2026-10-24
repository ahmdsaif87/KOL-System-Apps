<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Model_internal_evaluation extends MY_Model {
    
    public function __construct() {
        parent::__construct();
    }

    public function get_campaigns_list() {
        return $this->db->select('id, name, status, start_date, end_date')
            ->from('campaigns')
            ->order_by("CASE WHEN status = 'ACTIVE' THEN 1 WHEN status = 'OPEN_REGISTRATION' THEN 2 WHEN status = 'COMPLETED' THEN 3 ELSE 4 END, id DESC")
            ->get()->result();
    }

    public function get_filter_options() {
        $campaigns = $this->get_campaigns_list();
        
        $this->db->select('u.id, u.full_name, u.username');
        $this->db->from('aauth_users u');
        $this->db->join('aauth_user_to_group ug', 'ug.user_id = u.id', 'inner');
        $this->db->where_in('ug.group_id', [1, 5, 6]); // Admin, Operational, Leader KOL
        $this->db->group_by('u.id');
        $users = $this->db->get()->result();
        
        return ['campaigns' => $campaigns, 'users' => $users];
    }

    public function get_staff_operational_kpis($campaign_id = null) {
        // ONLY evaluate users with role STAFF (Group: Operational / id 5)
        $this->db->select('u.id, u.username, u.full_name, u.avatar, u.email');
        $this->db->from('aauth_users u');
        $this->db->join('aauth_user_to_group ug', 'ug.user_id = u.id', 'inner');
        $this->db->join('aauth_groups g', 'g.id = ug.group_id', 'inner');
        $this->db->group_start();
        $this->db->where('g.id', 5);
        $this->db->or_where('g.name', 'Operational');
        $this->db->group_end();

        // Strictly filter only staff that are in that campaign
        if (!empty($campaign_id)) {
            $this->db->where("u.id IN (
                SELECT DISTINCT ck.assigned_by FROM campaign_kols ck 
                WHERE ck.campaign_id = " . (int)$campaign_id . " AND ck.assigned_by IS NOT NULL
                UNION
                SELECT DISTINCT t.created_by FROM tasks t 
                JOIN campaign_kols ck ON ck.id = t.campaign_kol_id 
                WHERE ck.campaign_id = " . (int)$campaign_id . " AND t.created_by IS NOT NULL
            )", NULL, FALSE);
        } else {
            // All campaigns: only include staff who have at least 1 campaign assignment
            $this->db->where("u.id IN (
                SELECT DISTINCT ck.assigned_by FROM campaign_kols ck WHERE ck.assigned_by IS NOT NULL
                UNION
                SELECT DISTINCT t.created_by FROM tasks t WHERE t.created_by IS NOT NULL
            )", NULL, FALSE);
        }

        $this->db->group_by('u.id');
        $this->db->order_by('u.full_name', 'ASC');
        $staff_list = $this->db->get()->result();

        $results = [];

        foreach ($staff_list as $staff) {
            $item = new stdClass();
            $item->id = $staff->id;
            $item->username = $staff->username;
            $item->full_name = $staff->full_name ?: $staff->username;
            $item->avatar = $staff->avatar;
            $item->email = $staff->email;

            // 1. Total KOLs handled by this staff
            $this->db->select('COUNT(DISTINCT ck.id) as kol_count, COALESCE(SUM(ck.deal_amount), 0) as total_deal_amount');
            $this->db->from('campaign_kols ck');
            $this->db->where('ck.assigned_by', $staff->id);
            if (!empty($campaign_id)) {
                $this->db->where('ck.campaign_id', $campaign_id);
            }
            $ck_stat = $this->db->get()->row();
            $item->kol_count = (int)($ck_stat->kol_count ?? 0);
            $item->total_deal_amount = (float)($ck_stat->total_deal_amount ?? 0);

            // 2. Tasks handled by this staff
            $this->db->select('
                t.id as task_id,
                t.title as task_title,
                t.status as task_status,
                t.estimated_posting_date,
                k.id as kol_id,
                k.name as kol_name,
                k.avatar_url as kol_avatar,
                k.email as kol_username,
                c.id as campaign_id,
                c.name as campaign_name,
                p.id as posting_id,
                p.date_validation_status,
                p.actual_posted_at,
                p.live_url
            ');
            $this->db->from('tasks t');
            $this->db->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'inner');
            $this->db->join('kols k', 'k.id = ck.kol_id', 'inner');
            $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'inner');
            $this->db->join('postings p', 'p.task_id = t.id', 'left');
            $this->db->where('ck.assigned_by', $staff->id);
            if (!empty($campaign_id)) {
                $this->db->where('ck.campaign_id', $campaign_id);
            }
            $this->db->order_by('t.id', 'DESC');
            $tasks = $this->db->get()->result();

            $item->total_tasks = count($tasks);
            
            // Only evaluate staff who have KOLs or tasks in this campaign
            if ($item->kol_count == 0 && $item->total_tasks == 0) {
                continue;
            }

            $item->completed_tasks = 0;
            $item->total_postings = 0;
            $item->on_time_postings = 0;
            $item->late_postings = 0;
            $item->pending_postings = 0;

            foreach ($tasks as $t) {
                if ($t->task_status == 'DONE') {
                    $item->completed_tasks++;
                }
                if (!empty($t->posting_id)) {
                    $item->total_postings++;
                    if ($t->date_validation_status == 'ON_TIME') {
                        $item->on_time_postings++;
                    } elseif ($t->date_validation_status == 'LATE') {
                        $item->late_postings++;
                    } else {
                        $item->pending_postings++;
                    }
                }
            }

            $item->on_time_rate = ($item->total_postings > 0) ? round(($item->on_time_postings / $item->total_postings) * 100, 1) : null;
            $item->task_completion_rate = ($item->total_tasks > 0) ? round(($item->completed_tasks / $item->total_tasks) * 100, 1) : 0;

            // 3. Draft Review TAT & Revision Cycles
            $this->db->select('
                d.id,
                d.version,
                d.status,
                d.created_at,
                d.updated_at,
                TIMESTAMPDIFF(MINUTE, d.created_at, d.updated_at) as diff_minutes
            ');
            $this->db->from('drafts d');
            $this->db->join('tasks t', 't.id = d.task_id', 'inner');
            $this->db->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'inner');
            $this->db->where('ck.assigned_by', $staff->id);
            if (!empty($campaign_id)) {
                $this->db->where('ck.campaign_id', $campaign_id);
            }
            $drafts = $this->db->get()->result();

            $item->total_drafts = count($drafts);
            $total_version = 0;
            $total_review_hours = 0;
            $reviewed_drafts_count = 0;

            foreach ($drafts as $d) {
                $total_version += (int)$d->version;
                if (in_array($d->status, ['APPROVED', 'REVISION']) && $d->diff_minutes !== null) {
                    $hours = max(0.1, round((int)$d->diff_minutes / 60, 1));
                    $total_review_hours += $hours;
                    $reviewed_drafts_count++;
                }
            }

            $item->avg_revision_cycles = ($item->total_drafts > 0) ? round($total_version / $item->total_drafts, 1) : null;
            $item->avg_turnaround_hours = ($reviewed_drafts_count > 0) ? round($total_review_hours / $reviewed_drafts_count, 1) : null;

            // 4. Sample Dispatch Speed
            $this->db->select('
                s.id,
                s.status,
                s.shipped_at,
                ck.deal_date,
                DATEDIFF(s.shipped_at, ck.deal_date) as dispatch_days
            ');
            $this->db->from('kol_shipments s');
            $this->db->join('campaign_kols ck', 'ck.id = s.campaign_kol_id', 'inner');
            $this->db->where('ck.assigned_by', $staff->id);
            $this->db->where('s.shipped_at IS NOT NULL');
            if (!empty($campaign_id)) {
                $this->db->where('ck.campaign_id', $campaign_id);
            }
            $shipments = $this->db->get()->result();

            $item->total_shipments = count($shipments);
            $total_dispatch_days = 0;
            $valid_dispatch_count = 0;

            foreach ($shipments as $s) {
                if ($s->dispatch_days !== null && $s->dispatch_days >= 0) {
                    $total_dispatch_days += (int)$s->dispatch_days;
                    $valid_dispatch_count++;
                }
            }

            $item->avg_dispatch_days = ($valid_dispatch_count > 0) ? round($total_dispatch_days / $valid_dispatch_count, 1) : null;

            // 5. Performance Grade Calculation
            if ($item->kol_count == 0 && $item->total_tasks == 0) {
                $item->grade = 'PENDING';
                $item->grade_label = 'Belum Bertugas';
                $item->grade_badge = 'badge-soft-slate';
                $item->score = 0;
            } else {
                $score = 0;
                // SLA On-Time (Max 40 pts)
                if ($item->on_time_rate !== null) {
                    if ($item->on_time_rate >= 90) $score += 40;
                    elseif ($item->on_time_rate >= 75) $score += 30;
                    elseif ($item->on_time_rate >= 50) $score += 20;
                    else $score += 10;
                } else {
                    $score += 30; // Neutral baseline if no posting yet
                }

                // Review Draft Speed (Max 30 pts)
                if ($item->avg_turnaround_hours !== null) {
                    if ($item->avg_turnaround_hours <= 2) $score += 30;
                    elseif ($item->avg_turnaround_hours <= 8) $score += 25;
                    elseif ($item->avg_turnaround_hours <= 24) $score += 15;
                    else $score += 5;
                } else {
                    $score += 20;
                }

                // Dispatch Speed (Max 30 pts)
                if ($item->avg_dispatch_days !== null) {
                    if ($item->avg_dispatch_days <= 1) $score += 30;
                    elseif ($item->avg_dispatch_days <= 3) $score += 25;
                    elseif ($item->avg_dispatch_days <= 5) $score += 15;
                    else $score += 5;
                } else {
                    $score += 20;
                }

                $item->score = $score;

                if ($score >= 85) {
                    $item->grade = 'A';
                    $item->grade_label = 'Grade A (Sangat Baik)';
                    $item->grade_badge = 'badge-soft-emerald';
                } elseif ($score >= 65) {
                    $item->grade = 'B';
                    $item->grade_label = 'Grade B (Baik)';
                    $item->grade_badge = 'badge-soft-blue';
                } else {
                    $item->grade = 'C';
                    $item->grade_label = 'Grade C (Perlu Evaluasi)';
                    $item->grade_badge = 'badge-soft-rose';
                }
            }

            // Save tasks for modal drilldown
            $item->tasks = $tasks;
            $results[] = $item;
        }

        return $results;
    }

    public function get_operational_team_summary($staff_kpis = []) {
        $summary = new stdClass();
        $summary->total_staff = count($staff_kpis);
        $summary->active_staff = 0;
        $summary->total_kols = 0;
        $summary->total_tasks = 0;
        $summary->completed_tasks = 0;
        $summary->total_postings = 0;
        $summary->on_time_postings = 0;
        $summary->late_postings = 0;

        $turnaround_sum = 0;
        $turnaround_count = 0;
        $dispatch_sum = 0;
        $dispatch_count = 0;

        foreach ($staff_kpis as $s) {
            if ($s->kol_count > 0 || $s->total_tasks > 0) {
                $summary->active_staff++;
            }
            $summary->total_kols += $s->kol_count;
            $summary->total_tasks += $s->total_tasks;
            $summary->completed_tasks += $s->completed_tasks;
            $summary->total_postings += $s->total_postings;
            $summary->on_time_postings += $s->on_time_postings;
            $summary->late_postings += $s->late_postings;

            if ($s->avg_turnaround_hours !== null) {
                $turnaround_sum += $s->avg_turnaround_hours;
                $turnaround_count++;
            }
            if ($s->avg_dispatch_days !== null) {
                $dispatch_sum += $s->avg_dispatch_days;
                $dispatch_count++;
            }
        }

        $summary->team_on_time_rate = ($summary->total_postings > 0) ? round(($summary->on_time_postings / $summary->total_postings) * 100, 1) : 0;
        $summary->team_avg_turnaround_hours = ($turnaround_count > 0) ? round($turnaround_sum / $turnaround_count, 1) : 0;
        $summary->team_avg_dispatch_days = ($dispatch_count > 0) ? round($dispatch_sum / $dispatch_count, 1) : 0;
        $summary->task_completion_rate = ($summary->total_tasks > 0) ? round(($summary->completed_tasks / $summary->total_tasks) * 100, 1) : 0;

        return $summary;
    }


    public function get_campaign_summary($campaign_id) {
        if (empty($campaign_id)) return null;

        $this->db->select('
            c.id,
            c.name,
            c.status,
            c.budget as total_budget,
            c.target_views,
            c.target_cpv,
            c.start_date,
            c.end_date,
            c.brand_name
        ');
        $this->db->from('campaigns c');
        $this->db->where('c.id', $campaign_id);
        $camp = $this->db->get()->row();
        if (!$camp) return null;

        // Granular committed spent: sum task budget per KOL, or fallback to deal_amount if task not generated yet
        $this->db->select('
            ck.id,
            COALESCE(SUM(t.budget), 0) as task_budget,
            ck.deal_amount
        ');
        $this->db->from('campaign_kols ck');
        $this->db->join('tasks t', 't.campaign_kol_id = ck.id', 'left');
        $this->db->where('ck.campaign_id', $campaign_id);
        $this->db->group_by('ck.id');
        $kol_spents = $this->db->get()->result();

        $total_spent = 0;
        foreach ($kol_spents as $ks) {
            $total_spent += ($ks->task_budget > 0) ? (float)$ks->task_budget : (float)$ks->deal_amount;
        }
        $camp->actual_spent = $total_spent;

        // Actual Views & Engagements
        $this->db->select('
            COALESCE(SUM(pm.views), 0) as actual_views,
            COALESCE(SUM(pm.likes + pm.comments + pm.shares + pm.saves), 0) as actual_engagements
        ');
        $this->db->from('posting_metrics pm');
        $this->db->join('postings p', 'p.id = pm.posting_id', 'inner');
        $this->db->join('tasks t', 't.id = p.task_id', 'inner');
        $this->db->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'inner');
        $this->db->where('ck.campaign_id', $campaign_id);
        $metrics_res = $this->db->get()->row();

        $camp->actual_views = (int)($metrics_res->actual_views ?? 0);
        $camp->actual_engagements = (int)($metrics_res->actual_engagements ?? 0);

        // CPV Calculation
        $camp->actual_cpv = ($camp->actual_views > 0) ? ($camp->actual_spent / $camp->actual_views) : 0;

        // Percentage indicators
        $camp->budget_used_pct = ($camp->total_budget > 0) ? min(100, ($camp->actual_spent / $camp->total_budget) * 100) : 0;
        $camp->remaining_budget = max(0, $camp->total_budget - $camp->actual_spent);
        $camp->view_attainment_pct = ($camp->target_views > 0) ? round(($camp->actual_views / $camp->target_views) * 100, 1) : 0;

        // CPV efficiency savings percentage and nominal delta
        if ($camp->actual_cpv > 0 && $camp->target_cpv > 0) {
            $camp->cpv_savings_pct = round((($camp->target_cpv - $camp->actual_cpv) / $camp->target_cpv) * 100, 1);
            $camp->cpv_diff = round($camp->target_cpv - $camp->actual_cpv, 2);
        } else {
            $camp->cpv_savings_pct = 0;
            $camp->cpv_diff = 0;
        }

        // Status Reach Badge
        if ($camp->actual_views == 0) {
            $camp->reach_status = 'PENDING';
            $camp->reach_label  = 'Belum Ada Views';
            $camp->reach_badge  = 'badge-soft-slate';
        } elseif ($camp->view_attainment_pct >= 110) {
            $camp->reach_status = 'OVERACHIEVED';
            $camp->reach_label  = 'Overachieved';
            $camp->reach_badge  = 'badge-soft-emerald';
        } elseif ($camp->view_attainment_pct >= 95) {
            $camp->reach_status = 'ACHIEVED';
            $camp->reach_label  = 'Achieved';
            $camp->reach_badge  = 'badge-soft-blue';
        } else {
            $camp->reach_status = 'UNDER_TARGET';
            $camp->reach_label  = 'Under Target';
            $camp->reach_badge  = 'badge-soft-rose';
        }

        return $camp;
    }

    public function get_campaign_pics($campaign_id) {
        if (empty($campaign_id)) return [];

        $this->db->select('
            u.id,
            u.full_name,
            u.username,
            u.avatar,
            COUNT(DISTINCT ck.id) as kol_count,
            COALESCE(SUM(ck.deal_amount), 0) as total_deal_amount
        ');
        $this->db->from('campaign_kols ck');
        $this->db->join('aauth_users u', 'u.id = ck.assigned_by', 'inner');
        $this->db->where('ck.campaign_id', $campaign_id);
        $this->db->group_by('u.id');
        $pics = $this->db->get()->result();

        // Fallback: if assigned_by not set, check campaigns.pic_user_id
        if (empty($pics)) {
            $this->db->select('
                u.id,
                u.full_name,
                u.username,
                u.avatar,
                (SELECT COUNT(id) FROM campaign_kols WHERE campaign_id = '.$this->db->escape($campaign_id).') as kol_count,
                (SELECT COALESCE(SUM(deal_amount), 0) FROM campaign_kols WHERE campaign_id = '.$this->db->escape($campaign_id).') as total_deal_amount
            ');
            $this->db->from('campaigns c');
            $this->db->join('aauth_users u', 'u.id = c.pic_user_id', 'inner');
            $this->db->where('c.id', $campaign_id);
            $pics = $this->db->get()->result();
        }

        return $pics;
    }

    public function get_campaign_kols_performance($campaign_id, $pic_id = null) {
        if (empty($campaign_id)) return [];

        $this->db->select('
            ck.id as campaign_kol_id,
            ck.deal_amount,
            ck.jumlah_vt,
            ck.status as ck_status,
            k.id as kol_id,
            k.name as kol_name,
            k.email as kol_username,
            k.avatar_url as kol_avatar,
            u.id as pic_id,
            u.full_name as pic_name,
            u.username as pic_username,
            c.target_cpv as campaign_target_cpv,
            c.target_views as campaign_target_views
        ');
        $this->db->from('campaign_kols ck');
        $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'inner');
        $this->db->join('kols k', 'k.id = ck.kol_id', 'inner');
        $this->db->join('aauth_users u', 'u.id = ck.assigned_by', 'left');
        $this->db->where('ck.campaign_id', $campaign_id);
        if (!empty($pic_id)) {
            $this->db->where('ck.assigned_by', $pic_id);
        }
        $kols = $this->db->get()->result();

        foreach ($kols as $row) {
            $this->db->select('
                t.id as task_id,
                t.title as task_title,
                t.budget as task_budget,
                t.cpv as task_cpv,
                t.status as task_status,
                p.id as posting_id,
                p.live_url
            ');
            $this->db->from('tasks t');
            $this->db->join('postings p', 'p.task_id = t.id', 'left');
            $this->db->where('t.campaign_kol_id', $row->campaign_kol_id);
            $tasks = $this->db->get()->result();

            $total_cost = 0;
            $total_views = 0;
            $total_engagements = 0;
            $live_urls = [];

            foreach ($tasks as $t) {
                $total_cost += (float)$t->task_budget;
                if (!empty($t->live_url)) {
                    $live_urls[] = $t->live_url;
                }
                if ($t->posting_id) {
                    $this->db->select('COALESCE(views, 0) as views, COALESCE(likes + comments + shares + saves, 0) as engagements');
                    $this->db->from('posting_metrics');
                    $this->db->where('posting_id', $t->posting_id);
                    $m = $this->db->get()->row();
                    if ($m) {
                        $total_views += (int)$m->views;
                        $total_engagements += (int)$m->engagements;
                    }
                }
            }

            $row->cost = ($total_cost > 0) ? $total_cost : (float)$row->deal_amount;
            $row->views = $total_views;
            $row->engagements = $total_engagements;
            $row->live_urls = $live_urls;
            $row->task_count = count($tasks);

            $row->target_cpv = (float)($row->campaign_target_cpv > 0 ? $row->campaign_target_cpv : 0);
            if ($row->target_cpv > 0 && $row->cost > 0) {
                $row->target_views = (int)round($row->cost / $row->target_cpv);
            } else {
                $row->target_views = 0;
            }

            $row->actual_cpv = ($row->views > 0) ? round($row->cost / $row->views, 2) : 0;
            $row->attainment_pct = ($row->target_views > 0) ? round(($row->views / $row->target_views) * 100, 1) : 0;

            if ($row->views == 0) {
                $row->perf_status = 'PENDING';
                $row->perf_label  = 'Belum Tayang';
                $row->perf_badge  = 'badge-soft-slate';
            } elseif ($row->attainment_pct >= 110 || ($row->actual_cpv <= $row->target_cpv * 0.85 && $row->target_cpv > 0)) {
                $row->perf_status = 'OVERACHIEVED';
                $row->perf_label  = 'Overachieved';
                $row->perf_badge  = 'badge-soft-emerald';
            } elseif ($row->attainment_pct >= 95 || ($row->actual_cpv <= $row->target_cpv && $row->target_cpv > 0)) {
                $row->perf_status = 'ACHIEVED';
                $row->perf_label  = 'Achieved';
                $row->perf_badge  = 'badge-soft-blue';
            } else {
                $row->perf_status = 'UNDER_TARGET';
                $row->perf_label  = 'Under Target';
                $row->perf_badge  = 'badge-soft-rose';
            }
        }

        return $kols;
    }


    public function get_executive_summary($filter = []) {
        // Global sums
        $this->db->select('
            COUNT(DISTINCT c.id) as total_campaigns,
            SUM(c.budget) as total_budget,
            SUM(c.target_views) as total_target_views,
            AVG(c.target_cpv) as avg_target_cpv,
            AVG(c.target_cpe) as avg_target_cpe
        ');
        $this->db->from('campaigns c');
        $this->_apply_filters($filter, 'c', 'pic_user_id');
        $global = $this->db->get()->row();

        // Actual Spent (from unique tasks)
        $this->db->select('SUM(t.budget) as actual_spent');
        $this->db->from('tasks t');
        $this->db->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'inner');
        $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'inner');
        $this->_apply_filters($filter, 'c', 'pic_user_id');
        $spent_res = $this->db->get()->row();
        
        // Actual Views and Engagements (from posting metrics)
        $this->db->select('
            SUM(pm.views) as actual_views,
            SUM(pm.likes + pm.comments + pm.shares + pm.saves) as actual_engagements
        ');
        $this->db->from('posting_metrics pm');
        $this->db->join('postings p', 'p.id = pm.posting_id', 'inner');
        $this->db->join('tasks t', 't.id = p.task_id', 'inner');
        $this->db->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'inner');
        $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'inner');
        $this->_apply_filters($filter, 'c', 'pic_user_id');
        $metrics_res = $this->db->get()->row();

        $global->actual_spent = $spent_res->actual_spent ?? 0;
        $global->actual_views = $metrics_res->actual_views ?? 0;
        $global->actual_engagements = $metrics_res->actual_engagements ?? 0;

        $global->actual_cpv = ($global->actual_views > 0) ? ($global->actual_spent / $global->actual_views) : 0;
        
        return $global;
    }

    public function get_leader_kpi($filter = []) {
        $this->db->select('
            c.pic_user_id, 
            u.full_name,
            u.username,
            COUNT(DISTINCT c.id) as total_campaigns,
            SUM(c.budget) as total_budget,
            SUM(c.target_views) as total_target_views,
            AVG(c.target_cpv) as avg_target_cpv
        ');
        $this->db->from('campaigns c');
        $this->db->join('aauth_users u', 'u.id = c.pic_user_id', 'left');
        $this->_apply_filters($filter, 'c', 'pic_user_id');
        $this->db->group_by('c.pic_user_id');
        $leaders = $this->db->get()->result();

        foreach ($leaders as $l) {
            // Actual Spent
            $this->db->select('SUM(t.budget) as actual_spent');
            $this->db->from('tasks t');
            $this->db->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'inner');
            $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'inner');
            $this->db->where('c.pic_user_id', $l->pic_user_id);
            $this->_apply_filters($filter, 'c', 'pic_user_id');
            $spent_res = $this->db->get()->row();

            // Actual Views & Engagements
            $this->db->select('
                SUM(pm.views) as actual_views,
                SUM(pm.likes + pm.comments + pm.shares + pm.saves) as actual_engagements
            ');
            $this->db->from('posting_metrics pm');
            $this->db->join('postings p', 'p.id = pm.posting_id', 'inner');
            $this->db->join('tasks t', 't.id = p.task_id', 'inner');
            $this->db->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'inner');
            $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'inner');
            $this->db->where('c.pic_user_id', $l->pic_user_id);
            $this->_apply_filters($filter, 'c', 'pic_user_id');
            $metrics_res = $this->db->get()->row();

            $l->actual_spent = $spent_res->actual_spent ?? 0;
            $l->actual_views = $metrics_res->actual_views ?? 0;
            $l->actual_engagements = $metrics_res->actual_engagements ?? 0;

            $l->actual_cpv = ($l->actual_views > 0) ? ($l->actual_spent / $l->actual_views) : 0;
            $l->view_attainment_pct = ($l->total_target_views > 0) ? ($l->actual_views / $l->total_target_views) * 100 : 0;
            $l->budget_efficiency_pct = ($l->total_budget > 0) ? ($l->actual_spent / $l->total_budget) * 100 : 0;

            // Deal Conversion Rate
            $this->db->select('
                COUNT(ck.id) as total_negotiated,
                SUM(CASE WHEN ck.status IN (\'ACTIVE\', \'COMPLETED\') THEN 1 ELSE 0 END) as deal_success
            ');
            $this->db->from('campaign_kols ck');
            $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'left');
            $this->db->where('c.pic_user_id', $l->pic_user_id);
            $this->_apply_filters($filter, 'c', 'pic_user_id');
            $conv = $this->db->get()->row();
            
            $l->deal_conversion_pct = ($conv->total_negotiated > 0) ? ($conv->deal_success / $conv->total_negotiated) * 100 : 0;
        }

        return $leaders;
    }

    public function get_operational_kpi($filter = []) {
        $this->db->select('
            COUNT(p.id) as total_postings,
            SUM(CASE WHEN p.date_validation_status = \'ON_TIME\' THEN 1 ELSE 0 END) as on_time_postings,
            SUM(CASE WHEN p.date_validation_status = \'LATE\' THEN 1 ELSE 0 END) as late_postings,
            SUM(CASE WHEN t.status = \'DONE\' THEN 1 ELSE 0 END) as done_tasks
        ');
        $this->db->from('postings p');
        $this->db->join('tasks t', 't.id = p.task_id', 'left');
        $this->db->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'left');
        $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'left');
        $this->_apply_filters($filter, 'c', 'pic_user_id');
        $sla = $this->db->get()->row();

        $this->db->select('
            COUNT(d.id) as total_drafts,
            AVG(d.version) as avg_revision_cycles,
            AVG(CASE WHEN d.status = \'APPROVED\' THEN TIMESTAMPDIFF(HOUR, d.created_at, d.updated_at) ELSE NULL END) as avg_turnaround_hours
        ');
        $this->db->from('drafts d');
        $this->db->join('tasks t', 't.id = d.task_id', 'left');
        $this->db->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'left');
        $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'left');
        $this->_apply_filters($filter, 'c', 'pic_user_id');
        $draft_stats = $this->db->get()->row();

        $this->db->select('AVG(DATEDIFF(s.shipped_at, ck.deal_date)) as avg_dispatch_days');
        $this->db->from('kol_shipments s');
        $this->db->join('campaign_kols ck', 'ck.id = s.campaign_kol_id', 'inner');
        $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'left');
        $this->db->where('s.shipped_at IS NOT NULL');
        $this->_apply_filters($filter, 'c', 'pic_user_id');
        $dispatch_stats = $this->db->get()->row();

        return [
            'sla' => $sla,
            'drafts' => $draft_stats,
            'dispatch' => $dispatch_stats
        ];
    }

    public function get_admin_kpi($filter = []) {
        $this->db->select('
            COUNT(i.id) as total_invoices,
            SUM(CASE WHEN i.status = \'PAID\' THEN 1 ELSE 0 END) as paid_invoices,
            SUM(CASE WHEN i.status = \'PAID\' AND i.approved_at <= i.due_date THEN 1 ELSE 0 END) as paid_on_time,
            AVG(DATEDIFF(i.approved_at, i.created_at)) as avg_processing_days,
            SUM(CASE WHEN i.bupot_file IS NOT NULL AND i.rejection_reason IS NULL THEN 1 ELSE 0 END) as tax_compliant_invoices
        ');
        $this->db->from('invoices i');
        $this->db->join('campaign_kols ck', 'ck.id = i.campaign_kol_id', 'left');
        $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'left');
        $this->_apply_filters($filter, 'c', 'pic_user_id');
        $admin = $this->db->get()->row();

        $this->db->select('COUNT(id) as new_kols');
        $this->db->from('kols');
        $admin->new_kols = $this->db->get()->row()->new_kols ?? 0;
        
        return $admin;
    }

    public function get_campaign_scatter_data($filter = []) {
        $this->db->select('c.id, c.name, c.budget, c.target_views, c.target_cpv');
        $this->db->from('campaigns c');
        $this->_apply_filters($filter, 'c', 'pic_user_id');
        $campaigns = $this->db->get()->result();

        foreach ($campaigns as $c) {
            $this->db->select('SUM(t.budget) as actual_spent');
            $this->db->from('tasks t');
            $this->db->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'inner');
            $this->db->where('ck.campaign_id', $c->id);
            $spent_res = $this->db->get()->row();

            $this->db->select('SUM(pm.views) as actual_views');
            $this->db->from('posting_metrics pm');
            $this->db->join('postings p', 'p.id = pm.posting_id', 'inner');
            $this->db->join('tasks t', 't.id = p.task_id', 'inner');
            $this->db->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'inner');
            $this->db->where('ck.campaign_id', $c->id);
            $metrics_res = $this->db->get()->row();

            $c->actual_views = $metrics_res->actual_views ?? 0;
            $c->actual_spent = $spent_res->actual_spent ?? 0;
            $c->actual_cpv = ($c->actual_views > 0) ? ($c->actual_spent / $c->actual_views) : 0;
        }
        return $campaigns;
    }

    public function get_kol_drilldown_data($filter = []) {
        // Here we just fetch tasks, and then subquery their postings to avoid fan-out
        $this->db->select('
            c.name as campaign_name,
            c.target_cpv,
            k.name as kol_name,
            k.email as kol_username,
            u.full_name as pic_name,
            u.username as pic_username,
            t.id as task_id,
            t.budget as cost
        ');
        $this->db->from('tasks t');
        $this->db->join('campaign_kols ck', 'ck.id = t.campaign_kol_id', 'inner');
        $this->db->join('campaigns c', 'c.id = ck.campaign_id', 'inner');
        $this->db->join('kols k', 'k.id = ck.kol_id', 'inner');
        $this->db->join('aauth_users u', 'u.id = ck.assigned_by', 'left');
        $this->_apply_filters($filter, 'c', 'pic_user_id');
        $results = $this->db->get()->result();

        foreach ($results as $r) {
            $this->db->select('SUM(pm.views) as views, SUM(pm.likes + pm.comments + pm.shares + pm.saves) as engagements');
            $this->db->from('posting_metrics pm');
            $this->db->join('postings p', 'p.id = pm.posting_id', 'inner');
            $this->db->where('p.task_id', $r->task_id);
            $m = $this->db->get()->row();
            
            $r->views = $m->views ?? 0;
            $r->engagements = $m->engagements ?? 0;
            
            $r->actual_cpv = ($r->views > 0) ? ($r->cost / $r->views) : 0;
            $r->actual_cpe = ($r->engagements > 0) ? ($r->cost / $r->engagements) : 0;
        }
        
        return $results;
    }

    private function _apply_filters($filter, $table_alias = 'c', $pic_column = 'pic_user_id') {
        if (!empty($filter['campaign_id'])) {
            $this->db->where("$table_alias.id", $filter['campaign_id']);
        }
        if (!empty($filter['pic_id'])) {
            $this->db->where("$table_alias.$pic_column", $filter['pic_id']);
        }
    }
}
