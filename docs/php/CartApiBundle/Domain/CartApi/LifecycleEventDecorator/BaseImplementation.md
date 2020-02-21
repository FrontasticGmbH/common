# `abstract`  BaseImplementation

Fully Qualified: [`\Frontastic\Common\CartApiBundle\Domain\CartApi\LifecycleEventDecorator\BaseImplementation`](../../../../../../src/php/CartApiBundle/Domain/CartApi/LifecycleEventDecorator/BaseImplementation.php)


The before* Methods will be obviously called *before* the original method is
executed and will get all the parameters handed over, which the original
method will get called with. Overwriting this method can be useful if you want
to manipulate the handed over parameters by simply manipulating it. These
methods doesn't return anything.

The after* Methods will be oviously called *after* the orignal method is
executed and will get the unwrapped result from the original method handed
over. So if the original methods returns a Promise, the resolved value will be
handed over to this function here. Overwriting this method could be useful if
you want to manipulate the result. These methods need to return null if
nothing should be manipulating, thus will lead to the original result being
returned or they need to return the same data-type as the original method
returns, otherwise you will get Type-Errors at some point.

In order to make this class available to the Lifecycle-Decorator, you will
need to tag your service based on this class with
"cartApi.lifecycleEventListener": e.g. by adding the tag inside the
`services.xml` ``` <tag name="cartApi.lifecycleEventListener" /> ```

## Methods

