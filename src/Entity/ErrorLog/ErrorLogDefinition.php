<?php declare(strict_types=1);

namespace BOW\Preishoheit\Entity\ErrorLog;

use Shopware\Core\Framework\DataAbstractionLayer\EntityDefinition;
use Shopware\Core\Framework\DataAbstractionLayer\Field\CreatedAtField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\PrimaryKey;
use Shopware\Core\Framework\DataAbstractionLayer\Field\Flag\Required;
use Shopware\Core\Framework\DataAbstractionLayer\Field\IdField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\StringField;
use Shopware\Core\Framework\DataAbstractionLayer\Field\LongTextField;
use Shopware\Core\Framework\DataAbstractionLayer\FieldCollection;

class ErrorLogDefinition extends EntityDefinition
{
    public const ENTITY_NAME = 'bow_preishoheit_error_log';

    public function getEntityName(): string
    {
        return self::ENTITY_NAME;
    }

    public function getCollectionClass(): string
    {
        return ErrorLogCollection::class;
    }

    public function getEntityClass(): string
    {
        return ErrorLogEntity::class;
    }

    protected function defineFields(): FieldCollection
    {
        return new FieldCollection([
            (new IdField('id', 'id'))->addFlags(new Required(), new PrimaryKey()),
            (new StringField('product_id', 'productId'))->addFlags(new Required()),
            (new StringField('error_type', 'errorType'))->addFlags(new Required()),
            (new LongTextField('error_message', 'errorMessage'))->addFlags(new Required()),
            new CreatedAtField(),
        ]);
    }
}
