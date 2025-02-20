<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\Price;

use BOW\Preishoheit\Service\ErrorHandling\ErrorLogger;

class PriceAdjustmentService
{
    public function __construct(
        private readonly ErrorLogger $errorLogger
    ) {}

    public function calculateAdjustedPrice(float $basePrice, ?float $surchargePercentage): float
    {
        try {
            if ($basePrice <= 0) {
                throw new \InvalidArgumentException('Base price must be greater than 0');
            }

            if ($surchargePercentage === null) {
                return $basePrice;
            }

            return $basePrice * (1 + ($surchargePercentage / 100));
        } catch (\Exception $e) {
            $this->errorLogger->logApiError($e, [
                'basePrice' => $basePrice,
                'surchargePercentage' => $surchargePercentage
            ]);
            throw $e;
        }
    }

    public function calculateBulkAdjustedPrices(array $products): array
    {
        $adjustedPrices = [];
        foreach ($products as $product) {
            try {
                $adjustedPrices[$product['id']] = $this->calculateAdjustedPrice(
                    $product['price'],
                    $product['surchargePercentage'] ?? null
                );
            } catch (\Exception $e) {
                $this->errorLogger->logApiError($e, [
                    'product' => $product
                ]);
                // Skip failed products but continue processing others
                continue;
            }
        }
        return $adjustedPrices;
    }
}
