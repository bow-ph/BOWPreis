<?php declare(strict_types=1);

namespace BOW\Preishoheit\Tests\Unit\Service\Validation;

use BOW\Preishoheit\Service\ErrorHandling\ErrorHandler;
use BOW\Preishoheit\Service\Validation\PriceValidator;
use PHPUnit\Framework\TestCase;
use Shopware\Core\Framework\Context;

class PriceValidatorTest extends TestCase
{
    private PriceValidator $priceValidator;
    private ErrorHandler $errorHandler;
    private Context $context;

    protected function setUp(): void
    {
        $this->errorHandler = $this->createMock(ErrorHandler::class);
        $this->priceValidator = new PriceValidator($this->errorHandler);
        $this->context = Context::createDefaultContext();
    }

    public function testValidatePriceWithValidPrice(): void
    {
        $result = $this->priceValidator->validatePrice(10.0, 'testProductId', $this->context);
        $this->assertTrue($result);
    }

    public function testValidatePriceWithInvalidPrice(): void
    {
        $this->errorHandler->expects($this->once())
            ->method('handleError')
            ->with(
                'testProductId',
                'PRICE_VALIDATION',
                'Price must be greater than 0',
                $this->context
            );

        $result = $this->priceValidator->validatePrice(0.0, 'testProductId', $this->context);
        $this->assertFalse($result);
    }

    public function testValidatePriceChangeWithValidChange(): void
    {
        $result = $this->priceValidator->validatePriceChange(100.0, 110.0, 20.0, 'testProductId', $this->context);
        $this->assertTrue($result);
    }

    public function testValidatePriceChangeWithInvalidChange(): void
    {
        $this->errorHandler->expects($this->once())
            ->method('handleError');

        $result = $this->priceValidator->validatePriceChange(100.0, 150.0, 20.0, 'testProductId', $this->context);
        $this->assertFalse($result);
    }
}
