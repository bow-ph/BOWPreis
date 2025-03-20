<?php declare(strict_types=1);

namespace BOW\Preishoheit\Core\Content\Job;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class JobDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'bow_preishoheit_job';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new PrimaryKey(), new Required()),
            new StringField('job_id', 'jobId'),
            new StringField('status', 'status'),
            new JsonField('payload', 'payload'),
            new CreatedAtField(),
        ]);
    }

    public function getEntityClass(): string
    {
        return JobEntity::class;
    }

    public function getCollectionClass(): string
    {
        return JobCollection::class;
    }
}
