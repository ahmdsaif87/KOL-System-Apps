CREATE TABLE IF NOT EXISTS `stock_history` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `product_id` BIGINT UNSIGNED NOT NULL,
    `shipment_id` BIGINT UNSIGNED NULL,
    `change_type` ENUM('OUT_SHIPMENT','IN_RETURN','ADJUSTMENT','INITIAL') NOT NULL,
    `qty_change` INT NOT NULL COMMENT 'Negative = keluar, Positive = masuk',
    `stock_before` INT NOT NULL DEFAULT 0,
    `stock_after` INT NOT NULL DEFAULT 0,
    `notes` VARCHAR(255) NULL,
    `created_by` INT UNSIGNED NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    CONSTRAINT fk_stock_history_product FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON UPDATE CASCADE ON DELETE RESTRICT,
    CONSTRAINT fk_stock_history_shipment FOREIGN KEY (`shipment_id`) REFERENCES `kol_shipments`(`id`) ON UPDATE CASCADE ON DELETE SET NULL,
    INDEX idx_stock_history_product (`product_id`),
    INDEX idx_stock_history_shipment (`shipment_id`),
    INDEX idx_stock_history_type (`change_type`),
    INDEX idx_stock_history_created (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
