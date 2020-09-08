#  AccountHelper

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper`](../../../../../src/php/SprykerBundle/Domain/Account/AccountHelper.php)

## Methods

* [__construct()](#__construct)
* [getAccount()](#getaccount)
* [isLoggedIn()](#isloggedin)
* [getAuthHeader()](#getauthheader)
* [getAnonymousHeader()](#getanonymousheader)
* [getAutoHeader()](#getautoheader)

### __construct()

```php
public function __construct(
    \Frontastic\Catwalk\ApiCoreBundle\Domain\ContextService $contextService,
    SessionService $sessionService
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$contextService`|`\Frontastic\Catwalk\ApiCoreBundle\Domain\ContextService`||
`$sessionService`|[`SessionService`](SessionService.md)||

Return Value: `mixed`

### getAccount()

```php
public function getAccount(): Account
```

Return Value: [`Account`](../../../AccountApiBundle/Domain/Account.md)

### isLoggedIn()

```php
public function isLoggedIn(): bool
```

Return Value: `bool`

### getAuthHeader()

```php
public function getAuthHeader(
    ?string $token = null
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`?string`|`null`|

Return Value: `array`

### getAnonymousHeader()

```php
public function getAnonymousHeader(
    ?string $id = null
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$id`|`?string`|`null`|

Return Value: `array`

### getAutoHeader()

```php
public function getAutoHeader(
    ?string $id = null,
    ?string $token = null
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$id`|`?string`|`null`|
`$token`|`?string`|`null`|

Return Value: `array`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
