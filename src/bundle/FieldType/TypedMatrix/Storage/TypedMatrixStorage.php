<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FieldType\TypedMatrix\Storage;

use eZ\Publish\SPI\FieldType\FieldStorage;
use eZ\Publish\SPI\Persistence\Content\Field;
use eZ\Publish\SPI\Persistence\Content\VersionInfo;

class TypedMatrixStorage implements FieldStorage
{
    public function storeFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        // TODO: Implement storeFieldData() method.
    }

    public function getFieldData(VersionInfo $versionInfo, Field $field, array $context)
    {
        // TODO: Implement getFieldData() method.
    }

    public function deleteFieldData(VersionInfo $versionInfo, array $fieldIds, array $context)
    {
        // TODO: Implement deleteFieldData() method.
    }

    public function hasFieldData(): bool
    {
        return true;
    }

    public function getIndexData(VersionInfo $versionInfo, Field $field, array $context)
    {
        //TODO: indexation
        return null;
    }
}