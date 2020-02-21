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
public function __construct(\Frontastic\Common\AccountApiBundle\Domain\AccountApi accountApi, \Frontastic\Common\CoreBundle\Domain\Mailer mailer): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|`\Frontastic\Common\AccountApiBundle\Domain\AccountApi`|``|
`$mailer`|`\Frontastic\Common\CoreBundle\Domain\Mailer`|``|

### getSessionFor()


```php
public function getSessionFor(\Frontastic\Common\AccountApiBundle\Domain\Account account = null): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|`null`|

### sendConfirmationMail()


```php
public function sendConfirmationMail(\Frontastic\Common\AccountApiBundle\Domain\Account account): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### sendPasswordResetMail()


```php
public function sendPasswordResetMail(\Frontastic\Common\AccountApiBundle\Domain\Account account): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### get()


```php
public function get(string email): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$email`|`string`|``|

### exists()


```php
public function exists(string email): bool
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$email`|`string`|``|

### confirmEmail()


```php
public function confirmEmail(string confirmationToken): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$confirmationToken`|`string`|``|

### login()


```php
public function login(\Frontastic\Common\AccountApiBundle\Domain\Account account, ?\Frontastic\Common\CartApiBundle\Domain\Cart cart = null): bool
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|
`$cart`|`?\Frontastic\Common\CartApiBundle\Domain\Cart`|`null`|

### create()


```php
public function create(\Frontastic\Common\AccountApiBundle\Domain\Account account, ?\Frontastic\Common\CartApiBundle\Domain\Cart cart = null): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|
`$cart`|`?\Frontastic\Common\CartApiBundle\Domain\Cart`|`null`|

### update()


```php
public function update(\Frontastic\Common\AccountApiBundle\Domain\Account account): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

### updatePassword()


```php
public function updatePassword(\Frontastic\Common\AccountApiBundle\Domain\Account account, string oldPassword, string newPassword): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|
`$oldPassword`|`string`|``|
`$newPassword`|`string`|``|

### resetPassword()


```php
public function resetPassword(string token, string newPassword): \Frontastic\Common\AccountApiBundle\Domain\Account
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`|``|
`$newPassword`|`string`|``|

### remove()


```php
public function remove(\Frontastic\Common\AccountApiBundle\Domain\Account account): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|`\Frontastic\Common\AccountApiBundle\Domain\Account`|``|

