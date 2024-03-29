#  Account

**Fully Qualified**: [`\Frontastic\Common\AccountApiBundle\Domain\Account`](../../../../src/php/AccountApiBundle/Domain/Account.php)

**Extends**: [`ApiDataObject`](../../CoreBundle/Domain/ApiDataObject.md)

**Implements**: `\Symfony\Component\Security\Core\User\UserInterface`, [`\Serializable`](https://www.php.net/manual/de/class.serializable.php)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`accountId` | `string` |  | *Yes* | 
`email` | `string` |  | *Yes* | 
`salutation` | `string` |  | - | 
`firstName` | `string` |  | - | 
`lastName` | `string` |  | - | 
`birthday` | `\DateTime` |  | - | 
`groups` | [`Group`](Group.md)[] | `[]` | *Yes* | 
`confirmationToken` | `string` |  | - | 
`confirmed` | `bool` | `false` | - | 
`tokenValidUntil` | `\DateTime` |  | - | 
`addresses` | [`Address`](Address.md)[] | `[]` | *Yes* | 
`authToken` | `string|null` |  | - | 
`apiToken` | `string|null` |  | - | 
`dangerousInnerAccount` | `mixed` |  | - | Access original object from backend

## Methods

* [setPassword()](#setpassword)
* [isValidPassword()](#isvalidpassword)
* [getUsername()](#getusername)
* [getRoles()](#getroles)
* [getPassword()](#getpassword)
* [getSalt()](#getsalt)
* [eraseCredentials()](#erasecredentials)
* [assertPermission()](#assertpermission)
* [cleanForSession()](#cleanforsession)
* [serialize()](#serialize)
* [unserialize()](#unserialize)
* [__serialize()](#__serialize)
* [__unserialize()](#__unserialize)

### setPassword()

```php
public function setPassword(
    string $password
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$password`|`string`||

Return Value: `mixed`

### isValidPassword()

```php
public function isValidPassword(
    string $password
): bool
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$password`|`string`||

Return Value: `bool`

### getUsername()

```php
public function getUsername(): mixed
```

Return Value: `mixed`

### getRoles()

```php
public function getRoles(): mixed
```

Return Value: `mixed`

### getPassword()

```php
public function getPassword(): mixed
```

Return Value: `mixed`

### getSalt()

```php
public function getSalt(): mixed
```

Return Value: `mixed`

### eraseCredentials()

```php
public function eraseCredentials(): mixed
```

Return Value: `mixed`

### assertPermission()

```php
public function assertPermission(
    string $required
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$required`|`string`||

Return Value: `mixed`

### cleanForSession()

```php
public function cleanForSession(): Account
```

Return Value: [`Account`](Account.md)

### serialize()

```php
public function serialize(): mixed
```

Return Value: `mixed`

### unserialize()

```php
public function unserialize(
    mixed $serialized
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$serialized`|`mixed`||

Return Value: `mixed`

### __serialize()

```php
public function __serialize(): array
```

Return Value: `array`

### __unserialize()

```php
public function __unserialize(
    array $data
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$data`|`array`||

Return Value: `void`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
