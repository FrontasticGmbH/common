#  LegacyLifecycleEventDecorator

**Fully Qualified**: [`\Frontastic\Common\ProductSearchApiBundle\Domain\LegacyLifecycleEventDecorator`](../../../../src/php/ProductSearchApiBundle/Domain/LegacyLifecycleEventDecorator.php)

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
    \Psr\Container\ContainerInterface $container,
    \Psr\Log\LoggerInterface $logger,
    iterable $listeners = []
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$aggregate`|[`ProductSearchApi`](ProductSearchApi.md)||
`$container`|`\Psr\Container\ContainerInterface`||
`$logger`|`\Psr\Log\LoggerInterface`||
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
