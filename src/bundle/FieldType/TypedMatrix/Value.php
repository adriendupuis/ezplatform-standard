<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FieldType\TypedMatrix;

use eZ\Publish\Core\FieldType\Value as ValueInterface;

class Value extends ValueInterface
{
    /** @var TypedMatrixRow */
    private $rows;

    /**
     * @return TypedMatrixRow[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    public function __toString(): string
    {
        return '';
    }
}