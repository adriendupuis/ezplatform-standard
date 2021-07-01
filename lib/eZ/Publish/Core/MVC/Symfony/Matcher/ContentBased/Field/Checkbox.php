<?php

namespace eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\Field;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\FieldType\Checkbox\Value;
use eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;
use eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued;

class Checkbox extends MultipleValued implements MatcherInterface, ContentMatcherInterface
{
    use ContentMatcherTrait;

    public function matchContent(Content $content): bool
    {
        foreach (array_keys($this->values) as $fieldIdentifier) {
            /** @var $fieldValue null|Value */
            if (null !== ($fieldValue = $content->getFieldValue($fieldIdentifier)) && $fieldValue instanceof Value) {
                if ($fieldValue->bool) {
                    return true;
                }
            }
        }

        return false;
    }
}
