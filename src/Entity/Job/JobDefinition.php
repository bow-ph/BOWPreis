<?php declare(strict_types=1);

namespace Bow\Preishoheit\Core\Content\Job;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class JobDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'bow_preishoheit_job';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            new IdField('id', 'id'),
            new FkField('product_id', 'productId', 'product'),
            new StringField('external_id', 'externalId'),
            new StringField('status', 'status'),
            new CreatedAtField(),
        ]);
    }

    public function getEntityClass(): string
    {
        return JobEntity::class;
    }
}
