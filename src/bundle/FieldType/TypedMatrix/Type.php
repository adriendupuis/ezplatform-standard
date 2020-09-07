<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FieldType\TypedMatrix;

use eZ\Publish\API\Repository\Values\ContentType\FieldDefinition;
use eZ\Publish\Core\FieldType\FieldType;
use eZ\Publish\Core\FieldType\Value as CoreValue;
use eZ\Publish\SPI\FieldType\Value as SPIValue;
use EzSystems\EzPlatformAdminUi\FieldType\FieldDefinitionFormMapperInterface;
use EzSystems\EzPlatformAdminUi\Form\Data\FieldDefinitionData;
use EzSystems\EzPlatformContentForms\Data\Content\FieldData;
use EzSystems\EzPlatformContentForms\FieldType\FieldValueFormMapperInterface;
use Symfony\Component\Form\FormInterface;

class Type extends FieldType implements FieldValueFormMapperInterface, FieldDefinitionFormMapperInterface
{
    const IDENTIFIER = 'adtypedmatrix';

    public function getFieldTypeIdentifier(): string
    {
        return self::IDENTIFIER;
    }

    public function mapFieldDefinitionForm(FormInterface $fieldDefinitionForm, FieldDefinitionData $data): void
    {
        // TODO: Implement mapFieldDefinitionForm() method.
    }

    protected function createValueFromInput($inputValue)
    {
        // TODO: Implement createValueFromInput() method.
    }

    protected function checkValueStructure(CoreValue $value)
    {
        // TODO: Implement checkValueStructure() method.
    }

    public function getName(SPIValue $value, FieldDefinition $fieldDefinition, string $languageCode): string
    {
        // TODO: Implement getName() method.
    }

    public function getEmptyValue()
    {
        return new Value();
    }

    public function fromHash($hash)
    {
        // TODO: Implement fromHash() method.
    }

    public function toHash(SPIValue $value)
    {
        // TODO: Implement toHash() method.
    }

    public function mapFieldValueForm(FormInterface $fieldForm, FieldData $data)
    {
        // TODO: Implement mapFieldValueForm() method.
    }
}
