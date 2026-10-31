-- ==============================================================================
-- Migration Script: Multi-Brand Architecture Foundation
-- Target: KOLAB.ID Database (`kol`)
-- File: campaign/migrations/20261031_add_multi_brand_architecture.sql
-- Description:
--   1. Master `brands` table creation and initial seed (Brand 1: Metafora Parfume)
--   2. Conversion of `aauth_users` to InnoDB for foreign key support
--   3. Adding discriminator column `brand_id` and B-tree indexes
--   4. Backfilling existing data to Brand 1 (campaigns, products, operational staff)
--   5. Enforcing foreign key constraints with cascade/restrict rules
--   6. Altering `kols.rate_card_file` to allow NULL
--   7. Registering Master Brands menu item (app_id = 1)
--   8. Registering Aauth permissions for Brands and sensitive KOL PII viewing
-- Idempotency: Script is safely re-runnable without errors or duplicates.
-- ==============================================================================

USE `kol`;

SET @dbname = DATABASE();

-- ==============================================================================
-- 1. MASTER BRANDS TABLE
-- ==============================================================================
CREATE TABLE IF NOT EXISTS `brands` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL COMMENT 'Nama Brand, misal: Metafora Parfume',
  `code` varchar(50) NOT NULL COMMENT 'Slug/Kode unik, misal: METAFORA',
  `company_name` varchar(200) DEFAULT NULL COMMENT 'Nama PT / Badan Hukum untuk kontrak',
  `legal_entity_name` varchar(255) DEFAULT NULL COMMENT 'Alias Legal Entity untuk kompatibilitas',
  `logo` varchar(255) DEFAULT NULL COMMENT 'Logo Brand untuk kop surat & UI',
  `description` text DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `pic_name` varchar(150) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `deleted_at` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_brands_code` (`code`),
  KEY `idx_brands_active` (`is_active`),
  KEY `idx_brands_deleted_at` (`deleted_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ensure deleted_at exists if table existed previously without it
SET @col = 'deleted_at';
SET @tbl = 'brands';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tbl AND COLUMN_NAME = @col) > 0,
  'SELECT 1',
  'ALTER TABLE `brands` ADD COLUMN `deleted_at` datetime DEFAULT NULL AFTER `updated_at`, ADD KEY `idx_brands_deleted_at` (`deleted_at`);'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Ensure legal_entity_name exists if table existed previously without it
SET @col = 'legal_entity_name';
SET @tbl = 'brands';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tbl AND COLUMN_NAME = @col) > 0,
  'SELECT 1',
  'ALTER TABLE `brands` ADD COLUMN `legal_entity_name` varchar(255) DEFAULT NULL AFTER `company_name`;'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- Ensure website exists if table existed previously without it
SET @col = 'website';
SET @tbl = 'brands';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tbl AND COLUMN_NAME = @col) > 0,
  'SELECT 1',
  'ALTER TABLE `brands` ADD COLUMN `website` varchar(255) DEFAULT NULL AFTER `email`;'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- ==============================================================================
-- 2. SEED BRAND 1 (METAFORA PARFUME)
-- ==============================================================================
INSERT INTO `brands` (`id`, `name`, `code`, `company_name`, `legal_entity_name`, `logo`, `description`, `address`, `phone`, `email`, `website`, `pic_name`, `is_active`)
VALUES (1, 'Metafora Parfume', 'METAFORA', 'PT Metafora Multi Perkasa', 'PT Metafora Multi Perkasa', 'asset/logo.png', 'Official Brand Metafora', 'DKI Jakarta, Indonesia', '081234567890', 'contact@metafora.id', 'https://metafora.id', 'Superadmin', 1)
ON DUPLICATE KEY UPDATE 
  `name` = VALUES(`name`),
  `company_name` = VALUES(`company_name`),
  `legal_entity_name` = VALUES(`legal_entity_name`),
  `is_active` = VALUES(`is_active`);

-- ==============================================================================
-- 3. CONVERT aauth_users TO InnoDB FOR FOREIGN KEY SUPPORT
-- ==============================================================================
ALTER TABLE `aauth_users` ENGINE = InnoDB;

-- ==============================================================================
-- 4. DISCRIMINATOR COLUMNS & INDEXES
-- ==============================================================================

-- 4.1 aauth_users.brand_id (NULL = Superadmin / KOL, int = Scoped Staff)
SET @tablename = 'aauth_users';
SET @columnname = 'brand_id';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE `aauth_users` ADD COLUMN `brand_id` bigint(20) unsigned DEFAULT NULL AFTER `top_secret`, ADD KEY `idx_aauth_users_brand_id` (`brand_id`);'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 4.2 campaigns.brand_id
SET @tablename = 'campaigns';
SET @columnname = 'brand_id';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE `campaigns` ADD COLUMN `brand_id` bigint(20) unsigned NOT NULL DEFAULT 1 AFTER `id`, ADD KEY `idx_campaigns_brand_id` (`brand_id`);'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 4.3 products.brand_id
SET @tablename = 'products';
SET @columnname = 'brand_id';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE `products` ADD COLUMN `brand_id` bigint(20) unsigned NOT NULL DEFAULT 1 AFTER `id`, ADD KEY `idx_products_brand_id` (`brand_id`);'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 4.4 contract_templates.brand_id (NULL = Global MoU template available for all brands)
SET @tablename = 'contract_templates';
SET @columnname = 'brand_id';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE `contract_templates` ADD COLUMN `brand_id` bigint(20) unsigned DEFAULT NULL AFTER `id`, ADD KEY `idx_contract_templates_brand_id` (`brand_id`);'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- 4.5 audit_logs.brand_id
SET @tablename = 'audit_logs';
SET @columnname = 'brand_id';
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  'SELECT 1',
  'ALTER TABLE `audit_logs` ADD COLUMN `brand_id` bigint(20) unsigned DEFAULT NULL AFTER `user_id`, ADD KEY `idx_audit_logs_brand_id` (`brand_id`);'
));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;

