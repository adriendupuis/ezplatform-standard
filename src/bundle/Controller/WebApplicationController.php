<?php

namespace AdrienDupuis\EzPlatformStandardBundle\Controller;

use AdrienDupuis\EzPlatformStandardBundle\Service\WebApplicationService;
use eZ\Publish\API\Repository\Values\Content\Content;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WebApplicationController extends AbstractController
{
    const USE_PAGELAYOUT_FIELD_IDENTIFIER = 'use_pagelayout';
    const FILE_FIELD_IDENTIFIER = 'file';
    const INDEX_FIELD_IDENTIFIER = 'index';

    /** @var WebApplicationService */
    private $webApplicationService;

    public function __construct(WebApplicationService $webApplicationService)
    {
        $this->webApplicationService = $webApplicationService;
    }

    public function viewAction(ContentView $view): ContentView
    {
        $content = $view->getContent();

        $view->addParameters([
            'web_application_url' => $this->getWebApplicationUrl($content) ?? 'about:blank',
            'no_layout' => !$content->getFieldValue(self::USE_PAGELAYOUT_FIELD_IDENTIFIER)->bool,
        ]);

        return $view;
    }

    private function getWebApplicationUrl(Content $content): ?string
    {
        return $this->webApplicationService->getWebApplicationUrl($content, self::FILE_FIELD_IDENTIFIER, self::INDEX_FIELD_IDENTIFIER);
    }
}
