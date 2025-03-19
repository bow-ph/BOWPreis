<?php declare(strict_types=1);

namespace BOW\Preishoheit\Command;

use BOW\Preishoheit\Service\PreishoheitApi\PriceUpdateService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePricesCommand extends Command
{
    private PriceUpdateService $priceUpdateService;
    private EntityRepository $preishoheitProductRepository;
    private LoggerInterface $logger;

    public function __construct(
        PriceUpdateService $priceUpdateService,
        EntityRepository $preishoheitProductRepository,
        LoggerInterface $logger
    ) {
        parent::__construct('bow:preishoheit:update-prices');
        $this->priceUpdateService = $priceUpdateService;
        $this->preishoheitProductRepository = $preishoheitProductRepository;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this->setDescription('Update product prices using Preishoheit API')
            ->addOption('page', null, InputOption::VALUE_OPTIONAL, 'Page number for pagination', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = \Shopware\Core\Framework\Context::createDefaultContext();
        $page = (int) $input->getOption('page');

        $this->logger->info('Starting price update process', ['page' => $page]);

        try {
            $products = $this->getProducts($context, $page);

            if ($products->count() === 0) {
                $output->writeln('No products found for price update.');
                return Command::SUCCESS;
            }

            $this->priceUpdateService->updatePrices($products->getElements(), $context);

            $output->writeln('Price update completed successfully.');
            $this->logger->info('Price update completed successfully', ['count' => $products->count()]);

            return Command::SUCCESS;

        } catch (\Throwable $e) {
            $this->logger->error('Error updating prices', [
                'message' => $e->getMessage(),
                'exception' => $e
            ]);

            $output->writeln('Error updating prices: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }

    private function getProducts(Context $context, int $page): \Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult
    {
        $criteria = new \Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria();
        $criteria->addAssociation('product');
        $criteria->setLimit(20);
        $criteria->setOffset(($page - 1) * 20);

        return $this->preishoheitProductRepository->search($criteria, $context);
    }
}
