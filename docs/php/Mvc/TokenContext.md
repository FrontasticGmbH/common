# `interface`  TokenContext

**Fully Qualified**: [`\Frontastic\Common\Mvc\TokenContext`](../../../src/php/Mvc/TokenContext.php)

## Methods

* [getCurrentUserId()](#getcurrentuserid)
* [getCurrentUsername()](#getcurrentusername)
* [getCurrentUser()](#getcurrentuser)
* [hasToken()](#hastoken)
* [hasNonAnonymousToken()](#hasnonanonymoustoken)
* [getToken()](#gettoken)
* [isGranted()](#isgranted)
* [assertIsGranted()](#assertisgranted)

### getCurrentUserId()

```php
public function getCurrentUserId(): mixed
```

*If a security context and token exists, retrieve the user id.*

Throws UnauthenticatedUserException when no valid token exists.

Return Value: `mixed`

### getCurrentUsername()

```php
public function getCurrentUsername(): string
```

*If a security context and token exists, retrieve the username.*

Throws UnauthenticatedUserException when no valid token exists.

Return Value: `string`

### getCurrentUser()

```php
public function getCurrentUser(
    string $expectedClass
): \Symfony\Component\Security\Core\User\UserInterface
```

*Get the current User object*

Throws UnauthenticatedUserException when no valid token exists.

Argument|Type|Default|Description
--------|----|-------|-----------
`$expectedClass`|`string`||

Return Value: `\Symfony\Component\Security\Core\User\UserInterface`

### hasToken()

```php
public function hasToken(): bool
```

Return Value: `bool`

### hasNonAnonymousToken()

```php
public function hasNonAnonymousToken(): bool
```

Return Value: `bool`

### getToken()

```php
public function getToken(
    string $expectedClass
): \Symfony\Component\Security\Core\Authentication\Token\TokenInterface
```

*Get the Security Token*

Throws UnauthenticatedUserException when no valid token exists.

Argument|Type|Default|Description
--------|----|-------|-----------
`$expectedClass`|`string`||

Return Value: `\Symfony\Component\Security\Core\Authentication\Token\TokenInterface`

### isGranted()

```php
public function isGranted(
    mixed $attributes,
    ?object $object = null
): bool
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$attributes`|`mixed`||
`$object`|`?object`|`null`|

Return Value: `bool`

### assertIsGranted()

```php
public function assertIsGranted(
    mixed $attributes,
    ?object $object = null
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$attributes`|`mixed`||
`$object`|`?object`|`null`|

Return Value: `void`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
