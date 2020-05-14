#  ShopwareProductApi

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\ProductApi\ShopwareProductApi`](../../../../../src/php/ShopwareBundle/Domain/ProductApi/ShopwareProductApi.php)

**Extends**: [`AbstractShopwareApi`](../AbstractShopwareApi.md)

**Implements**: [`ProductApi`](../../../ProductApiBundle/Domain/ProductApi.md)

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
    ClientInterface $client,
    DataMapperResolver $mapperResolver,
    LocaleCreator $localeCreator,
    string $defaultLanguage,
    EnabledFacetService $enabledFacetService,
    ShopwareProjectConfigApiFactory $projectConfigApiFactory
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`ClientInterface`](../ClientInterface.md)||
`$mapperResolver`|[`DataMapperResolver`](../DataMapper/DataMapperResolver.md)||
`$localeCreator`|[`LocaleCreator`](../Locale/LocaleCreator.md)||
`$defaultLanguage`|`string`||
`$enabledFacetService`|[`EnabledFacetService`](../../../ProductApiBundle/Domain/ProductApi/EnabledFacetService.md)||
`$projectConfigApiFactory`|[`ShopwareProjectConfigApiFactory`](../ProjectConfigApi/ShopwareProjectConfigApiFactory.md)||

Return Value: `mixed`

### getCategories()

```php
public function getCategories(
    Query\CategoryQuery $query
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](../../../ProductApiBundle/Domain/ProductApi/Query.md)\CategoryQuery||

Return Value: `array`

### getProductTypes()

```php
public function getProductTypes(
    Query\ProductTypeQuery $query
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](../../../ProductApiBundle/Domain/ProductApi/Query.md)\ProductTypeQuery||

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
`$query`|[`Query`](../../../ProductApiBundle/Domain/ProductApi/Query.md)\ProductQuery||
`$mode`|`string`|`self::QUERY_SYNC`|

Return Value: `object`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): ClientInterface
```

Return Value: [`ClientInterface`](../ClientInterface.md)

