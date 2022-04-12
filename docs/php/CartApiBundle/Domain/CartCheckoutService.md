# `interface`  CartCheckoutService

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\CartCheckoutService`](../../../../src/php/CartApiBundle/Domain/CartCheckoutService.php)

## Methods

* [getPayedAmount()](#getpayedamount)
* [hasCompletePayments()](#hascompletepayments)
* [isPaymentCompleted()](#ispaymentcompleted)
* [isReadyForCheckout()](#isreadyforcheckout)

### getPayedAmount()

```php
public function getPayedAmount(
    Cart $cart
): int
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||

Return Value: `int`

### hasCompletePayments()

```php
public function hasCompletePayments(
    Cart $cart
): bool
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||

Return Value: `bool`

### isPaymentCompleted()

```php
public function isPaymentCompleted(
    Payment $payment
): bool
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$payment`|[`Payment`](Payment.md)||

Return Value: `bool`

### isReadyForCheckout()

```php
public function isReadyForCheckout(
    Cart $cart
): bool
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||

Return Value: `bool`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
