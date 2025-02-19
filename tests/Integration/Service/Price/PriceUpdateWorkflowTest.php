<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Integration\Service\Price;

use BOW\Preishoheit\Service\PreishoheitApi\PriceUpdateService;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class PriceUpdateWorkflowTest extends TestCase
{
    use IntegrationTestBehaviour;

    private Context $context;
    private PriceUpdateService $updateService;

    protected function setUp(): void
    {
        $this->context = Context::createDefaultContext();
        $this->updateService = $this->getContainer()->get(PriceUpdateService::class);
    }

    public function testCompleteUpdateWorkflow(): void
    {
        // Create test product
        $productId = $this->createTestProduct();
        $preishoheitProductId = $this->createPreishoheitProduct($productId);

        // Trigger price update
        $this->updateService->updatePrices([$preishoheitProductId], $this->context);

        // Verify price history entry
        $historyEntries = $this->getPriceHistory();
        static::assertCount(1, $historyEntries);
        static::assertEquals($productId, $historyEntries[0]->getProductId());
    }

    public function testCronjobExecution(): void
    {
        $command = $this->getContainer()->get('bow_preishoheit.command.update_prices');
        $exitCode = $command->run(new ArrayInput([]), new NullOutput());

        static::assertEquals(0, $exitCode);
    }

    private function createTestProduct(): string
    {
        $productRepository = $this->getContainer()->get('product.repository');
        $id = Uuid::randomHex();

        $productRepository->create([
            [
                'id' => $id,
                'name' => 'Test Product',
                'productNumber' => 'TEST-001',
                'stock' => 10,
                'price' => [['currencyId' => Defaults::CURRENCY, 'gross' => 100, 'net' => 84.03, 'linked' => true]],
                'manufacturer' => ['name' => 'test'],
                'tax' => ['name' => '19%', 'taxRate' => 19],
            ]
        ], $this->context);

        return $id;
    }

    private function createPreishoheitProduct(string $productId): string
    {
        $repository = $this->getContainer()->get('bow_preishoheit_product.repository');
        $id = Uuid::randomHex();

        $repository->create([
            [
                'id' => $id,
                'productId' => $productId,
                'active' => true,
                'surchargePercentage' => 10.0
            ]
        ], $this->context);

        return $id;
    }

    private function getPriceHistory(): array
    {
        $repository = $this->getContainer()->get('bow_preishoheit_price_history.repository');
        $criteria = new Criteria();
        $criteria->addAssociation('product');

        return $repository->search($criteria, $this->context)->getElements();
    }
}
