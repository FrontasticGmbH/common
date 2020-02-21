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
public function getForUser(string userId, string locale): \Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$userId`|`string`|``|
`$locale`|`string`|``|

### getAnonymous()


```php
public function getAnonymous(string anonymousId, string locale): \Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$anonymousId`|`string`|``|
`$locale`|`string`|``|

### getById()


```php
public function getById(string cartId, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartId`|`string`|``|
`$locale`|`string`|`null`|

### setCustomLineItemType()


```php
public function setCustomLineItemType(array lineItemType): void
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
public function setTaxCategory(array taxCategory): void
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
public function addToCart(\Frontastic\Common\CartApiBundle\Domain\Cart cart, \Frontastic\Common\CartApiBundle\Domain\LineItem lineItem, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$lineItem`|`\Frontastic\Common\CartApiBundle\Domain\LineItem`|``|
`$locale`|`string`|`null`|

### updateLineItem()


```php
public function updateLineItem(\Frontastic\Common\CartApiBundle\Domain\Cart cart, \Frontastic\Common\CartApiBundle\Domain\LineItem lineItem, int count, ?array custom = null, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$lineItem`|`\Frontastic\Common\CartApiBundle\Domain\LineItem`|``|
`$count`|`int`|``|
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

### removeLineItem()


```php
public function removeLineItem(\Frontastic\Common\CartApiBundle\Domain\Cart cart, \Frontastic\Common\CartApiBundle\Domain\LineItem lineItem, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$lineItem`|`\Frontastic\Common\CartApiBundle\Domain\LineItem`|``|
`$locale`|`string`|`null`|

### setEmail()


```php
public function setEmail(\Frontastic\Common\CartApiBundle\Domain\Cart cart, string email, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$email`|`string`|``|
`$locale`|`string`|`null`|

### setShippingMethod()


```php
public function setShippingMethod(\Frontastic\Common\CartApiBundle\Domain\Cart cart, string shippingMethod, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$shippingMethod`|`string`|``|
`$locale`|`string`|`null`|

### setCustomField()


```php
public function setCustomField(\Frontastic\Common\CartApiBundle\Domain\Cart cart, array fields, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$fields`|`array`|``|
`$locale`|`string`|`null`|

### setShippingAddress()


```php
public function setShippingAddress(\Frontastic\Common\CartApiBundle\Domain\Cart cart, array address, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$address`|`array`|``|
`$locale`|`string`|`null`|

### setBillingAddress()


```php
public function setBillingAddress(\Frontastic\Common\CartApiBundle\Domain\Cart cart, array address, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$address`|`array`|``|
`$locale`|`string`|`null`|

### addPayment()


```php
public function addPayment(\Frontastic\Common\CartApiBundle\Domain\Cart cart, \Frontastic\Common\CartApiBundle\Domain\Payment payment, ?array custom = null, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$payment`|`\Frontastic\Common\CartApiBundle\Domain\Payment`|``|
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

### redeemDiscountCode()


```php
public function redeemDiscountCode(\Frontastic\Common\CartApiBundle\Domain\Cart cart, string code, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$code`|`string`|``|
`$locale`|`string`|`null`|

### removeDiscountCode()


```php
public function removeDiscountCode(\Frontastic\Common\CartApiBundle\Domain\Cart cart, string discountId, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$discountId`|`string`|``|
`$locale`|`string`|`null`|

### order()


```php
public function order(\Frontastic\Common\CartApiBundle\Domain\Cart cart): \Frontastic\Common\CartApiBundle\Domain\Order
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### getOrder()


```php
public function getOrder(string orderId): \Frontastic\Common\CartApiBundle\Domain\Order
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$orderId`|`string`|``|

### getOrders()


```php
public function getOrders(string accountId): array
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|

### startTransaction()


```php
public function startTransaction(\Frontastic\Common\CartApiBundle\Domain\Cart cart): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### commit()


```php
public function commit(string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart
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


