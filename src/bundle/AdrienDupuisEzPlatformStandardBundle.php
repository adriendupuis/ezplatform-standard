<?php

namespace AdrienDupuis\EzPlatformStandardBundle;

use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class AdrienDupuisEzPlatformStandardBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new FallbackSearchEngine\CompilerPass(), PassConfig::TYPE_OPTIMIZE);
    }
}
