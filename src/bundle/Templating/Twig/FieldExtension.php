<?php

namespace AdrienDupuis\EzPlatformStandardBundle\Templating\Twig;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\ContentExtension;
use eZ\Publish\Core\MVC\Symfony\Templating\Twig\Extension\FieldRenderingExtension;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FieldExtension extends AbstractExtension
{
    public function __construct(ContentExtension $contentExtension, FieldRenderingExtension $fieldRenderingExtension)
    {
        $this->contentExtension = $contentExtension;
        $this->fieldRenderingExtension = $fieldRenderingExtension;
    }

    public function getName()
    {
        return 'ezplatform.adriendupuis.field_extension';
    }

    public function getFunctions()
    {
        return [
            new TwigFunction(
                'ez_render_first_not_empty_field',
                [$this, 'renderFirstNotEmptyField'],
                ['is_safe' => ['html'], 'needs_environment' => true]
            ),
            new TwigFunction(
                'ez_first_not_empty_field_value',
                [$this, 'getFirstNotEmptyFieldValue']
            ),
            new TwigFunction(
                'ez_first_not_empty_field_identifier',
                [$this, 'getFirstNotEmptyFieldIdentifier']
            ),
        ];
    }

    public function renderFirstNotEmptyField(Content $content, array $fieldIdentifierList, array $params = []): string
    {
        $forcedLanguage = isset($params['lang']) ? $params['lang'] : null;
        if (null !== $fieldDefIdentifier = $this->getFirstNotEmptyFieldIdentifier($content, $fieldDefIdentifierList, $forcedLanguage)) {
            return $this->fieldRenderingExtension->renderField($content, $fieldDefIdentifier, $params);
        }

        return '';
    }

    public function getFirstNotEmptyFieldValue(Content $content, array $fieldDefIdentifierList, $forcedLanguage = null)
    {
        if (null !== $fieldDefIdentifier = $this->getFirstNotEmptyFieldIdentifier($content, $fieldDefIdentifierList, $forcedLanguage)) {
            return $this->contentExtension->getTranslatedFieldValue($content, $fieldDefIdentifier, $forcedLanguage);
        }

        return null;
    }

    public function getFirstNotEmptyFieldIdentifier(Content $content, array $fieldDefIdentifierList, $forcedLanguage = null): ?string
    {
        foreach ($fieldDefIdentifierList as $fieldDefIdentifier) {
            $this->contentExtension->isFieldEmpty($content, $fieldDefIdentifier, $forcedLanguage);

            return $fieldDefIdentifier;
        }

        return null;
    }
}
