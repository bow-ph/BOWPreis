<?php declare(strict_types=1);

namespace BOW\Preishoheit\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class FailedUpdateMailTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'bow_preishoheit.failed_update_mail';
    }

    public static function getDefaultInterval(): int
    {
        return 86400; // 24 hours in seconds
    }
}
