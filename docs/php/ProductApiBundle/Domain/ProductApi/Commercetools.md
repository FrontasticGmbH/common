#  Commercetools

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools`](../../../../../src/php/ProductApiBundle/Domain/ProductApi/Commercetools.php)

**Implements**: [`ProductApi`](../ProductApi.md)

## Methods

* [__construct()](#__construct)
* [getCategories()](#getcategories)
* [getProductTypes()](#getproducttypes)
* [getProduct()](#getproduct)
* [query()](#query)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    Commercetools\Client $client,
    Commercetools\Mapper $mapper,
    Commercetools\Locale\CommercetoolsLocaleCreator $localeCreator,
    EnabledFacetService $enabledFacetService,
    string $defaultLocale
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`Commercetools`](Commercetools.md)\Client||
`$mapper`|[`Commercetools`](Commercetools.md)\Mapper||
`$localeCreator`|[`Commercetools`](Commercetools.md)\Locale\CommercetoolsLocaleCreator||
`$enabledFacetService`|[`EnabledFacetService`](EnabledFacetService.md)||
`$defaultLocale`|`string`||

Return Value: `mixed`

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
`$query`|[`Query`](Query.md)\ProductQuery||
`$mode`|`string`|`self::QUERY_SYNC`|

Return Value: `object`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): Commercetools\Client
```

Return Value: [`Commercetools`](Commercetools.md)\Client

