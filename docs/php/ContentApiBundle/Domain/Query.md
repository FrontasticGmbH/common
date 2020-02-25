#  Query

**Fully Qualified**: [`\Frontastic\Common\ContentApiBundle\Domain\Query`](../../../../src/php/ContentApiBundle/Domain/Query.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Description
--------|----|-------|-----------
`contentType`|`string`||
`query`|`string`||
`contentIds`|`array`||
`attributes`|`AttributeFilter[]`|`[]`|

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

