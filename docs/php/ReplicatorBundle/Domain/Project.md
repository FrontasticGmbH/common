#  Project

**Fully Qualified**: [`\Frontastic\Common\ReplicatorBundle\Domain\Project`](../../../../src/php/ReplicatorBundle/Domain/Project.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`projectId` | `string` |  | - | 
`name` | `string` |  | - | 
`customer` | `string` |  | - | 
`apiKey` | `string` |  | - | In the config this is the `secret`.
`previewUrl` | `string` |  | - | 
`publicUrl` | `string` |  | - | 
`webpackPort` | `int` |  | - | 
`ssrPort` | `int` |  | - | 
`configuration` | `array` | `[]` | - | 
`data` | `array` | `[]` | - | Additional external project data from sources like tideways. Does not follow any defined schema.
`languages` | `string[]` | `[]` | - | 
`defaultLanguage` | `string` |  | - | 
`projectSpecific` | `string[]` | `[]` | - | 
`endpoints` | [`Endpoint`](Endpoint.md)[] | `[]` | - | 

## Methods

* [getConfigurationSection()](#getconfigurationsection)

### getConfigurationSection()

```php
public function getConfigurationSection(
    string $sectionName
): object
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$sectionName`|`string`||

Return Value: `object`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
