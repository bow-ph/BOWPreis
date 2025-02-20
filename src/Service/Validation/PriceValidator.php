<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\Validation;

use BOW\Preishoheit\Service\ErrorHandling\ErrorHandler;
use Shopware\Core\Framework\Context;

class PriceValidator
{
    private ErrorHandler $errorHandler;

    public function __construct(ErrorHandler $errorHandler)
    {
        $this->errorHandler = $errorHandler;
    }

    public function validatePrice(float $price, string $productId, Context $context): bool
    {
        if ($price <= 0) {
            $this->errorHandler->handleError(
                $productId,
                'PRICE_VALIDATION',
                'Price must be greater than 0',
                $context
            );
            return false;
        }

        return true;
    }

    public function validatePriceChange(float $oldPrice, float $newPrice, float $maxChangePercentage, string $productId, Context $context): bool
    {
        if ($oldPrice <= 0) {
            $this->errorHandler->handleError(
                $productId,
                'PRICE_VALIDATION',
                'Old price must be greater than 0',
                $context
            );
            return false;
        }

        $changePercentage = abs(($newPrice - $oldPrice) / $oldPrice * 100);
        if ($changePercentage > $maxChangePercentage) {
            $this->errorHandler->handleError(
                $productId,
                'PRICE_VALIDATION',
                sprintf('Price change of %.2f%% exceeds maximum allowed change of %.2f%%', $changePercentage, $maxChangePercentage),
                $context
            );
            return false;
        }

        return true;
    }
}
