<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Schema_v17 extends CI_Migration {

    public function up()
    {
        // 1. Update tabel kols (Tambah Informasi Bank, NPWP, dan KTP)
        $this->db->query("
            ALTER TABLE kols 
            ADD COLUMN bank_name VARCHAR(100) NULL AFTER rate_card_file,
            ADD COLUMN bank_account_number VARCHAR(50) NULL AFTER bank_name,
            ADD COLUMN bank_account_name VARCHAR(150) NULL AFTER bank_account_number,
            ADD COLUMN npwp_number VARCHAR(50) NULL AFTER bank_account_name,
            ADD COLUMN npwp_file VARCHAR(500) NULL AFTER npwp_number,
            ADD COLUMN ktp_number VARCHAR(50) NULL AFTER npwp_file,
            ADD COLUMN ktp_file VARCHAR(500) NULL AFTER ktp_number;
        ");

        // 2. Tambah tabel campaign_kol_contracts
        $this->db->query("
            CREATE TABLE IF NOT EXISTS campaign_kol_contracts (
                id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                campaign_kol_id     BIGINT UNSIGNED NOT NULL,
                contract_no         VARCHAR(100)    NOT NULL,
                contract_file       VARCHAR(500)    NULL,
                status              ENUM('DRAFT','WAITING_KOL_SIGNATURE','SIGNED','VOID') NOT NULL DEFAULT 'DRAFT',
                signed_at           DATETIME        NULL,
                created_by          BIGINT UNSIGNED NULL,
                created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uk_contract_no (contract_no),
                KEY idx_contracts_campaign_kol (campaign_kol_id),
                CONSTRAINT fk_contracts_campaign_kol FOREIGN KEY (campaign_kol_id) REFERENCES campaign_kols(id) ON UPDATE CASCADE ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 3. Tambah tabel invoices
        $this->db->query("
            CREATE TABLE IF NOT EXISTS invoices (
                id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                campaign_kol_id     BIGINT UNSIGNED NOT NULL,
                invoice_no          VARCHAR(100)    NOT NULL,
                subtotal            DECIMAL(15,2)   NOT NULL DEFAULT 0,
                tax_amount          DECIMAL(15,2)   NOT NULL DEFAULT 0,
                grand_total         DECIMAL(15,2)   NOT NULL DEFAULT 0,
                invoice_file        VARCHAR(500)    NULL,
                due_date            DATE            NULL,
                status              ENUM('UNPAID','PARTIAL','PAID','CANCELLED') NOT NULL DEFAULT 'UNPAID',
                created_by          BIGINT UNSIGNED NULL,
                created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uk_invoice_no (invoice_no),
                KEY idx_invoices_campaign_kol (campaign_kol_id),
                CONSTRAINT fk_invoices_campaign_kol FOREIGN KEY (campaign_kol_id) REFERENCES campaign_kols(id) ON UPDATE CASCADE ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 4. Tambah tabel payments (Termin Pembayaran)
        $this->db->query("
            CREATE TABLE IF NOT EXISTS payments (
                id                  BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                invoice_id          BIGINT UNSIGNED NOT NULL,
                payment_date        DATETIME        NOT NULL,
                amount              DECIMAL(15,2)   NOT NULL DEFAULT 0,
                payment_method      VARCHAR(50)     NULL,
                payment_proof_file  VARCHAR(500)    NULL,
                notes               TEXT            NULL,
                created_by          BIGINT UNSIGNED NULL,
                created_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at          DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_payments_invoice (invoice_id),
                CONSTRAINT fk_payments_invoice FOREIGN KEY (invoice_id) REFERENCES invoices(id) ON UPDATE CASCADE ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    }

    public function down()
    {
        // Rollback tabel payments
        $this->db->query("DROP TABLE IF EXISTS payments;");

        // Rollback tabel invoices
        $this->db->query("DROP TABLE IF EXISTS invoices;");

        // Rollback tabel campaign_kol_contracts
        $this->db->query("DROP TABLE IF EXISTS campaign_kol_contracts;");

        // Rollback kolom di tabel kols
        $this->db->query("
            ALTER TABLE kols 
            DROP COLUMN bank_name,
            DROP COLUMN bank_account_number,
            DROP COLUMN bank_account_name,
            DROP COLUMN npwp_number,
            DROP COLUMN npwp_file,
            DROP COLUMN ktp_number,
            DROP COLUMN ktp_file;
        ");
    }
}
