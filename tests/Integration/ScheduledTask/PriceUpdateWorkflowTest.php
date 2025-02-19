<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Integration\ScheduledTask;

use BOW\Preishoheit\ScheduledTask\PriceUpdateTask;
use BOW\Preishoheit\ScheduledTask\PriceUpdateTaskHandler;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use PHPUnit\Framework\TestCase;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class PriceUpdateWorkflowTest extends TestCase
{
    use IntegrationTestBehaviour;

    private EntityRepository $scheduledTaskRepository;
    private SystemConfigService $systemConfigService;
    private Context $context;

    protected function setUp(): void
    {
        $this->scheduledTaskRepository = $this->getContainer()->get('scheduled_task.repository');
        $this->systemConfigService = $this->getContainer()->get(SystemConfigService::class);
        $this->context = Context::createDefaultContext();
    }

    public function testScheduledTaskExecution(): void
    {
        // Create scheduled task
        $taskId = $this->scheduledTaskRepository->create([[
            'name' => PriceUpdateTask::getTaskName(),
            'scheduledTaskClass' => PriceUpdateTask::class,
            'runInterval' => 900,
            'status' => 'scheduled',
            'nextExecutionTime' => new \DateTime(),
        ]], $this->context)->getIds()[0];

        // Configure update interval
        $this->systemConfigService->set('BOWPreishoheit.config.updateInterval', 900);

        // Get task handler
        $handler = $this->getContainer()->get(PriceUpdateTaskHandler::class);
        
        // Execute task
        $handler->run();

        // Verify task execution
        $task = $this->scheduledTaskRepository->search(
            (new Criteria())->addFilter(new EqualsFilter('id', $taskId)),
            $this->context
        )->first();

        static::assertNotNull($task);
        static::assertEquals('finished', $task->getStatus());
        static::assertGreaterThan(new \DateTime(), $task->getNextExecutionTime());
    }

    public function testIntervalConfiguration(): void
    {
        $intervals = [300, 900, 1800, 3600, 43200, 86400];

        foreach ($intervals as $interval) {
            $this->systemConfigService->set('BOWPreishoheit.config.updateInterval', $interval);
            
            $taskId = $this->scheduledTaskRepository->create([[
                'name' => PriceUpdateTask::getTaskName(),
                'scheduledTaskClass' => PriceUpdateTask::class,
                'runInterval' => $interval,
                'status' => 'scheduled',
                'nextExecutionTime' => new \DateTime(),
            ]], $this->context)->getIds()[0];

            $handler = $this->getContainer()->get(PriceUpdateTaskHandler::class);
            $handler->run();

            $task = $this->scheduledTaskRepository->search(
                (new Criteria())->addFilter(new EqualsFilter('id', $taskId)),
                $this->context
            )->first();

            static::assertEquals($interval, $task->getRunInterval());
            static::assertEquals('finished', $task->getStatus());
        }
    }
}
