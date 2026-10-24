<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Gmv_porto_tables extends CI_Migration
{
    public function up()
    {
        // ── 1. Add platform_uid to kol_platforms (idempotent) ──────────────
        $fields_kp = $this->db->query("SHOW COLUMNS FROM `kol_platforms` LIKE 'platform_uid'")->num_rows();
        if ($fields_kp === 0) {
            $this->dbforge->add_column('kol_platforms', [
                'platform_uid' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => TRUE,
                    'default'    => NULL,
                    'comment'    => 'TikTok numeric UID for FastMoss API (cached permanently)',
                    'after'      => 'username',
                ],
            ]);
        }

        // ── 2. Verify kols GMV columns exist (should already be there) ──────
        $gmv_cols = ['total_gmv_estimate', 'gmv_updated_at', 'gmv_sync_status', 'gmv_sync_message'];
        foreach ($gmv_cols as $col) {
            $exists = $this->db->query("SHOW COLUMNS FROM `kols` LIKE '$col'")->num_rows();
            if ($exists === 0) {
                // Defensive add — spec says they should already exist
                switch ($col) {
                    case 'total_gmv_estimate':
                        $this->dbforge->add_column('kols', [
                            'total_gmv_estimate' => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => TRUE, 'default' => NULL],
                        ]);
                        break;
                    case 'gmv_updated_at':
                        $this->dbforge->add_column('kols', [
                            'gmv_updated_at' => ['type' => 'DATETIME', 'null' => TRUE, 'default' => NULL],
                        ]);
                        break;
                    case 'gmv_sync_status':
                        $this->db->query("ALTER TABLE `kols` ADD COLUMN `gmv_sync_status` ENUM('OK','EMPTY','FAILED','NEVER','SKIP') NOT NULL DEFAULT 'NEVER'");
                        break;
                    case 'gmv_sync_message':
                        $this->dbforge->add_column('kols', [
                            'gmv_sync_message' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => TRUE, 'default' => NULL],
                        ]);
                        break;
                }
            }
        }

        // ── 3. Create kol_shop_snapshots ────────────────────────────────────
        $table_exists = $this->db->query("SHOW TABLES LIKE 'kol_shop_snapshots'")->num_rows();
        if ($table_exists === 0) {
            $this->dbforge->add_field([
                'id' => [
                    'type'           => 'BIGINT',
                    'constraint'     => 20,
                    'unsigned'       => TRUE,
                    'auto_increment' => TRUE,
                ],
                'kol_id' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'unsigned'   => TRUE,
                    'null'       => FALSE,
                ],
                'platform_username' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 150,
                    'null'       => FALSE,
                    'default'    => '',
                ],
                'product_id' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => FALSE,
                ],
                'title' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 500,
                    'null'       => TRUE,
                    'default'    => NULL,
                ],
                'product_url' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 1000,
                    'null'       => TRUE,
                    'default'    => NULL,
                ],
                'shop_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => TRUE,
                    'default'    => NULL,
                ],
                'price' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                    'null'       => TRUE,
                    'default'    => NULL,
                    'comment'    => 'Price in IDR (or currency unit)',
                ],
                'sold_count' => [
                    'type'       => 'BIGINT',
                    'constraint' => 20,
                    'null'       => TRUE,
                    'default'    => NULL,
                ],
                'gmv_estimate' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '15,2',
                    'null'       => TRUE,
                    'default'    => NULL,
                    'comment'    => 'Native GMV from FastMoss, or price*sold_count if native unavailable',
                ],
                'currency' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 10,
                    'null'       => FALSE,
                    'default'    => 'IDR',
                ],
                'rating' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '4,2',
                    'null'       => TRUE,
                    'default'    => NULL,
                ],
                'review_count' => [
                    'type'    => 'INT',
                    'null'    => TRUE,
                    'default' => NULL,
                ],
                'snapshot_at' => [
                    'type' => 'DATE',
                    'null' => FALSE,
                ],
                'raw_json' => [
                    'type' => 'MEDIUMTEXT',
                    'null' => TRUE,
                ],
                'created_at' => [
                    'type'    => 'DATETIME',
                    'null'    => FALSE,
                    'default' => 'CURRENT_TIMESTAMP',
                ],
            ]);

            $this->dbforge->add_key('id', TRUE); // PK
            $this->dbforge->create_table('kol_shop_snapshots', TRUE); // IF NOT EXISTS

            // Unique constraint: one product per kol per snapshot date
            $this->db->query("ALTER TABLE `kol_shop_snapshots`
                ADD UNIQUE KEY `uq_kol_product_snapshot` (`kol_id`, `product_id`, `snapshot_at`)");

            // Index for fast per-kol queries
            $this->db->query("ALTER TABLE `kol_shop_snapshots`
                ADD INDEX `idx_kol_snapshot` (`kol_id`, `snapshot_at`)");
        }
    }

    public function down()
    {
        $this->dbforge->drop_table('kol_shop_snapshots', TRUE);
        // Not dropping platform_uid — too risky (may have live data)
    }
}