* [beforeGetForUser()](#beforeGetForUser)
* [afterGetForUser()](#afterGetForUser)
* [beforeGetAnonymous()](#beforeGetAnonymous)
* [afterGetAnonymous()](#afterGetAnonymous)
* [beforeGetById()](#beforeGetById)
* [afterGetById()](#afterGetById)
* [beforeAddToCart()](#beforeAddToCart)
* [afterAddToCart()](#afterAddToCart)
* [beforeUpdateLineItem()](#beforeUpdateLineItem)
* [afterUpdateLineItem()](#afterUpdateLineItem)
* [beforeRemoveLineItem()](#beforeRemoveLineItem)
* [afterRemoveLineItem()](#afterRemoveLineItem)
* [beforeSetEmail()](#beforeSetEmail)
* [afterSetEmail()](#afterSetEmail)
* [beforeSetShippingMethod()](#beforeSetShippingMethod)
* [afterSetShippingMethod()](#afterSetShippingMethod)
* [beforeSetCustomField()](#beforeSetCustomField)
* [afterSetCustomField()](#afterSetCustomField)
* [beforeSetShippingAddress()](#beforeSetShippingAddress)
* [afterSetShippingAddress()](#afterSetShippingAddress)
* [beforeSetBillingAddress()](#beforeSetBillingAddress)
* [afterSetBillingAddress()](#afterSetBillingAddress)
* [beforeAddPayment()](#beforeAddPayment)
* [afterAddPayment()](#afterAddPayment)
* [beforeRedeemDiscountCode()](#beforeRedeemDiscountCode)
* [afterRedeemDiscountCode()](#afterRedeemDiscountCode)
* [beforeOrder()](#beforeOrder)
* [afterOrder()](#afterOrder)
* [beforeGetOrder()](#beforeGetOrder)
* [afterGetOrder()](#afterGetOrder)
* [beforeGetOrders()](#beforeGetOrders)
* [afterGetOrders()](#afterGetOrders)
* [beforeStartTransaction()](#beforeStartTransaction)
* [beforeCommit()](#beforeCommit)
* [afterCommit()](#afterCommit)


### beforeGetForUser()


```php
public function beforeGetForUser([CartApi](../../CartApi.md) $cartApi, string $userId, string $locale): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$userId`|`string`|``|
`$locale`|`string`|``|

### afterGetForUser()


```php
public function afterGetForUser([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): ?[Cart](../../Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

### beforeGetAnonymous()


```php
public function beforeGetAnonymous([CartApi](../../CartApi.md) $cartApi, string $anonymousId, string $locale): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$anonymousId`|`string`|``|
`$locale`|`string`|``|

### afterGetAnonymous()


```php
public function afterGetAnonymous([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): ?[Cart](../../Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

### beforeGetById()


```php
public function beforeGetById([CartApi](../../CartApi.md) $cartApi, string $cartId, string $locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cartId`|`string`|``|
`$locale`|`string`|`null`|

### afterGetById()


```php
public function afterGetById([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): ?[Cart](../../Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

### beforeAddToCart()


```php
public function beforeAddToCart([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart, [LineItem](../../LineItem.md) $lineItem, string $locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|
`$lineItem`|`[LineItem](../../LineItem.md)`|``|
`$locale`|`string`|`null`|

### afterAddToCart()


```php
public function afterAddToCart([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): ?[Cart](../../Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

### beforeUpdateLineItem()


```php
public function beforeUpdateLineItem([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart, [LineItem](../../LineItem.md) $lineItem, int $count, ?array $custom = null, string $locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|
`$lineItem`|`[LineItem](../../LineItem.md)`|``|
`$count`|`int`|``|
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

### afterUpdateLineItem()


```php
public function afterUpdateLineItem([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): ?[Cart](../../Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

### beforeRemoveLineItem()


```php
public function beforeRemoveLineItem([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart, [LineItem](../../LineItem.md) $lineItem, string $locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|
`$lineItem`|`[LineItem](../../LineItem.md)`|``|
`$locale`|`string`|`null`|

### afterRemoveLineItem()


```php
public function afterRemoveLineItem([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): ?[Cart](../../Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

### beforeSetEmail()


```php
public function beforeSetEmail([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart, string $email, string $locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|
`$email`|`string`|``|
`$locale`|`string`|`null`|

### afterSetEmail()


```php
public function afterSetEmail([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): ?[Cart](../../Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

### beforeSetShippingMethod()


```php
public function beforeSetShippingMethod([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart, string $shippingMethod, string $locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|
`$shippingMethod`|`string`|``|
`$locale`|`string`|`null`|

### afterSetShippingMethod()


```php
public function afterSetShippingMethod([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): ?[Cart](../../Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

### beforeSetCustomField()


```php
public function beforeSetCustomField([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart, array $fields, string $locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|
`$fields`|`array`|``|
`$locale`|`string`|`null`|

### afterSetCustomField()


```php
public function afterSetCustomField([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): ?[Cart](../../Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

### beforeSetShippingAddress()


```php
public function beforeSetShippingAddress([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart, array $address, string $locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|
`$address`|`array`|``|
`$locale`|`string`|`null`|

### afterSetShippingAddress()


```php
public function afterSetShippingAddress([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): ?[Cart](../../Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

### beforeSetBillingAddress()


```php
public function beforeSetBillingAddress([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart, array $address, string $locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|
`$address`|`array`|``|
`$locale`|`string`|`null`|

### afterSetBillingAddress()


```php
public function afterSetBillingAddress([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): ?[Cart](../../Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

### beforeAddPayment()


```php
public function beforeAddPayment([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart, [Payment](../../Payment.md) $payment, ?array $custom = null, string $locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|
`$payment`|`[Payment](../../Payment.md)`|``|
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

### afterAddPayment()


```php
public function afterAddPayment([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): ?[Cart](../../Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

### beforeRedeemDiscountCode()


```php
public function beforeRedeemDiscountCode([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart, string $code, string $locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|
`$code`|`string`|``|
`$locale`|`string`|`null`|

### afterRedeemDiscountCode()


```php
public function afterRedeemDiscountCode([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): ?[Cart](../../Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

### beforeOrder()


```php
public function beforeOrder([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

### afterOrder()


```php
public function afterOrder([CartApi](../../CartApi.md) $cartApi, [Order](../../Order.md) $order): ?[Order](../../Order.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$order`|`[Order](../../Order.md)`|``|

### beforeGetOrder()


```php
public function beforeGetOrder([CartApi](../../CartApi.md) $cartApi, string $orderId): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$orderId`|`string`|``|

### afterGetOrder()


```php
public function afterGetOrder([CartApi](../../CartApi.md) $cartApi, [Order](../../Order.md) $orderId): ?[Order](../../Order.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$orderId`|`[Order](../../Order.md)`|``|

### beforeGetOrders()


```php
public function beforeGetOrders([CartApi](../../CartApi.md) $cartApi, string $accountId): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$accountId`|`string`|``|

### afterGetOrders()


```php
public function afterGetOrders([CartApi](../../CartApi.md) $cartApi, array $orders): ?array
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$orders`|`array`|``|

### beforeStartTransaction()


```php
public function beforeStartTransaction([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

### beforeCommit()


```php
public function beforeCommit([CartApi](../../CartApi.md) $cartApi, string $locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$locale`|`string`|`null`|

### afterCommit()


```php
public function afterCommit([CartApi](../../CartApi.md) $cartApi, [Cart](../../Cart.md) $cart): ?[Cart](../../Cart.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`[CartApi](../../CartApi.md)`|``|
`$cart`|`[Cart](../../Cart.md)`|``|

