Adrien Dupuis' eZ Platform Standard Bundle
==========================================

Bundle providing features for front-office and extending the standard theme.

Install
-------

1. Add to [composer.json `repositories`](https://getcomposer.org/doc/04-schema.md#repositories): `{ "type": "vcs", "url": "https://github.com/adriendupuis/ezplatform-standard.git" }`
1. Execute `composer require adriendupuis/ezplatform-admin;`
1. Add to config/bundles.php: `AdrienDupuis\EzPlatformAdminBundle\AdrienDupuisEzPlatformAdminBundle::class => ['all' => true],`

Features
--------

## Fallback Search Engine

The fallback search engine is a wrapper receiving a list of search engines.
* Before searching, it loop on this list and execute the search on the first healthy search engine.
* For indexing, two configurable cases:
  - Index on every healthy engine; Skip unavailable ones.
  - Skip whole index if one engine is unhealthy.

TODO
----

- Twig functions and filters
- Enhance Fallback Search Engine indexation scenarios
