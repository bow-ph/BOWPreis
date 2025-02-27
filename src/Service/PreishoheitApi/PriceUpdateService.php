<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\PreishoheitApi;

use BOW\Preishoheit\Entity\Product\PreishoheitProductEntity;
use BOW\Preishoheit\Service\Price\PriceAdjustmentService;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\Uuid\Uuid;

class PriceUpdateService
{
    private PreishoheitApiClient $apiClient;
    private EntityRepository $productRepository;
    private EntityRepository $priceHistoryRepository;
    private EntityRepository $errorLogRepository;
    private PriceAdjustmentService $priceAdjustmentService;

    public function __construct(
        PreishoheitApiClient $apiClient,
        EntityRepository $productRepository,
        EntityRepository $priceHistoryRepository,
        EntityRepository $errorLogRepository,
        PriceAdjustmentService $priceAdjustmentService
    ) {
        $this->apiClient = $apiClient;
        $this->productRepository = $productRepository;
        $this->priceHistoryRepository = $priceHistoryRepository;
        $this->errorLogRepository = $errorLogRepository;
        $this->priceAdjustmentService = $priceAdjustmentService;
    }

    public function updatePrices(array $products, Context $context): void
    {
        try {
            $identifiers = $this->collectProductIdentifiers($products);
            $jobResponse = $this->apiClient->createJob('amazon', $identifiers);
            
            if (!isset($jobResponse['job_id'])) {
                throw new PreishoheitApiException('No job ID received from API');
            }

            $jobId = $jobResponse['job_id'];
            $this->processJobResults($jobId, $products, $context);
        } catch (PreishoheitApiException $e) {
            $this->logError('API_ERROR', $e->getMessage(), $context);
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

    private function processJobResults(string $jobId, array $products, Context $context): void
    {
        $results = $this->apiClient->downloadJobResult($jobId);
        
        foreach ($results['data'] ?? [] as $result) {
            $this->processPriceUpdate($result, $context);
        }
    }

    private function processPriceUpdate(array $priceData, Context $context): void
    {
        try {
            if (!isset($priceData['ean'], $priceData['price'])) {
                throw new PreishoheitApiException('Invalid price data received');
            }

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('product.ean', $priceData['ean']));
            $preishoheitProduct = $this->productRepository->search($criteria, $context)->first();

            if (!$preishoheitProduct) {
                throw new PreishoheitApiException('Product not found for EAN: ' . $priceData['ean']);
            }

            $oldPrice = $preishoheitProduct->getProduct()->getPrice()->getGross();
            $newPrice = $this->priceAdjustmentService->calculateAdjustedPrice(
                $priceData['price'],
                $preishoheitProduct->getSurchargePercentage()
            );

            // Update product price
            $this->productRepository->update([
                [
                    'id' => $preishoheitProduct->getId(),
                    'price' => [['gross' => $newPrice, 'net' => $newPrice / 1.19]], // Assuming 19% VAT
                ]
            ], $context);

            // Log price history
            $this->priceHistoryRepository->create([
                [
                    'id' => Uuid::randomHex(),
                    'ean' => $priceData['ean'],
                    'productName' => $preishoheitProduct->getProduct()->getName(),
                    'oldPrice' => $oldPrice,
                    'newPrice' => $newPrice,
                ]
            ], $context);

        } catch (\Exception $e) {
            $this->logError('PRICE_UPDATE', $e->getMessage(), $context);
            throw $e;
        }
    }

    private function logError(string $type, string $message, Context $context): void
    {
        $this->errorLogRepository->create([
            [
                'id' => Uuid::randomHex(),
                'errorType' => $type,
                'errorMessage' => $message,
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
        $this->errorLogRepository->create([
            [
                'id' => Uuid::randomHex(),
                'errorType' => 'API_REQUEST',
                'errorMessage' => json_encode($requestData),
            ]
        ], $context);
    }

    private function logApiResponse(array $responseData, Context $context): void
    {
        $this->errorLogRepository->create([
            [
                'id' => Uuid::randomHex(),
                'errorType' => 'API_RESPONSE',
                'errorMessage' => json_encode($responseData),
            ]
        ], $context);
    }
}
