#  ProductQuery

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery`](../../../../../../src/php/ProductApiBundle/Domain/ProductApi/Query/ProductQuery.php)

**Extends**: [`Query`](../Query.md)

Property|Type|Default|Description
--------|----|-------|-----------
`category`|`string`||
`sku`|`string`||
`skus`|`array`||
`productId`|`string`||
`productIds`|`array`||
`productType`|`string`||
`currency`|`string`||
`query`|`string`||This is a full text search on the API
`filter`|[`Filter`](Filter.md)[]|`[]`|
`facets`|[`Facet`](Facet.md)[]|`[]`|
`sortAttributes`|`mixed`|`[]`|Map of sort attributes => sort order
`fuzzy`|`bool`|`false`|

## Methods

* [validate()](#validate)

### validate()

```php
public function validate(): void
```

Return Value: `void`
