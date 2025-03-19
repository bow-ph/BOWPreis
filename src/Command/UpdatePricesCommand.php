<?php declare(strict_types=1);

namespace BOW\Preishoheit\Command;

use BOW\Preishoheit\Service\PreishoheitApi\PriceUpdateService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePricesCommand extends Command
{
    protected static $defaultName = 'bow:preishoheit:update-prices';

    private PriceUpdateService $priceUpdateService;
    private EntityRepository $preishoheitProductRepository;
    private LoggerInterface $logger;

    public function __construct(
        PriceUpdateService $priceUpdateService,
        EntityRepository $preishoheitProductRepository,
        LoggerInterface $logger
    ) {
        parent::__construct();
        $this->priceUpdateService = $priceUpdateService;
        $this->preishoheitProductRepository = $preishoheitProductRepository;
        $this->logger = $logger;
    }

    protected function configure(): void
    {
        $this->setName('bow:preishoheit:update-prices')
            ->setDescription('Updates product prices from Preishoheit API')
            ->addOption('page', null, InputOption::VALUE_OPTIONAL, 'Page number for pagination', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = \Shopware\Core\Framework\Context::createDefaultContext();
        $page = (int) $input->getOption('page');

        try {
            $criteria = new Criteria();
            $criteria->setLimit(20);
            $criteria->setOffset(($page - 1) * 20);
            $products = $this->preishoheitProductRepository->search($criteria, $context);

            if ($products->count() === 0) {
                $output->writeln('No products found for price update.');
                return Command::SUCCESS;
            }

            $this->priceUpdateService->updatePrices($products->getElements(), $context);

            $output->writeln('Price update completed successfully');
            $this->logger->info('Price update successfully completed', ['page' => $page]);

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $output->writeln('Error updating prices: ' . $e->getMessage());
            $this->logger->error('Error updating prices', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return Command::FAILURE;
        }
    }
}
