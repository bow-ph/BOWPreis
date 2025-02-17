<?php declare(strict_types=1);

namespace BOW\Preishoheit\Entity\PriceHistory;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class PriceHistoryEntity extends Entity
{
    use EntityIdTrait;

    protected string $ean;
    protected string $productName;
    protected float $oldPrice;
    protected float $newPrice;

    public function getEan(): string
    {
        return $this->ean;
    }

    public function setEan(string $ean): void
    {
        $this->ean = $ean;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): void
    {
        $this->productName = $productName;
    }

    public function getOldPrice(): float
    {
        return $this->oldPrice;
    }

    public function setOldPrice(float $oldPrice): void
    {
        $this->oldPrice = $oldPrice;
    }

    public function getNewPrice(): float
    {
        return $this->newPrice;
    }

    public function setNewPrice(float $newPrice): void
    {
        $this->newPrice = $newPrice;
    }
}
