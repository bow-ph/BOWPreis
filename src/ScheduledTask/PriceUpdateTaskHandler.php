<?php declare(strict_types=1);

namespace BOW\Preishoheit\ScheduledTask;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
use Shopware\Core\System\SystemConfig\SystemConfigService;

class PriceUpdateTaskHandler extends ScheduledTaskHandler
{
    private PriceUpdateService $priceUpdateService;
    private EntityRepository $preishoheitProductRepository;
    private SystemConfigService $systemConfigService;
    private LoggerInterface $logger;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        PriceUpdateService $priceUpdateService,
        EntityRepository $preishoheitProductRepository,
        SystemConfigService $systemConfigService,
        LoggerInterface $logger
    ) {
        parent::__construct($scheduledTaskRepository);
        $this->priceUpdateService = $priceUpdateService;
        $this->preishoheitProductRepository = $preishoheitProductRepository;
        $this->systemConfigService = $systemConfigService;
        $this->logger = $logger;
    }

    public static function getHandledMessages(): iterable
    {
        return [PriceUpdateTask::class];
    }

    public function run(): void
    {
        $context = Context::createDefaultContext();

        $this->logger->info('Scheduled price update started.');

        try {
            $page = 1;
            do {
                $products = $this->getProducts($context, $page);
                if ($products->count() === 0) {
                    break;
                }

                $this->priceUpdateService->updatePrices($products->getElements(), $context);
                $page++;
            } while ($products->count() > 0);

            $interval = $this->systemConfigService->get('BOWPreishoheit.config.updateInterval') ?? PriceUpdateTask::getDefaultInterval();
            $this->updateNextExecutionTime($interval);

            $this->logger->info('Scheduled price update completed successfully', [
                'pages_processed' => $page - 1,
            ]);

        } catch (\Throwable $e) {
            $this->logger->error('Error during scheduled price update', [
                'message' => $e->getMessage(),
                'exception' => $e
            ]);

            throw $e;
        }
    }

    private function getProducts(Context $context, int $page): EntitySearchResult
    {
        $criteria = new Criteria();
        $criteria->addAssociation('product');
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->setLimit(20);
        $criteria->setOffset(($page - 1) * 20);
        
        return $this->preishoheitProductRepository->search($criteria, $context);
    }
}
