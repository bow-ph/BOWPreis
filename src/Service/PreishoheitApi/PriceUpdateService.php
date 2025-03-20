<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\PreishoheitApi;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;
use BOW\Preishoheit\Exception\PreishoheitApiException;
use BOW\Preishoheit\Service\Price\PriceAdjustmentService;

class PriceUpdateService
{
    private PreishoheitApiClient $apiClient;
    private EntityRepository $productRepository;
    private EntityRepository $priceHistoryRepository;
    private EntityRepository $errorLogRepository;
    private PriceAdjustmentService $priceAdjustmentService;
    private LoggerInterface $logger;

    public function __construct(
        PreishoheitApiClient $apiClient,
        EntityRepository $productRepository,
        EntityRepository $priceHistoryRepository,
        EntityRepository $errorLogRepository,
        PriceAdjustmentService $priceAdjustmentService,
        LoggerInterface $logger
    ) {
        $this->apiClient = $apiClient;
        $this->productRepository = $productRepository;
        $this->priceHistoryRepository = $priceHistoryRepository;
        $this->errorLogRepository = $errorLogRepository;
        $this->priceAdjustmentService = $priceAdjustmentService;
        $this->logger = $logger;
    }

    public function updatePrices(array $products, Context $context): void
    {
        try {
            $identifiers = $this->collectProductIdentifiers($products);

            if (empty($identifiers)) {
                throw new PreishoheitApiException('No product identifiers available.');
            }

            $jobResponse = $this->apiClient->createJob('amazon', $identifiers);

            if (!isset($jobResponse['job_id'])) {
                throw new PreishoheitApiException('No job ID received from API.');
            }

            $this->logger->info('Price update job created successfully.', ['jobId' => $jobResponse['job_id']]);
        } catch (\Throwable $e) {
            $this->logError('API_ERROR', $e->getMessage(), $context);
            throw $e;
        }
    }

    private function collectProductIdentifiers(array $products): array
    {
        $identifiers = [];
        foreach ($products as $product) {
            if ($product->getProduct() && $product->getProduct()->getEan()) {
                $identifiers[] = $product->getProduct()->getEan();
            }
        }
        return $identifiers;
    }

    private function processPriceUpdate(array $priceData, Context $context): void
    {
        if (empty($priceData['ean']) || !isset($priceData['price'])) {
            $message = 'Incomplete price data received.';
            $this->logError('PRICE_DATA_ERROR', $message, $context);
            throw new PreishoheitApiException($message);
        }

        $criteria = new Criteria();
        $criteria->addAssociation('product');
        $criteria->addFilter(new EqualsFilter('product.ean', $priceData['ean']));

        $preishoheitProduct = $this->productRepository->search($criteria, $context)->first();

        if (!$preishoheitProduct || !$preishoheitProduct->getProduct()) {
            $message = 'Product not found for EAN: ' . $priceData['ean'];
            $this->logError('PRODUCT_NOT_FOUND', $message, $context);
            throw new PreishoheitApiException($message);
        }

        $oldPrice = $preishoheitProduct->getProduct()->getPrice()->getGross();
        $newPrice = $this->priceAdjustmentService->calculateAdjustedPrice(
            (float)$priceData['price'],
            $preishoheitProduct->getSurchargePercentage()
        );

        $this->productRepository->update([
            [
                'id' => $preishoheitProduct->getProduct()->getId(),
                'price' => ['gross' => $newPrice, 'net' => $newPrice / 1.19],
            ]
        ], $context);

        $this->priceHistoryRepository->create([
            [
                'id' => Uuid::randomHex(),
                'productId' => $preishoheitProduct->getProduct()->getId(),
                'oldPrice' => $oldPrice,
                'newPrice' => $newPrice
            ]
        ], $context);

        $this->logger->info('Price updated successfully.', [
            'productId' => $preishoheitProduct->getProduct()->getId(),
            'oldPrice' => $oldPrice,
            'newPrice' => $newPrice
        ]);
    }

    private function logError(string $type, string $message, Context $context): void
    {
        $this->logger->error(sprintf('Error [%s]: %s', $type, $message));

        $this->errorLogRepository->create([
            [
                'id' => Uuid::randomHex(),
                'errorType' => $type,
                'errorMessage' => $message
            ]
        ], $context);
    }
}
