<?php

namespace eZ\Publish\Core\MVC\Symfony\Matcher\ContentBased\Field;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\FieldType\Selection\Value;

class Selection implements ContentMatcherInterface
{
    use ContentMatcherTrait;

    /** @var array[] */
    private $matchingConfig;

    /** {@inheritDoc} */
    public function setMatchingConfig($matchingConfig)
    {
        if (!is_array($matchingConfig)) {
            throw new \InvalidArgumentException('Invalid matching config for Field\Selection; It should be an associative array identifier=>value[]');
        }
        $this->matchingConfig = $matchingConfig;
        foreach ($this->matchingConfig as $fieldIdentifier => $fieldScalarValues) {
            if (!is_array($fieldScalarValues)) {
                $this->matchingConfig[$fieldIdentifier] = [$fieldScalarValues];
            }
            $this->matchingConfig[$fieldIdentifier] = array_map('strtolower', $this->matchingConfig[$fieldIdentifier]);
        }
    }

    public function matchContent(Content $content): bool
    {
        foreach ($this->matchingConfig as $fieldIdentifier => $fieldScalarValues) {
            if (null !== ($fieldValue = $content->getFieldValue($fieldIdentifier)) && $fieldValue instanceof Value) {
                dump($fieldValue);
                if (empty($fieldValue->selection)) {
                    if (in_array(null, $fieldScalarValues, true)) {
                        return true;
                    }
                } else {
                    foreach ($fieldValue->selection as $optionIndex) {
                        if (in_array($optionIndex, $fieldScalarValues, true)) {
                            return true;
                        }
                    }
                    dump($content->getContentType()->getFieldDefinition($fieldIdentifier)->getFieldSettings());
                    $options = $content->getContentType()->getFieldDefinition($fieldIdentifier)->getFieldSettings()['options'];
                    foreach ($fieldValue->selection as $optionIndex) {
                        $optionItem = $options[$optionIndex];
                        if (in_array(strtolower($optionItem), $fieldScalarValues, true)) {
                            return true;
                        }
                    }
                }
            }
        }

        return false;
    }
}
