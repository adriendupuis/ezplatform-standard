<?php

namespace AdrienDupuis\EzPlatformStandardBundle\Controller;

use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\MVC\ConfigResolverInterface as ConfigResolver;
use eZ\Publish\Core\FieldType;
use eZ\Publish\API\Repository\Values\Content\Field;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebApplicationController extends AbstractController
{
    const USE_PAGELAYOUT_FIELD_IDENTIFIER = 'use_pagelayout';
    const FILE_FIELD_IDENTIFIER = 'file';
    const INDEX_FIELD_IDENTIFIER = 'index';

    /** @var ConfigResolver */
    private $configResolver;

    public function __construct(ConfigResolver $configResolver) {
        $this->configResolver = $configResolver;
    }

    public function viewAction(ContentView $view)
    {
        $content = $view->getContent();

        $usePagelayout = $content->getFieldValue(self::USE_PAGELAYOUT_FIELD_IDENTIFIER)->bool;
dump($content);
        $view->addParameters([
            'web_application_url' => $this->getWebApplicationUrl($content) ?? 'about:blank',
            'no_layout' => !$usePagelayout,
        ]);

        return $view;
    }

    private function getWebApplicationUrl(Content $content): ?string
    {
        /** @var FieldType\BinaryFile\Value $fileFieldValue */
        $fileFieldValue = $content->getFieldValue(self::FILE_FIELD_IDENTIFIER);

        /** @var FieldType\TextLine\Value $indexFieldValue */
        $indexFieldValue = $content->getFieldValue(self::INDEX_FIELD_IDENTIFIER);

        $contentId = $content->id;
        $versionId = $content->versionInfo->id;

        $storagePath = "{$this->configResolver->getParameter('var_dir')}/{$this->configResolver->getParameter('storage_dir')}";
        $originalPath = "{$storagePath}/original/{$fileFieldValue->id}";
        $extractPath = "{$storagePath}/images/web_application/{$contentId}/{$versionId}";
        $indexPath = "{$extractPath}/{$indexFieldValue->text}";
        $baseUrl = '';
        $webApplicationUrl = "$baseUrl/$indexPath";

        switch ($fileFieldValue->mimeType) {
            case 'application/zip':
                if (!is_dir($extractPath)) {
                    $zip = new \ZipArchive();
                    if ($zip->open($originalPath)) {
                        $zip->extractTo($extractPath);
                        $zip->close();
                    } else {
                        throw new \Exception('Unopenable archive');
                    }
                }
                return $webApplicationUrl;
        }


        return null;
    }

}
