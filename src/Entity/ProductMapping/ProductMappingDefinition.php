<?php declare(strict_types=1);

namespace Bow\Preishoheit\Entity\ProductMapping;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ProductMappingDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'bow_preishoheit_product_mapping';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getEntityClass(): string
    {
        return ProductMappingEntity::class;
    }

    public function getCollectionClass(): string
    {
        return ProductMappingCollection::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            new IdField('id', 'id'),
            new FkField('product_id', 'productId', ProductDefinition::class),
            new StringField('external_id', 'externalId'),
            new CreatedAtField(),
            new UpdatedAtField(),
        ]);
    }
}
