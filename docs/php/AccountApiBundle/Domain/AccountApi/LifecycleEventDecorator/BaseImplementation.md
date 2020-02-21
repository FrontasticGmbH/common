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

* [beforeGet()](#beforeGet)
* [afterGet()](#afterGet)
* [beforeConfirmEmail()](#beforeConfirmEmail)
* [afterConfirmEmail()](#afterConfirmEmail)
* [beforeCreate()](#beforeCreate)
* [afterCreate()](#afterCreate)
* [beforeVerifyEmail()](#beforeVerifyEmail)
* [afterVerifyEmail()](#afterVerifyEmail)
* [beforeUpdate()](#beforeUpdate)
* [afterUpdate()](#afterUpdate)
* [beforeUpdatePassword()](#beforeUpdatePassword)
* [afterUpdatePassword()](#afterUpdatePassword)
* [beforeGeneratePasswordResetToken()](#beforeGeneratePasswordResetToken)
* [afterGeneratePasswordResetToken()](#afterGeneratePasswordResetToken)
* [beforeResetPassword()](#beforeResetPassword)
* [afterResetPassword()](#afterResetPassword)
* [beforeLogin()](#beforeLogin)
* [afterLogin()](#afterLogin)
* [beforeGetAddresses()](#beforeGetAddresses)
* [afterGetAddresses()](#afterGetAddresses)
* [beforeAddAddress()](#beforeAddAddress)
* [afterAddAddress()](#afterAddAddress)
* [beforeUpdateAddress()](#beforeUpdateAddress)
* [afterUpdateAddress()](#afterUpdateAddress)
* [beforeRemoveAddress()](#beforeRemoveAddress)
* [afterRemoveAddress()](#afterRemoveAddress)
* [beforeSetDefaultBillingAddress()](#beforeSetDefaultBillingAddress)
* [afterSetDefaultBillingAddress()](#afterSetDefaultBillingAddress)
* [beforeSetDefaultShippingAddress()](#beforeSetDefaultShippingAddress)
* [afterSetDefaultShippingAddress()](#afterSetDefaultShippingAddress)


### beforeGet()


```php
public function beforeGet(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string email): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$email`|`string`|``|

### afterGet()


```php
public function afterGet(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeConfirmEmail()


```php
public function beforeConfirmEmail(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string token): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$token`|`string`|``|

### afterConfirmEmail()


```php
public function afterConfirmEmail(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeCreate()


```php
public function beforeCreate(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account, ?\Frontastic\Common\CartApiBundle\Domain\Cart cart = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|
`$cart`|`?\Frontastic\Common\CartApiBundle\Domain\Cart`|`null`|

### afterCreate()


```php
public function afterCreate(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeVerifyEmail()


```php
public function beforeVerifyEmail(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string token): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$token`|`string`|``|

### afterVerifyEmail()


```php
public function afterVerifyEmail(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeUpdate()


```php
public function beforeUpdate(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### afterUpdate()


```php
public function afterUpdate(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeUpdatePassword()


```php
public function beforeUpdatePassword(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string accountId, string oldPassword, string newPassword): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$accountId`|`string`|``|
`$oldPassword`|`string`|``|
`$newPassword`|`string`|``|

### afterUpdatePassword()


```php
public function afterUpdatePassword(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeGeneratePasswordResetToken()


```php
public function beforeGeneratePasswordResetToken(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### afterGeneratePasswordResetToken()


```php
public function afterGeneratePasswordResetToken(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeResetPassword()


```php
public function beforeResetPassword(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string token, string newPassword): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$token`|`string`|``|
`$newPassword`|`string`|``|

### afterResetPassword()


```php
public function afterResetPassword(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeLogin()


```php
public function beforeLogin(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account, ?\Frontastic\Common\CartApiBundle\Domain\Cart cart = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|
`$cart`|`?\Frontastic\Common\CartApiBundle\Domain\Cart`|`null`|

### afterLogin()


```php
public function afterLogin(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, bool successful): ?bool
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$successful`|`bool`|``|

### beforeGetAddresses()


```php
public function beforeGetAddresses(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string accountId): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$accountId`|`string`|``|

### afterGetAddresses()


```php
public function afterGetAddresses(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, array addresses): ?array
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$addresses`|`array`|``|

### beforeAddAddress()


```php
public function beforeAddAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string accountId, \Frontastic\Common\AccountApiBundle\Domain\Address address): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$accountId`|`string`|``|
`$address`|`\Frontastic\Common\AccountApiBundle\Domain\Address`|``|

### afterAddAddress()


```php
public function afterAddAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeUpdateAddress()


```php
public function beforeUpdateAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string accountId, \Frontastic\Common\AccountApiBundle\Domain\Address address): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$accountId`|`string`|``|
`$address`|`\Frontastic\Common\AccountApiBundle\Domain\Address`|``|

### afterUpdateAddress()


```php
public function afterUpdateAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeRemoveAddress()


```php
public function beforeRemoveAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string accountId, string addressId): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$accountId`|`string`|``|
`$addressId`|`string`|``|

### afterRemoveAddress()


```php
public function afterRemoveAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeSetDefaultBillingAddress()


```php
public function beforeSetDefaultBillingAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string accountId, string addressId): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$accountId`|`string`|``|
`$addressId`|`string`|``|

### afterSetDefaultBillingAddress()


```php
public function afterSetDefaultBillingAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### beforeSetDefaultShippingAddress()


```php
public function beforeSetDefaultShippingAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, string accountId, string addressId): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$accountId`|`string`|``|
`$addressId`|`string`|``|

### afterSetDefaultShippingAddress()


```php
public function afterSetDefaultShippingAddress(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\AccountApiBundle\Domain\Account account): ?\Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

