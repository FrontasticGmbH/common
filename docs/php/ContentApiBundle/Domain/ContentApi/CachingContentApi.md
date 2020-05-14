#  CachingContentApi

**Fully Qualified**: [`\Frontastic\Common\ContentApiBundle\Domain\ContentApi\CachingContentApi`](../../../../../src/php/ContentApiBundle/Domain/ContentApi/CachingContentApi.php)

**Implements**: [`ContentApi`](../ContentApi.md)

## Methods

* [__construct()](#__construct)
* [getContentTypes()](#getcontenttypes)
* [getContent()](#getcontent)
* [query()](#query)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    ContentApi $aggregate,
    \Psr\SimpleCache\CacheInterface $cache,
    int $cacheTtlSec = 600
): mixed
```

*Warning - configuring the cacheTtl is considered experimental and subject to change.*

Argument|Type|Default|Description
--------|----|-------|-----------
`$aggregate`|[`ContentApi`](../ContentApi.md)||
`$cache`|`\Psr\SimpleCache\CacheInterface`||
`$cacheTtlSec`|`int`|`600`|

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

