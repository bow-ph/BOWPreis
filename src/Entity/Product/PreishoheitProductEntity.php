<?php declare(strict_types=1);

namespace BOW\Preishoheit\Entity\Product;

use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;

class PreishoheitProductEntity extends Entity
{
    use EntityIdTrait;

    protected bool $active;
    protected ?float $surchargePercentage;
    protected ?float $discountPercentage;
    protected ?ProductEntity $product;
    protected string $productId;
    protected string $productVersionId;

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getSurchargePercentage(): ?float
    {
        return $this->surchargePercentage;
    }

    public function setSurchargePercentage(?float $surchargePercentage): void
    {
        $this->surchargePercentage = $surchargePercentage;
    }

    public function getDiscountPercentage(): ?float
    {
        return $this->discountPercentage;
    }

    public function setDiscountPercentage(?float $discountPercentage): void
    {
        $this->discountPercentage = $discountPercentage;
    }

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(?ProductEntity $product): void
    {
        $this->product = $product;
    }

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function getProductVersionId(): string
    {
        return $this->productVersionId;
    }

    public function setProductVersionId(string $productVersionId): void
    {
        $this->productVersionId = $productVersionId;
    }
}
