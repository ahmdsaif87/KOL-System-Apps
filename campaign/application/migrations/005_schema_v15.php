<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration 005: Nuke KOL tables & rebuild from schema v1.5
 *
 * WARNING: This drops ALL KOL-system tables and recreates them from
 * kol_system_schema_v1.5.sql. All existing KOL data will be lost.
 *
 * Changes from v1.4 → v1.5 (deal-first budgeting model):
 * - tasks: `rate` RENAMED to `budget`
 * - task_rate_histories RENAMED to `task_budget_histories` (old_rate/new_rate -> old_budget/new_budget)
 * - campaign_kols: +`deal_amount` (DECIMAL 15,2), +`currency_code` (CHAR 3)
 * - campaign_kols: `sow` RENAMED to `deal_scope` (LONGTEXT)
 */
class Migration_Schema_v15 extends CI_Migration {

    /**
     * KOL-system tables in dependency order (children first).
     * Cicool core tables (aauth_*, ci_*, dll) are NOT touched.
     */
    private $kol_tables = [
        // Scoring & analytics
        'score_histories',
        // Apify
        'apify_jobs',
        // Performance
        'posting_metrics',
        // Posting
        'postings',
        // Draft
        'draft_review_histories',
        'drafts',
        // Brief
        'brief_content_histories',
        'brief_approval_histories',
        'briefs',
        // Task
        'task_budget_histories',
        'task_rate_histories', // Legacy table name in v1.4
        'task_status_histories',
        'tasks',
        // Campaign-KOL
        'campaign_kol_variants',
        'campaign_kols',
        'campaign_variants',
        'campaigns',
        // KOL master
        'kol_rate_cards',
        'kol_platforms',
        'kol_categories',
        'kol_approval_histories',
        'kols',
        // Master data
        'variants',
        'platforms',
        'categories',
        'tiers',
        // Generic
        'attachments',
        'audit_logs',
    ];

    public function up()
    {
        // 1. Drop all existing KOL tables (leave Cicool core intact)
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
        foreach ($this->kol_tables as $table) {
            $this->dbforge->drop_table($table, TRUE);
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');

        // 2. Execute kol_system_schema_v1.5.sql
        $sql_file = FCPATH . 'kol_system_schema_v1.5.sql';
        if (!file_exists($sql_file)) {
            show_error('kol_system_schema_v1.5.sql file not found in project root directory.');
            return;
        }

        $sql = file_get_contents($sql_file);
        $mysqli = $this->db->conn_id;

        if ($mysqli instanceof mysqli) {
            // Use multi_query for proper handling of multi-statement SQL
            if (!$mysqli->multi_query($sql)) {
                show_error("Failed to execute kol_system_schema_v1.5.sql: " . $mysqli->error);
            }

            // Consume all results to free the connection for subsequent queries
            do {
                if ($result = $mysqli->store_result()) {
                    $result->free();
                }
            } while ($mysqli->more_results() && $mysqli->next_result());

            // Check for errors in the last result set
            if ($mysqli->errno) {
                show_error("Error during kol_system_schema_v1.5.sql execution: " . $mysqli->error);
            }
        } else {
            // Fallback for non-mysqli driver (PDO, etc.)
            // Remove single-line comments but preserve strings
            $sql = preg_replace('/--.*$/m', '', $sql);
            $sqls = explode(';', $sql);
            foreach ($sqls as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    $this->db->query($query);
                }
            }
        }

        log_message('info', 'Migration 005: KOL system schema v1.5 applied successfully.');
    }

    public function down()
    {
        // Drop all KOL tables; cannot restore data after nuke
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
        foreach ($this->kol_tables as $table) {
            $this->dbforge->drop_table($table, TRUE);
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');

        log_message('info', 'Migration 005 rolled back: all KOL tables dropped.');
    }
}
