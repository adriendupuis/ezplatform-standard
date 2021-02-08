Adrien Dupuis' eZ Platform Standard Bundle
==========================================

Bundle providing features for front-office and extending the standard theme.

Install
-------

1. Add to [composer.json `repositories`](https://getcomposer.org/doc/04-schema.md#repositories): `{ "type": "vcs", "url": "https://github.com/adriendupuis/ezplatform-standard.git" }`
1. Execute `composer require adriendupuis/ezplatform-admin;`
1. Add to config/bundles.php: `AdrienDupuis\EzPlatformAdminBundle\AdrienDupuisEzPlatformAdminBundle::class => ['all' => true],`

TODO
----

- Twig functions and filters
