<?php declare(strict_types=1);

namespace Bow\Preishoheit\Service;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;

class MappingService
{
    public function __construct(private Connection $connection) {}

    public function getAllMappings(): array
    {
        return $this->connection->fetchAllAssociative('
            SELECT HEX(m.id) AS id, HEX(m.product_id) AS productId, p.product_number AS productName, m.external_id AS externalId
            FROM bow_preishoheit_product_mapping m
            LEFT JOIN product p ON m.product_id = p.id
        ');
    }

    public function createMapping(string $productId, string $externalId): void
    {
        $this->connection->insert('bow_preishoheit_product_mapping', [
            'id' => Uuid::randomBytes(),
            'product_id' => Uuid::fromHexToBytes($productId),
            'external_id' => $externalId,
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    public function deleteMapping(string $id): void
    {
        $this->connection->delete('bow_preishoheit_product_mapping', [
            'id' => Uuid::fromHexToBytes($id)
        ]);
    }

    public function saveApprovedResults(array $approvedResults): void
{
    foreach ($approvedResults as $result) {
        $this->updateResult($result['jobId'], $result);
        $this->overwriteProductDataIfNeeded($result);
    }
}

private function overwriteProductDataIfNeeded(array $result): void
{
    $configService = $this->getConfigService();

    $overwriteEAN = $configService->get('BowPreishoheit.settings.overwriteEAN');
    $overwriteTitle = $configService->get('BowPreishoheit.settings.overwriteTitle');
    $overwritePrice = $configService->get('BowPreishoheit.settings.overwritePrice');

    $updates = [];
    if ($overwriteEAN) {
        $updates['ean'] = $result['ean'];
    }
    if ($overwriteTitle) {
        $updates['name'] = $result['title'];
    }
    if ($overwritePrice) {
        $updates['price'] = json_encode([
            ['currencyId' => $this->getDefaultCurrencyId(), 'gross' => (float)$result['price'], 'net' => (float)$result['price'], 'linked' => false]
        ]);
    }

    if (!empty($updates)) {
        $productId = $this->getProductIdByJobId($result['jobId']);
        $this->connection->update('product', $updates, ['id' => Uuid::fromHexToBytes($productId)]);
    }
}

private function getConfigService(): SystemConfigService
{
    return $this->container->get(SystemConfigService::class);
}

private function getDefaultCurrencyId(): string
{
    return Defaults::CURRENCY;
}

private function getProductIdByJobId(string $jobId): string
{
    return $this->connection->fetchOne('SELECT HEX(product_id) FROM bow_preishoheit_job WHERE id = :id', [
        'id' => Uuid::fromHexToBytes($jobId)
    ]);
}


}
