<?php declare(strict_types=1);

namespace BOW\Preishoheit\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1739807100CreatePreishoheitTables extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1739807100;
    }

    public function update(Connection $connection): void
    {
        // Create product selection table
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `bow_preishoheit_product` (
                `id` BINARY(16) NOT NULL,
                `active` TINYINT(1) NOT NULL DEFAULT 1,
                `surcharge_percentage` DOUBLE DEFAULT NULL,
                `discount_percentage` DOUBLE DEFAULT NULL,
                `product_id` BINARY(16) NOT NULL,
                `product_version_id` BINARY(16) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`),
                KEY `fk.bow_preishoheit_product.product_id` (`product_id`,`product_version_id`),
                CONSTRAINT `fk.bow_preishoheit_product.product_id` FOREIGN KEY (`product_id`,`product_version_id`)
                    REFERENCES `product` (`id`,`version_id`) ON DELETE CASCADE ON UPDATE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        // Create price history table
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `bow_preishoheit_price_history` (
                `id` BINARY(16) NOT NULL,
                `ean` VARCHAR(255) NOT NULL,
                `product_name` VARCHAR(255) NOT NULL,
                `old_price` DOUBLE NOT NULL,
                `new_price` DOUBLE NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');

        // Create error log table
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `bow_preishoheit_error_log` (
                `id` BINARY(16) NOT NULL,
                `product_id` VARCHAR(255) NOT NULL,
                `error_type` VARCHAR(255) NOT NULL,
                `error_message` LONGTEXT NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // No destructive updates needed
    }
}
