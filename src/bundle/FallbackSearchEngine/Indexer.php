<?php

namespace AdrienDupuis\EzPlatformStandardBundle\FallbackSearchEngine;

use AdrienDupuis\EzPlatformStandardBundle\FallbackSearchEngine\Handler as FallbackSearchHandler;
use Doctrine\DBAL\Connection;
use eZ\Bundle\EzPublishCoreBundle\ApiLoader\Exception\InvalidSearchEngine;
use eZ\Bundle\EzPublishCoreBundle\ApiLoader\SearchEngineIndexerFactory;
use eZ\Publish\Core\Search\Common\IncrementalIndexer;
use eZ\Publish\SPI\Persistence\Handler as PersistenceHandler;
use Psr\Log\LoggerInterface;

class Indexer extends IncrementalIndexer
{
    /** @var string[] */
    private $searchEngineAliases;

    /** @var SearchEngineIndexerFactory */
    private $searchEngineIndexerFactory;

    public function __construct(
        LoggerInterface $logger,
        PersistenceHandler $persistenceHandler,
        Connection $connection,
        FallbackSearchHandler $searchHandler,
        array $searchEngineAliases,
        SearchEngineIndexerFactory $searchEngineIndexerFactory
    ) {
        parent::__construct($logger, $persistenceHandler, $connection, $searchHandler);

        $this->searchEngineAliases = $searchEngineAliases;
        $this->searchEngineIndexerFactory = $searchEngineIndexerFactory;
    }

    public function getName()
    {
        $name = 'Fallback Pseudo Search Engine wrapping ';
        $indexers = $this->searchEngineIndexerFactory->getSearchEngineIndexers();
        /**
         * @var int    $index
         * @var string $alias
         */
        foreach ($this->searchEngineAliases as $index => $alias) {
            if ($index) {
                if (count($indexers) < $index + 1) {
                    $name .= ', ';
                } else {
                    $name .= ' & ';
                }
            }
            $name .= $this->getSearchEngineIndexer($alias)->getName();
        }

        return $name;
    }

    private function getSearchEngineIndexer(string $alias): IncrementalIndexer
    {
        $searchEngineIndexers = $this->searchEngineIndexerFactory->getSearchEngineIndexers();
        if (array_key_exists($alias, $searchEngineIndexers)) {
            return $searchEngineIndexers[$alias];
        }
        throw new InvalidSearchEngine("Invalid search engine '{$alias}'. Could not find any service tagged with 'ezplatform.search_engine.indexer' with alias '{$alias}'.");
    }

    public function updateSearchIndex(array $contentIds, $commit)
    {
        foreach ($this->searchEngineAliases as $alias) {
            $this->getSearchEngineIndexer($alias)->updateSearchIndex($contentIds, $commit);
        }
    }

    public function purge()
    {
        foreach ($this->searchEngineAliases as $alias) {
            $this->getSearchEngineIndexer($alias)->purge();
        }
    }
}
