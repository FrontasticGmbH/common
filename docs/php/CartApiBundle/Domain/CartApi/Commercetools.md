#  Commercetools

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools`](../../../../../src/php/CartApiBundle/Domain/CartApi/Commercetools.php)

**Extends**: [`CartApiBase`](../CartApiBase.md)

## Methods

* [__construct()](#__construct)
* [setCustomType()](#setcustomtype)
* [getAvailableShippingMethodsImplementation()](#getavailableshippingmethodsimplementation)
* [getShippingMethodsImplementation()](#getshippingmethodsimplementation)
* [getDangerousInnerClient()](#getdangerousinnerclient)
* [updatePaymentStatus()](#updatepaymentstatus)
* [getPayment()](#getpayment)
* [updatePaymentInterfaceId()](#updatepaymentinterfaceid)

### __construct()

```php
public function __construct(
    Commercetools\Client $client,
    Commercetools\Mapper $cartMapper,
    Commercetools\Locale\CommercetoolsLocaleCreator $localeCreator,
    OrderIdGenerator $orderIdGenerator,
    \Psr\Log\LoggerInterface $logger
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`Commercetools`](../../../ProductApiBundle/Domain/ProductApi/Commercetools.md)\Client||
`$cartMapper`|[`Commercetools`](Commercetools.md)\Mapper||
`$localeCreator`|[`Commercetools`](../../../ProductApiBundle/Domain/ProductApi/Commercetools.md)\Locale\CommercetoolsLocaleCreator||
`$orderIdGenerator`|[`OrderIdGenerator`](../OrderIdGenerator.md)||
`$logger`|`\Psr\Log\LoggerInterface`||

Return Value: `mixed`

### setCustomType()

```php
public function setCustomType(
    Cart $cart,
    string $key,
    string $localeString = null
): Cart
```

*Intentionally not part of the CartAPI interface.*

Only for use in scenarios where CommerceTools is set as the backend API.

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||
`$key`|`string`||
`$localeString`|`string`|`null`|

Return Value: [`Cart`](../Cart.md)

### getAvailableShippingMethodsImplementation()

```php
public function getAvailableShippingMethodsImplementation(
    Cart $cart,
    string $localeString
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||
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

*Get *dangerous* inner client*

This method exists to enable you to use features which are not yet part
of the abstraction layer.

Be aware that any usage of this method might seriously hurt backwards
compatibility and the future abstractions might differ a lot from the
vendor provided abstraction.

Use this with care for features necessary in your customer and talk with
Frontastic about provising an abstraction.

Return Value: `mixed`

### updatePaymentStatus()

```php
public function updatePaymentStatus(
    Payment $payment
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$payment`|[`Payment`](../Payment.md)||

Return Value: `void`

### getPayment()

```php
public function getPayment(
    string $paymentId
): ?Payment
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$paymentId`|`string`||

Return Value: ?[`Payment`](../Payment.md)

### updatePaymentInterfaceId()

```php
public function updatePaymentInterfaceId(
    Payment $payment
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$payment`|[`Payment`](../Payment.md)||

Return Value: `void`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
