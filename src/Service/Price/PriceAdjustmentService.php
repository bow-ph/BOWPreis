<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\Price;

use Psr\Log\LoggerInterface;

class PriceAdjustmentService
{
    public function __construct(
        private readonly LoggerInterface $logger
    ) {}

    public function calculateAdjustedPrice(float $basePrice, ?float $surchargePercentage): float
    {
        try {
            if ($basePrice <= 0) {
                throw new \InvalidArgumentException('Base price must be greater than zero');
            }

            $adjustedPrice = $basePrice;
            
            if ($surchargePercentage !== null) {
                $adjustedPrice *= (1 + $surchargePercentage / 100);
            }

            return $adjustedPrice;
        } catch (\Throwable $e) {
            $this->logger->error('Price adjustment calculation error', [
                'message' => $e->getMessage(),
                'basePrice' => $basePrice,
                'surchargePercentage' => $surchargePercentage,
                'exception' => $e,
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
            } catch (\Throwable $e) {
                $this->logger->error('Bulk price adjustment error for product', [
                    'productId' => $product['id'],
                    'message' => $e->getMessage(),
                ]);

                // Fehlerhafte Produkte überspringen, um Verarbeitung fortzusetzen
                continue;
            }
        }

        return $adjustedPrices;
    }
}
