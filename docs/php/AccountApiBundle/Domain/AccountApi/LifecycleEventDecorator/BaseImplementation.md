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
public function beforeGet([AccountApi](../../AccountApi.md) $accountApi, string $email): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$email`|`string`|``|

### afterGet()


```php
public function afterGet([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account): ?[Account](../../Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|

### beforeConfirmEmail()


```php
public function beforeConfirmEmail([AccountApi](../../AccountApi.md) $accountApi, string $token): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$token`|`string`|``|

### afterConfirmEmail()


```php
public function afterConfirmEmail([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account): ?[Account](../../Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|

### beforeCreate()


```php
public function beforeCreate([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account, ?[Cart](../../../../CartApiBundle/Domain/Cart.md) $cart = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|
`$cart`|`?[Cart](../../../../CartApiBundle/Domain/Cart.md)`|`null`|

### afterCreate()


```php
public function afterCreate([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account): ?[Account](../../Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|

### beforeVerifyEmail()


```php
public function beforeVerifyEmail([AccountApi](../../AccountApi.md) $accountApi, string $token): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$token`|`string`|``|

### afterVerifyEmail()


```php
public function afterVerifyEmail([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account): ?[Account](../../Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|

### beforeUpdate()


```php
public function beforeUpdate([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|

### afterUpdate()


```php
public function afterUpdate([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account): ?[Account](../../Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|

### beforeUpdatePassword()


```php
public function beforeUpdatePassword([AccountApi](../../AccountApi.md) $accountApi, string $accountId, string $oldPassword, string $newPassword): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$accountId`|`string`|``|
`$oldPassword`|`string`|``|
`$newPassword`|`string`|``|

### afterUpdatePassword()


```php
public function afterUpdatePassword([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account): ?[Account](../../Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|

### beforeGeneratePasswordResetToken()


```php
public function beforeGeneratePasswordResetToken([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|

### afterGeneratePasswordResetToken()


```php
public function afterGeneratePasswordResetToken([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account): ?[Account](../../Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|

### beforeResetPassword()


```php
public function beforeResetPassword([AccountApi](../../AccountApi.md) $accountApi, string $token, string $newPassword): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$token`|`string`|``|
`$newPassword`|`string`|``|

### afterResetPassword()


```php
public function afterResetPassword([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account): ?[Account](../../Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|

### beforeLogin()


```php
public function beforeLogin([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account, ?[Cart](../../../../CartApiBundle/Domain/Cart.md) $cart = null): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|
`$cart`|`?[Cart](../../../../CartApiBundle/Domain/Cart.md)`|`null`|

### afterLogin()


```php
public function afterLogin([AccountApi](../../AccountApi.md) $accountApi, bool $successful): ?bool
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$successful`|`bool`|``|

### beforeGetAddresses()


```php
public function beforeGetAddresses([AccountApi](../../AccountApi.md) $accountApi, string $accountId): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$accountId`|`string`|``|

### afterGetAddresses()


```php
public function afterGetAddresses([AccountApi](../../AccountApi.md) $accountApi, array $addresses): ?array
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$addresses`|`array`|``|

### beforeAddAddress()


```php
public function beforeAddAddress([AccountApi](../../AccountApi.md) $accountApi, string $accountId, [Address](../../Address.md) $address): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$accountId`|`string`|``|
`$address`|`[Address](../../Address.md)`|``|

### afterAddAddress()


```php
public function afterAddAddress([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account): ?[Account](../../Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|

### beforeUpdateAddress()


```php
public function beforeUpdateAddress([AccountApi](../../AccountApi.md) $accountApi, string $accountId, [Address](../../Address.md) $address): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$accountId`|`string`|``|
`$address`|`[Address](../../Address.md)`|``|

### afterUpdateAddress()


```php
public function afterUpdateAddress([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account): ?[Account](../../Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|

### beforeRemoveAddress()


```php
public function beforeRemoveAddress([AccountApi](../../AccountApi.md) $accountApi, string $accountId, string $addressId): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$accountId`|`string`|``|
`$addressId`|`string`|``|

### afterRemoveAddress()


```php
public function afterRemoveAddress([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account): ?[Account](../../Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|

### beforeSetDefaultBillingAddress()


```php
public function beforeSetDefaultBillingAddress([AccountApi](../../AccountApi.md) $accountApi, string $accountId, string $addressId): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$accountId`|`string`|``|
`$addressId`|`string`|``|

### afterSetDefaultBillingAddress()


```php
public function afterSetDefaultBillingAddress([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account): ?[Account](../../Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|

### beforeSetDefaultShippingAddress()


```php
public function beforeSetDefaultShippingAddress([AccountApi](../../AccountApi.md) $accountApi, string $accountId, string $addressId): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$accountId`|`string`|``|
`$addressId`|`string`|``|

### afterSetDefaultShippingAddress()


```php
public function afterSetDefaultShippingAddress([AccountApi](../../AccountApi.md) $accountApi, [Account](../../Account.md) $account): ?[Account](../../Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](../../AccountApi.md)`|``|
`$account`|`[Account](../../Account.md)`|``|

