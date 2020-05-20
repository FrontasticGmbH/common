#  Commercetools

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools`](../../../../../src/php/CartApiBundle/Domain/CartApi/Commercetools.php)

**Implements**: [`CartApi`](../CartApi.md)

## Methods

* [__construct()](#__construct)
* [getForUser()](#getforuser)
* [getAnonymous()](#getanonymous)
* [getById()](#getbyid)
* [addToCart()](#addtocart)
* [updateLineItem()](#updatelineitem)
* [removeLineItem()](#removelineitem)
* [setEmail()](#setemail)
* [setShippingMethod()](#setshippingmethod)
* [setCustomField()](#setcustomfield)
* [setCustomType()](#setcustomtype)
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
* [setCustomLineItemType()](#setcustomlineitemtype)
* [getCustomLineItemType()](#getcustomlineitemtype)
* [setTaxCategory()](#settaxcategory)
* [getTaxCategory()](#gettaxcategory)
* [updatePaymentStatus()](#updatepaymentstatus)
* [getPayment()](#getpayment)
* [updatePaymentInterfaceId()](#updatepaymentinterfaceid)

### __construct()

```php
public function __construct(
    Commercetools\Client $client,
    Commercetools\Mapper $cartMapper,
    Commercetools\Locale\CommercetoolsLocaleCreator $localeCreator,
    OrderIdGenerator $orderIdGenerator
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`Commercetools`](../../../ProductApiBundle/Domain/ProductApi/Commercetools.md)\Client||
`$cartMapper`|[`Commercetools`](Commercetools.md)\Mapper||
`$localeCreator`|[`Commercetools`](../../../ProductApiBundle/Domain/ProductApi/Commercetools.md)\Locale\CommercetoolsLocaleCreator||
`$orderIdGenerator`|[`OrderIdGenerator`](../OrderIdGenerator.md)||

Return Value: `mixed`

### getForUser()

```php
public function getForUser(
    Account $account,
    string $localeString
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||
`$localeString`|`string`||

Return Value: [`Cart`](../Cart.md)

### getAnonymous()

```php
public function getAnonymous(
    string $anonymousId,
    string $localeString
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$anonymousId`|`string`||
`$localeString`|`string`||

Return Value: [`Cart`](../Cart.md)

### getById()

```php
public function getById(
    string $cartId,
    string $localeString = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartId`|`string`||
`$localeString`|`string`|`null`|

Return Value: [`Cart`](../Cart.md)

### addToCart()

```php
public function addToCart(
    Cart $cart,
    LineItem $lineItem,
    string $localeString = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||
`$lineItem`|[`LineItem`](../LineItem.md)||
`$localeString`|`string`|`null`|

Return Value: [`Cart`](../Cart.md)

### updateLineItem()

```php
public function updateLineItem(
    Cart $cart,
    LineItem $lineItem,
    int $count,
    ?array $custom = null,
    string $localeString = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||
`$lineItem`|[`LineItem`](../LineItem.md)||
`$count`|`int`||
`$custom`|`?array`|`null`|
`$localeString`|`string`|`null`|

Return Value: [`Cart`](../Cart.md)

### removeLineItem()

```php
public function removeLineItem(
    Cart $cart,
    LineItem $lineItem,
    string $localeString = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||
`$lineItem`|[`LineItem`](../LineItem.md)||
`$localeString`|`string`|`null`|

Return Value: [`Cart`](../Cart.md)

### setEmail()

```php
public function setEmail(
    Cart $cart,
    string $email,
    string $localeString = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||
`$email`|`string`||
`$localeString`|`string`|`null`|

Return Value: [`Cart`](../Cart.md)

### setShippingMethod()

```php
public function setShippingMethod(
    Cart $cart,
    string $shippingMethod,
    string $localeString = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||
`$shippingMethod`|`string`||
`$localeString`|`string`|`null`|

Return Value: [`Cart`](../Cart.md)

### setCustomField()

```php
public function setCustomField(
    Cart $cart,
    array $fields,
    string $localeString = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||
`$fields`|`array`||
`$localeString`|`string`|`null`|

Return Value: [`Cart`](../Cart.md)

### setCustomType()

```php
public function setCustomType(
    Cart $cart,
    string $key,
    string $localeString = null
): Cart
```

*Intentionally not part of the CartAPI interface.*

Only for use in scenarios where CommerceTools is set as the backend API.

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||
`$key`|`string`||
`$localeString`|`string`|`null`|

Return Value: [`Cart`](../Cart.md)

### setShippingAddress()

```php
public function setShippingAddress(
    Cart $cart,
    Address $address,
    string $localeString = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||
`$address`|[`Address`](../../../AccountApiBundle/Domain/Address.md)||
`$localeString`|`string`|`null`|

Return Value: [`Cart`](../Cart.md)

### setBillingAddress()

```php
public function setBillingAddress(
    Cart $cart,
    Address $address,
    string $localeString = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||
`$address`|[`Address`](../../../AccountApiBundle/Domain/Address.md)||
`$localeString`|`string`|`null`|

Return Value: [`Cart`](../Cart.md)

### addPayment()

```php
public function addPayment(
    Cart $cart,
    Payment $payment,
    ?array $custom = null,
    string $localeString = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||
`$payment`|[`Payment`](../Payment.md)||
`$custom`|`?array`|`null`|
`$localeString`|`string`|`null`|

Return Value: [`Cart`](../Cart.md)

### redeemDiscountCode()

```php
public function redeemDiscountCode(
    Cart $cart,
    string $code,
    string $localeString = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||
`$code`|`string`||
`$localeString`|`string`|`null`|

Return Value: [`Cart`](../Cart.md)

### removeDiscountCode()

```php
public function removeDiscountCode(
    Cart $cart,
    LineItem $discountLineItem,
    string $localeString = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||
`$discountLineItem`|[`LineItem`](../LineItem.md)||
`$localeString`|`string`|`null`|

Return Value: [`Cart`](../Cart.md)

### order()

```php
public function order(
    Cart $cart,
    string $locale = null
): Order
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||
`$locale`|`string`|`null`|

Return Value: [`Order`](../Order.md)

### getOrder()

```php
public function getOrder(
    Account $account,
    string $orderId,
    string $locale = null
): Order
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||
`$orderId`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Order`](../Order.md)

### getOrders()

```php
public function getOrders(
    Account $account,
    string $locale = null
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||
`$locale`|`string`|`null`|

Return Value: `array`

### startTransaction()

```php
public function startTransaction(
    Cart $cart
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../Cart.md)||

Return Value: `void`

### commit()

```php
public function commit(
    string $localeString = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$localeString`|`string`|`null`|

Return Value: [`Cart`](../Cart.md)

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
