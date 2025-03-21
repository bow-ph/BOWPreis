<?php declare(strict_types=1);

namespace Bow\Preishoheit\Entity\ProductMapping;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class ProductMappingCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ProductMappingEntity::class;
    }
}
