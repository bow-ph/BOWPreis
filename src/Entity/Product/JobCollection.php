<?php declare(strict_types=1);

namespace BOW\Preishoheit\Core\Content\Job;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

/**
 * @method void add(JobEntity $entity)
 * @method JobEntity[] getIterator()
 * @method JobEntity[] getElements()
 * @method JobEntity|null get(string $key)
 * @method JobEntity|null first()
 * @method JobEntity|null last()
 */
class JobCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return JobEntity::class;
    }
}
