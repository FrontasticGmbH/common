#  ShopifyCartApi

**Fully Qualified**: [`\Frontastic\Common\ShopifyBundle\Domain\CartApi\ShopifyCartApi`](../../../../../src/php/ShopifyBundle/Domain/CartApi/ShopifyCartApi.php)

**Extends**: [`CartApiBase`](../../../CartApiBundle/Domain/CartApiBase.md)

## Methods

* [__construct()](#__construct)
* [getAvailableShippingMethodsImplementation()](#getavailableshippingmethodsimplementation)
* [getShippingMethodsImplementation()](#getshippingmethodsimplementation)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    ShopifyClient $client,
    AccountApi $accountApi,
    ShopifyProductMapper $productMapper,
    ShopifyAccountMapper $accountMapper
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`ShopifyClient`](../ShopifyClient.md)||
`$accountApi`|[`AccountApi`](../../../AccountApiBundle/Domain/AccountApi.md)||
`$productMapper`|[`ShopifyProductMapper`](../Mapper/ShopifyProductMapper.md)||
`$accountMapper`|[`ShopifyAccountMapper`](../Mapper/ShopifyAccountMapper.md)||

Return Value: `mixed`

### getAvailableShippingMethodsImplementation()

```php
public function getAvailableShippingMethodsImplementation(
    Cart $cart,
    string $locale
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$locale`|`string`||

Return Value: `array`

### getShippingMethodsImplementation()

```php
public function getShippingMethodsImplementation(
    string $locale,
    bool $onlyMatching = false
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$locale`|`string`||
`$onlyMatching`|`bool`|`false`|

Return Value: `array`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
