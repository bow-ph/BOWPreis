<?php declare(strict_types=1);

namespace BOW\Preishoheit\Entity\Product;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                      add(PreishoheitProductEntity $entity)
 * @method void                      set(string $key, PreishoheitProductEntity $entity)
 * @method PreishoheitProductEntity[]    getIterator()
 * @method PreishoheitProductEntity[]    getElements()
 * @method PreishoheitProductEntity|null get(string $key)
 * @method PreishoheitProductEntity|null first()
 * @method PreishoheitProductEntity|null last()
 */
class PreishoheitProductCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return PreishoheitProductEntity::class;
    }
}
