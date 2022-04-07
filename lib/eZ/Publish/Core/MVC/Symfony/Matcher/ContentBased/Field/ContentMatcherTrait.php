<?php

namespace eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\Field;

use eZ\Publish\API\Repository\Values\Content\ContentInfo;
use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\Core\MVC\Symfony\View\ContentValueView;
use eZ\Publish\Core\MVC\Symfony\View\LocationValueView;
use eZ\Publish\Core\MVC\Symfony\View\View;

/** @uses ContentMatcherInterface::matchContent */
trait ContentMatcherTrait
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
}
