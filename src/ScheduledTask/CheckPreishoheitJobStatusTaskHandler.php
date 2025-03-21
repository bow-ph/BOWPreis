<?php declare(strict_types=1);

namespace Bow\Preishoheit\ScheduledTask;

use Bow\Preishoheit\Service\JobService;
use Bow\Preishoheit\Service\PreishoheitApiService;
use Bow\Preishoheit\Service\ResultMappingService;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\Uuid\Uuid;

class CheckPreishoheitJobStatusTaskHandler extends ScheduledTaskHandler
{
    public function __construct(
        private JobService $jobService,
        private PreishoheitApiService $apiService,
        private ResultMappingService $resultMappingService
    ) {}

    public static function getHandledMessages(): iterable
    {
        return [CheckPreishoheitJobStatusTask::class];
    }

    public function run(): void
    {
        $pendingJobs = $this->jobService->getPendingJobs();

        foreach ($pendingJobs as $job) {
            $statusResult = $this->apiService->checkJobStatus($job['external_id']);

            if ($statusResult['status'] === 'finished') {
                $this->resultMappingService->saveResult($job['id'], $statusResult['result']);

                $this->jobService->updateJobStatus(Uuid::fromBytesToHex($job['id']), 'finished');
            } elseif ($statusResult['status'] === 'failed') {
                $this->jobService->updateJobStatus(Uuid::fromBytesToHex($job['id']), 'failed');
            }
        }
    }
}
