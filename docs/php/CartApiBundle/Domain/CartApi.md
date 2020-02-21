# `interface`  CartApi

Fully Qualified: [`\Frontastic\Common\CartApiBundle\Domain\CartApi`](../../../../src/php/CartApiBundle/Domain/CartApi.php)




## Methods

* [getForUser()](#getForUser)
* [getAnonymous()](#getAnonymous)
* [getById()](#getById)
* [setCustomLineItemType()](#setCustomLineItemType)
* [getCustomLineItemType()](#getCustomLineItemType)
* [setTaxCategory()](#setTaxCategory)
* [getTaxCategory()](#getTaxCategory)
* [addToCart()](#addToCart)
* [updateLineItem()](#updateLineItem)
* [removeLineItem()](#removeLineItem)
* [setEmail()](#setEmail)
* [setShippingMethod()](#setShippingMethod)
* [setCustomField()](#setCustomField)
* [setShippingAddress()](#setShippingAddress)
* [setBillingAddress()](#setBillingAddress)
* [addPayment()](#addPayment)
* [redeemDiscountCode()](#redeemDiscountCode)
* [removeDiscountCode()](#removeDiscountCode)
* [order()](#order)
* [getOrder()](#getOrder)
* [getOrders()](#getOrders)
* [startTransaction()](#startTransaction)
* [commit()](#commit)
* [getDangerousInnerClient()](#getDangerousInnerClient)


### getForUser()


```php
public function getForUser(string $userId, string $locale): [Cart](Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$userId`|`string`|``|
`$locale`|`string`|``|

### getAnonymous()


```php
public function getAnonymous(string $anonymousId, string $locale): [Cart](Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$anonymousId`|`string`|``|
`$locale`|`string`|``|

### getById()


```php
public function getById(string $cartId, string $locale = null): [Cart](Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartId`|`string`|``|
`$locale`|`string`|`null`|

### setCustomLineItemType()


```php
public function setCustomLineItemType(array $lineItemType): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$lineItemType`|`array`|``|

### getCustomLineItemType()


```php
public function getCustomLineItemType(): array
```







### setTaxCategory()


```php
public function setTaxCategory(array $taxCategory): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$taxCategory`|`array`|``|

### getTaxCategory()


```php
public function getTaxCategory(): array
```







### addToCart()


```php
public function addToCart([Cart](Cart.md) $cart, [LineItem](LineItem.md) $lineItem, string $locale = null): [Cart](Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`[Cart](Cart.md)`|``|
`$lineItem`|`[LineItem](LineItem.md)`|``|
`$locale`|`string`|`null`|

### updateLineItem()


```php
public function updateLineItem([Cart](Cart.md) $cart, [LineItem](LineItem.md) $lineItem, int $count, ?array $custom = null, string $locale = null): [Cart](Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`[Cart](Cart.md)`|``|
`$lineItem`|`[LineItem](LineItem.md)`|``|
`$count`|`int`|``|
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

### removeLineItem()


```php
public function removeLineItem([Cart](Cart.md) $cart, [LineItem](LineItem.md) $lineItem, string $locale = null): [Cart](Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`[Cart](Cart.md)`|``|
`$lineItem`|`[LineItem](LineItem.md)`|``|
`$locale`|`string`|`null`|

### setEmail()


```php
public function setEmail([Cart](Cart.md) $cart, string $email, string $locale = null): [Cart](Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`[Cart](Cart.md)`|``|
`$email`|`string`|``|
`$locale`|`string`|`null`|

### setShippingMethod()


```php
public function setShippingMethod([Cart](Cart.md) $cart, string $shippingMethod, string $locale = null): [Cart](Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`[Cart](Cart.md)`|``|
`$shippingMethod`|`string`|``|
`$locale`|`string`|`null`|

### setCustomField()


```php
public function setCustomField([Cart](Cart.md) $cart, array $fields, string $locale = null): [Cart](Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`[Cart](Cart.md)`|``|
`$fields`|`array`|``|
`$locale`|`string`|`null`|

### setShippingAddress()


```php
public function setShippingAddress([Cart](Cart.md) $cart, array $address, string $locale = null): [Cart](Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`[Cart](Cart.md)`|``|
`$address`|`array`|``|
`$locale`|`string`|`null`|

### setBillingAddress()


```php
public function setBillingAddress([Cart](Cart.md) $cart, array $address, string $locale = null): [Cart](Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`[Cart](Cart.md)`|``|
`$address`|`array`|``|
`$locale`|`string`|`null`|

### addPayment()


```php
public function addPayment([Cart](Cart.md) $cart, [Payment](Payment.md) $payment, ?array $custom = null, string $locale = null): [Cart](Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`[Cart](Cart.md)`|``|
`$payment`|`[Payment](Payment.md)`|``|
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

### redeemDiscountCode()


```php
public function redeemDiscountCode([Cart](Cart.md) $cart, string $code, string $locale = null): [Cart](Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`[Cart](Cart.md)`|``|
`$code`|`string`|``|
`$locale`|`string`|`null`|

### removeDiscountCode()


```php
public function removeDiscountCode([Cart](Cart.md) $cart, string $discountId, string $locale = null): [Cart](Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`[Cart](Cart.md)`|``|
`$discountId`|`string`|``|
`$locale`|`string`|`null`|

### order()


```php
public function order([Cart](Cart.md) $cart): [Order](Order.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`[Cart](Cart.md)`|``|

### getOrder()


```php
public function getOrder(string $orderId): [Order](Order.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$orderId`|`string`|``|

### getOrders()


```php
public function getOrders(string $accountId): array
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|

### startTransaction()


```php
public function startTransaction([Cart](Cart.md) $cart): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`[Cart](Cart.md)`|``|

### commit()


```php
public function commit(string $locale = null): [Cart](Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$locale`|`string`|`null`|

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


