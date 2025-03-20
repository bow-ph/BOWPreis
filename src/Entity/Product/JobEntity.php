<?php declare(strict_types=1);

namespace BOW\Preishoheit\Core\Content\Job;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;

class JobEntity extends Entity
{
    protected string $jobId;
    protected string $status;
    protected array $payload;

    public function getJobId(): string
    {
        return $this->jobId;
    }

    public function setJobId(string $jobId): void
    {
        $this->jobId = $jobId;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getPayload(): array
    {
        return $this->payload;
    }

    public function setPayload(array $payload): void
    {
        $this->payload = $payload;
    }
}
