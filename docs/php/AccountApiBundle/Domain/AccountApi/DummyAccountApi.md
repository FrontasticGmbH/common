#  DummyAccountApi

**Fully Qualified**: [`\Frontastic\Common\AccountApiBundle\Domain\AccountApi\DummyAccountApi`](../../../../../src/php/AccountApiBundle/Domain/AccountApi/DummyAccountApi.php)

**Implements**: [`AccountApi`](../AccountApi.md)

## Methods

* [getSalutations()](#getsalutations)
* [confirmEmail()](#confirmemail)
* [create()](#create)
* [update()](#update)
* [updatePassword()](#updatepassword)
* [generatePasswordResetToken()](#generatepasswordresettoken)
* [resetPassword()](#resetpassword)
* [login()](#login)
* [refreshAccount()](#refreshaccount)
* [getAddresses()](#getaddresses)
* [addAddress()](#addaddress)
* [updateAddress()](#updateaddress)
* [removeAddress()](#removeaddress)
* [setDefaultBillingAddress()](#setdefaultbillingaddress)
* [setDefaultShippingAddress()](#setdefaultshippingaddress)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### getSalutations()

```php
public function getSalutations(
    string $locale
): ?array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$locale`|`string`||

Return Value: `?array`

### confirmEmail()

```php
public function confirmEmail(
    string $token,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Account`](../Account.md)

### create()

```php
public function create(
    Account $account,
    ?Cart $cart = null,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../Account.md)||
`$cart`|?[`Cart`](../../../CartApiBundle/Domain/Cart.md)|`null`|
`$locale`|`string`|`null`|

Return Value: [`Account`](../Account.md)

### update()

```php
public function update(
    Account $account,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../Account.md)||
`$locale`|`string`|`null`|

Return Value: [`Account`](../Account.md)

### updatePassword()

```php
public function updatePassword(
    Account $account,
    string $oldPassword,
    string $newPassword,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../Account.md)||
`$oldPassword`|`string`||
`$newPassword`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Account`](../Account.md)

### generatePasswordResetToken()

```php
public function generatePasswordResetToken(
    string $email
): PasswordResetToken
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$email`|`string`||

Return Value: [`PasswordResetToken`](../PasswordResetToken.md)

### resetPassword()

```php
public function resetPassword(
    string $token,
    string $newPassword,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`||
`$newPassword`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Account`](../Account.md)

### login()

```php
public function login(
    Account $account,
    ?Cart $cart = null,
    string $locale = null
): ?Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../Account.md)||
`$cart`|?[`Cart`](../../../CartApiBundle/Domain/Cart.md)|`null`|
`$locale`|`string`|`null`|

Return Value: ?[`Account`](../Account.md)

### refreshAccount()

```php
public function refreshAccount(
    Account $account,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../Account.md)||
`$locale`|`string`|`null`|

Return Value: [`Account`](../Account.md)

### getAddresses()

```php
public function getAddresses(
    Account $account,
    string $locale = null
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../Account.md)||
`$locale`|`string`|`null`|

Return Value: `array`

### addAddress()

```php
public function addAddress(
    Account $account,
    Address $address,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../Account.md)||
`$address`|[`Address`](../Address.md)||
`$locale`|`string`|`null`|

Return Value: [`Account`](../Account.md)

### updateAddress()

```php
public function updateAddress(
    Account $account,
    Address $address,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../Account.md)||
`$address`|[`Address`](../Address.md)||
`$locale`|`string`|`null`|

Return Value: [`Account`](../Account.md)

### removeAddress()

```php
public function removeAddress(
    Account $account,
    string $addressId,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../Account.md)||
`$addressId`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Account`](../Account.md)

### setDefaultBillingAddress()

```php
public function setDefaultBillingAddress(
    Account $account,
    string $addressId,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../Account.md)||
`$addressId`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Account`](../Account.md)

### setDefaultShippingAddress()

```php
public function setDefaultShippingAddress(
    Account $account,
    string $addressId,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../Account.md)||
`$addressId`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Account`](../Account.md)

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
