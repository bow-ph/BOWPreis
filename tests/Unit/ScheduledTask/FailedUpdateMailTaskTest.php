<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Unit\ScheduledTask;

use BOW\Preishoheit\ScheduledTask\FailedUpdateMailTask;
use PHPUnit\Framework\TestCase;

class FailedUpdateMailTaskTest extends TestCase
{
    public function testGetTaskName(): void
    {
        $task = new FailedUpdateMailTask();
        static::assertEquals('bow_preishoheit.failed_update_mail', $task->getTaskName());
    }

    public function testGetDefaultInterval(): void
    {
        $task = new FailedUpdateMailTask();
        static::assertEquals(86400, $task->getDefaultInterval());
    }
}
