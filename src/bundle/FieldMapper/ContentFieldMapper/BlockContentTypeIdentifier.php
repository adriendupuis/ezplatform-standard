<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FieldMapper\ContentFieldMapper;

use eZ\Publish\SPI\Persistence\Content as SPIContent;
use eZ\Publish\SPI\Persistence\Content\Type\Handler as ContentTypeHandler;
use eZ\Publish\SPI\Search\Field;
use eZ\Publish\SPI\Search\FieldType;
use EzSystems\EzPlatformSolrSearchEngine\FieldMapper\ContentFieldMapper;

class BlockContentTypeIdentifier extends ContentFieldMapper
{
    /** @var ContentTypeHandler */
    protected $contentTypeHandler;

    public function __construct(ContentTypeHandler $contentTypeHandler)
    {
        $this->contentTypeHandler = $contentTypeHandler;
    }

    public function accept(SPIContent $content)
    {
        return true;
    }

    public function mapFields(SPIContent $content)
    {
        $contentTypeIdentifier = $this->contentTypeHandler->load($content->versionInfo->contentInfo->contentTypeId)->identifier;

        return [
            new Field(
                'content_type_identifier',
                $contentTypeIdentifier,
                new FieldType\IdentifierField()
            ),
        ];
    }
}
