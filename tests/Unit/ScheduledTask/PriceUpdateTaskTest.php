<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Unit\ScheduledTask;

use BOW\Preishoheit\ScheduledTask\PriceUpdateTask;
use PHPUnit\Framework\TestCase;

class PriceUpdateTaskTest extends TestCase
{
    public function testGetTaskName(): void
    {
        $task = new PriceUpdateTask();
        static::assertEquals('bow_preishoheit.price_update', $task->getTaskName());
    }

    public function testGetDefaultInterval(): void
    {
        $task = new PriceUpdateTask();
        static::assertEquals(900, $task->getDefaultInterval());
    }

    public function testGetIntervalOptions(): void
    {
        $options = PriceUpdateTask::getIntervalOptions();
        
        static::assertCount(6, $options);
        static::assertArrayHasKey(300, $options);
        static::assertArrayHasKey(900, $options);
        static::assertArrayHasKey(1800, $options);
        static::assertArrayHasKey(3600, $options);
        static::assertArrayHasKey(43200, $options);
        static::assertArrayHasKey(86400, $options);
    }
}
