# `interface`  AccountApi

Fully Qualified: [`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`](../../../../src/php/AccountApiBundle/Domain/AccountApi.php)




## Methods

* [get()](#get)
* [confirmEmail()](#confirmEmail)
* [create()](#create)
* [verifyEmail()](#verifyEmail)
* [update()](#update)
* [updatePassword()](#updatePassword)
* [generatePasswordResetToken()](#generatePasswordResetToken)
* [resetPassword()](#resetPassword)
* [login()](#login)
* [getAddresses()](#getAddresses)
* [addAddress()](#addAddress)
* [updateAddress()](#updateAddress)
* [removeAddress()](#removeAddress)
* [setDefaultBillingAddress()](#setDefaultBillingAddress)
* [setDefaultShippingAddress()](#setDefaultShippingAddress)
* [getDangerousInnerClient()](#getDangerousInnerClient)


### get()


```php
public function get(string $email): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$email`|`string`|``|

### confirmEmail()


```php
public function confirmEmail(string $token): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`|``|

### create()


```php
public function create([Account](Account.md) $account, ?[Cart](../../CartApiBundle/Domain/Cart.md) $cart = null): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`[Account](Account.md)`|``|
`$cart`|`?[Cart](../../CartApiBundle/Domain/Cart.md)`|`null`|

### verifyEmail()


```php
public function verifyEmail(string $token): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`|``|

### update()


```php
public function update([Account](Account.md) $account): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`[Account](Account.md)`|``|

### updatePassword()


```php
public function updatePassword(string $accountId, string $oldPassword, string $newPassword): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$oldPassword`|`string`|``|
`$newPassword`|`string`|``|

### generatePasswordResetToken()


```php
public function generatePasswordResetToken([Account](Account.md) $account): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`[Account](Account.md)`|``|

### resetPassword()


```php
public function resetPassword(string $token, string $newPassword): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`|``|
`$newPassword`|`string`|``|

### login()


```php
public function login([Account](Account.md) $account, ?[Cart](../../CartApiBundle/Domain/Cart.md) $cart = null): bool
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`[Account](Account.md)`|``|
`$cart`|`?[Cart](../../CartApiBundle/Domain/Cart.md)`|`null`|

### getAddresses()


```php
public function getAddresses(string $accountId): array
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|

### addAddress()


```php
public function addAddress(string $accountId, [Address](Address.md) $address): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$address`|`[Address](Address.md)`|``|

### updateAddress()


```php
public function updateAddress(string $accountId, [Address](Address.md) $address): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$address`|`[Address](Address.md)`|``|

### removeAddress()


```php
public function removeAddress(string $accountId, string $addressId): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$addressId`|`string`|``|

### setDefaultBillingAddress()


```php
public function setDefaultBillingAddress(string $accountId, string $addressId): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$addressId`|`string`|``|

### setDefaultShippingAddress()


```php
public function setDefaultShippingAddress(string $accountId, string $addressId): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$addressId`|`string`|``|

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


