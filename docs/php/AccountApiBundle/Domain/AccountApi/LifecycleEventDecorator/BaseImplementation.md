# `abstract`  BaseImplementation

**Fully Qualified**: [`\Frontastic\Common\AccountApiBundle\Domain\AccountApi\LifecycleEventDecorator\BaseImplementation`](../../../../../../src/php/AccountApiBundle/Domain/AccountApi/LifecycleEventDecorator/BaseImplementation.php)

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

* [beforeGetSalutations()](#beforegetsalutations)
* [afterGetSalutations()](#aftergetsalutations)
* [beforeConfirmEmail()](#beforeconfirmemail)
* [afterConfirmEmail()](#afterconfirmemail)
* [beforeCreate()](#beforecreate)
* [afterCreate()](#aftercreate)
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
* [beforeRefreshAccount()](#beforerefreshaccount)
* [afterRefreshAccount()](#afterrefreshaccount)
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

### beforeGetSalutations()

```php
public function beforeGetSalutations(
    AccountApi $accountApi,
    string $locale
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$locale`|`string`||

Return Value: `void`

### afterGetSalutations()

```php
public function afterGetSalutations(
    AccountApi $accountApi,
    ?array $salutations
): ?array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$salutations`|`?array`||

Return Value: `?array`

### beforeConfirmEmail()

```php
public function beforeConfirmEmail(
    AccountApi $accountApi,
    string $token,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$token`|`string`||
`$locale`|`string`|`null`|

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
    ?Cart $cart = null,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||
`$cart`|?[`Cart`](../../../../CartApiBundle/Domain/Cart.md)|`null`|
`$locale`|`string`|`null`|

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

### beforeUpdate()

```php
public function beforeUpdate(
    AccountApi $accountApi,
    Account $account,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||
`$locale`|`string`|`null`|

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
    Account $account,
    string $oldPassword,
    string $newPassword,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||
`$oldPassword`|`string`||
`$newPassword`|`string`||
`$locale`|`string`|`null`|

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
    string $email
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$email`|`string`||

Return Value: `void`

### afterGeneratePasswordResetToken()

```php
public function afterGeneratePasswordResetToken(
    AccountApi $accountApi,
    \Frontastic\Common\AccountApiBundle\Domain\PasswordResetToken $token
): ?\Frontastic\Common\AccountApiBundle\Domain\PasswordResetToken
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$token`|`\Frontastic\Common\AccountApiBundle\Domain\PasswordResetToken`||

Return Value: `?\Frontastic\Common\AccountApiBundle\Domain\PasswordResetToken`

### beforeResetPassword()

```php
public function beforeResetPassword(
    AccountApi $accountApi,
    string $token,
    string $newPassword,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$token`|`string`||
`$newPassword`|`string`||
`$locale`|`string`|`null`|

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
    ?Cart $cart = null,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||
`$cart`|?[`Cart`](../../../../CartApiBundle/Domain/Cart.md)|`null`|
`$locale`|`string`|`null`|

Return Value: `void`

### afterLogin()

```php
public function afterLogin(
    AccountApi $accountApi,
    Account $account
): ?Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: ?[`Account`](../../Account.md)

### beforeRefreshAccount()

```php
public function beforeRefreshAccount(
    AccountApi $accountApi,
    Account $account,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||
`$locale`|`string`|`null`|

Return Value: `void`

### afterRefreshAccount()

```php
public function afterRefreshAccount(
    AccountApi $accountApi,
    Account $account
): ?Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||

Return Value: ?[`Account`](../../Account.md)

### beforeGetAddresses()

```php
public function beforeGetAddresses(
    AccountApi $accountApi,
    Account $account,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||
`$locale`|`string`|`null`|

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
    Account $account,
    Address $address,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||
`$address`|[`Address`](../../Address.md)||
`$locale`|`string`|`null`|

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
    Account $account,
    Address $address,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||
`$address`|[`Address`](../../Address.md)||
`$locale`|`string`|`null`|

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
    Account $account,
    string $addressId,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||
`$addressId`|`string`||
`$locale`|`string`|`null`|

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
    Account $account,
    string $addressId,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||
`$addressId`|`string`||
`$locale`|`string`|`null`|

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
    Account $account,
    string $addressId,
    string $locale = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](../../AccountApi.md)||
`$account`|[`Account`](../../Account.md)||
`$addressId`|`string`||
`$locale`|`string`|`null`|

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

