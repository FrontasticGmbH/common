#  Contentful

**Fully Qualified**: [`\Frontastic\Common\ContentApiBundle\Domain\ContentApi\Contentful`](../../../../../src/php/ContentApiBundle/Domain/ContentApi/Contentful.php)

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
    \Contentful\Delivery\Client $client,
    \Contentful\RichText\Renderer $richTextRenderer,
    Contentful\LocaleMapper $localeMapper,
    string $defaultLocale
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|`\Contentful\Delivery\Client`||
`$richTextRenderer`|`\Contentful\RichText\Renderer`||
`$localeMapper`|[`Contentful`](Contentful.md)\LocaleMapper||
`$defaultLocale`|`string`||

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

