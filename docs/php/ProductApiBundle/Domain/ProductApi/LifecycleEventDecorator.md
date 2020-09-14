#  LifecycleEventDecorator

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\LifecycleEventDecorator`](../../../../../src/php/ProductApiBundle/Domain/ProductApi/LifecycleEventDecorator.php)

**Implements**: [`ProductApi`](../ProductApi.md)

## Methods

* [__construct()](#__construct)
* [getAggregate()](#getaggregate)
* [getCategories()](#getcategories)
* [queryCategories()](#querycategories)
* [getProductTypes()](#getproducttypes)
* [getProduct()](#getproduct)
* [query()](#query)

### __construct()

```php
public function __construct(
    ProductApi $aggregate,
    iterable $listeners = []
): mixed
```

*LifecycleEventDecorator constructor.*

Argument|Type|Default|Description
--------|----|-------|-----------
`$aggregate`|[`ProductApi`](../ProductApi.md)||
`$listeners`|`iterable`|`[]`|

Return Value: `mixed`

### getAggregate()

```php
public function getAggregate(): object
```

Return Value: `object`

### getCategories()

```php
public function getCategories(
    Query\CategoryQuery $query
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](Query.md)\CategoryQuery||

Return Value: `array`

### queryCategories()

```php
public function queryCategories(
    Query\CategoryQuery $query
): Result
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](Query.md)\CategoryQuery||

Return Value: [`Result`](Result.md)

### getProductTypes()

```php
public function getProductTypes(
    Query\ProductTypeQuery $query
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](Query.md)\ProductTypeQuery||

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
`$query`|`mixed`||
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
`$query`|[`Query`](Query.md)\ProductQuery||
`$mode`|`string`|`self::QUERY_SYNC`|

Return Value: `object`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
