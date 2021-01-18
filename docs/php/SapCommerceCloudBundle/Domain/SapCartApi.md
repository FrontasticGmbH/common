#  SapCartApi

**Fully Qualified**: [`\Frontastic\Common\SapCommerceCloudBundle\Domain\SapCartApi`](../../../../src/php/SapCommerceCloudBundle/Domain/SapCartApi.php)

**Extends**: [`CartApiBase`](../../CartApiBundle/Domain/CartApiBase.md)

## Methods

* [__construct()](#__construct)
* [getAvailableShippingMethodsImplementation()](#getavailableshippingmethodsimplementation)
* [getShippingMethodsImplementation()](#getshippingmethodsimplementation)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    SapClient $client,
    SapDataMapper $dataMapper,
    SapLocaleCreator $localeCreator,
    OrderIdGeneratorV2 $orderIdGenerator
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`SapClient`](SapClient.md)||
`$dataMapper`|[`SapDataMapper`](SapDataMapper.md)||
`$localeCreator`|[`SapLocaleCreator`](Locale/SapLocaleCreator.md)||
`$orderIdGenerator`|[`OrderIdGeneratorV2`](../../CartApiBundle/Domain/OrderIdGeneratorV2.md)||

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
`$cart`|[`Cart`](../../CartApiBundle/Domain/Cart.md)||
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
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
