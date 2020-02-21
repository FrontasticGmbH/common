#  Cart

Fully Qualified: [`\Frontastic\Common\CartApiBundle\Domain\Cart`](../../../../src/php/CartApiBundle/Domain/Cart.php)



Property|Type|Default|Description
--------|----|-------|-----------
`cartId`|`string`|``|
`cartVersion`|`string`|``|
`custom`|`array`|`[]`|
`lineItems`|`[LineItem](LineItem.md)[]`|`[]`|
`email`|`string`|``|
`birthday`|`\DateTimeImmutable`|``|
`shippingMethod`|`?[ShippingMethod](ShippingMethod.md)`|``|
`shippingAddress`|`?[Address](../../AccountApiBundle/Domain/Address.md)`|``|
`billingAddress`|`?[Address](../../AccountApiBundle/Domain/Address.md)`|``|
`sum`|`int`|``|
`currency`|`string`|``|
`payments`|`[Payment](Payment.md)[]`|`[]`|
`discountCodes`|`string[]`|`[]`|
`dangerousInnerCart`|`mixed`|``|Access original object from backend

## Methods

* [getPayedAmount()](#getPayedAmount)
* [hasUser()](#hasUser)
* [hasShippingAddress()](#hasShippingAddress)
* [hasBillingAddress()](#hasBillingAddress)
* [hasAddresses()](#hasAddresses)
* [hasCompletePayments()](#hasCompletePayments)
* [isComplete()](#isComplete)


### getPayedAmount()


```php
public function getPayedAmount(): int
```







### hasUser()


```php
public function hasUser(): bool
```







### hasShippingAddress()


```php
public function hasShippingAddress(): bool
```







### hasBillingAddress()


```php
public function hasBillingAddress(): bool
```







### hasAddresses()


```php
public function hasAddresses(): bool
```







### hasCompletePayments()


```php
public function hasCompletePayments(): bool
```







### isComplete()


```php
public function isComplete(): bool
```







