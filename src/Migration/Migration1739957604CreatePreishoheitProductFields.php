<?php declare(strict_types=1);

namespace BOW\Preishoheit\Migration;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Migration\MigrationStep;

class Migration1739957604CreatePreishoheitProductFields extends MigrationStep
{
    public function getCreationTimestamp(): int
    {
        return 1739957604;
    }

    public function update(Connection $connection): void
    {
        $sql = <<<SQL
            ALTER TABLE `bow_preishoheit_product`
            ADD COLUMN `surcharge_percentage` DECIMAL(10, 2) NULL,
            ADD COLUMN `is_active` TINYINT(1) NOT NULL DEFAULT '0',
            ADD COLUMN `created_at` DATETIME(3) NOT NULL,
            ADD COLUMN `updated_at` DATETIME(3) NULL;
        SQL;

        $connection->executeStatement($sql);
    }

    public function updateDestructive(Connection $connection): void
    {
    }
}
