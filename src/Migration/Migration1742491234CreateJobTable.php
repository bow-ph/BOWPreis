<?php declare(strict_types=1);

namespace BOW\Preishoheit\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1742491234CreateJobTable extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1742491234; // hier Unix-Timestamp eindeutig anpassen
    }

    public function update(Connection $connection): void
    {
        $connection->executeStatement('
            CREATE TABLE IF NOT EXISTS `bow_preishoheit_job` (
                `id` BINARY(16) NOT NULL,
                `job_id` VARCHAR(255) NOT NULL,
                `status` VARCHAR(50) NOT NULL DEFAULT \'pending\',
                `product_group` VARCHAR(255),
                `identifiers` JSON NULL,
                `countries` JSON NULL,
                `categories` JSON NULL,
                `dynamic_product_group_id` BINARY(16) NULL,
                `created_at` DATETIME(3) NOT NULL,
                `updated_at` DATETIME(3) NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // Keine destruktiven Änderungen erforderlich
    }
}
