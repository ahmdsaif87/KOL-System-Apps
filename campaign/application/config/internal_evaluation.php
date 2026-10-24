<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
 * Internal Evaluation — tunable scoring thresholds.
 * Loaded by Model_internal_evaluation::_thresholds().
 * Change numbers here, not in the model.
 */
$config['internal_evaluation'] = [
    // SLA On-Time brackets: [min_rate => points], evaluated top-down, first match wins. Max 40 pts.
    'on_time_brackets' => [
        ['min' => 90, 'points' => 40],
        ['min' => 75, 'points' => 30],
        ['min' => 50, 'points' => 20],
        ['min' => 0,  'points' => 10],
    ],
    // Draft review TAT (hours): [max => points], top-down. Max 30 pts.
    'tat_brackets' => [
        ['max' => 2,  'points' => 30],
        ['max' => 8,  'points' => 25],
        ['max' => 24, 'points' => 15],
        ['max' => PHP_FLOAT_MAX, 'points' => 5],
    ],
    // Sample dispatch (days, deal_date -> shipped_at): [max => points], top-down. Max 30 pts.
    'dispatch_brackets' => [
        ['max' => 1,  'points' => 30],
        ['max' => 3,  'points' => 25],
        ['max' => 5,  'points' => 15],
        ['max' => PHP_FLOAT_MAX, 'points' => 5],
    ],
    // Grade cutoffs (total 100)
    'grade_a_min' => 85,
    'grade_b_min' => 65,
    // Drill-down cap per staff modal (perf guard)
    'tasks_per_staff_limit' => 50,
    // Staff list page size
    'staff_page_size' => 200,
    // Provisional-grade guards
    'min_decided_postings' => 5,
    'min_reviewed_drafts' => 3,
    'provisional_suffix' => 'Sementara',
    // Grace period after campaign end_date before a COMPLETED campaign
    // grade becomes final (waiting for Apify scraping H+3/H+7 to mature).
    'evaluation_grace_days' => 7,

    // ==========================================
    // LEADER EVALUATION CONFIGURATIONS
    // ==========================================
    // Target user IDs for Leader aggregation (fallback: 25)
    'leader_target_user_ids' => [25],
    
    // Total Weights: View (50) + CPV (20) + Deal (15) + Approval TAT (15) = 100
    'leader_view_brackets' => [
        ['min' => 100, 'points' => 50],
        ['min' => 80,  'points' => 40],
        ['min' => 50,  'points' => 20],
        ['min' => 0,   'points' => 10],
    ],
    'leader_cpv_brackets' => [
        ['max' => 50,  'points' => 20],
        ['max' => 100, 'points' => 15],
        ['max' => 200, 'points' => 5],
        ['max' => PHP_FLOAT_MAX, 'points' => 0],
    ],
    'leader_deal_brackets' => [
        ['min' => 50, 'points' => 15],
        ['min' => 30, 'points' => 10],
        ['min' => 10, 'points' => 5],
        ['min' => 0,  'points' => 0],
    ],
    'leader_approval_brackets' => [
        ['max' => 2,  'points' => 15],
        ['max' => 8,  'points' => 10],
        ['max' => 24, 'points' => 5],
        ['max' => PHP_FLOAT_MAX, 'points' => 0],
    ],

    // Leader Grade cutoffs (total 100)
    'leader_grade_a_min' => 85,
    'leader_grade_b_min' => 65,
    
    // Minimum campaigns required to remove "Provisional" status for leaders
    'min_leader_campaigns' => 3,
];
