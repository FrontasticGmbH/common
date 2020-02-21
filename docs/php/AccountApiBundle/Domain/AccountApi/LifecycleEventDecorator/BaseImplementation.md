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

* [beforeGet()](#beforeget)
* [afterGet()](#afterget)
* [beforeConfirmEmail()](#beforeconfirmemail)
* [afterConfirmEmail()](#afterconfirmemail)
* [beforeCreate()](#beforecreate)
* [afterCreate()](#aftercreate)
* [beforeVerifyEmail()](#beforeverifyemail)
* [afterVerifyEmail()](#afterverifyemail)
* [beforeUpdate()](#beforeupdate)
* [afterUpdate()](#afterupdate)
* [beforeUpdatePassword()](#beforeupdatepassword)
* [afterUpdatePassword()](#afterupdatepassword)
* [beforeGeneratePasswordResetToken()](#beforegeneratepasswordresettoken)
* [afterGeneratePasswordResetToken()](#aftergeneratepasswordresettoken)
* [beforeResetPassword()](#beforeresetpassword)
* [afterResetPassword()](#afterresetpassword)
* [beforeLogin()](#beforelogin)
* [afterLogin()](#afterlogin)
* [beforeGetAddresses()](#beforegetaddresses)
* [afterGetAddresses()](#aftergetaddresses)
* [beforeAddAddress()](#beforeaddaddress)
* [afterAddAddress()](#afteraddaddress)
* [beforeUpdateAddress()](#beforeupdateaddress)
* [afterUpdateAddress()](#afterupdateaddress)
* [beforeRemoveAddress()](#beforeremoveaddress)
* [afterRemoveAddress()](#afterremoveaddress)
* [beforeSetDefaultBillingAddress()](#beforesetdefaultbillingaddress)
* [afterSetDefaultBillingAddress()](#aftersetdefaultbillingaddress)
* [beforeSetDefaultShippingAddress()](#beforesetdefaultshippingaddress)
* [afterSetDefaultShippingAddress()](#aftersetdefaultshippingaddress)


### beforeGet()


```php
public function beforeGet(
    AccountApi $accountApi,
    string $email
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$email`|`string`||

Return Value: `void`

### afterGet()


```php
public function afterGet(
    AccountApi $accountApi,
    Account $account
): ?Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: ?[`Account`](../../Account.md)

### beforeConfirmEmail()


```php
public function beforeConfirmEmail(
    AccountApi $accountApi,
    string $token
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$token`|`string`||

Return Value: `void`

### afterConfirmEmail()


```php
public function afterConfirmEmail(
    AccountApi $accountApi,
    Account $account
): ?Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: ?[`Account`](../../Account.md)

### beforeCreate()


```php
public function beforeCreate(
    AccountApi $accountApi,
    Account $account,
    ?Cart $cart = null
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||
`$cart`|?[`Cart`](../../../../CartApiBundle/Domain/Cart.md)|`null`|

Return Value: `void`

### afterCreate()


```php
public function afterCreate(
    AccountApi $accountApi,
    Account $account
): ?Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: ?[`Account`](../../Account.md)

### beforeVerifyEmail()


```php
public function beforeVerifyEmail(
    AccountApi $accountApi,
    string $token
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$token`|`string`||

Return Value: `void`

### afterVerifyEmail()


```php
public function afterVerifyEmail(
    AccountApi $accountApi,
    Account $account
): ?Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: ?[`Account`](../../Account.md)

### beforeUpdate()


```php
public function beforeUpdate(
    AccountApi $accountApi,
    Account $account
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: `void`

### afterUpdate()


```php
public function afterUpdate(
    AccountApi $accountApi,
    Account $account
): ?Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: ?[`Account`](../../Account.md)

### beforeUpdatePassword()


```php
public function beforeUpdatePassword(
    AccountApi $accountApi,
    string $accountId,
    string $oldPassword,
    string $newPassword
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$accountId`|`string`||
`$oldPassword`|`string`||
`$newPassword`|`string`||

Return Value: `void`

### afterUpdatePassword()


```php
public function afterUpdatePassword(
    AccountApi $accountApi,
    Account $account
): ?Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: ?[`Account`](../../Account.md)

### beforeGeneratePasswordResetToken()


```php
public function beforeGeneratePasswordResetToken(
    AccountApi $accountApi,
    Account $account
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: `void`

### afterGeneratePasswordResetToken()


```php
public function afterGeneratePasswordResetToken(
    AccountApi $accountApi,
    Account $account
): ?Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: ?[`Account`](../../Account.md)

### beforeResetPassword()


```php
public function beforeResetPassword(
    AccountApi $accountApi,
    string $token,
    string $newPassword
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$token`|`string`||
`$newPassword`|`string`||

Return Value: `void`

### afterResetPassword()


```php
public function afterResetPassword(
    AccountApi $accountApi,
    Account $account
): ?Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: ?[`Account`](../../Account.md)

### beforeLogin()


```php
public function beforeLogin(
    AccountApi $accountApi,
    Account $account,
    ?Cart $cart = null
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||
`$cart`|?[`Cart`](../../../../CartApiBundle/Domain/Cart.md)|`null`|

Return Value: `void`

### afterLogin()


```php
public function afterLogin(
    AccountApi $accountApi,
    bool $successful
): ?bool
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$successful`|`bool`||

Return Value: `?bool`

### beforeGetAddresses()


```php
public function beforeGetAddresses(
    AccountApi $accountApi,
    string $accountId
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$accountId`|`string`||

Return Value: `void`

### afterGetAddresses()


```php
public function afterGetAddresses(
    AccountApi $accountApi,
    array $addresses
): ?array
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$addresses`|`array`||

Return Value: `?array`

### beforeAddAddress()


```php
public function beforeAddAddress(
    AccountApi $accountApi,
    string $accountId,
    Address $address
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$accountId`|`string`||
`$address`|[`Address`](../../Address.md)||

Return Value: `void`

### afterAddAddress()


```php
public function afterAddAddress(
    AccountApi $accountApi,
    Account $account
): ?Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: ?[`Account`](../../Account.md)

### beforeUpdateAddress()


```php
public function beforeUpdateAddress(
    AccountApi $accountApi,
    string $accountId,
    Address $address
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$accountId`|`string`||
`$address`|[`Address`](../../Address.md)||

Return Value: `void`

### afterUpdateAddress()


```php
public function afterUpdateAddress(
    AccountApi $accountApi,
    Account $account
): ?Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: ?[`Account`](../../Account.md)

### beforeRemoveAddress()


```php
public function beforeRemoveAddress(
    AccountApi $accountApi,
    string $accountId,
    string $addressId
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$accountId`|`string`||
`$addressId`|`string`||

Return Value: `void`

### afterRemoveAddress()


```php
public function afterRemoveAddress(
    AccountApi $accountApi,
    Account $account
): ?Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: ?[`Account`](../../Account.md)

### beforeSetDefaultBillingAddress()


```php
public function beforeSetDefaultBillingAddress(
    AccountApi $accountApi,
    string $accountId,
    string $addressId
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$accountId`|`string`||
`$addressId`|`string`||

Return Value: `void`

### afterSetDefaultBillingAddress()


```php
public function afterSetDefaultBillingAddress(
    AccountApi $accountApi,
    Account $account
): ?Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: ?[`Account`](../../Account.md)

### beforeSetDefaultShippingAddress()


```php
public function beforeSetDefaultShippingAddress(
    AccountApi $accountApi,
    string $accountId,
    string $addressId
): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$accountId`|`string`||
`$addressId`|`string`||

Return Value: `void`

### afterSetDefaultShippingAddress()


```php
public function afterSetDefaultShippingAddress(
    AccountApi $accountApi,
    Account $account
): ?Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: ?[`Account`](../../Account.md)

