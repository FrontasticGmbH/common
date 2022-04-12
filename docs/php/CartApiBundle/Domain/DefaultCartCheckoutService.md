#  DefaultCartCheckoutService

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\DefaultCartCheckoutService`](../../../../src/php/CartApiBundle/Domain/DefaultCartCheckoutService.php)

**Implements**: [`CartCheckoutService`](CartCheckoutService.md)

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

*We only consider payments with status "paid" as completed*

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

*Some commerce backends might consider a cart ready without payment(s).*

This method will return true if there are no payments or if all payments
had paid status and the total amounts are equal to cart total amount.

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](Cart.md)||

Return Value: `bool`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
