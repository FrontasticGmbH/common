#  AccountService

Fully Qualified: [`\Frontastic\Common\AccountApiBundle\Domain\AccountService`](../../../../src/php/AccountApiBundle/Domain/AccountService.php)




## Methods

* [__construct()](#construct)
* [getSessionFor()](#getSessionFor)
* [sendConfirmationMail()](#sendConfirmationMail)
* [sendPasswordResetMail()](#sendPasswordResetMail)
* [get()](#get)
* [exists()](#exists)
* [confirmEmail()](#confirmEmail)
* [login()](#login)
* [create()](#create)
* [update()](#update)
* [updatePassword()](#updatePassword)
* [resetPassword()](#resetPassword)
* [remove()](#remove)


### __construct()


```php
public function __construct([AccountApi](AccountApi.md) $accountApi, \Frontastic\Common\CoreBundle\Domain\Mailer $mailer): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`[AccountApi](AccountApi.md)`|``|
`$mailer`|`\Frontastic\Common\CoreBundle\Domain\Mailer`|``|

### getSessionFor()


```php
public function getSessionFor([Account](Account.md) $account = null): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`[Account](Account.md)`|`null`|

### sendConfirmationMail()


```php
public function sendConfirmationMail([Account](Account.md) $account): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`[Account](Account.md)`|``|

### sendPasswordResetMail()


```php
public function sendPasswordResetMail([Account](Account.md) $account): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`[Account](Account.md)`|``|

### get()


```php
public function get(string $email): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$email`|`string`|``|

### exists()


```php
public function exists(string $email): bool
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$email`|`string`|``|

### confirmEmail()


```php
public function confirmEmail(string $confirmationToken): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$confirmationToken`|`string`|``|

### login()


```php
public function login([Account](Account.md) $account, ?[Cart](../../CartApiBundle/Domain/Cart.md) $cart = null): bool
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`[Account](Account.md)`|``|
`$cart`|`?[Cart](../../CartApiBundle/Domain/Cart.md)`|`null`|

### create()


```php
public function create([Account](Account.md) $account, ?[Cart](../../CartApiBundle/Domain/Cart.md) $cart = null): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`[Account](Account.md)`|``|
`$cart`|`?[Cart](../../CartApiBundle/Domain/Cart.md)`|`null`|

### update()


```php
public function update([Account](Account.md) $account): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`[Account](Account.md)`|``|

### updatePassword()


```php
public function updatePassword([Account](Account.md) $account, string $oldPassword, string $newPassword): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`[Account](Account.md)`|``|
`$oldPassword`|`string`|``|
`$newPassword`|`string`|``|

### resetPassword()


```php
public function resetPassword(string $token, string $newPassword): [Account](Account.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`|``|
`$newPassword`|`string`|``|

### remove()


```php
public function remove([Account](Account.md) $account): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`[Account](Account.md)`|``|

