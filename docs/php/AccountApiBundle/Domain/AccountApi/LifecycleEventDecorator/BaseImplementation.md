# `abstract`  BaseImplementation

Fully Qualified: [`\Frontastic\Common\AccountApiBundle\Domain\AccountApi\LifecycleEventDecorator\BaseImplementation`](../../../../../../src/php/AccountApiBundle/Domain/AccountApi/LifecycleEventDecorator/BaseImplementation.php)


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
"accountApi.lifecycleEventListener": e.g. by adding the tag inside the
`services.xml` ``` <tag name="accountApi.lifecycleEventListener" /> ```

## Methods

### beforeGet

`function beforeGet(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string email): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$email`|`string`|``|

### afterGet

`function afterGet(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeConfirmEmail

`function beforeConfirmEmail(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string token): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$token`|`string`|``|

### afterConfirmEmail

`function afterConfirmEmail(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeCreate

`function beforeCreate(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account, ?\Frontastic\Common\CartApiBundle\Domain\Cart cart = null): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|
`$cart`|`?\Frontastic\Common\CartApiBundle\Domain\Cart`|`null`|

### afterCreate

`function afterCreate(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeVerifyEmail

`function beforeVerifyEmail(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string token): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$token`|`string`|``|

### afterVerifyEmail

`function afterVerifyEmail(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeUpdate

`function beforeUpdate(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### afterUpdate

`function afterUpdate(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeUpdatePassword

`function beforeUpdatePassword(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string accountId, string oldPassword, string newPassword): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$accountId`|`string`|``|
`$oldPassword`|`string`|``|
`$newPassword`|`string`|``|

### afterUpdatePassword

`function afterUpdatePassword(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeGeneratePasswordResetToken

`function beforeGeneratePasswordResetToken(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### afterGeneratePasswordResetToken

`function afterGeneratePasswordResetToken(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeResetPassword

`function beforeResetPassword(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string token, string newPassword): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$token`|`string`|``|
`$newPassword`|`string`|``|

### afterResetPassword

`function afterResetPassword(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeLogin

`function beforeLogin(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account, ?\Frontastic\Common\CartApiBundle\Domain\Cart cart = null): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|
`$cart`|`?\Frontastic\Common\CartApiBundle\Domain\Cart`|`null`|

### afterLogin

`function afterLogin(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, bool successful): ?bool`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$successful`|`bool`|``|

### beforeGetAddresses

`function beforeGetAddresses(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string accountId): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$accountId`|`string`|``|

### afterGetAddresses

`function afterGetAddresses(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, array addresses): ?array`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$addresses`|`array`|``|

### beforeAddAddress

`function beforeAddAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string accountId, \Frontastic\Common\AccountApiBundle\Domain\Address address): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$accountId`|`string`|``|
`$address`|`\Frontastic\Common\AccountApiBundle\Domain\Address`|``|

### afterAddAddress

`function afterAddAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeUpdateAddress

`function beforeUpdateAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string accountId, \Frontastic\Common\AccountApiBundle\Domain\Address address): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$accountId`|`string`|``|
`$address`|`\Frontastic\Common\AccountApiBundle\Domain\Address`|``|

### afterUpdateAddress

`function afterUpdateAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeRemoveAddress

`function beforeRemoveAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string accountId, string addressId): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$accountId`|`string`|``|
`$addressId`|`string`|``|

### afterRemoveAddress

`function afterRemoveAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeSetDefaultBillingAddress

`function beforeSetDefaultBillingAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string accountId, string addressId): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$accountId`|`string`|``|
`$addressId`|`string`|``|

### afterSetDefaultBillingAddress

`function afterSetDefaultBillingAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeSetDefaultShippingAddress

`function beforeSetDefaultShippingAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string accountId, string addressId): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$accountId`|`string`|``|
`$addressId`|`string`|``|

### afterSetDefaultShippingAddress

`function afterSetDefaultShippingAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account`






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

