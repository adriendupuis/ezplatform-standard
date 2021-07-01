<?php

namespace eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\Field;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\MatcherInterface;

interface ContentMatcherInterface extends MatcherInterface
{
    public function matchContent(Content $content): bool;
}
