<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\PreishoheitApi;

use BOW\Preishoheit\Entity\Product\PreishoheitProductEntity;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\Uuid\Uuid;

class PriceUpdateService
{
    private PreishoheitApiClient $apiClient;
    private EntityRepository $productRepository;
    private EntityRepository $priceHistoryRepository;
    private EntityRepository $errorLogRepository;

    public function __construct(
        PreishoheitApiClient $apiClient,
        EntityRepository $productRepository,
        EntityRepository $priceHistoryRepository,
        EntityRepository $errorLogRepository
    ) {
        $this->apiClient = $apiClient;
        $this->productRepository = $productRepository;
        $this->priceHistoryRepository = $priceHistoryRepository;
        $this->errorLogRepository = $errorLogRepository;
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
        // Implementation will handle price updates and history logging
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
}
