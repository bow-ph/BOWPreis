<?php declare(strict_types=1);

namespace Bow\Preishoheit\Entity\JobResult;

use Shopware\Core\Framework\DataAbstractionLayer\EntityCollection;

class JobResultCollection extends EntityCollection
{
    protected function getExpectedClass(): string
    {
        return JobResultEntity::class;
    }
}
