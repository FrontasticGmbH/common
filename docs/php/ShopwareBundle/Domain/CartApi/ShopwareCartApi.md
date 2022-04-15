#  ShopwareCartApi

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\CartApi\ShopwareCartApi`](../../../../../src/php/ShopwareBundle/Domain/CartApi/ShopwareCartApi.php)

**Extends**: [`CartApiBase`](../../../CartApiBundle/Domain/CartApiBase.md)

## Methods

* [__construct()](#__construct)
* [getAvailableShippingMethodsImplementation()](#getavailableshippingmethodsimplementation)
* [getShippingMethodsImplementation()](#getshippingmethodsimplementation)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    ClientInterface $client,
    LocaleCreator $localeCreator,
    DataMapperResolver $mapperResolver,
    ShopwareProjectConfigApiFactory $projectConfigApiFactory,
    AccountApi $accountApi,
    ?string $defaultLanguage,
    ?CartCheckoutService $cartCheckoutService = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`ClientInterface`](../ClientInterface.md)||
`$localeCreator`|[`LocaleCreator`](../Locale/LocaleCreator.md)||
`$mapperResolver`|[`DataMapperResolver`](../DataMapper/DataMapperResolver.md)||
`$projectConfigApiFactory`|[`ShopwareProjectConfigApiFactory`](../ProjectConfigApi/ShopwareProjectConfigApiFactory.md)||
`$accountApi`|[`AccountApi`](../../../AccountApiBundle/Domain/AccountApi.md)||
`$defaultLanguage`|`?string`||
`$cartCheckoutService`|?[`CartCheckoutService`](../../../CartApiBundle/Domain/CartCheckoutService.md)|`null`|

Return Value: `mixed`

### getAvailableShippingMethodsImplementation()

```php
public function getAvailableShippingMethodsImplementation(
    Cart $cart,
    string $localeString
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$localeString`|`string`||

Return Value: `array`

### getShippingMethodsImplementation()

```php
public function getShippingMethodsImplementation(
    string $localeString,
    bool $onlyMatching = false
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$localeString`|`string`||
`$onlyMatching`|`bool`|`false`|

Return Value: `array`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): ClientInterface
```

Return Value: [`ClientInterface`](../ClientInterface.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
