#  Cart

Fully Qualified: [`\Frontastic\Common\CartApiBundle\Domain\Cart`](../../../../src/php/CartApiBundle/Domain/Cart.php)

Property|Type|Default|Description
--------|----|-------|-----------
`cartId`|`string`||
`cartVersion`|`string`||
`custom`|`array`|`[]`|
`lineItems`|`LineItem[]`|`[]`|
`email`|`string`||
`birthday`|`\DateTimeImmutable`||
`shippingMethod`|`?ShippingMethod`||
`shippingAddress`|`?Address`||
`billingAddress`|`?Address`||
`sum`|`int`||
`currency`|`string`||
`payments`|`Payment[]`|`[]`|
`discountCodes`|`string[]`|`[]`|
`dangerousInnerCart`|`mixed`||Access original object from backend

## Methods

* [getPayedAmount()](#getpayedamount)
* [hasUser()](#hasuser)
* [hasShippingAddress()](#hasshippingaddress)
* [hasBillingAddress()](#hasbillingaddress)
* [hasAddresses()](#hasaddresses)
* [hasCompletePayments()](#hascompletepayments)
* [isComplete()](#iscomplete)

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

### isComplete()

```php
public function isComplete(): bool
```

Return Value: `bool`

