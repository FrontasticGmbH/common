# `interface`  AccountApi

Fully Qualified: [`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`](../../../../src/php/AccountApiBundle/Domain/AccountApi.php)




## Methods

### get

`function get(string email): \Frontastic\Common\AccountApiBundle\Domain\Account`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$email`|`string`|``|

### confirmEmail

`function confirmEmail(string token): \Frontastic\Common\AccountApiBundle\Domain\Account`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`|``|

### create

`function create(\Frontastic\Common\AccountApiBundle\Domain\Account account, ?\Frontastic\Common\CartApiBundle\Domain\Cart cart = null): \Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|
`$cart`|`?\Frontastic\Common\CartApiBundle\Domain\Cart`|`null`|

### verifyEmail

`function verifyEmail(string token): \Frontastic\Common\AccountApiBundle\Domain\Account`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`|``|

### update

`function update(\Frontastic\Common\AccountApiBundle\Domain\Account account): \Frontastic\Common\AccountApiBundle\Domain\Account`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### updatePassword

`function updatePassword(string accountId, string oldPassword, string newPassword): \Frontastic\Common\AccountApiBundle\Domain\Account`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$oldPassword`|`string`|``|
`$newPassword`|`string`|``|

### generatePasswordResetToken

`function generatePasswordResetToken(\Frontastic\Common\AccountApiBundle\Domain\Account account): \Frontastic\Common\AccountApiBundle\Domain\Account`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### resetPassword

`function resetPassword(string token, string newPassword): \Frontastic\Common\AccountApiBundle\Domain\Account`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`|``|
`$newPassword`|`string`|``|

### login

`function login(\Frontastic\Common\AccountApiBundle\Domain\Account account, ?\Frontastic\Common\CartApiBundle\Domain\Cart cart = null): bool`






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|
`$cart`|`?\Frontastic\Common\CartApiBundle\Domain\Cart`|`null`|

### getAddresses

`function getAddresses(string accountId): array`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|

### addAddress

`function addAddress(string accountId, \Frontastic\Common\AccountApiBundle\Domain\Address address): \Frontastic\Common\AccountApiBundle\Domain\Account`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$address`|`\Frontastic\Common\AccountApiBundle\Domain\Address`|``|

### updateAddress

`function updateAddress(string accountId, \Frontastic\Common\AccountApiBundle\Domain\Address address): \Frontastic\Common\AccountApiBundle\Domain\Account`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$address`|`\Frontastic\Common\AccountApiBundle\Domain\Address`|``|

### removeAddress

`function removeAddress(string accountId, string addressId): \Frontastic\Common\AccountApiBundle\Domain\Account`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$addressId`|`string`|``|

### setDefaultBillingAddress

`function setDefaultBillingAddress(string accountId, string addressId): \Frontastic\Common\AccountApiBundle\Domain\Account`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$addressId`|`string`|``|

### setDefaultShippingAddress

`function setDefaultShippingAddress(string accountId, string addressId): \Frontastic\Common\AccountApiBundle\Domain\Account`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$addressId`|`string`|``|

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


