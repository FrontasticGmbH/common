#  Project

**Fully Qualified**: [`\Frontastic\Common\ReplicatorBundle\Domain\Project`](../../../../src/php/ReplicatorBundle/Domain/Project.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`projectId` | `string` |  | *Yes* | 
`name` | `string` |  | *Yes* | 
`customer` | `string` |  | *Yes* | 
`apiKey` | `string` |  | *Yes* | In the config this is the `secret`.
`previewUrl` | `string` |  | *Yes* | 
`publicUrl` | `string` |  | *Yes* | 
`webpackPort` | `int` |  | *Yes* | 
`ssrPort` | `int` |  | *Yes* | 
`configuration` | `array` | `[]` | *Yes* | 
`data` | `array` | `[]` | *Yes* | Additional external project data from sources like tideways. Does not follow any defined schema.
`languages` | `string[]` | `[]` | *Yes* | 
`defaultLanguage` | `string` |  | *Yes* | 
`projectSpecific` | `string[]` | `[]` | *Yes* | 
`endpoints` | [`Endpoint`](Endpoint.md)[] | `[]` | *Yes* | 

## Methods

* [getConfigurationSection()](#getconfigurationsection)

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

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
