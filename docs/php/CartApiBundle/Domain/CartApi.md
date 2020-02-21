# `interface`  CartApi

Fully Qualified: [`\Frontastic\Common\CartApiBundle\Domain\CartApi`](../../../../src/php/CartApiBundle/Domain/CartApi.php)




## Methods

### getForUser

`function getForUser(string userId, string locale): \Frontastic\Common\CartApiBundle\Domain\Cart`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$userId`|`string`|``|
`$locale`|`string`|``|

### getAnonymous

`function getAnonymous(string anonymousId, string locale): \Frontastic\Common\CartApiBundle\Domain\Cart`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$anonymousId`|`string`|``|
`$locale`|`string`|``|

### getById

`function getById(string cartId, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$cartId`|`string`|``|
`$locale`|`string`|`null`|

### setCustomLineItemType

`function setCustomLineItemType(array lineItemType): void`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$lineItemType`|`array`|``|

### getCustomLineItemType

`function getCustomLineItemType(): array`




**


### setTaxCategory

`function setTaxCategory(array taxCategory): void`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$taxCategory`|`array`|``|

### getTaxCategory

`function getTaxCategory(): array`




**


### addToCart

`function addToCart(\Frontastic\Common\CartApiBundle\Domain\Cart cart, \Frontastic\Common\CartApiBundle\Domain\LineItem lineItem, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$lineItem`|`\Frontastic\Common\CartApiBundle\Domain\LineItem`|``|
`$locale`|`string`|`null`|

### updateLineItem

`function updateLineItem(\Frontastic\Common\CartApiBundle\Domain\Cart cart, \Frontastic\Common\CartApiBundle\Domain\LineItem lineItem, int count, ?array custom = null, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart`






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$lineItem`|`\Frontastic\Common\CartApiBundle\Domain\LineItem`|``|
`$count`|`int`|``|
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

### removeLineItem

`function removeLineItem(\Frontastic\Common\CartApiBundle\Domain\Cart cart, \Frontastic\Common\CartApiBundle\Domain\LineItem lineItem, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$lineItem`|`\Frontastic\Common\CartApiBundle\Domain\LineItem`|``|
`$locale`|`string`|`null`|

### setEmail

`function setEmail(\Frontastic\Common\CartApiBundle\Domain\Cart cart, string email, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$email`|`string`|``|
`$locale`|`string`|`null`|

### setShippingMethod

`function setShippingMethod(\Frontastic\Common\CartApiBundle\Domain\Cart cart, string shippingMethod, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart`






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$shippingMethod`|`string`|``|
`$locale`|`string`|`null`|

### setCustomField

`function setCustomField(\Frontastic\Common\CartApiBundle\Domain\Cart cart, array fields, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart`






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$fields`|`array`|``|
`$locale`|`string`|`null`|

### setShippingAddress

`function setShippingAddress(\Frontastic\Common\CartApiBundle\Domain\Cart cart, array address, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$address`|`array`|``|
`$locale`|`string`|`null`|

### setBillingAddress

`function setBillingAddress(\Frontastic\Common\CartApiBundle\Domain\Cart cart, array address, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$address`|`array`|``|
`$locale`|`string`|`null`|

### addPayment

`function addPayment(\Frontastic\Common\CartApiBundle\Domain\Cart cart, \Frontastic\Common\CartApiBundle\Domain\Payment payment, ?array custom = null, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$payment`|`\Frontastic\Common\CartApiBundle\Domain\Payment`|``|
`$custom`|`?array`|`null`|
`$locale`|`string`|`null`|

### redeemDiscountCode

`function redeemDiscountCode(\Frontastic\Common\CartApiBundle\Domain\Cart cart, string code, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart`






Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$code`|`string`|``|
`$locale`|`string`|`null`|

### removeDiscountCode

`function removeDiscountCode(\Frontastic\Common\CartApiBundle\Domain\Cart cart, string discountId, string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|
`$discountId`|`string`|``|
`$locale`|`string`|`null`|

### order

`function order(\Frontastic\Common\CartApiBundle\Domain\Cart cart): \Frontastic\Common\CartApiBundle\Domain\Order`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### getOrder

`function getOrder(string orderId): \Frontastic\Common\CartApiBundle\Domain\Order`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$orderId`|`string`|``|

### getOrders

`function getOrders(string accountId): array`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|

### startTransaction

`function startTransaction(\Frontastic\Common\CartApiBundle\Domain\Cart cart): void`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|`\Frontastic\Common\CartApiBundle\Domain\Cart`|``|

### commit

`function commit(string locale = null): \Frontastic\Common\CartApiBundle\Domain\Cart`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$locale`|`string`|`null`|

### getDangerousInnerClient

`function getDangerousInnerClient(): mixed`


*Get *dangerous* inner client*

*This method exists to enable you to use features which are not yet part
of the abstraction layer.

Be aware that any usage of this method might seriously hurt backwards
compatibility and the future abstractions might differ a lot from the
vendor provided abstraction.

Use this with care for features necessary in your customer and talk with
Frontastic about provising an abstraction.*


