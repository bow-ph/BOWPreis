<?php declare(strict_types=1);

namespace BOW\Preishoheit\ScheduledTask;

use BOW\Preishoheit\Service\PreishoheitApi\PriceUpdateService;
use BOW\Preishoheit\Service\ErrorHandling\ErrorLogger;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\MessageQueue\ScheduledTask\ScheduledTaskHandler;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(handles: PriceUpdateTask::class)]
class PriceUpdateTaskHandler extends ScheduledTaskHandler
{
    private PriceUpdateService $priceUpdateService;
    private EntityRepository $preishoheitProductRepository;
    private SystemConfigService $systemConfigService;
    private ErrorLogger $errorLogger;

    public function __construct(
        EntityRepository $scheduledTaskRepository,
        PriceUpdateService $priceUpdateService,
        EntityRepository $preishoheitProductRepository,
        SystemConfigService $systemConfigService,
        ErrorLogger $errorLogger
    ) {
        parent::__construct($scheduledTaskRepository);
        $this->priceUpdateService = $priceUpdateService;
        $this->preishoheitProductRepository = $preishoheitProductRepository;
        $this->systemConfigService = $systemConfigService;
        $this->errorLogger = $errorLogger;
    }

    public static function getHandledMessages(): iterable
    {
        return [PriceUpdateTask::class];
    }

    public function run(): void
    {
        $context = Context::createDefaultContext();
        
        try {
            $criteria = new Criteria();
            $criteria->addAssociation('product');
            $criteria->addFilter(new EqualsFilter('active', true));
            
            $products = $this->preishoheitProductRepository->search($criteria, $context);
            
            if ($products->count() === 0) {
                $this->errorLogger->info('No active products found for scheduled price update');
                return;
            }

            $interval = $this->systemConfigService->get('BOWPreishoheit.config.updateInterval') ?? PriceUpdateTask::getDefaultInterval();
            $this->updateNextExecutionTime($interval);

            $this->priceUpdateService->updatePrices($products->getElements(), $context);
            $this->errorLogger->info('Scheduled price update completed successfully', [
                'productCount' => $products->count(),
                'interval' => $interval
            ]);
        } catch (\Exception $e) {
            $this->errorLogger->error('Error during scheduled price update: ' . $e->getMessage(), [
                'exception' => get_class($e),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function updateNextExecutionTime(int $interval): void
    {
        $this->taskEntity->setNextExecutionTime(
            (new \DateTime())->modify(sprintf('+%d seconds', $interval))
        );
    }
}
