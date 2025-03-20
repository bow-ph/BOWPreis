<?php declare(strict_types=1);

namespace BOW\Preishoheit\Entity\Product;

use Shopware\Core\Framework\DataAbstractionLayer\Entity;
use Shopware\Core\Content\Product\ProductEntity;
use Shopware\Core\Framework\DataAbstractionLayer\EntityIdTrait;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use DateTimeInterface;

class PreishoheitProductEntity extends Entity
{
    use EntityIdTrait;

    protected string $productId;
    protected ?ProductEntity $product = null;
    protected string $jobId;
    protected ?array $priceData = null;
    protected ?DateTimeInterface $synchronizedAt = null;

    public function getProductId(): string
    {
        return $this->productId;
    }

    public function setProductId(string $productId): void
    {
        $this->productId = $productId;
    }

    public function getProduct(): ?ProductEntity
    {
        return $this->product;
    }

    public function setProduct(ProductEntity $product): void
    {
        $this->product = $product;
    }

    public function getJobId(): string
    {
        return $this->jobId;
    }

    public function setJobId(string $jobId): void
    {
        $this->jobId = $jobId;
    }

    public function getPriceData(): ?array
    {
        return $this->priceData;
    }

    public function setPriceData(?array $priceData): void
    {
        $this->priceData = $priceData;
    }

    public function getSynchronizedAt(): ?DateTimeInterface
    {
        return $this->synchronizedAt;
    }

    public function setSynchronizedAt(?DateTimeInterface $synchronizedAt): void
    {
        $this->synchronizedAt = $synchronizedAt;
    }
}
