#  Mapper

**Fully Qualified**: [`\Frontastic\Common\FindologicBundle\Domain\ProductSearchApi\Mapper`](../../../../../src/php/FindologicBundle/Domain/ProductSearchApi/Mapper.php)

## Methods

* [queryToRequest()](#querytorequest)
* [getAttributesForRequest()](#getattributesforrequest)
* [getSortAttributesForRequest()](#getsortattributesforrequest)
* [dataToProducts()](#datatoproducts)
* [dataToFacets()](#datatofacets)
* [dataToFacet()](#datatofacet)

### queryToRequest()

```php
public function queryToRequest(
    Query\ProductQuery $query
): SearchRequest
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](../../../ProductApiBundle/Domain/ProductApi/Query.md)\ProductQuery||

Return Value: [`SearchRequest`](../SearchRequest.md)

### getAttributesForRequest()

```php
public function getAttributesForRequest(
    Query\ProductQuery $query
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](../../../ProductApiBundle/Domain/ProductApi/Query.md)\ProductQuery||

Return Value: `array`

### getSortAttributesForRequest()

```php
public function getSortAttributesForRequest(
    Query\ProductQuery $query
): ?string
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](../../../ProductApiBundle/Domain/ProductApi/Query.md)\ProductQuery||

Return Value: `?string`

### dataToProducts()

```php
public function dataToProducts(
    array $items,
    Query\ProductQuery $query
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$items`|`array`||
`$query`|[`Query`](../../../ProductApiBundle/Domain/ProductApi/Query.md)\ProductQuery||

Return Value: `array`

### dataToFacets()

```php
public function dataToFacets(
    array $filterData,
    Query\ProductQuery $query
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$filterData`|`array`||
`$query`|[`Query`](../../../ProductApiBundle/Domain/ProductApi/Query.md)\ProductQuery||

Return Value: `array`

### dataToFacet()

```php
public function dataToFacet(
    array $facetData
): Result\Facet
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$facetData`|`array`||

Return Value: [`Result`](../../../ProductApiBundle/Domain/ProductApi/Result.md)\Facet

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
