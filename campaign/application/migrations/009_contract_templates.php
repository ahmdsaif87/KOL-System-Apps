<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Contract_templates extends CI_Migration {

    public function up()
    {
        // 1. Create contract_templates table
        $this->db->query("
            CREATE TABLE IF NOT EXISTS contract_templates (
                id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                title               VARCHAR(255)    NOT NULL,
                content             TEXT            NULL,
                created_by          BIGINT UNSIGNED NULL,
                created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 2. Modify campaign_kol_contracts
        $this->db->query("
            ALTER TABLE campaign_kol_contracts
            ADD COLUMN contract_template_id BIGINT UNSIGNED NULL AFTER campaign_kol_id,
            ADD COLUMN contract_html TEXT NULL AFTER contract_file,
            ADD COLUMN kol_signature_image TEXT NULL AFTER contract_html,
            ADD COLUMN admin_signature_image TEXT NULL AFTER kol_signature_image,
            ADD COLUMN expired_at DATE NULL AFTER signed_at,
            MODIFY COLUMN status ENUM('DRAFT','SENT','KOL_REVIEWED','SIGNED','ACTIVE','COMPLETED','EXPIRED','CANCELLED') NOT NULL DEFAULT 'DRAFT';
        ");
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS contract_templates;");

        $this->db->query("
            ALTER TABLE campaign_kol_contracts
            DROP COLUMN contract_template_id,
            DROP COLUMN contract_html,
            DROP COLUMN kol_signature_image,
            DROP COLUMN admin_signature_image,
            DROP COLUMN expired_at,
            MODIFY COLUMN status ENUM('DRAFT','WAITING_KOL_SIGNATURE','SIGNED','VOID') NOT NULL DEFAULT 'DRAFT';
        ");
    }
}
