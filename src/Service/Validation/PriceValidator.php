<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\Validation;

use Psr\Log\LoggerInterface;
use Shopware\Core\Framework\Context;

class PriceValidator
{
    private LoggerInterface $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function validatePrice(float $price, string $productId, Context $context): bool
    {
        if ($price <= 0) {
            $this->logger->warning('Price validation failed: Price must be greater than 0', [
                'productId' => $productId,
                'price' => $price
            ]);
            return false;
        }

        return true;
    }

    public function validatePriceChange(float $oldPrice, float $newPrice, float $maxChangePercentage, string $productId, Context $context): bool
    {
        if ($oldPrice <= 0) {
            $this->logger->warning('Price validation failed: Old price must be greater than 0', [
                'productId' => $productId,
                'oldPrice' => $oldPrice
            ]);
            return false;
        }

        $changePercentage = abs(($newPrice - $oldPrice) / $oldPrice * 100);

        if ($changePercentage > $maxChangePercentage) {
            $this->logger->warning('Price validation failed: Price change exceeds maximum allowed', [
                'productId' => $productId,
                'oldPrice' => $oldPrice,
                'newPrice' => $newPrice,
                'changePercentage' => $changePercentage,
                'maxAllowedPercentage' => $maxChangePercentage
            ]);
            return false;
        }

        return true;
    }
}