#  CachedShopwareProjectConfigApi

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\CachedShopwareProjectConfigApi`](../../../../../src/php/ShopwareBundle/Domain/ProjectConfigApi/CachedShopwareProjectConfigApi.php)

**Implements**: [`ShopwareProjectConfigApiInterface`](ShopwareProjectConfigApiInterface.md)

## Methods

* [__construct()](#__construct)
* [getCountryByCriteria()](#getcountrybycriteria)
* [getCurrency()](#getcurrency)
* [getPaymentMethods()](#getpaymentmethods)
* [getProjectConfig()](#getprojectconfig)
* [getSalutation()](#getsalutation)
* [getSalutations()](#getsalutations)

### __construct()

```php
public function __construct(
    ShopwareProjectConfigApiInterface $aggregate,
    \Psr\SimpleCache\CacheInterface $cache,
    bool $debug = false,
    int $cacheTtl = self::DEFAULT_CACHE_TTL
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$aggregate`|[`ShopwareProjectConfigApiInterface`](ShopwareProjectConfigApiInterface.md)||
`$cache`|`\Psr\SimpleCache\CacheInterface`||
`$debug`|`bool`|`false`|
`$cacheTtl`|`int`|`self::DEFAULT_CACHE_TTL`|

Return Value: `mixed`

### getCountryByCriteria()

```php
public function getCountryByCriteria(
    string $criteria
): ?ShopwareCountry
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$criteria`|`string`||

Return Value: ?[`ShopwareCountry`](ShopwareCountry.md)

### getCurrency()

```php
public function getCurrency(
    string $currencyId
): ?ShopwareCurrency
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$currencyId`|`string`||

Return Value: ?[`ShopwareCurrency`](ShopwareCurrency.md)

### getPaymentMethods()

```php
public function getPaymentMethods(): array
```

Return Value: `array`

### getProjectConfig()

```php
public function getProjectConfig(): array
```

Return Value: `array`

### getSalutation()

```php
public function getSalutation(
    string $criteria
): ?ShopwareSalutation
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$criteria`|`string`||

Return Value: ?[`ShopwareSalutation`](ShopwareSalutation.md)

### getSalutations()

```php
public function getSalutations(
    ?string $criteria = null,
    ?string $locale = null
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$criteria`|`?string`|`null`|
`$locale`|`?string`|`null`|

Return Value: `array`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
