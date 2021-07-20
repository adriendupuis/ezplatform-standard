Adrien Dupuis' eZ Platform Standard Bundle
==========================================

Bundle providing features for front-office and extending the standard theme.

Install
-------

1. Add to [composer.json `repositories`](https://getcomposer.org/doc/04-schema.md#repositories): `{ "type": "vcs", "url": "https://github.com/adriendupuis/ezplatform-standard.git" }`
1. Execute `composer require adriendupuis/ezplatform-standard;`
1. Add to config/bundles.php: `AdrienDupuis\EzPlatformStandardBundle\AdrienDupuisEzPlatformStandardBundle::class => ['all' => true],`

Features
--------

### Solr Search Engine

* The content type identifier is indexed as `content_type_identifier_id`
  - At search runtime, it avoids loading content types by their identifiers to know their IDs;
  - While debugging in Solr admin, verbose memorizable strings can be used instead of IDs; Examples: `content_type_identifier_id:folder`, `content_type_identifier_id:(folder OR usergroup) AND document_type_id:location`

TODO
----

- Twig functions and filters
