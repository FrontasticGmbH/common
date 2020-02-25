# `interface`  AccountApi

**Fully Qualified**: [`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`](../../../../src/php/AccountApiBundle/Domain/AccountApi.php)

## Methods

* [get()](#get)
* [confirmEmail()](#confirmemail)
* [create()](#create)
* [verifyEmail()](#verifyemail)
* [update()](#update)
* [updatePassword()](#updatepassword)
* [generatePasswordResetToken()](#generatepasswordresettoken)
* [resetPassword()](#resetpassword)
* [login()](#login)
* [getAddresses()](#getaddresses)
* [addAddress()](#addaddress)
* [updateAddress()](#updateaddress)
* [removeAddress()](#removeaddress)
* [setDefaultBillingAddress()](#setdefaultbillingaddress)
* [setDefaultShippingAddress()](#setdefaultshippingaddress)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### get()

```php
public function get(
    string $email
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$email`|`string`||

Return Value: [`Account`](Account.md)

### confirmEmail()

```php
public function confirmEmail(
    string $token
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`||

Return Value: [`Account`](Account.md)

### create()

```php
public function create(
    Account $account,
    ?Cart $cart = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](Account.md)||
`$cart`|?[`Cart`](../../CartApiBundle/Domain/Cart.md)|`null`|

Return Value: [`Account`](Account.md)

### verifyEmail()

```php
public function verifyEmail(
    string $token
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`||

Return Value: [`Account`](Account.md)

### update()

```php
public function update(
    Account $account
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](Account.md)||

Return Value: [`Account`](Account.md)

### updatePassword()

```php
public function updatePassword(
    string $accountId,
    string $oldPassword,
    string $newPassword
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`||
`$oldPassword`|`string`||
`$newPassword`|`string`||

Return Value: [`Account`](Account.md)

### generatePasswordResetToken()

```php
public function generatePasswordResetToken(
    Account $account
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](Account.md)||

Return Value: [`Account`](Account.md)

### resetPassword()

```php
public function resetPassword(
    string $token,
    string $newPassword
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`||
`$newPassword`|`string`||

Return Value: [`Account`](Account.md)

### login()

```php
public function login(
    Account $account,
    ?Cart $cart = null
): bool
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](Account.md)||
`$cart`|?[`Cart`](../../CartApiBundle/Domain/Cart.md)|`null`|

Return Value: `bool`

### getAddresses()

```php
public function getAddresses(
    string $accountId
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`||

Return Value: `array`

### addAddress()

```php
public function addAddress(
    string $accountId,
    Address $address
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`||
`$address`|[`Address`](Address.md)||

Return Value: [`Account`](Account.md)

### updateAddress()

```php
public function updateAddress(
    string $accountId,
    Address $address
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`||
`$address`|[`Address`](Address.md)||

Return Value: [`Account`](Account.md)

### removeAddress()

```php
public function removeAddress(
    string $accountId,
    string $addressId
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`||
`$addressId`|`string`||

Return Value: [`Account`](Account.md)

### setDefaultBillingAddress()

```php
public function setDefaultBillingAddress(
    string $accountId,
    string $addressId
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`||
`$addressId`|`string`||

Return Value: [`Account`](Account.md)

### setDefaultShippingAddress()

```php
public function setDefaultShippingAddress(
    string $accountId,
    string $addressId
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`||
`$addressId`|`string`||

Return Value: [`Account`](Account.md)

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

*Get *dangerous* inner client*

This method exists to enable you to use features which are not yet part
of the abstraction layer.

Be aware that any usage of this method might seriously hurt backwards
compatibility and the future abstractions might differ a lot from the
vendor provided abstraction.

Use this with care for features necessary in your customer and talk with
Frontastic about provising an abstraction.

Return Value: `mixed`

