<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FallbackSearchEngine\Ping;

interface PingInterface
{
    public function ping(): bool;
}
