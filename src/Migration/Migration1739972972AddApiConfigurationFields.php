<?php declare(strict_types=1);

namespace BOW\Preishoheit\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1739972972AddApiConfigurationFields extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1739972972;
    }

    public function update(Connection $connection): void
    {
        // Add API configuration fields to bow_preishoheit_product table
        $connection->executeStatement('
            ALTER TABLE `bow_preishoheit_product`
            ADD COLUMN `api_sync_enabled` TINYINT(1) NOT NULL DEFAULT 1,
            ADD COLUMN `api_last_sync` DATETIME(3) NULL,
            ADD COLUMN `api_sync_interval` INT DEFAULT 24,
            ADD COLUMN `api_sync_priority` INT DEFAULT 0;
        ');

        // Add API configuration fields to bow_preishoheit_error_log table
        $connection->executeStatement('
            ALTER TABLE `bow_preishoheit_error_log`
            ADD COLUMN `api_request_data` JSON NULL,
            ADD COLUMN `api_response_data` JSON NULL;
        ');
    }

    public function updateDestructive(Connection $connection): void
    {
        // No destructive updates needed
    }
}
