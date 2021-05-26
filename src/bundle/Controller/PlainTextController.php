<?php

namespace AdrienDupuis\EzPlatformStandardBundle\Controller;

use eZ\Publish\Core\FieldType;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use eZ\Publish\Core\Repository\Values\ContentType\FieldDefinition;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class PlainTextController extends AbstractController
{
    const DEFAULT_MEDIA_TYPE = 'plain/text';
    const MEDIA_TYPE_SELECT_FIELD_IDENTIFIER = 'media_type_select';
    const MEDIA_TYPE_STRING_FIELD_IDENTIFIER = 'media_type_string';
    const PLAIN_TEXT_FIELD_IDENTIFIER = 'plain_text';

    public function viewAction(ContentView $view): Response
    {
        $content = $view->getContent();

        $mediaType = null;
        if (empty($mediaType)) {
            /** @var FieldType\TextLine\Value $mediaTypeStringValue */
            $mediaTypeStringValue = $content->getFieldValue(self::MEDIA_TYPE_STRING_FIELD_IDENTIFIER);
            $mediaType = $mediaTypeStringValue->text;
        }
        if (empty($mediaType)) {
            /** @var FieldType\Selection\Value $mediaTypeSelectValue */
            $mediaTypeSelectValue = $content->getFieldValue(self::MEDIA_TYPE_SELECT_FIELD_IDENTIFIER);
            /** @var FieldDefinition $mediaTypeSelectDef */
            $mediaTypeSelectDef = $content->getContentType()->getFieldDefinition(self::MEDIA_TYPE_SELECT_FIELD_IDENTIFIER);
            $mediaType = $mediaTypeSelectDef->fieldSettings['options'][$mediaTypeSelectValue->selection[0]];
            if (preg_match('@.*: (?<media_type>\w*/\w*)$@', $mediaType, $matches)) {
                $mediaType = $matches['media_type'];
            }
        }
        if (empty($mediaType)) {
            $mediaType = self::DEFAULT_MEDIA_TYPE;
        }

        $plainText = $content->getFieldValue(self::PLAIN_TEXT_FIELD_IDENTIFIER)->text ?? '';

        $response = $view->getResponse() ?? new Response();
        $response->headers->add(['Content-Type' => "$mediaType; charset=UTF-8"]);
        $response->setContent($plainText);

        return $response;
    }
}
