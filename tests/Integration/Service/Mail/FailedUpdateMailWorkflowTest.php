<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Integration\Service\Mail;

use BOW\Preishoheit\Service\Mail\FailedUpdateMailService;
use BOW\Preishoheit\ScheduledTask\FailedUpdateMailTask;
use BOW\Preishoheit\ScheduledTask\FailedUpdateMailTaskHandler;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use PHPUnit\Framework\TestCase;

class FailedUpdateMailWorkflowTest extends TestCase
{
    use IntegrationTestBehaviour;

    private EntityRepository $scheduledTaskRepository;
    private EntityRepository $errorLogRepository;
    private Context $context;

    protected function setUp(): void
    {
        $this->scheduledTaskRepository = $this->getContainer()->get('scheduled_task.repository');
        $this->errorLogRepository = $this->getContainer()->get('bow_preishoheit_error_log.repository');
        $this->context = Context::createDefaultContext();
    }

    public function testMailTaskExecution(): void
    {
        // Create test errors
        $this->createTestErrors();

        // Create scheduled task
        $taskId = $this->scheduledTaskRepository->create([[
            'name' => FailedUpdateMailTask::getTaskName(),
            'scheduledTaskClass' => FailedUpdateMailTask::class,
            'runInterval' => 86400,
            'status' => 'scheduled',
            'nextExecutionTime' => new \DateTime(),
        ]], $this->context)->getIds()[0];

        // Get task handler
        $handler = $this->getContainer()->get(FailedUpdateMailTaskHandler::class);
        
        // Execute task
        $handler->run();

        // Verify task execution
        $task = $this->scheduledTaskRepository->search(
            (new Criteria())->addFilter(new EqualsFilter('id', $taskId)),
            $this->context
        )->first();

        static::assertNotNull($task);
        static::assertEquals('finished', $task->getStatus());
    }

    private function createTestErrors(): void
    {
        $this->errorLogRepository->create([
            [
                'productId' => 'test-product-1',
                'message' => 'Test error 1',
                'createdAt' => new \DateTime(),
            ],
            [
                'productId' => 'test-product-2',
                'message' => 'Test error 2',
                'createdAt' => new \DateTime(),
            ],
        ], $this->context);
    }
}
