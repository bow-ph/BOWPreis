<?php declare(strict_types=1);

namespace Bow\Preishoheit\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1711000000CreateBowPreishoheitTables extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1711000000;
    }

    public function update(Connection $connection): void
    {
        // Tabelle: bow_preishoheit_job
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `bow_preishoheit_job` (
                `id` BINARY(16) NOT NULL PRIMARY KEY,
                `product_id` BINARY(16) NOT NULL,
                `external_id` VARCHAR(255) NOT NULL,
                `status` VARCHAR(50) NOT NULL DEFAULT "pending",
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                CONSTRAINT `fk.bow_job.product_id` FOREIGN KEY (`product_id`)
                    REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            );
        ');

        // Tabelle: bow_preishoheit_job_result
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `bow_preishoheit_job_result` (
                `id` BINARY(16) NOT NULL PRIMARY KEY,
                `job_id` BINARY(16) NOT NULL,
                `price` DECIMAL(10,2) NOT NULL,
                `title` VARCHAR(255) NOT NULL,
                `ean` VARCHAR(64) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                CONSTRAINT `fk.bow_job_result.job_id` FOREIGN KEY (`job_id`)
                    REFERENCES `bow_preishoheit_job` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            );
        ');

        // Tabelle: bow_preishoheit_product_mapping
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `bow_preishoheit_product_mapping` (
                `id` BINARY(16) NOT NULL PRIMARY KEY,
                `product_id` BINARY(16) NOT NULL,
                `external_id` VARCHAR(255) NOT NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                CONSTRAINT `fk.bow_product_mapping.product_id` FOREIGN KEY (`product_id`)
                    REFERENCES `product` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
            );
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // Hier ggf. destruktive Updates implementieren, aktuell nicht notwendig
    }
}
