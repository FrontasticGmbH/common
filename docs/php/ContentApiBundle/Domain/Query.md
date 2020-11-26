#  Query

**Fully Qualified**: [`\Frontastic\Common\ContentApiBundle\Domain\Query`](../../../../src/php/ContentApiBundle/Domain/Query.php)

**Extends**: [`ApiDataObject`](../../CoreBundle/Domain/ApiDataObject.md)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`contentType` | `string` |  | - | 
`query` | `string` |  | - | 
`contentIds` | `array` |  | - | 
`attributes` | [`AttributeFilter`](AttributeFilter.md)[] | `[]` | - | 

## Methods

* [fromArray()](#fromarray)

### fromArray()

```php
static public function fromArray(
    array $data,
    bool $ignoreAdditionalAttributes = false
): Query
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$data`|`array`||
`$ignoreAdditionalAttributes`|`bool`|`false`|

Return Value: [`Query`](Query.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
