<?php declare(strict_types=1);

namespace BOW\Preishoheit\Entity\Product;

use Shopware\Core\Content\Product\ProductDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\BoolField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ManyToOneAssociationField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\ReferenceVersionField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\UpdatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class PreishoheitProductDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'bow_preishoheit_product';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return PreishoheitProductCollection::class;
    }

    public function getEntityClass(): string
    {
        return PreishoheitProductEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new BoolField('active', 'active'))->addFlags(new Required()),
            (new FloatField('surcharge_percentage', 'surchargePercentage')),
            (new FloatField('discount_percentage', 'discountPercentage')),
            
            (new FkField('product_id', 'productId', ProductDefinition::class))->addFlags(new Required()),
            (new ReferenceVersionField(ProductDefinition::class))->addFlags(new Required()),
            
            new ManyToOneAssociationField(
                'product',
                'product_id',
                ProductDefinition::class,
                'id'
            ),
            new CreatedAtField(),
            new UpdatedAtField(),
        ]);
    }
}
