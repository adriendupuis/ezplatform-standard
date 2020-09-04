<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FieldType\TypedMatrix;

use eZ\Publish\Core\Persistence\Legacy\Content\FieldValue\Converter;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldDefinition;
use eZ\Publish\Core\Persistence\Legacy\Content\StorageFieldValue;
use eZ\Publish\SPI\Persistence\Content\FieldValue;
use eZ\Publish\SPI\Persistence\Content\Type\FieldDefinition;

class LegacyConverter implements Converter
{
    public function toStorageValue(FieldValue $value, StorageFieldValue $storageFieldValue)
    {
        // TODO: Implement toStorageValue() method.
    }

    public function toFieldValue(StorageFieldValue $value, FieldValue $fieldValue)
    {
        // TODO: Implement toFieldValue() method.
    }

    public function toStorageFieldDefinition(FieldDefinition $fieldDef, StorageFieldDefinition $storageDef)
    {
        // TODO: Implement toStorageFieldDefinition() method.
    }

    public function toFieldDefinition(StorageFieldDefinition $storageDef, FieldDefinition $fieldDef)
    {
        // TODO: Implement toFieldDefinition() method.
    }

    /**
     * @inheritDoc
     */
    public function getIndexColumn()
    {
        return 'sort_key_string';
    }
}