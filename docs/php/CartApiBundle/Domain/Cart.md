#  Cart

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\Cart`](../../../../src/php/CartApiBundle/Domain/Cart.php)

**Extends**: [`ApiDataObject`](../../CoreBundle/Domain/ApiDataObject.md)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`cartId` | `string` |  | *Yes* | 
`cartVersion` | `string` |  | - | 
`lineItems` | [`LineItem`](LineItem.md)[] | `[]` | *Yes* | 
`email` | `string` |  | - | 
`birthday` | `\DateTimeImmutable` |  | - | 
`shippingInfo` | ?[`ShippingInfo`](ShippingInfo.md) |  | - | 
`shippingMethod` | ?[`ShippingMethod`](ShippingMethod.md) |  | - | 
`shippingAddress` | `?\Frontastic\Common\CartApiBundle\Domain\Address` |  | - | 
`billingAddress` | `?\Frontastic\Common\CartApiBundle\Domain\Address` |  | - | 
`sum` | `int` |  | *Yes* | 
`currency` | `string` |  | *Yes* | 
`payments` | [`Payment`](Payment.md)[] | `[]` | *Yes* | 
`discountCodes` | `string[]` | `[]` | *Yes* | 
`taxed` | ?[`Tax`](Tax.md) |  | - | 
`dangerousInnerCart` | `mixed` |  | - | Access original object from backend

## Methods

* [getPayedAmount()](#getpayedamount)
* [hasUser()](#hasuser)
* [hasShippingAddress()](#hasshippingaddress)
* [hasBillingAddress()](#hasbillingaddress)
* [hasAddresses()](#hasaddresses)
* [hasCompletePayments()](#hascompletepayments)
* [isReadyForCheckout()](#isreadyforcheckout)
* [isComplete()](#iscomplete)
* [getPaymentById()](#getpaymentbyid)

### getPayedAmount()

```php
public function getPayedAmount(): int
```

Return Value: `int`

### hasUser()

```php
public function hasUser(): bool
```

Return Value: `bool`

### hasShippingAddress()

```php
public function hasShippingAddress(): bool
```

Return Value: `bool`

### hasBillingAddress()

```php
public function hasBillingAddress(): bool
```

Return Value: `bool`

### hasAddresses()

```php
public function hasAddresses(): bool
```

Return Value: `bool`

### hasCompletePayments()

```php
public function hasCompletePayments(): bool
```

Return Value: `bool`

### isReadyForCheckout()

```php
public function isReadyForCheckout(): bool
```

Return Value: `bool`

### isComplete()

```php
public function isComplete(): bool
```

Return Value: `bool`

### getPaymentById()

```php
public function getPaymentById(
    string $paymentId
): Payment
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$paymentId`|`string`||

Return Value: [`Payment`](Payment.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
