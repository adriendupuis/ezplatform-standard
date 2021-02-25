<?php

namespace AdrienDupuis\EzPlatformStandardBundle\Event;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\API\Repository\Values\Content\Field;
use Symfony\Contracts\EventDispatcher\Event;

class WebApplicationExtractionEvent extends Event
{
    /** @var Content */
    private $content;

    /** @var Field */
    private $field;

    /** @var string */
    private $originalPath;

    /** @var string */
    private $extractionPath;

    /** @var bool */
    private $success;

    public function __construct(
        Content $content,
        Field $field,
        string $originalPath,
        string $extractionPath,
        bool $success
    ) {
        $this->content = $content;
        $this->field = $field;
        $this->originalPath = $originalPath;
        $this->extractionPath = $extractionPath;
        $this->success = $success;
    }

    public function getContent(): Content
    {
        return $this->content;
    }

    public function getField(): Content
    {
        return $this->field;
    }

    public function getOriginalPath(): string
    {
        return $this->originalPath;
    }

    public function getExtractionPath(): string
    {
        return $this->extractionPath;
    }

    public function isSuccessful(): bool
    {
        return $this->success;
    }
}
