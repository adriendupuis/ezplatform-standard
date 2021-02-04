<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FallbackSearchEngine\Ping;

class LegacyPing implements PingInterface
{
    public function ping(): bool
    {
        return true;
    }
}
