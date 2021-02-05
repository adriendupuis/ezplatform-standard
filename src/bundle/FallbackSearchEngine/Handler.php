<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FallbackSearchEngine;

use AdrienDupuis\EzPlatformStandardBundle\FallbackSearchEngine\Ping\PingInterface;
use eZ\Bundle\EzPublishCoreBundle\ApiLoader\Exception\InvalidSearchEngine;
use eZ\Bundle\EzPublishCoreBundle\ApiLoader\SearchEngineFactory;
use eZ\Publish\API\Repository\Values\Content\LocationQuery;
use eZ\Publish\API\Repository\Values\Content\Query;
use eZ\Publish\API\Repository\Values\Content\Query\Criterion;
use eZ\Publish\API\Repository\Values\Content\Search\SearchResult;
use eZ\Publish\Core\Base\Exceptions\InvalidArgumentException;
use eZ\Publish\SPI\Persistence\Content;
use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Search\VersatileHandler;
use Psr\Log\LoggerInterface;

class Handler implements VersatileHandler
{
    const ALIAS = 'fallback';

    /** @var string[] */
    private $searchEngineAliases;

    /** @var SearchEngineFactory */
    private $searchEngineFactory;

    /** @var LoggerInterface */
    private $logger;

    /** @var PingInterface[] */
    private $searchEnginePingServices;

    /** @var VersatileHandler */
    private $innerSearchEngine;

    public function __construct(
        array $searchEngineAliases,
        SearchEngineFactory $searchEngineFactory,
        LoggerInterface $logger = null
    ) {
        $this->searchEngineAliases = $searchEngineAliases;
        $this->searchEngineFactory = $searchEngineFactory;
        $this->logger = $logger;

        if (0 === count($searchEngineAliases)) {
            throw new InvalidArgumentException('$searchEngineAliases', 'The fallback search engine chain\'s alias list can\'t be empty.');
        } elseif (1 === count($searchEngineAliases)) {
            $this->logger->notice('The fallback search engine chain\'s alias list must contain more than one alias to be useful.');
        }
        if (in_array(self::ALIAS, $searchEngineAliases)) {
            throw new InvalidArgumentException('$searchEngineAliases', 'The fallback search engine chain can\'t contain the fallback handler\'s alias itself.');
        }
    }

    public function registerSearchEnginePingService(PingInterface $searchEnginePingService, string $searchEngineAlias)
    {
        $this->searchEnginePingServices[$searchEngineAlias] = $searchEnginePingService;
    }

    public function getSearchEnginePingService(string $searchEngineAlias): PingInterface
    {
        if (!array_key_exists($searchEngineAlias, $this->searchEnginePingServices)) {
            throw new InvalidSearchEngine("Search engine '{$searchEngineAlias}' has no ping service. Could not find any service tagged with 'ezplatform.search_engine.ping' with alias '{$searchEngineAlias}'.");
        }

        return $this->searchEnginePingServices[$searchEngineAlias];
    }

    public function setInnerSearchEngine()
    {
        $this->innerSearchEngine = $this->getAvailableSearchEngine();
    }

    public function getInnerSearchEngine(): ?VersatileHandler
    {
        if (is_null($this->innerSearchEngine)) {
            $this->setInnerSearchEngine();
        }

        return $this->innerSearchEngine;
    }

    public function getAvailableSearchEngine(): ?VersatileHandler
    {
        foreach ($this->searchEngineAliases as $index => $alias) {
            if ($this->getSearchEnginePingService($alias)->ping()) {
                if ($index && !is_null($this->logger)) {
                    $this->logger->notice("Use '{$alias}' search service as substitute.");
                }

                return $this->getSearchEngine($alias);
            } elseif (!$index && !is_null($this->logger)) {
                $this->logger->warning("Main search service '{$alias}' do not ping.");
            }
        }
        if (!is_null($this->logger)) {
            $this->logger->error('No search service available.');
        }

        return null;
    }

    private function getSearchEngine(string $alias): VersatileHandler
    {
        $searchEngines = $this->searchEngineFactory->getSearchEngines();
        if (array_key_exists($alias, $searchEngines)) {
            return $searchEngines[$alias];
        }
        throw new InvalidSearchEngine("Invalid search engine '{$alias}'. Could not find any service tagged with 'ezplatform.search_engine' with alias '{$alias}'.");
    }

    private function getEmptySearchResult(): SearchResult
    {
        return new SearchResult([
            'time' => 0,
            'totalCount' => 0,
            'searchHits' => [],
        ]);
    }

    /* Search */

    public function supports(int $capabilityFlag): bool
    {
        return $this->getInnerSearchEngine()->supports($capabilityFlag);
    }

    /**
     * @throws \eZ\Publish\API\Repository\Exceptions\InvalidArgumentException
     */
    public function findContent(Query $query, array $languageFilter = []): SearchResult
    {
        if (empty($this->getInnerSearchEngine())) {
            return $this->getEmptySearchResult();
        }

        return $this->getInnerSearchEngine()->findContent($query, $languageFilter);
    }

    public function findSingle(Criterion $filter, array $languageFilter = [])
    {
        return $this->getInnerSearchEngine()->findSingle($query, $languageFilter);
    }

    public function findLocations(LocationQuery $query, array $languageFilter = []): SearchResult
    {
        if (empty($this->getInnerSearchEngine())) {
            return $this->getEmptySearchResult();
        }

        return $this->getInnerSearchEngine()->findLocations($query, $languageFilter);
    }

    public function suggest($prefix, $fieldPaths = [], $limit = 10, Criterion $filter = null)
    {
        return $this->getInnerSearchEngine()->suggest($prefix, $fieldPaths, $limit, $filter);
    }

    /* Index */

    public function deleteTranslation(int $contentId, string $languageCode): void
    {
        foreach ($this->searchEngineAliases as $alias) {
            $this->getSearchEngine($alias)->deleteTranslation($contentId, $languageCode);
        }
    }

    public function indexContent(Content $content)
    {
        foreach ($this->searchEngineAliases as $alias) {
            $this->getSearchEngine($alias)->indexContent($content);
        }
    }

    public function deleteContent($contentId, $versionId = null)
    {
        foreach ($this->searchEngineAliases as $alias) {
            $this->getSearchEngine($alias)->deleteContent($contentId, $versionId);
        }
    }

    public function indexLocation(Location $location)
    {
        foreach ($this->searchEngineAliases as $alias) {
            $this->getSearchEngine($alias)->indexContent($location);
        }
    }

    public function deleteLocation($locationId, $contentId)
    {
        foreach ($this->searchEngineAliases as $alias) {
            $this->getSearchEngine($alias)->deleteLocation($locationId, $contentId);
        }
    }

    public function purgeIndex()
    {
        foreach ($this->searchEngineAliases as $alias) {
            $this->getSearchEngine($alias)->purgeIndex();
        }
    }
}
