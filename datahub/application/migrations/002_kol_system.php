<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Kol_system extends CI_Migration {

    public function up()
    {
        $sql_file = FCPATH . 'kol_system_schema_v1.sql';
        if (file_exists($sql_file)) {
            $sql = file_get_contents($sql_file);
            // Hapus comments
            $sql = preg_replace('/--.*$/m', '', $sql);
            $sqls = explode(';', $sql);
            foreach ($sqls as $query) {
                $query = trim($query);
                if (!empty($query)) {
                    $this->db->query($query);
                }
            }
        } else {
            show_error('kol_system_schema_v1.sql file not found in root.');
        }
    }

    public function down()
    {
        // Drop tables in reverse order to handle foreign keys
        $tables = [
            'posting_metrics',
            'postings',
            'drafts',
            'brief_approval_histories',
            'briefs',
            'task_rate_histories',
            'task_status_histories',
            'tasks',
            'campaign_kol_variants',
            'campaign_kols',
            'campaign_variants',
            'campaigns',
            'kol_rate_cards',
            'kol_platforms',
            'kol_categories',
            'kols',
            'platforms',
            'categories',
            'tiers',
            'attachments',
            'audit_logs',
            'apify_jobs',
            'score_histories'
        ];
        
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
        foreach ($tables as $table) {
            $this->dbforge->drop_table($table, TRUE);
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');
    }
}
