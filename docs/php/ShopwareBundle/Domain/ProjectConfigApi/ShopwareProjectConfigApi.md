#  ShopwareProjectConfigApi

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApi`](../../../../../src/php/ShopwareBundle/Domain/ProjectConfigApi/ShopwareProjectConfigApi.php)

**Extends**: [`AbstractShopwareApi`](../AbstractShopwareApi.md)

**Implements**: [`ShopwareProjectConfigApiInterface`](ShopwareProjectConfigApiInterface.md)

## Methods

* [getCountryByCriteria()](#getcountrybycriteria)
* [getCurrency()](#getcurrency)
* [getPaymentMethods()](#getpaymentmethods)
* [getProjectConfig()](#getprojectconfig)
* [getSalutation()](#getsalutation)
* [getSalutations()](#getsalutations)
* [getShippingMethods()](#getshippingmethods)

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
    ?string $criteria = null
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$criteria`|`?string`|`null`|

Return Value: `array`

### getShippingMethods()

```php
public function getShippingMethods(): array
```

Return Value: `array`

