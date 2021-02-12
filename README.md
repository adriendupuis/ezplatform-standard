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

### Twig functions and filters

* “First not empty field” collection (a bit like the [`ez_content_field_identifier_first_filled_image`](https://doc.ibexa.co/en/master/guide/twig_functions_reference/#ez_content_field_identifier_first_filled_image))
  - `ez_render_first_not_empty_field(content, field_identifier_list, params)`; Example: `{{ ez_render_first_not_empty_field(content, ['intro', 'introduction', 'desc', 'description', 'summary']) }}`
  - `ez_first_not_empty_field_value`
  - `ez_first_not_empty_field_identifier`

TODO
----

- Twig functions and filters
