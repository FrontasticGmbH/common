#  SapProductApi

**Fully Qualified**: [`\Frontastic\Common\SapCommerceCloudBundle\Domain\SapProductApi`](../../../../src/php/SapCommerceCloudBundle/Domain/SapProductApi.php)

**Implements**: [`ProductApi`](../../ProductApiBundle/Domain/ProductApi.md)

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
    SapClient $client,
    SapLocaleCreator $localeCreator,
    SapDataMapper $dataMapper
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`SapClient`](SapClient.md)||
`$localeCreator`|[`SapLocaleCreator`](Locale/SapLocaleCreator.md)||
`$dataMapper`|[`SapDataMapper`](SapDataMapper.md)||

Return Value: `mixed`

### getCategories()

```php
public function getCategories(
    Query\CategoryQuery $query
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](../../ProductApiBundle/Domain/ProductApi/Query.md)\CategoryQuery||

Return Value: `array`

### getProductTypes()

```php
public function getProductTypes(
    Query\ProductTypeQuery $query
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](../../ProductApiBundle/Domain/ProductApi/Query.md)\ProductTypeQuery||

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
`$query`|[`Query`](../../ProductApiBundle/Domain/ProductApi/Query.md)\ProductQuery||
`$mode`|`string`|`self::QUERY_SYNC`|

Return Value: `object`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

