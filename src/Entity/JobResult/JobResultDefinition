<?php declare(strict_types=1);

namespace Bow\Preishoheit\Entity\JobResult;

use Bow\Preishoheit\Entity\Job\JobDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class JobResultDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'bow_preishoheit_job_result';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return JobResultEntity::class;
    }

    public function getCollectionClass(): string
    {
        return JobResultCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            new IdField('id', 'id'),
            new FkField('job_id', 'jobId', JobDefinition::class),
            new FloatField('price', 'price'),
            new StringField('title', 'title'),
            new StringField('ean', 'ean'),
            new CreatedAtField(),
            new UpdatedAtField(),
        ]);
    }
}
