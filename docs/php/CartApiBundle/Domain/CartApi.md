# `interface`  CartApi

Fully Qualified: [`\Frontastic\Common\CartApiBundle\Domain\CartApi`](../../../../src/php/CartApiBundle/Domain/CartApi.php)

## Methods

* [getForUser()](#getforuser)
* [getAnonymous()](#getanonymous)
* [getById()](#getbyid)
* [setCustomLineItemType()](#setcustomlineitemtype)
* [getCustomLineItemType()](#getcustomlineitemtype)
* [setTaxCategory()](#settaxcategory)
* [getTaxCategory()](#gettaxcategory)
* [addToCart()](#addtocart)
* [updateLineItem()](#updatelineitem)
* [removeLineItem()](#removelineitem)
* [setEmail()](#setemail)
* [setShippingMethod()](#setshippingmethod)
* [setCustomField()](#setcustomfield)
* [setShippingAddress()](#setshippingaddress)
* [setBillingAddress()](#setbillingaddress)
* [addPayment()](#addpayment)
* [redeemDiscountCode()](#redeemdiscountcode)
* [removeDiscountCode()](#removediscountcode)
* [order()](#order)
* [getOrder()](#getorder)
* [getOrders()](#getorders)
* [startTransaction()](#starttransaction)
* [commit()](#commit)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### getForUser()

```php
public function getForUser(
    string $userId,
    string $locale
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$userId`|`string`||
`$locale`|`string`||

Return Value: [`Cart`](Cart.md)

### getAnonymous()

```php
public function getAnonymous(
    string $anonymousId,
    string $locale
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$anonymousId`|`string`||
`$locale`|`string`||

Return Value: [`Cart`](Cart.md)

### getById()

```php
public function getById(
    string $cartId,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartId`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Cart`](Cart.md)

### setCustomLineItemType()

```php
public function setCustomLineItemType(
    array $lineItemType
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$lineItemType`|`array`||

Return Value: `void`

### getCustomLineItemType()

```php
public function getCustomLineItemType(): array
```

Return Value: `array`

### setTaxCategory()

```php
public function setTaxCategory(
    array $taxCategory
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$taxCategory`|`array`||

Return Value: `void`

### getTaxCategory()

```php
public function getTaxCategory(): array
```

Return Value: `array`

### addToCart()

```php
public function addToCart(
    Cart $cart,
    LineItem $lineItem,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||
`$lineItem`|[`LineItem`](LineItem.md)||
`$locale`|`string`|`null`|

Return Value: [`Cart`](Cart.md)

### updateLineItem()

```php
public function updateLineItem(
    Cart $cart,
    LineItem $lineItem,
    int $count,
    ?array $custom = null,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||
`$lineItem`|[`LineItem`](LineItem.md)||
`$count`|`int`||
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

Return Value: [`Cart`](Cart.md)

### removeLineItem()

```php
public function removeLineItem(
    Cart $cart,
    LineItem $lineItem,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||
`$lineItem`|[`LineItem`](LineItem.md)||
`$locale`|`string`|`null`|

Return Value: [`Cart`](Cart.md)

### setEmail()

```php
public function setEmail(
    Cart $cart,
    string $email,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||
`$email`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Cart`](Cart.md)

### setShippingMethod()

```php
public function setShippingMethod(
    Cart $cart,
    string $shippingMethod,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||
`$shippingMethod`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Cart`](Cart.md)

### setCustomField()

```php
public function setCustomField(
    Cart $cart,
    array $fields,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||
`$fields`|`array`||
`$locale`|`string`|`null`|

Return Value: [`Cart`](Cart.md)

### setShippingAddress()

```php
public function setShippingAddress(
    Cart $cart,
    array $address,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||
`$address`|`array`||
`$locale`|`string`|`null`|

Return Value: [`Cart`](Cart.md)

### setBillingAddress()

```php
public function setBillingAddress(
    Cart $cart,
    array $address,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||
`$address`|`array`||
`$locale`|`string`|`null`|

Return Value: [`Cart`](Cart.md)

### addPayment()

```php
public function addPayment(
    Cart $cart,
    Payment $payment,
    ?array $custom = null,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||
`$payment`|[`Payment`](Payment.md)||
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

Return Value: [`Cart`](Cart.md)

### redeemDiscountCode()

```php
public function redeemDiscountCode(
    Cart $cart,
    string $code,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||
`$code`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Cart`](Cart.md)

### removeDiscountCode()

```php
public function removeDiscountCode(
    Cart $cart,
    string $discountId,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||
`$discountId`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Cart`](Cart.md)

### order()

```php
public function order(
    Cart $cart
): Order
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||

Return Value: [`Order`](Order.md)

### getOrder()

```php
public function getOrder(
    string $orderId
): Order
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$orderId`|`string`||

Return Value: [`Order`](Order.md)

### getOrders()

```php
public function getOrders(
    string $accountId
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`||

Return Value: `array`

### startTransaction()

```php
public function startTransaction(
    Cart $cart
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||

Return Value: `void`

### commit()

```php
public function commit(
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$locale`|`string`|`null`|

Return Value: [`Cart`](Cart.md)

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

