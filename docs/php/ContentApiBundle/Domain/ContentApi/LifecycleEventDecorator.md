#  LifecycleEventDecorator

**Fully Qualified**: [`\Frontastic\Common\ContentApiBundle\Domain\ContentApi\LifecycleEventDecorator`](../../../../../src/php/ContentApiBundle/Domain/ContentApi/LifecycleEventDecorator.php)

**Implements**: [`ContentApi`](../ContentApi.md)

## Methods

* [__construct()](#__construct)
* [getAggregate()](#getaggregate)
* [getContentTypes()](#getcontenttypes)
* [getContent()](#getcontent)
* [query()](#query)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    ContentApi $aggregate,
    iterable $listeners = []
): mixed
```

*LifecycleEventDecorator constructor.*

Argument|Type|Default|Description
--------|----|-------|-----------
`$aggregate`|[`ContentApi`](../ContentApi.md)||
`$listeners`|`iterable`|`[]`|

Return Value: `mixed`

### getAggregate()

```php
public function getAggregate(): mixed
```

Return Value: `mixed`

### getContentTypes()

```php
public function getContentTypes(): array
```

Return Value: `array`

### getContent()

```php
public function getContent(
    string $contentId,
    string $locale = null,
    string $mode = self::QUERY_SYNC
): ?object
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$contentId`|`string`||
`$locale`|`string`|`null`|
`$mode`|`string`|`self::QUERY_SYNC`|

Return Value: `?object`

### query()

```php
public function query(
    Query $query,
    string $locale = null,
    string $mode = self::QUERY_SYNC
): ?object
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](../Query.md)||
`$locale`|`string`|`null`|
`$mode`|`string`|`self::QUERY_SYNC`|

Return Value: `?object`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
