<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FieldType\TypedMatrix;

use eZ\Publish\Core\FieldType\Value as ValueInterface;

class Value extends ValueInterface
{
    /** @var TypedMatrixRow */
    private $rows = [];

    public function __construct($rows = [])
    {
        $this->setRows($rows);
    }

    /**
     * @return TypedMatrixRow[]
     */
    public function getRows(): array
    {
        return $this->rows;
    }

    /**
     * @param TypedMatrixRow[] $rows
     *
     * @return $this
     */
    public function setRows(array $rows): self
    {
        $this->rows = $rows;

        return $this;
    }

    public function __toString(): string
    {
        return '';
    }
}
