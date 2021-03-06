# `interface`  ProductApi

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi`](../../../../src/php/ProductApiBundle/Domain/ProductApi.php)

## Methods

* [getCategories()](#getcategories)
* [queryCategories()](#querycategories)
* [getProductTypes()](#getproducttypes)
* [getProduct()](#getproduct)
* [query()](#query)
* [getDangerousInnerClient()](#getdangerousinnerclient)

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
    mixed $query,
    string $mode = self::QUERY_SYNC
): ?object
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|`mixed`||This might also be a `ProductQuery` for backwards compatibility reasons.
`$mode`|`string`|`self::QUERY_SYNC`|One of the QUERY_* connstants. Execute the query synchronously or asynchronously?

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
`$mode`|`string`|`self::QUERY_SYNC`|One of the QUERY_* connstants. Execute the query synchronously or asynchronously?

Return Value: `object`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

*Get *dangerous* inner client*

This method exists to enable you to use features which are not yet part
of the abstraction layer.

Be aware that any usage of this method might seriously hurt backwards
compatibility and the future abstractions might differ a lot from the
vendor provided abstraction.

Use this with care for features necessary in your customer and talk with
Frontastic about provising an abstraction.

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
