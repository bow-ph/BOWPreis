<?php declare(strict_types=1);

namespace Bow\Preishoheit\Entity\Job;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class JobCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return JobEntity::class;
    }
}
