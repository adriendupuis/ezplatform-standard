<?php

namespace AdrienDupuis\EzPlatformStandardBundle\Controller;

use eZ\Publish\Core\FieldType;
use eZ\Publish\Core\MVC\Symfony\View\ContentView;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ImageController extends AbstractController
{
    public function viewAction(ContentView $view): BinaryFileResponse
    {
        $content = $view->getContent();
        /** @var FieldType\Image\Value $imageFieldValue */
        $imageFieldValue = $content->getFieldValue('image');

        // TODO: Handle DFS
        return new BinaryFileResponse("./{$imageFieldValue->uri}");
    }
}
