<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\Job;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\Framework\DataAbstractionLayer\Exception\DataAbstractionLayerException;

class JobPersistenceService
{
    private EntityRepository $jobRepository;
    private LoggerInterface $logger;

    public function __construct(
        EntityRepository $jobRepository,
        LoggerInterface $logger
    ) {
        $this->jobRepository = $jobRepository;
        $this->logger = $logger;
    }

    public function saveJob(array $jobData, Context $context): void
    {
        try {
            $jobPayload = [
                'id' => Uuid::randomHex(),
                'externalJobId' => $jobData['id'] ?? null,
                'status' => $jobData['status'] ?? 'pending',
                'productGroup' => $jobData['productGroup'] ?? null,
                'countries' => $jobData['countries'] ?? [],
                'identifiers' => $jobData['identifiers'] ?? [],
                'categories' => $jobData['categories'] ?? [],
                'dynamicProductGroupId' => $jobData['dynamicProductGroupId'] ?? null,
            ];

            $this->jobRepository->create([$jobPayload], $context);

            $this->logger->info('Job successfully saved.', [
                'jobId' => $jobPayload['id'],
                'externalJobId' => $jobPayload['externalJobId']
            ]);
        } catch (DataAbstractionLayerException $e) {
            $this->logger->error('Error saving job to the database', [
                'error' => $e->getMessage(),
                'jobData' => $jobData
            ]);

            throw $e;
        }
    }
}