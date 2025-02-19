<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Unit\Service\PreishoheitApi;

use BOW\Preishoheit\Service\PreishoheitApi\PreishoheitApiClient;
use BOW\Preishoheit\Service\PreishoheitApi\PriceUpdateService;
use BOW\Preishoheit\Service\Price\PriceAdjustmentService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\EntitySearchResult;

class PriceUpdateServiceTest extends TestCase
{
    private PriceUpdateService $service;
    private PreishoheitApiClient $apiClient;
    private EntityRepository $productRepository;
    private EntityRepository $priceHistoryRepository;
    private EntityRepository $errorLogRepository;
    private PriceAdjustmentService $priceAdjustmentService;
    private Context $context;

    protected function setUp(): void
    {
        $this->apiClient = $this->createMock(PreishoheitApiClient::class);
        $this->productRepository = $this->createMock(EntityRepository::class);
        $this->priceHistoryRepository = $this->createMock(EntityRepository::class);
        $this->errorLogRepository = $this->createMock(EntityRepository::class);
        $this->priceAdjustmentService = $this->createMock(PriceAdjustmentService::class);
        $this->context = $this->createMock(Context::class);

        $this->service = new PriceUpdateService(
            $this->apiClient,
            $this->productRepository,
            $this->priceHistoryRepository,
            $this->errorLogRepository,
            $this->priceAdjustmentService
        );
    }

    public function testUpdatePricesSuccess(): void
    {
        $products = $this->createMock(EntitySearchResult::class);
        $products->method('first')->willReturn(null);

        $this->apiClient->expects($this->once())
            ->method('createJob')
            ->with('amazon', [])
            ->willReturn(['job_id' => 'test-job-id']);

        $this->apiClient->expects($this->once())
            ->method('downloadJobResult')
            ->with('test-job-id')
            ->willReturn(['data' => []]);

        $this->service->updatePrices([], $this->context);
    }

    public function testUpdatePricesWithApiError(): void
    {
        $this->apiClient->expects($this->once())
            ->method('createJob')
            ->willThrowException(new \Exception('API Error'));

        $this->errorLogRepository->expects($this->once())
            ->method('create')
            ->with(
                $this->callback(function ($data) {
                    return isset($data[0]['errorType'])
                        && $data[0]['errorType'] === 'API_ERROR';
                }),
                $this->context
            );

        $this->service->updatePrices([], $this->context);
    }

    public function testCollectProductIdentifiers(): void
    {
        $product = $this->createMock(\stdClass::class);
        $product->method('getProduct')->willReturn(
            (object)['ean' => '1234567890']
        );

        $result = $this->service->collectProductIdentifiers([$product]);

        $this->assertEquals(['1234567890'], $result);
    }
}
