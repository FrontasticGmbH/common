#  DummyCartApi

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\CartApi\DummyCartApi`](../../../../../src/php/CartApiBundle/Domain/CartApi/DummyCartApi.php)

**Extends**: [`CartApiBase`](../CartApiBase.md)

## Methods

* [getAvailableShippingMethodsImplementation()](#getavailableshippingmethodsimplementation)
* [getShippingMethodsImplementation()](#getshippingmethodsimplementation)
* [getDangerousInnerClient()](#getdangerousinnerclient)
* [getDangerousInnerMapper()](#getdangerousinnermapper)
* [getDangerousInnerLocaleCreator()](#getdangerousinnerlocalecreator)
* [updatePaymentStatus()](#updatepaymentstatus)
* [getPayment()](#getpayment)
* [updatePaymentInterfaceId()](#updatepaymentinterfaceid)

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

Return Value: `mixed`

### getDangerousInnerMapper()

```php
public function getDangerousInnerMapper(): Commercetools\Mapper
```

Return Value: [`Commercetools`](Commercetools.md)\Mapper

### getDangerousInnerLocaleCreator()

```php
public function getDangerousInnerLocaleCreator(): Commercetools\Locale\CommercetoolsLocaleCreator
```

Return Value: [`Commercetools`](../../../ProductApiBundle/Domain/ProductApi/Commercetools.md)\Locale\CommercetoolsLocaleCreator

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
