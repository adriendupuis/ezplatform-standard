<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FallbackSearchEngine\Ping;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class ElasticsearchPing implements PingInterface
{
    /** @var string */
    private $baseUrl;

    /** @var HttpClientInterface */
    private $httpClient;

    public function __construct(string $baseUrl, HttpClientInterface $httpClient)
    {
        $this->baseUrl = $baseUrl;
        $this->httpClient = $httpClient;
    }

    /** @todo Sevaral hosts, cluster health */
    public function ping(): bool
    {
        if (empty($this->baseUrl)) {
            return false;
        }

        try {
            return 200 === $this->httpClient->request('GET', $this->baseUrl)->getStatusCode();
        } catch (\Throwable $throwable) {
            return false;
        }
    }
}
