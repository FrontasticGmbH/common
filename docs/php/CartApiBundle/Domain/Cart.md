#  Cart

Fully Qualified: [`\Frontastic\Common\CartApiBundle\Domain\Cart`](../../../../src/php/CartApiBundle/Domain/Cart.php)



Property|Type|Default|Description
--------|----|-------|-----------
`cartId`|`string`|``|
`cartVersion`|`string`|``|
`custom`|`array`|`[]`|
`lineItems`|`\Frontastic\Common\CartApiBundle\Domain\LineItem[]`|`[]`|
`email`|`string`|``|
`birthday`|`\DateTimeImmutable`|``|
`shippingMethod`|`?\Frontastic\Common\CartApiBundle\Domain\ShippingMethod`|``|
`shippingAddress`|`?\Frontastic\Common\AccountApiBundle\Domain\Address`|``|
`billingAddress`|`?\Frontastic\Common\AccountApiBundle\Domain\Address`|``|
`sum`|`int`|``|
`currency`|`string`|``|
`payments`|`\Frontastic\Common\CartApiBundle\Domain\Payment[]`|`[]`|
`discountCodes`|`string[]`|`[]`|
`dangerousInnerCart`|`mixed`|``|Access original object from backend

## Methods

### getPayedAmount

`function getPayedAmount(): int`







### hasUser

`function hasUser(): bool`







### hasShippingAddress

`function hasShippingAddress(): bool`







### hasBillingAddress

`function hasBillingAddress(): bool`







### hasAddresses

`function hasAddresses(): bool`







### hasCompletePayments

`function hasCompletePayments(): bool`







### isComplete

`function isComplete(): bool`







