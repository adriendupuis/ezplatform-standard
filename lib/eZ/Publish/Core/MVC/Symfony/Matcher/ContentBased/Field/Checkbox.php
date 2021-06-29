<?php

namespace eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\Field;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\FieldType\Checkbox\Value;
use eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;
use eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\MultipleValued;
use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;
use eZ\Publish\Core\MVC\Symfony\View\LocationValueView;
use eZ\Publish\Core\MVC\Symfony\View\View;

class Checkbox extends MultipleValued implements MatcherInterface
{
    public function matchLocation(Location $location): bool
    {
        return $this->matchContent($location->getContent());
    }

    public function matchContentInfo(ContentInfo $contentInfo): bool
    {
        return $this->matchContent($contentInfo->getMainLocation()->getContent());
    }

    public function match(View $view): bool
    {
        if ($view instanceof ContentValueView) {
            /* @var $view ContentValueView */
            return $this->matchContent($view->getContent());
        }
        if ($view instanceof LocationValueView) {
            /* @var $view LocationValueView */
            return $this->matchContent($view->getLocation()->getContent());
        }

        return false;
    }

    private function matchContent(Content $content): bool
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
