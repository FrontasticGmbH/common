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
public function get(string email): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$email`|`string`|``|

### confirmEmail()


```php
public function confirmEmail(string token): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`|``|

### create()


```php
public function create(\Frontastic\Common\AccountApiBundle\Domain\Account account, ?\Frontastic\Common\CartApiBundle\Domain\Cart cart = null): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|
`$cart`|`?\Frontastic\Common\CartApiBundle\Domain\Cart`|`null`|

### verifyEmail()


```php
public function verifyEmail(string token): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`|``|

### update()


```php
public function update(\Frontastic\Common\AccountApiBundle\Domain\Account account): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### updatePassword()


```php
public function updatePassword(string accountId, string oldPassword, string newPassword): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$oldPassword`|`string`|``|
`$newPassword`|`string`|``|

### generatePasswordResetToken()


```php
public function generatePasswordResetToken(\Frontastic\Common\AccountApiBundle\Domain\Account account): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### resetPassword()


```php
public function resetPassword(string token, string newPassword): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`|``|
`$newPassword`|`string`|``|

### login()


```php
public function login(\Frontastic\Common\AccountApiBundle\Domain\Account account, ?\Frontastic\Common\CartApiBundle\Domain\Cart cart = null): bool
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|
`$cart`|`?\Frontastic\Common\CartApiBundle\Domain\Cart`|`null`|

### getAddresses()


```php
public function getAddresses(string accountId): array
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|

### addAddress()


```php
public function addAddress(string accountId, \Frontastic\Common\AccountApiBundle\Domain\Address address): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$address`|`\Frontastic\Common\AccountApiBundle\Domain\Address`|``|

### updateAddress()


```php
public function updateAddress(string accountId, \Frontastic\Common\AccountApiBundle\Domain\Address address): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$address`|`\Frontastic\Common\AccountApiBundle\Domain\Address`|``|

### removeAddress()


```php
public function removeAddress(string accountId, string addressId): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$addressId`|`string`|``|

### setDefaultBillingAddress()


```php
public function setDefaultBillingAddress(string accountId, string addressId): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$addressId`|`string`|``|

### setDefaultShippingAddress()


```php
public function setDefaultShippingAddress(string accountId, string addressId): \Frontastic\Common\AccountApiBundle\Domain\Account
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


