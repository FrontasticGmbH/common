#  Account

Fully Qualified: [`\Frontastic\Common\AccountApiBundle\Domain\Account`](../../../../src/php/AccountApiBundle/Domain/Account.php)



Property|Type|Default|Description
--------|----|-------|-----------
`accountId`|`string`|``|
`email`|`string`|``|
`salutation`|`string`|``|
`firstName`|`string`|``|
`lastName`|`string`|``|
`birthday`|`\DateTime`|``|
`data`|`array`|`[]`|
`groups`|`\Frontastic\Common\AccountApiBundle\Domain\Group[]`|`[]`|
`confirmationToken`|`string`|``|
`confirmed`|`string`|`false`|
`tokenValidUntil`|`\Frontastic\Common\AccountApiBundle\Domain\DateTime`|``|
`addresses`|`\Frontastic\Common\AccountApiBundle\Domain\Address[]`|`[]`|
`dangerousInnerAccount`|`mixed`|``|Access original object from backend

## Methods

* [setPassword()](#setPassword)
* [isValidPassword()](#isValidPassword)
* [getUsername()](#getUsername)
* [getRoles()](#getRoles)
* [getPassword()](#getPassword)
* [getSalt()](#getSalt)
* [eraseCredentials()](#eraseCredentials)
* [assertPermission()](#assertPermission)
* [cleanForSession()](#cleanForSession)
* [serialize()](#serialize)
* [unserialize()](#unserialize)


### setPassword()


```php
public function setPassword(string password): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$password`|`string`|``|

### isValidPassword()


```php
public function isValidPassword(string password): bool
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$password`|`string`|``|

### getUsername()


```php
public function getUsername(): mixed
```







### getRoles()


```php
public function getRoles(): mixed
```







### getPassword()


```php
public function getPassword(): mixed
```







### getSalt()


```php
public function getSalt(): mixed
```







### eraseCredentials()


```php
public function eraseCredentials(): mixed
```







### assertPermission()


```php
public function assertPermission(string required): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$required`|`string`|``|

### cleanForSession()


```php
public function cleanForSession(): \Frontastic\Common\AccountApiBundle\Domain\Account
```







### serialize()


```php
public function serialize(): mixed
```







### unserialize()


```php
public function unserialize(mixed serialized): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$serialized`|`mixed`|``|

