<?php declare(strict_types=1);

namespace Bow\Preishoheit\Service;

use Doctrine\DBAL\Connection;
use Shopware\Core\Framework\Uuid\Uuid;

class ResultMappingService
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function saveResult(string $jobId, array $resultData): void
    {
        $this->connection->insert('bow_preishoheit_job_result', [
            'id' => Uuid::randomBytes(),
            'job_id' => Uuid::fromHexToBytes($jobId),
            'price' => $resultData['price'],
            'title' => $resultData['title'],
            'ean' => $resultData['ean'],
            'created_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ]);
    }

    public function getAllResults(): array
    {
        return $this->connection->fetchAllAssociative('
            SELECT HEX(job_id) as jobId, price, title, ean FROM bow_preishoheit_job_result
        ');
    }

    public function getResultByJobId(string $jobId): ?array
    {
        return $this->connection->fetchAssociative('
            SELECT HEX(job_id) as jobId, price, title, ean, created_at, updated_at
            FROM bow_preishoheit_job_result
            WHERE job_id = :jobId
        ', [
            'jobId' => Uuid::fromHexToBytes($jobId)
        ]) ?: null;
    }

    public function updateResult(string $jobId, array $data): void
    {
        $this->connection->update('bow_preishoheit_job_result', [
            'price' => $data['price'],
            'title' => $data['title'],
            'ean' => $data['ean'],
            'updated_at' => (new \DateTime())->format('Y-m-d H:i:s')
        ], [
            'job_id' => Uuid::fromHexToBytes($jobId)
        ]);
    }
}
