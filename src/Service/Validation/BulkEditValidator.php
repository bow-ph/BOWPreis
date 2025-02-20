<?php declare(strict_types=1);

namespace BOW\Preishoheit\Service\Validation;

use BOW\Preishoheit\Exception\ValidationException;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;

class BulkEditValidator
{
    private EntityRepository $productRepository;

    public function __construct(EntityRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function validateBulkEdit(array $productIds, float $value, string $adjustmentType, Context $context): void
    {
        if (empty($productIds)) {
            throw new ValidationException('No products selected for bulk edit');
        }

        if ($value < 0) {
            throw new ValidationException('Adjustment value cannot be negative');
        }

        if ($adjustmentType === 'percentage' && $value > 100) {
            throw new ValidationException('Percentage value cannot exceed 100%');
        }

        $criteria = new Criteria($productIds);
        $products = $this->productRepository->search($criteria, $context);

        if ($products->count() !== count($productIds)) {
            throw new ValidationException('One or more selected products do not exist');
        }

        if ($adjustmentType === 'fixed') {
            foreach ($products as $product) {
                if ($product->getPrice() === null || $product->getPrice()->first() === null) {
                    throw new ValidationException('All selected products must have a valid price');
                }
            }
        }
    }
}
