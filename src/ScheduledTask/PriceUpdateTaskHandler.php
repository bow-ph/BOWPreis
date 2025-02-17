<?php declare(strict_types=1);

namespace BOW\Preishoheit\ScheduledTask;

use BOW\Preishoheit\Service\PreishoheitApi\PriceUpdateService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class PriceUpdateTaskHandler extends ScheduledTaskHandler
{
    private PriceUpdateService $priceUpdateService;
    private EntityRepository $preishoheitProductRepository;
    private SystemConfigService $systemConfigService;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        PriceUpdateService $priceUpdateService,
        EntityRepository $preishoheitProductRepository,
        SystemConfigService $systemConfigService
    ) {
        parent::__construct($scheduledTaskRepository);
        $this->priceUpdateService = $priceUpdateService;
        $this->preishoheitProductRepository = $preishoheitProductRepository;
        $this->systemConfigService = $systemConfigService;
    }

    public static function getHandledMessages(): iterable
    {
        return [PriceUpdateTask::class];
    }

    public function run(): void
    {
        $context = Context::createDefaultContext();
        
        $criteria = new Criteria();
        $criteria->addAssociation('product');
        $criteria->addFilter(new EqualsFilter('active', true));
        
        $products = $this->preishoheitProductRepository->search($criteria, $context);
        
        if ($products->count() === 0) {
            return;
        }

        $this->priceUpdateService->updatePrices($products->getElements(), $context);
    }
}
