<?php declare(strict_types=1);

namespace BOW\Preishoheit\Command;

use BOW\Preishoheit\Service\PreishoheitApi\PriceUpdateService;
use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;

class UpdatePricesCommand extends Command
{
    private PriceUpdateService $priceUpdateService;
    private EntityRepository $preishoheitProductRepository;
    private SystemConfigService $configService;
    private LoggerInterface $logger;
    private EntityRepository $countryRepository;

    public function __construct(
        PriceUpdateService $priceUpdateService,
        EntityRepository $preishoheitProductRepository,
        SystemConfigService $configService,
        LoggerInterface $logger,
        EntityRepository $countryRepository
    ) {
        parent::__construct('bow:preishoheit:update-prices');
        $this->priceUpdateService = $priceUpdateService;
        $this->preishoheitProductRepository = $preishoheitProductRepository;
        $this->configService = $configService;
        $this->logger = $logger;
        $this->countryRepository = $countryRepository;
    }

    protected function configure(): void
    {
        $this->setDescription('Update product prices using Preishoheit API')
            ->addOption('page', null, InputOption::VALUE_OPTIONAL, 'Page number for pagination', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = Context::createDefaultContext();
        $page = (int) $input->getOption('page');

        $mappingMethod = $this->configService->get('BOWPreishoheit.config.mappingMethod', 'ean');
        if (!in_array($mappingMethod, ['ean', 'product_number'])) {
            $this->logger->warning('Invalid mapping method, defaulting to "ean".');
            $mappingMethod = 'ean';
        }

        $productGroup = $this->configService->get('BOWPreishoheit.config.productGroup', 'amazon');
        if (empty($productGroup)) {
            $this->logger->warning('No product group defined, defaulting to "amazon".');
            $productGroup = 'amazon';
        }

        $countriesConfig = $this->configService->get('BOWPreishoheit.config.countrySelection');
        $countries = $countriesConfig ?: $this->getActiveCountries($context);

        $this->logger->info('Starting price update process', [
            'page' => $page,
            'mappingMethod' => $mappingMethod,
            'productGroup' => $productGroup,
            'countries' => $countries
        ]);

        try {
            $products = $this->getProducts($context, $page);

            if ($products->count() === 0) {
                $output->writeln('No products found for price update.');
                $this->logger->info('No products found for price update', ['page' => $page]);
                return Command::SUCCESS;
            }

            $this->priceUpdateService->updatePrices($products->getElements(), $context, $productGroup, $countries);

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
        $criteria = new Criteria();
        $criteria->addAssociation('product');
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->setLimit(20);
        $criteria->setOffset(($page - 1) * 20);

        return $this->preishoheitProductRepository->search($criteria, $context);
    }

    private function getActiveCountries(Context $context): array
    {
        $criteria = new Criteria();
        $criteria->addFilter(new EqualsFilter('active', true));
        $countries = $this->countryRepository->search($criteria, $context);

        return array_map(fn($country) => $country->getIso(), $countries->getElements());
    }
}