-- ==============================================================================
-- 5. DATA BACKFILL TO BRAND 1
-- ==============================================================================
-- Ensure existing legacy records belong to Brand 1 (Metafora)
UPDATE `campaigns` SET `brand_id` = 1 WHERE `brand_id` IS NULL OR `brand_id` = 0;
UPDATE `products` SET `brand_id` = 1 WHERE `brand_id` IS NULL OR `brand_id` = 0;

-- Assign existing operational and leader staff to Brand 1
-- Note: devmetafora remains brand_id = NULL (Superadmin global context)
-- Note: KOL talent accounts remain brand_id = NULL (talent pool is global)
UPDATE `aauth_users` 
SET `brand_id` = 1 
WHERE `username` IN ('operational_budi', 'masterkol', 'operational_siti', 'operational_rian') 
  AND (`brand_id` IS NULL OR `brand_id` = 0);

-- ==============================================================================
-- 6. ALTER kols.rate_card_file TO BE NULLABLE
-- ==============================================================================
ALTER TABLE `kols` MODIFY COLUMN `rate_card_file` VARCHAR(500) NULL DEFAULT NULL;

-- ==============================================================================
-- 7. FOREIGN KEY CONSTRAINTS (Referential Integrity)
-- ==============================================================================

-- 7.1 FK: campaigns.brand_id -> brands.id (ON DELETE RESTRICT ON UPDATE CASCADE)
SET @fk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @dbname AND TABLE_NAME = 'campaigns' AND CONSTRAINT_NAME = 'fk_campaigns_brand_id');
SET @sql = IF(@fk_exists = 0,
  'ALTER TABLE `campaigns` ADD CONSTRAINT `fk_campaigns_brand_id` FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 7.2 FK: products.brand_id -> brands.id (ON DELETE RESTRICT ON UPDATE CASCADE)
SET @fk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @dbname AND TABLE_NAME = 'products' AND CONSTRAINT_NAME = 'fk_products_brand_id');
SET @sql = IF(@fk_exists = 0,
  'ALTER TABLE `products` ADD CONSTRAINT `fk_products_brand_id` FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE;',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 7.3 FK: contract_templates.brand_id -> brands.id (ON DELETE SET NULL ON UPDATE CASCADE)
SET @fk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @dbname AND TABLE_NAME = 'contract_templates' AND CONSTRAINT_NAME = 'fk_contract_templates_brand_id');
SET @sql = IF(@fk_exists = 0,
  'ALTER TABLE `contract_templates` ADD CONSTRAINT `fk_contract_templates_brand_id` FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- 7.4 FK: aauth_users.brand_id -> brands.id (ON DELETE SET NULL ON UPDATE CASCADE)
SET @fk_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = @dbname AND TABLE_NAME = 'aauth_users' AND CONSTRAINT_NAME = 'fk_aauth_users_brand_id');
SET @sql = IF(@fk_exists = 0,
  'ALTER TABLE `aauth_users` ADD CONSTRAINT `fk_aauth_users_brand_id` FOREIGN KEY (`brand_id`) REFERENCES `brands`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;',
  'SELECT 1'
);
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

-- ==============================================================================
-- 8. MENU REGISTRATION FOR MASTER BRANDS
-- ==============================================================================
-- Register Brands menu item under Master Data (parent 114) for app_id = 1 (Campaign)
INSERT INTO `menu` (`id`, `label`, `type`, `icon_color`, `link`, `sort`, `parent`, `icon`, `menu_type_id`, `active`, `app_id`)
SELECT 164, 'Brands', 'menu', 'default', '{admin_url}/brands', 37, 114, 'fa-building-o', 1, 1, 1
WHERE NOT EXISTS (SELECT 1 FROM `menu` WHERE `id` = 164 OR `link` IN ('{admin_url}/brands', 'administrator/brands'));

-- ==============================================================================
-- 9. AAUTH PERMISSIONS REGISTRATION & ASSIGNMENT
-- ==============================================================================

-- 9.1 Register Brands Permissions
INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'brands_list', 'List Brands' 
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'brands_list');

INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'brands_add', 'Add Brands' 
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'brands_add');

INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'brands_update', 'Update Brands' 
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'brands_update');

INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'brands_delete', 'Delete Brands' 
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'brands_delete');

INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'brands_view', 'View Brands' 
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'brands_view');

-- 9.2 Register Sensitive KOL Data Viewing Permission (Bank Details, NIK, NPWP)
INSERT INTO `aauth_perms` (`name`, `definition`)
SELECT 'kols_view_bank_detail', 'View KOL Sensitive Data: Bank Account, NIK, NPWP'
WHERE NOT EXISTS (SELECT 1 FROM `aauth_perms` WHERE `name` = 'kols_view_bank_detail');

-- 9.3 Assign Permissions to Superadmin (group_id = 1) Idempotently
INSERT INTO `aauth_perm_to_group` (`perm_id`, `group_id`)
SELECT p.id, 1 
FROM `aauth_perms` p
WHERE (p.name LIKE 'brands_%' OR p.name = 'kols_view_bank_detail')
  AND NOT EXISTS (
    SELECT 1 FROM `aauth_perm_to_group` ptg 
    WHERE ptg.perm_id = p.id AND ptg.group_id = 1
  );
