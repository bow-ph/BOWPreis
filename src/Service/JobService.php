<?php declare(strict_types=1);

namespace Bow\Preishoheit\Service;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;

class JobService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function createJob(string $productId): string
    {
        $jobId = Uuid::randomBytes();
        $this->connection->insert('bow_preishoheit_job', [
            'id' => $jobId,
            'product_id' => Uuid::fromHexToBytes($productId),
            'status' => 'pending',
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);

        return Uuid::fromBytesToHex($jobId);
    }

    public function updateJobStatus(string $jobId, string $status): void
    {
        $this->connection->update('bow_preishoheit_job', [
            'status' => $status,
            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ], ['id' => Uuid::fromHexToBytes($jobId)]);
    }

    public function getPendingJobs(): array
{
    return $this->connection->fetchAllAssociative('
        SELECT id, HEX(id) AS id_hex, product_id, external_id, status 
        FROM bow_preishoheit_job 
        WHERE status = :status', 
        ['status' => 'pending']);
}

}
