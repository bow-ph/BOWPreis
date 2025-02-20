<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Unit\Service\Price;

use BOW\Preishoheit\Service\ErrorHandling\ErrorLogger;
use BOW\Preishoheit\Service\Price\PriceAdjustmentService;
use PHPUnit\Framework\TestCase;

class PriceAdjustmentServiceTest extends TestCase
{
    private PriceAdjustmentService $service;
    private ErrorLogger $errorLogger;

    protected function setUp(): void
    {
        $this->errorLogger = $this->createMock(ErrorLogger::class);
        $this->service = new PriceAdjustmentService($this->errorLogger);
    }

    public function testCalculateAdjustedPriceWithoutSurcharge(): void
    {
        $basePrice = 100.00;
        $result = $this->service->calculateAdjustedPrice($basePrice, null);
        
        $this->assertEquals($basePrice, $result);
    }

    public function testCalculateAdjustedPriceWithPositiveSurcharge(): void
    {
        $basePrice = 100.00;
        $surchargePercentage = 10.0;
        $expectedPrice = 110.00;
        
        $result = $this->service->calculateAdjustedPrice($basePrice, $surchargePercentage);
        
        $this->assertEquals($expectedPrice, $result);
    }

    public function testCalculateAdjustedPriceWithNegativeSurcharge(): void
    {
        $basePrice = 100.00;
        $surchargePercentage = -10.0;
        $expectedPrice = 90.00;
        
        $result = $this->service->calculateAdjustedPrice($basePrice, $surchargePercentage);
        
        $this->assertEquals($expectedPrice, $result);
    }

    public function testCalculateAdjustedPriceWithZeroBasePrice(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Base price must be greater than 0');
        
        $this->service->calculateAdjustedPrice(0.00, 10.0);
    }

    public function testCalculateAdjustedPriceWithNegativeBasePrice(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Base price must be greater than 0');
        
        $this->service->calculateAdjustedPrice(-100.00, 10.0);
    }

    public function testCalculateBulkAdjustedPrices(): void
    {
        $products = [
            ['id' => '1', 'price' => 100.00, 'surchargePercentage' => 10.0],
            ['id' => '2', 'price' => 200.00, 'surchargePercentage' => -5.0],
            ['id' => '3', 'price' => 300.00, 'surchargePercentage' => null]
        ];

        $expectedPrices = [
            '1' => 110.00,
            '2' => 190.00,
            '3' => 300.00
        ];

        $result = $this->service->calculateBulkAdjustedPrices($products);

        $this->assertEquals($expectedPrices, $result);
    }

    public function testCalculateBulkAdjustedPricesWithInvalidProduct(): void
    {
        $products = [
            ['id' => '1', 'price' => 100.00, 'surchargePercentage' => 10.0],
            ['id' => '2', 'price' => -100.00, 'surchargePercentage' => 5.0],
            ['id' => '3', 'price' => 300.00, 'surchargePercentage' => null]
        ];

        $expectedPrices = [
            '1' => 110.00,
            '3' => 300.00
        ];

        $this->errorLogger->expects($this->once())
            ->method('logApiError')
            ->with(
                $this->isInstanceOf(\InvalidArgumentException::class),
                $this->arrayHasKey('product')
            );

        $result = $this->service->calculateBulkAdjustedPrices($products);

        $this->assertEquals($expectedPrices, $result);
    }
}
