<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Performance\Service\PreishoheitApi;

use BOW\Preishoheit\Service\PreishoheitApi\PriceUpdateService;
use Shopware\Core\Framework\Test\TestCaseBase\IntegrationTestBehaviour;
use PHPUnit\Framework\TestCase;

class PriceUpdatePerformanceTest extends TestCase
{
    use IntegrationTestBehaviour;

    private PriceUpdateService $priceUpdateService;

    protected function setUp(): void
    {
        $this->priceUpdateService = $this->getContainer()->get(PriceUpdateService::class);
    }

    public function testBulkPriceUpdatePerformance(): void
    {
        $this->markTestIncomplete('Performance test to be implemented');
    }
}
