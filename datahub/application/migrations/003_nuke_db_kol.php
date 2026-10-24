<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Nuke_db_kol extends CI_Migration {

    public function up()
    {
        // 1. Drop all existing tables
        $tables = $this->db->list_tables();
        
        $this->db->query('SET FOREIGN_KEY_CHECKS = 0;');
        foreach ($tables as $table) {
            $this->dbforge->drop_table($table, TRUE);
        }
        $this->db->query('SET FOREIGN_KEY_CHECKS = 1;');

        // 2. Execute kol.sql
        $sql_file = FCPATH . 'kol.sql';
        if (file_exists($sql_file)) {
            $sql = file_get_contents($sql_file);
            
            $mysqli = $this->db->conn_id;
            
            if ($mysqli instanceof mysqli) {
                // Use multi_query which safely handles strings containing semicolons
                if (!$mysqli->multi_query($sql)) {
                    show_error("Failed to execute kol.sql: " . $mysqli->error);
                }
                
                // Consume all results to free the connection for subsequent queries
                do {
                    if ($result = $mysqli->store_result()) {
                        $result->free();
                    }
                } while ($mysqli->more_results() && $mysqli->next_result());
                
            } else {
                // Fallback for non-mysqli driver
                $sql = preg_replace('/--.*$/m', '', $sql);
                $sqls = explode(';', $sql);
                foreach ($sqls as $query) {
                    $query = trim($query);
                    if (!empty($query)) {
                        $this->db->query($query);
                    }
                }
            }
        } else {
            show_error('kol.sql file not found in root directory.');
        }
    }

    public function down()
    {
        // Cannot realistically restore nuked DB
    }
}
