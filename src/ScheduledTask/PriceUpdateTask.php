<?php declare(strict_types=1);

namespace BOW\Preishoheit\ScheduledTask;

use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTask;

class PriceUpdateTask extends ScheduledTask
{
    public static function getTaskName(): string
    {
        return 'bow_preishoheit.price_update';
    }

    public static function getDefaultInterval(): int
    {
        return 900; // 15 minutes default
    }
}
