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
        return 900; // 15 minutes - recommended for shops with dynamic pricing
    }

    public static function getIntervalOptions(): array
    {
        return [
            300 => 'Every 5 minutes - For shops with highly volatile prices',
            900 => 'Every 15 minutes - Recommended for dynamic pricing',
            1800 => 'Every 30 minutes - For shops with frequent price changes',
            3600 => 'Every hour - For medium-sized shops',
            43200 => 'Twice daily - Morning and evening price sync',
            86400 => 'Once daily - For daily price updates'
        ];
    }
}
