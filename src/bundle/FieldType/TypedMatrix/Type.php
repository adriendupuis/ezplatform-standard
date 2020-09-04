<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FieldType\TypedMatrix;

use eZ\Publish\Core\FieldType\FieldType;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformContentForms\FieldType\FieldValueFormMapperInterface;

class Type extends FieldType implements FieldValueFormMapperInterface, FieldDefinitionFormMapperInterface
{
    const IDENTIFIER = 'adtypedmatrix';

    public function getFieldTypeIdentifier(): string
    {
        return self::IDENTIFIER;
    }
}