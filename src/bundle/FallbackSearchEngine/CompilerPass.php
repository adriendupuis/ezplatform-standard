<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FallbackSearchEngine;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class CompilerPass implements CompilerPassInterface
{
    public const SEARCH_ENGINE_FALLBACK_HANDLER = 'ezplatform.search_engine.fallback.handler';
    public const SEARCH_ENGINE_PING_SERVICE_TAG = 'ezplatform.search_engine.ping';

    /**
     * Registers all found search engines to the SearchEngineFactory.
     *
     * @throws \LogicException
     */
    public function process(ContainerBuilder $container)
    {
        $searchEngineFallbackHandlerDefinition = $container->getDefinition(self::SEARCH_ENGINE_FALLBACK_HANDLER);
        $searchEnginePingServices = $container->findTaggedServiceIds(self::SEARCH_ENGINE_PING_SERVICE_TAG);

        foreach ($searchEnginePingServices as $serviceId => $attributes) {
            foreach ($attributes as $attribute) {
                $searchEngineFallbackHandlerDefinition->addMethodCall(
                    'registerSearchEnginePingService',
                    [
                        new Reference($serviceId),
                        $attribute['alias'],
                    ]
                );
            }
        }
    }
}
