<?php declare(strict_types=1);

namespace BOW\Preishoheit\ScheduledTask;

use BOW\Preishoheit\Service\Mail\FailedUpdateMailService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: FailedUpdateMailTask::class)]
class FailedUpdateMailTaskHandler extends ScheduledTaskHandler
{
    private FailedUpdateMailService $mailService;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        FailedUpdateMailService $mailService
    ) {
        parent::__construct($scheduledTaskRepository);
        $this->mailService = $mailService;
    }

    public function run(): void
    {
        $this->mailService->sendDailySummary(Context::createDefaultContext());
    }
}
