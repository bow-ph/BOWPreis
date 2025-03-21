<?php declare(strict_types=1);

namespace Bow\Preishoheit\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class CheckPreishoheitJobStatusTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'bow.preishoheit.check_job_status';
    }

    public static function getDefaultInterval(): int
    {
        return 300; // Default 5 Minuten (kann später via Settings angepasst werden)
    }
}
