<?php declare(strict_types=1);

namespace BOW\Preishoheit\Command;

use BOW\Preishoheit\Service\PreishoheitApi\PriceUpdateService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class UpdatePricesCommand extends Command
{
    protected static $defaultName = 'bow:preishoheit:update-prices';

    private PriceUpdateService $priceUpdateService;
    private EntityRepository $preishoheitProductRepository;

    public function __construct(
        PriceUpdateService $priceUpdateService,
        EntityRepository $preishoheitProductRepository
    ) {
        parent::__construct();
        $this->priceUpdateService = $priceUpdateService;
        $this->preishoheitProductRepository = $preishoheitProductRepository;
    }

    protected function configure(): void
    {
        $this->setDescription('Updates product prices from Preishoheit API')
            ->addOption('page', null, InputOption::VALUE_OPTIONAL, 'Page number for pagination', 1);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $context = Context::createDefaultContext();
        $page = (int) $input->getOption('page');
        
        $products = $this->getProducts($context, $page);
        
        if ($products->count() === 0) {
            $output->writeln('No active products found for price update');
            return Command::SUCCESS;
        }

        try {
            $this->priceUpdateService->updatePrices($products->getElements(), $context);
            $output->writeln('Price update completed successfully');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $output->writeln('Error updating prices: ' . $e->getMessage());
            return Command::FAILURE;
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
