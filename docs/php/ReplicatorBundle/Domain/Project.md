#  Project

**Fully Qualified**: [`\Frontastic\Common\ReplicatorBundle\Domain\Project`](../../../../src/php/ReplicatorBundle/Domain/Project.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`projectId` | `string` |  | *Yes* | 
`name` | `string` |  | *Yes* | 
`customer` | `string` |  | *Yes* | 
`apiKey` | `string` |  | *Yes* | In the config this is the `secret`.
`previewUrl` | `string` |  | - | 
`preview` | `object` |  | - | 
`publicUrl` | `string` |  | *Yes* | 
`webpackPort` | `int` |  | *Yes* | 
`ssrPort` | `int` |  | *Yes* | 
`encryptedFieldsPublicKey` | `string|null` | `null` | - | 
`configuration` | `array` | `[]` | *Yes* | 
`data` | `array` | `[]` | *Yes* | Additional external project data from sources like tideways. Does not follow any defined schema.
`languages` | `string[]` | `[]` | *Yes* | 
`defaultLanguage` | `string` |  | *Yes* | 
`projectSpecific` | `string[]` | `[]` | *Yes* | 
`endpoints` | [`Endpoint`](Endpoint.md)[] | `[]` | *Yes* | 

## Methods

- [Project](#project)
  - [Methods](#methods)
    - [getConfigurationSection()](#getconfigurationsection)
    - [getExtensionRunnerManagerUrl()](#getextensionrunnermanagerurl)

### getConfigurationSection()

```php
public function getConfigurationSection(
    string …$sectionNamePath
): object
```

Argument|Type|Default|Description
--------|----|-------|-----------
`…$sectionNamePath`|`string`||

Return Value: `object`

### getExtensionRunnerManagerUrl()

```php
public function getExtensionRunnerManagerUrl(
    string $environment
): string
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$environment`|`string`||

Return Value: `string`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
