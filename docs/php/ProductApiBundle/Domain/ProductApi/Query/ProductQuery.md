#  ProductQuery

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery`](../../../../../../src/php/ProductApiBundle/Domain/ProductApi/Query/ProductQuery.php)

**Extends**: [`ProductApi`](../../ProductApi.md)\PaginatedQuery

Property|Type|Default|Description
--------|----|-------|-----------
`category`|`?string`||
`sku`|`?string`||
`skus`|`?string[]`||
`productId`|`?string`||
`productIds`|`?string[]`||
`productType`|`?string`||
`query`|`?string`||This is a full text search on the API
`filter`|[`Filter`](Filter.md)[]|`[]`|Filters that will be applied *before* the actual facets. CommerceTools allowed a list of filter strings, too, but this is deprecated in commercetools.
`facets`|[`Facet`](Facet.md)[]|`[]`|
`sortAttributes`|`string[]`|`[]`|Map of sort attributes => sort order
`fuzzy`|`bool`|`false`|

## Methods

* [validate()](#validate)

### validate()

```php
public function validate(): void
```

Return Value: `void`

