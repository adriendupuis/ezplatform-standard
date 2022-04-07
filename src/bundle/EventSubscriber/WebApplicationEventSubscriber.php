<?php

namespace AdrienDupuis\EzPlatformStandardBundle\EventSubscriber;

use AdrienDupuis\EzPlatformStandardBundle\Service\WebApplicationService;
use eZ\Publish\API\Repository\Events\Content\DeleteContentEvent;
use eZ\Publish\API\Repository\Events\Content\DeleteVersionEvent;
use eZ\Publish\API\Repository\Events\Trash\TrashEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class WebApplicationEventSubscriber implements EventSubscriberInterface
{
    /** @var WebApplicationService */
    private $webApplicationService;

    public function __construct(WebApplicationService $webApplicationService)
    {
        $this->webApplicationService = $webApplicationService;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            DeleteVersionEvent::class => 'onDeleteVersion',
            DeleteContentEvent::class => 'onDeleteContent',
            TrashEvent::class => 'onTrash',
        ];
    }

    public function onDeleteVersion(DeleteVersionEvent $deleteVersionEvent)
    {
        $contentId = $deleteVersionEvent->getVersionInfo()->getContentInfo()->id;
        $versionId = $deleteVersionEvent->getVersionInfo()->id;
        $this->webApplicationService->onDelete($contentId, $versionId);
    }

    public function onDeleteContent(DeleteContentEvent $deleteContentEvent)
    {
        $contentId = $deleteContentEvent->getContentInfo()->id;
        $this->webApplicationService->onDelete($contentId);
    }

    public function onTrash(TrashEvent $trashEvent)
    {
        $contentId = $trashEvent->getTrashItem()->getContentInfo()->id;
        $this->webApplicationService->onDelete($contentId);
    }
}
