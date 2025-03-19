<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\PreishoheitApi;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;
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
            $jobResponse = $this->apiClient->createJob('amazon', $identifiers);

            if (!isset($jobResponse['job_id'])) {
                throw new PreishoheitApiException('No job ID received from API');
            }

            $this->logger->info('Price update job created successfully', ['jobId' => $jobResponse['job_id']]);
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
        try {
            if (!isset($priceData['ean'], $priceData['price'])) {
                throw new PreishoheitApiException('Incomplete price data received');
            }
    
            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('product.ean', $priceData['ean']));
            $preishoheitProduct = $this->productRepository->search($criteria, $context)->first();
    
            if (!$preishoheitProduct || !$preishoheitProduct->getProduct()) {
                throw new PreishoheitApiException('Product not found for EAN: ' . $priceData['ean']);
            }
    
            $oldPrice = $preishoheitProduct->getProduct()->getPrice()->getGross();
            $newPrice = $this->priceAdjustmentService->calculateAdjustedPrice(
                (float)$priceData['price'],
                $preishoheitProduct->getSurchargePercentage()
            );
    
            $this->productRepository->update([
                [
                    'id' => $preishoheitProduct->getProduct()->getId(),
                    'price' => ['gross' => $newPrice, 'net' => $newPrice / 1.19], // Assuming 19% VAT
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
    
            $this->logger->info('Price updated successfully', [
                'productId' => $preishoheitProduct->getProduct()->getId(),
                'oldPrice' => $oldPrice,
                'newPrice' => $newPrice
            ]);
        } catch (\Throwable $e) {
            $this->logError('PRICE_UPDATE', $e->getMessage(), $context);
            throw $e;
        }
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

    private function getProducts(Context $context, int $page): EntitySearchResult
    {
        $criteria = new Criteria();
        $criteria->addAssociation('product');
        $criteria->addFilter(new EqualsFilter('active', true));
        $criteria->setLimit(20);
        $criteria->setOffset(($page - 1) * 20);
        
        return $this->productRepository->search($criteria, $context);
    }

    private function logApiRequest(array $requestData, Context $context): void
    {
        $this->logger->info('API request sent', ['request' => $requestData]);
    }

    private function logApiResponse(array $responseData, Context $context): void
    {
        $this->logger->info('API response received', ['response' => $responseData]);
    }
}