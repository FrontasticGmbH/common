#  SprykerCartApi

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCartApi`](../../../../../src/php/SprykerBundle/Domain/Cart/SprykerCartApi.php)

**Extends**: [`SprykerApiBase`](../../BaseApi/SprykerApiBase.md)

**Implements**: [`CartApi`](../../../CartApiBundle/Domain/CartApi.md)

## Methods

* [__construct()](#__construct)
* [getForUser()](#getforuser)
* [getAnonymous()](#getanonymous)
* [setCustomLineItemType()](#setcustomlineitemtype)
* [getCustomLineItemType()](#getcustomlineitemtype)
* [setTaxCategory()](#settaxcategory)
* [getTaxCategory()](#gettaxcategory)
* [addToCart()](#addtocart)
* [updateLineItem()](#updatelineitem)
* [removeLineItem()](#removelineitem)
* [setEmail()](#setemail)
* [setAccount()](#setaccount)
* [setShippingMethod()](#setshippingmethod)
* [setCustomField()](#setcustomfield)
* [setShippingAddress()](#setshippingaddress)
* [setBillingAddress()](#setbillingaddress)
* [addPayment()](#addpayment)
* [updatePayment()](#updatepayment)
* [redeemDiscountCode()](#redeemdiscountcode)
* [order()](#order)
* [getOrder()](#getorder)
* [getOrders()](#getorders)
* [startTransaction()](#starttransaction)
* [commit()](#commit)
* [getDangerousInnerClient()](#getdangerousinnerclient)
* [getById()](#getbyid)
* [setRawApiInput()](#setrawapiinput)
* [removeDiscountCode()](#removediscountcode)

### __construct()

```php
public function __construct(
    SprykerClientInterface $client,
    MapperResolver $mapperResolver,
    AccountHelper $accountHelper,
    \Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\SprykerCartInterface $guestCart,
    \Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\SprykerCartInterface $customerCart,
    LocaleCreator $localeCreator,
    array $orderIncludes = []
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`SprykerClientInterface`](../SprykerClientInterface.md)||
`$mapperResolver`|[`MapperResolver`](../MapperResolver.md)||
`$accountHelper`|[`AccountHelper`](../Account/AccountHelper.md)||
`$guestCart`|`\Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\SprykerCartInterface`||
`$customerCart`|`\Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\SprykerCartInterface`||
`$localeCreator`|[`LocaleCreator`](../Locale/LocaleCreator.md)||
`$orderIncludes`|`array`|`[]`|

Return Value: `mixed`

### getForUser()

```php
public function getForUser(
    Account $account,
    string $locale
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||
`$locale`|`string`||

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

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

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

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
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$lineItem`|[`LineItem`](../../../CartApiBundle/Domain/LineItem.md)||
`$locale`|`string`|`null`|

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

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
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$lineItem`|[`LineItem`](../../../CartApiBundle/Domain/LineItem.md)||
`$count`|`int`||
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

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
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$lineItem`|[`LineItem`](../../../CartApiBundle/Domain/LineItem.md)||
`$locale`|`string`|`null`|

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

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
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$email`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

### setAccount()

```php
public function setAccount(
    Cart $cart,
    Account $account
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

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
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$shippingMethod`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

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
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$fields`|`array`||
`$locale`|`string`|`null`|

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

### setShippingAddress()

```php
public function setShippingAddress(
    Cart $cart,
    Address $address,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$address`|[`Address`](../../../AccountApiBundle/Domain/Address.md)||
`$locale`|`string`|`null`|

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

### setBillingAddress()

```php
public function setBillingAddress(
    Cart $cart,
    Address $address,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$address`|[`Address`](../../../AccountApiBundle/Domain/Address.md)||
`$locale`|`string`|`null`|

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

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
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$payment`|[`Payment`](../../../CartApiBundle/Domain/Payment.md)||
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

### updatePayment()

```php
public function updatePayment(
    Cart $cart,
    Payment $payment,
    string $localeString
): Payment
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$payment`|[`Payment`](../../../CartApiBundle/Domain/Payment.md)||
`$localeString`|`string`||

Return Value: [`Payment`](../../../CartApiBundle/Domain/Payment.md)

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
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$code`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

### order()

```php
public function order(
    Cart $cart,
    string $locale = null
): Order
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$locale`|`string`|`null`|

Return Value: [`Order`](../../../CartApiBundle/Domain/Order.md)

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

Return Value: [`Order`](../../../CartApiBundle/Domain/Order.md)

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
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||

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

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

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

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

### setRawApiInput()

```php
public function setRawApiInput(
    Cart $cart,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$locale`|`string`|`null`|

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

### removeDiscountCode()

```php
public function removeDiscountCode(
    Cart $cart,
    LineItem $discountLineItem,
    string $locale = null
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$discountLineItem`|[`LineItem`](../../../CartApiBundle/Domain/LineItem.md)||
`$locale`|`string`|`null`|

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).