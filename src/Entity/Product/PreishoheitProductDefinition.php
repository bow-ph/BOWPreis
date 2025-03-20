<?php declare(strict_types=1);

namespace BOW\Preishoheit\Entity\Product;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\FkField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\JsonField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\DateTimeField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\ApiAware;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;

class PreishoheitProductDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'bow_preishoheit_product';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey(), new ApiAware()),
            (new FkField('product_id', 'productId', 'product'))->addFlags(new Required(), new ApiAware()),
            (new StringField('job_id', 'jobId'))->addFlags(new Required(), new ApiAware()),
            (new JsonField('price_data', 'priceData'))->addFlags(new ApiAware()),
            (new DateTimeField('synchronized_at', 'synchronizedAt'))->addFlags(new ApiAware()),
            new CreatedAtField(),
        ]);
    }

    public function getCollectionClass(): string
    {
        return PreishoheitProductCollection::class;
    }

    public function getEntityClass(): string
    {
        return PreishoheitProductEntity::class;
    }
}
