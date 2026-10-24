<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration 004: Nuke KOL tables & rebuild from schema v1.4
 *
 * WARNING: This drops ALL KOL-system tables and recreates them from
 * kol_system_schema_v1.4.sql. All existing KOL data will be lost.
 *
 * Changes from v1.3 → v1.4:
 * - kols: +date_of_birth, +province, +address, +country, +avatar_url
 * - kol_platforms: +last_synced_at
 * - campaigns: +description, +campaign_type, +client_name, +brand_category, +pic_user_id
 * - tasks: +content_type, +title
 * - postings: +caption, +thumbnail_url, +duration_seconds, +status
 * - briefs: +revision_count
 * - NEW TABLE: brief_content_histories
 */
class Migration_Schema_v14 extends CI_Migration {

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
        'task_rate_histories',
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

        // 2. Execute kol_system_schema_v1.4.sql
        $sql_file = FCPATH . 'kol_system_schema_v1.4.sql';
        if (!file_exists($sql_file)) {
            show_error('kol_system_schema_v1.4.sql file not found in project root directory.');
            return;
        }

        $sql = file_get_contents($sql_file);
        $mysqli = $this->db->conn_id;

        if ($mysqli instanceof mysqli) {
            // Use multi_query for proper handling of multi-statement SQL
            if (!$mysqli->multi_query($sql)) {
                show_error("Failed to execute kol_system_schema_v1.4.sql: " . $mysqli->error);
            }

            // Consume all results to free the connection for subsequent queries
            do {
                if ($result = $mysqli->store_result()) {
                    $result->free();
                }
            } while ($mysqli->more_results() && $mysqli->next_result());

            // Check for errors in the last result set
            if ($mysqli->errno) {
                show_error("Error during kol_system_schema_v1.4.sql execution: " . $mysqli->error);
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

        log_message('info', 'Migration 004: KOL system schema v1.4 applied successfully.');
    }

    public function down()
    {
        // Drop all KOL tables; cannot restore data after nuke
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
        foreach ($this->kol_tables as $table) {
            $this->dbforge->drop_table($table, TRUE);
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');

        log_message('info', 'Migration 004 rolled back: all KOL tables dropped.');
    }
}
