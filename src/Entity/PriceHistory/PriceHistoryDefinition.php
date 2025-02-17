<?php declare(strict_types=1);

namespace BOW\Preishoheit\Entity\PriceHistory;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FloatField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class PriceHistoryDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'bow_preishoheit_price_history';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return PriceHistoryCollection::class;
    }

    public function getEntityClass(): string
    {
        return PriceHistoryEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('ean', 'ean'))->addFlags(new Required()),
            (new StringField('product_name', 'productName'))->addFlags(new Required()),
            (new FloatField('old_price', 'oldPrice'))->addFlags(new Required()),
            (new FloatField('new_price', 'newPrice'))->addFlags(new Required()),
            new CreatedAtField(),
        ]);
    }
}
