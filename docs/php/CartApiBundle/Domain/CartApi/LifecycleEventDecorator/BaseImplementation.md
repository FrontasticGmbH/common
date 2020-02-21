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
public function beforeGetForUser(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, string userId, string locale): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$userId`|`string`|``|
`$locale`|`string`|``|

### afterGetForUser()


```php
public function afterGetForUser(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): ?\Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### beforeGetAnonymous()


```php
public function beforeGetAnonymous(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, string anonymousId, string locale): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$anonymousId`|`string`|``|
`$locale`|`string`|``|

### afterGetAnonymous()


```php
public function afterGetAnonymous(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): ?\Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### beforeGetById()


```php
public function beforeGetById(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, string cartId, string locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cartId`|`string`|``|
`$locale`|`string`|`null`|

### afterGetById()


```php
public function afterGetById(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): ?\Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### beforeAddToCart()


```php
public function beforeAddToCart(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart, \Frontastic\Common\CartApiBundle\Domain\LineItem lineItem, string locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$lineItem`|`\Frontastic\Common\CartApiBundle\Domain\LineItem`|``|
`$locale`|`string`|`null`|

### afterAddToCart()


```php
public function afterAddToCart(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): ?\Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### beforeUpdateLineItem()


```php
public function beforeUpdateLineItem(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart, \Frontastic\Common\CartApiBundle\Domain\LineItem lineItem, int count, ?array custom = null, string locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$lineItem`|`\Frontastic\Common\CartApiBundle\Domain\LineItem`|``|
`$count`|`int`|``|
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

### afterUpdateLineItem()


```php
public function afterUpdateLineItem(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): ?\Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### beforeRemoveLineItem()


```php
public function beforeRemoveLineItem(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart, \Frontastic\Common\CartApiBundle\Domain\LineItem lineItem, string locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$lineItem`|`\Frontastic\Common\CartApiBundle\Domain\LineItem`|``|
`$locale`|`string`|`null`|

### afterRemoveLineItem()


```php
public function afterRemoveLineItem(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): ?\Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### beforeSetEmail()


```php
public function beforeSetEmail(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart, string email, string locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$email`|`string`|``|
`$locale`|`string`|`null`|

### afterSetEmail()


```php
public function afterSetEmail(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): ?\Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### beforeSetShippingMethod()


```php
public function beforeSetShippingMethod(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart, string shippingMethod, string locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$shippingMethod`|`string`|``|
`$locale`|`string`|`null`|

### afterSetShippingMethod()


```php
public function afterSetShippingMethod(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): ?\Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### beforeSetCustomField()


```php
public function beforeSetCustomField(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart, array fields, string locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$fields`|`array`|``|
`$locale`|`string`|`null`|

### afterSetCustomField()


```php
public function afterSetCustomField(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): ?\Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### beforeSetShippingAddress()


```php
public function beforeSetShippingAddress(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart, array address, string locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$address`|`array`|``|
`$locale`|`string`|`null`|

### afterSetShippingAddress()


```php
public function afterSetShippingAddress(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): ?\Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### beforeSetBillingAddress()


```php
public function beforeSetBillingAddress(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart, array address, string locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$address`|`array`|``|
`$locale`|`string`|`null`|

### afterSetBillingAddress()


```php
public function afterSetBillingAddress(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): ?\Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### beforeAddPayment()


```php
public function beforeAddPayment(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart, \Frontastic\Common\CartApiBundle\Domain\Payment payment, ?array custom = null, string locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$payment`|`\Frontastic\Common\CartApiBundle\Domain\Payment`|``|
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

### afterAddPayment()


```php
public function afterAddPayment(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): ?\Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### beforeRedeemDiscountCode()


```php
public function beforeRedeemDiscountCode(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart, string code, string locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$code`|`string`|``|
`$locale`|`string`|`null`|

### afterRedeemDiscountCode()


```php
public function afterRedeemDiscountCode(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): ?\Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### beforeOrder()


```php
public function beforeOrder(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### afterOrder()


```php
public function afterOrder(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Order order): ?\Frontastic\Common\CartApiBundle\Domain\Order
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$order`|`\Frontastic\Common\CartApiBundle\Domain\Order`|``|

### beforeGetOrder()


```php
public function beforeGetOrder(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, string orderId): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$orderId`|`string`|``|

### afterGetOrder()


```php
public function afterGetOrder(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Order orderId): ?\Frontastic\Common\CartApiBundle\Domain\Order
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$orderId`|`\Frontastic\Common\CartApiBundle\Domain\Order`|``|

### beforeGetOrders()


```php
public function beforeGetOrders(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, string accountId): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$accountId`|`string`|``|

### afterGetOrders()


```php
public function afterGetOrders(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, array orders): ?array
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$orders`|`array`|``|

### beforeStartTransaction()


```php
public function beforeStartTransaction(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### beforeCommit()


```php
public function beforeCommit(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, string locale = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$locale`|`string`|`null`|

### afterCommit()


```php
public function afterCommit(\Frontastic\Common\CartApiBundle\Domain\CartApi cartApi, \Frontastic\Common\CartApiBundle\Domain\Cart cart): ?\Frontastic\Common\CartApiBundle\Domain\Cart
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|`\Frontastic\Common\CartApiBundle\Domain\CartApi`|``|
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

