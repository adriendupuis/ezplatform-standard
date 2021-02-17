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

### Views

#### `link`

Parameters:
- `href_url` [string] (optional): Raw URI to use as href attribute.
- `href_field` [string] (optional): Identifier of the field to use to generate the URL. If the field is a ezobject
- `inner_html` [string] (optional): Raw HTML to use as inner HTML
- `inner_field` [string] (optional): Identifier of the field to use to generate inner HTML


Example:

```twig
    {{ render(controller(
        'ez_content:viewAction',
        {
            locationId: target_location_id,
            viewType: 'link',
            params: {
                'inner_field': 'intro',
            },
        }
    )) }}
```

### Content Types

* `plain_text`: a content type to deliver plain text media types
  - Install: `bin/console kaliop:migration:migrate --path vendor/adriendupuis/ezplatform-standard/MigrationVersions/plaintext.yaml;`
  - Usage example: robots.txt — see vendor/adriendupuis/ezplatform-standard/MigrationVersions/plaintext.robots.txt.yaml
* `web_application`: a content type to upload static HTML
  - Install: `bin/console kaliop:migration:migrate --path vendor/adriendupuis/ezplatform-standard/MigrationVersions/web_application.yaml;`

TODO
----

- Twig functions and filters
- Continue Web Application
  - create a WebApplicationService
  - support at least .html, .xhtml, .tar and .tgz
  - clean-up on DeleteVersionEvent and DeleteContentEvent
  - full and embed views
