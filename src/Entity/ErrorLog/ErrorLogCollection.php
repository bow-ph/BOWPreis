<?php declare(strict_types=1);

namespace BOW\Preishoheit\Entity\ErrorLog;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void                 add(ErrorLogEntity $entity)
 * @method void                 set(string $key, ErrorLogEntity $entity)
 * @method ErrorLogEntity[]     getIterator()
 * @method ErrorLogEntity[]     getElements()
 * @method ErrorLogEntity|null  get(string $key)
 * @method ErrorLogEntity|null  first()
 * @method ErrorLogEntity|null  last()
 */
class ErrorLogCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return ErrorLogEntity::class;
    }
}
