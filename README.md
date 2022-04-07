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
                'inner_field': 'short_title',
            },
        }
    )) }}
```

### View Matchers

#### `Field\Checkbox`

Match if a checkbox is checked.

The parameter is a checkbox field identifier or an array of identifiers. If one field exists, is a checkbox and is checked, it matches.

```yaml
match:
  Field\CheckboxValue: 'is_special'
```

Due to matching rules ordered from top to bottom, is the checkbox distinguish two cases, test checkbox first; For example:

```yaml
content_view:
  full:
    folder_special:
      template: '@ezdesign/full/folder_special.html.twig'
      match:
        Identifier\ContentType: ['folder']
        Field\Checkbox: ['is_special']
    folder:
      template: '@ezdesign/full/folder.html.twig'
      match:
        Identifier\ContentType: ['folder']
```

As this matcher doesn't work on content metadata but works with content field values, it's a bit slow and resource consuming.
If multiple matchers are used, this Field\* ones should be the last ones. 

#### `Field\Selection`

Match if selection has a given option selected.

The parameters are composed of
- a selection field identifier as key
- an option, or array of options, as value

About options:
- `null` means the absence of selected option; 
- Option index can be used;
- The (case insensitive) text of the option can be used — the text must be in the default language, not from an other content type's translation.

```yaml
Field\Selection:
    feature: [~, 0, 'normal']
```

### Content Types

* `plain_text`: a content type to deliver plain text media types
  - Install: `bin/console kaliop:migration:migrate --path vendor/adriendupuis/ezplatform-standard/MigrationVersions/plaintext.yaml;`
  - Usage example: robots.txt — see vendor/adriendupuis/ezplatform-standard/[MigrationVersions/plaintext.robots.txt.yaml](MigrationVersions/plaintext.robots.txt.yaml)
* `web_application`: a content type to upload static HTML
  - Install: `bin/console kaliop:migration:migrate --path vendor/adriendupuis/ezplatform-standard/MigrationVersions/web_application.yaml;`

### `FileTypeWhiteList` Validator

For field types using files (`ezbinaryfile`, `ezimage`, `ezmedia`), the `FileTypeWhiteList` can replace the `FileExtensionBlackList`.

| FileExtensionBlackList                              | FileTypeWhiteList                                             |
| --------------------------------------------------- | ------------------------------------------------------------- |
| Use file extension and can be fooled.               | Use real file type whatever the extension is.                 |
| Black list must be updated if a new danger appears. | White list must be updated if a new authorized usage appears. |

TODO
----

- Twig functions and filters
- Continue Web Application
  - create a WebApplicationService
  - support at least .html, .xhtml, .tar and .tgz
  - clean-up on DeleteVersionEvent and DeleteContentEvent
  - full and embed views
  - Use abstraction to handle DFS
- Continue FileTypeWhiteListValidator
  - Handle DFS
  - Do not activate it by default?
  - Validate default white list
- A new field type or an override of a ezbinaryfile where authorized mime types can be defined
