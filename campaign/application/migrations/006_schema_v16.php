<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Schema_v16 extends CI_Migration {

    public function up()
    {
        // 1. Tambah tabel products
        $this->db->query("
            CREATE TABLE IF NOT EXISTS products (
                id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                sku           VARCHAR(50)     NOT NULL,
                name          VARCHAR(150)    NOT NULL,
                category      VARCHAR(100)    NULL,
                stock         INT             NOT NULL DEFAULT 0,
                price         DECIMAL(15,2)   NOT NULL DEFAULT 0,
                is_active     TINYINT(1)      NOT NULL DEFAULT 1,
                created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                UNIQUE KEY uk_products_sku (sku)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 2. Tambah tabel kol_shipments
        $this->db->query("
            CREATE TABLE IF NOT EXISTS kol_shipments (
                id                   BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                campaign_kol_id      BIGINT UNSIGNED NOT NULL,
                recipient_name       VARCHAR(150)    NOT NULL,
                shipping_address     TEXT            NOT NULL,
                courier              VARCHAR(50)     NULL,
                tracking_no          VARCHAR(100)    NULL,
                shipping_fee         DECIMAL(15,2)   NOT NULL DEFAULT 0,
                status               ENUM('PENDING','PROCESSING','SHIPPED','DELIVERED','RETURN_PENDING','RETURNED') NOT NULL DEFAULT 'PENDING',
                shipped_at           DATETIME        NULL,
                delivered_at         DATETIME        NULL,
                notes                TEXT            NULL,
                created_by           BIGINT UNSIGNED NOT NULL,
                created_at           DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                updated_at           DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                KEY idx_shipments_campaign_kol (campaign_kol_id),
                CONSTRAINT fk_shipments_campaign_kol FOREIGN KEY (campaign_kol_id) REFERENCES campaign_kols(id) ON UPDATE CASCADE ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 3. Tambah tabel kol_shipment_items
        $this->db->query("
            CREATE TABLE IF NOT EXISTS kol_shipment_items (
                id            BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
                shipment_id   BIGINT UNSIGNED NOT NULL,
                product_id    BIGINT UNSIGNED NOT NULL,
                qty           INT UNSIGNED    NOT NULL DEFAULT 1,
                created_at    DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
                PRIMARY KEY (id),
                CONSTRAINT fk_shipment_items_shipment FOREIGN KEY (shipment_id) REFERENCES kol_shipments(id) ON UPDATE CASCADE ON DELETE CASCADE,
                CONSTRAINT fk_shipment_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON UPDATE CASCADE ON DELETE RESTRICT
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");

        // 4. Update tabel campaign_kols (drop kolom pengiriman lama)
        $this->db->query("ALTER TABLE campaign_kols DROP COLUMN shipping_status;");
        $this->db->query("ALTER TABLE campaign_kols DROP COLUMN shipped_at;");
        $this->db->query("ALTER TABLE campaign_kols DROP COLUMN shipping_address;");
        $this->db->query("ALTER TABLE campaign_kols DROP COLUMN shipping_tracking_no;");

        // 5. Update tabel kols (tambah kolom rate_card_file)
        $this->db->query("ALTER TABLE kols ADD COLUMN rate_card_file VARCHAR(500) NULL AFTER profile_url;");
    }

    public function down()
    {
        // Rollback kols
        $this->db->query("ALTER TABLE kols DROP COLUMN rate_card_file;");

        // Rollback campaign_kols
        $this->db->query("ALTER TABLE campaign_kols ADD COLUMN shipping_status ENUM('NOT_SENT','SENT','RECEIVED') NOT NULL DEFAULT 'NOT_SENT';");
        $this->db->query("ALTER TABLE campaign_kols ADD COLUMN shipped_at DATETIME NULL;");
        $this->db->query("ALTER TABLE campaign_kols ADD COLUMN shipping_address TEXT NULL;");
        $this->db->query("ALTER TABLE campaign_kols ADD COLUMN shipping_tracking_no VARCHAR(100) NULL;");

        // Rollback tabel baru
        $this->db->query("DROP TABLE IF EXISTS kol_shipment_items;");
        $this->db->query("DROP TABLE IF EXISTS kol_shipments;");
        $this->db->query("DROP TABLE IF EXISTS products;");
    }
}
