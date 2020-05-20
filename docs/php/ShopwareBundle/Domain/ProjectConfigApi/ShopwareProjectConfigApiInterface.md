# `interface`  ShopwareProjectConfigApiInterface

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiInterface`](../../../../../src/php/ShopwareBundle/Domain/ProjectConfigApi/ShopwareProjectConfigApiInterface.php)

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
`$criteria`|`string`||- can be ISO2 country code, ISO3 country code, or country name

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

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
