# `abstract`  BaseImplementation

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\CartApi\LifecycleEventDecorator\BaseImplementation`](../../../../../../src/php/CartApiBundle/Domain/CartApi/LifecycleEventDecorator/BaseImplementation.php)

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

* [beforeGetForUser()](#beforegetforuser)
* [afterGetForUser()](#aftergetforuser)
* [beforeGetAnonymous()](#beforegetanonymous)
* [afterGetAnonymous()](#aftergetanonymous)
* [beforeGetById()](#beforegetbyid)
* [afterGetById()](#aftergetbyid)
* [beforeAddToCart()](#beforeaddtocart)
* [afterAddToCart()](#afteraddtocart)
* [beforeUpdateLineItem()](#beforeupdatelineitem)
* [afterUpdateLineItem()](#afterupdatelineitem)
* [beforeRemoveLineItem()](#beforeremovelineitem)
* [afterRemoveLineItem()](#afterremovelineitem)
* [beforeSetEmail()](#beforesetemail)
* [afterSetEmail()](#aftersetemail)
* [beforeSetShippingMethod()](#beforesetshippingmethod)
* [afterSetShippingMethod()](#aftersetshippingmethod)
* [beforeSetCustomField()](#beforesetcustomfield)
* [afterSetCustomField()](#aftersetcustomfield)
* [beforeSetShippingAddress()](#beforesetshippingaddress)
* [afterSetShippingAddress()](#aftersetshippingaddress)
* [beforeSetBillingAddress()](#beforesetbillingaddress)
* [afterSetBillingAddress()](#aftersetbillingaddress)
* [beforeAddPayment()](#beforeaddpayment)
* [afterAddPayment()](#afteraddpayment)
* [beforeRedeemDiscountCode()](#beforeredeemdiscountcode)
* [afterRedeemDiscountCode()](#afterredeemdiscountcode)
* [beforeOrder()](#beforeorder)
* [afterOrder()](#afterorder)
* [beforeGetOrder()](#beforegetorder)
* [afterGetOrder()](#aftergetorder)
* [beforeGetOrders()](#beforegetorders)
* [afterGetOrders()](#aftergetorders)
* [beforeStartTransaction()](#beforestarttransaction)
* [beforeCommit()](#beforecommit)
* [afterCommit()](#aftercommit)

### beforeGetForUser()

```php
public function beforeGetForUser(
    CartApi $cartApi,
    string $userId,
    string $locale
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$userId`|`string`||
`$locale`|`string`||

Return Value: `void`

### afterGetForUser()

```php
public function afterGetForUser(
    CartApi $cartApi,
    Cart $cart
): ?Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: ?[`Cart`](../../Cart.md)

### beforeGetAnonymous()

```php
public function beforeGetAnonymous(
    CartApi $cartApi,
    string $anonymousId,
    string $locale
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$anonymousId`|`string`||
`$locale`|`string`||

Return Value: `void`

### afterGetAnonymous()

```php
public function afterGetAnonymous(
    CartApi $cartApi,
    Cart $cart
): ?Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: ?[`Cart`](../../Cart.md)

### beforeGetById()

```php
public function beforeGetById(
    CartApi $cartApi,
    string $cartId,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cartId`|`string`||
`$locale`|`string`|`null`|

Return Value: `void`

### afterGetById()

```php
public function afterGetById(
    CartApi $cartApi,
    Cart $cart
): ?Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: ?[`Cart`](../../Cart.md)

### beforeAddToCart()

```php
public function beforeAddToCart(
    CartApi $cartApi,
    Cart $cart,
    LineItem $lineItem,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||
`$lineItem`|[`LineItem`](../../LineItem.md)||
`$locale`|`string`|`null`|

Return Value: `void`

### afterAddToCart()

```php
public function afterAddToCart(
    CartApi $cartApi,
    Cart $cart
): ?Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: ?[`Cart`](../../Cart.md)

### beforeUpdateLineItem()

```php
public function beforeUpdateLineItem(
    CartApi $cartApi,
    Cart $cart,
    LineItem $lineItem,
    int $count,
    ?array $custom = null,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||
`$lineItem`|[`LineItem`](../../LineItem.md)||
`$count`|`int`||
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

Return Value: `void`

### afterUpdateLineItem()

```php
public function afterUpdateLineItem(
    CartApi $cartApi,
    Cart $cart
): ?Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: ?[`Cart`](../../Cart.md)

### beforeRemoveLineItem()

```php
public function beforeRemoveLineItem(
    CartApi $cartApi,
    Cart $cart,
    LineItem $lineItem,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||
`$lineItem`|[`LineItem`](../../LineItem.md)||
`$locale`|`string`|`null`|

Return Value: `void`

### afterRemoveLineItem()

```php
public function afterRemoveLineItem(
    CartApi $cartApi,
    Cart $cart
): ?Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: ?[`Cart`](../../Cart.md)

### beforeSetEmail()

```php
public function beforeSetEmail(
    CartApi $cartApi,
    Cart $cart,
    string $email,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||
`$email`|`string`||
`$locale`|`string`|`null`|

Return Value: `void`

### afterSetEmail()

```php
public function afterSetEmail(
    CartApi $cartApi,
    Cart $cart
): ?Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: ?[`Cart`](../../Cart.md)

### beforeSetShippingMethod()

```php
public function beforeSetShippingMethod(
    CartApi $cartApi,
    Cart $cart,
    string $shippingMethod,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||
`$shippingMethod`|`string`||
`$locale`|`string`|`null`|

Return Value: `void`

### afterSetShippingMethod()

```php
public function afterSetShippingMethod(
    CartApi $cartApi,
    Cart $cart
): ?Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: ?[`Cart`](../../Cart.md)

### beforeSetCustomField()

```php
public function beforeSetCustomField(
    CartApi $cartApi,
    Cart $cart,
    array $fields,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||
`$fields`|`array`||
`$locale`|`string`|`null`|

Return Value: `void`

### afterSetCustomField()

```php
public function afterSetCustomField(
    CartApi $cartApi,
    Cart $cart
): ?Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: ?[`Cart`](../../Cart.md)

### beforeSetShippingAddress()

```php
public function beforeSetShippingAddress(
    CartApi $cartApi,
    Cart $cart,
    array $address,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||
`$address`|`array`||
`$locale`|`string`|`null`|

Return Value: `void`

### afterSetShippingAddress()

```php
public function afterSetShippingAddress(
    CartApi $cartApi,
    Cart $cart
): ?Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: ?[`Cart`](../../Cart.md)

### beforeSetBillingAddress()

```php
public function beforeSetBillingAddress(
    CartApi $cartApi,
    Cart $cart,
    array $address,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||
`$address`|`array`||
`$locale`|`string`|`null`|

Return Value: `void`

### afterSetBillingAddress()

```php
public function afterSetBillingAddress(
    CartApi $cartApi,
    Cart $cart
): ?Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: ?[`Cart`](../../Cart.md)

### beforeAddPayment()

```php
public function beforeAddPayment(
    CartApi $cartApi,
    Cart $cart,
    Payment $payment,
    ?array $custom = null,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||
`$payment`|[`Payment`](../../Payment.md)||
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

Return Value: `void`

### afterAddPayment()

```php
public function afterAddPayment(
    CartApi $cartApi,
    Cart $cart
): ?Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: ?[`Cart`](../../Cart.md)

### beforeRedeemDiscountCode()

```php
public function beforeRedeemDiscountCode(
    CartApi $cartApi,
    Cart $cart,
    string $code,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||
`$code`|`string`||
`$locale`|`string`|`null`|

Return Value: `void`

### afterRedeemDiscountCode()

```php
public function afterRedeemDiscountCode(
    CartApi $cartApi,
    Cart $cart
): ?Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: ?[`Cart`](../../Cart.md)

### beforeOrder()

```php
public function beforeOrder(
    CartApi $cartApi,
    Cart $cart
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: `void`

### afterOrder()

```php
public function afterOrder(
    CartApi $cartApi,
    Order $order
): ?Order
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$order`|[`Order`](../../Order.md)||

Return Value: ?[`Order`](../../Order.md)

### beforeGetOrder()

```php
public function beforeGetOrder(
    CartApi $cartApi,
    string $orderId
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$orderId`|`string`||

Return Value: `void`

### afterGetOrder()

```php
public function afterGetOrder(
    CartApi $cartApi,
    Order $orderId
): ?Order
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$orderId`|[`Order`](../../Order.md)||

Return Value: ?[`Order`](../../Order.md)

### beforeGetOrders()

```php
public function beforeGetOrders(
    CartApi $cartApi,
    string $accountId
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$accountId`|`string`||

Return Value: `void`

### afterGetOrders()

```php
public function afterGetOrders(
    CartApi $cartApi,
    array $orders
): ?array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$orders`|`array`||

Return Value: `?array`

### beforeStartTransaction()

```php
public function beforeStartTransaction(
    CartApi $cartApi,
    Cart $cart
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: `void`

### beforeCommit()

```php
public function beforeCommit(
    CartApi $cartApi,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$locale`|`string`|`null`|

Return Value: `void`

### afterCommit()

```php
public function afterCommit(
    CartApi $cartApi,
    Cart $cart
): ?Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartApi`|[`CartApi`](../../CartApi.md)||
`$cart`|[`Cart`](../../Cart.md)||

Return Value: ?[`Cart`](../../Cart.md)

