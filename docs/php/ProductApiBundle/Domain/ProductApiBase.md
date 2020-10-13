# `abstract`  ProductApiBase

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApiBase`](../../../../src/php/ProductApiBundle/Domain/ProductApiBase.php)

**Implements**: [`ProductApi`](ProductApi.md)

## Methods

* [__construct()](#__construct)
* [getCategories()](#getcategories)
* [queryCategories()](#querycategories)
* [getProductTypes()](#getproducttypes)
* [getProduct()](#getproduct)
* [query()](#query)

### __construct()

```php
public function __construct(
    ProductSearchApi $productSearchApi
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$productSearchApi`|[`ProductSearchApi`](../../ProductSearchApiBundle/Domain/ProductSearchApi.md)||

Return Value: `mixed`

### getCategories()

```php
public function getCategories(
    Query\CategoryQuery $query
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](ProductApi/Query.md)\CategoryQuery||

Return Value: `array`

### queryCategories()

```php
public function queryCategories(
    Query\CategoryQuery $query
): Result
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](ProductApi/Query.md)\CategoryQuery||

Return Value: [`Result`](ProductApi/Result.md)

### getProductTypes()

```php
public function getProductTypes(
    Query\ProductTypeQuery $query
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](ProductApi/Query.md)\ProductTypeQuery||

Return Value: `array`

### getProduct()

```php
public function getProduct(
    mixed $originalQuery,
    string $mode = self::QUERY_SYNC
): ?object
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$originalQuery`|`mixed`||
`$mode`|`string`|`self::QUERY_SYNC`|

Return Value: `?object`

### query()

```php
public function query(
    Query\ProductQuery $query,
    string $mode = self::QUERY_SYNC
): object
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](ProductApi/Query.md)\ProductQuery||
`$mode`|`string`|`self::QUERY_SYNC`|

Return Value: `object`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
