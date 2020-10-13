#  LifecycleEventDecorator

**Fully Qualified**: [`\Frontastic\Common\ProductSearchApiBundle\Domain\LifecycleEventDecorator`](../../../../src/php/ProductSearchApiBundle/Domain/LifecycleEventDecorator.php)

**Implements**: [`ProductSearchApi`](ProductSearchApi.md)

## Methods

* [__construct()](#__construct)
* [getAggregate()](#getaggregate)
* [query()](#query)
* [getSearchableAttributes()](#getsearchableattributes)

### __construct()

```php
public function __construct(
    ProductSearchApi $aggregate,
    iterable $listeners = []
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$aggregate`|[`ProductSearchApi`](ProductSearchApi.md)||
`$listeners`|`iterable`|`[]`|

Return Value: `mixed`

### getAggregate()

```php
public function getAggregate(): ProductSearchApi
```

Return Value: [`ProductSearchApi`](ProductSearchApi.md)

### query()

```php
public function query(
    Query\ProductQuery $query
): \GuzzleHttp\Promise\PromiseInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](../../ProductApiBundle/Domain/ProductApi/Query.md)\ProductQuery||

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

### getSearchableAttributes()

```php
public function getSearchableAttributes(): \GuzzleHttp\Promise\PromiseInterface
```

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
