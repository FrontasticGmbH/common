#  AccountService

**Fully Qualified**: [`\Frontastic\Common\AccountApiBundle\Domain\AccountService`](../../../../src/php/AccountApiBundle/Domain/AccountService.php)

## Methods

* [__construct()](#__construct)
* [getSessionFor()](#getsessionfor)
* [sendConfirmationMail()](#sendconfirmationmail)
* [sendPasswordResetMail()](#sendpasswordresetmail)
* [confirmEmail()](#confirmemail)
* [login()](#login)
* [refresh()](#refresh)
* [create()](#create)
* [update()](#update)
* [updatePassword()](#updatepassword)
* [resetPassword()](#resetpassword)

### __construct()

```php
public function __construct(
    AccountApi $accountApi,
    Mailer $mailer
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](AccountApi.md)||
`$mailer`|[`Mailer`](../../CoreBundle/Domain/Mailer.md)||

Return Value: `mixed`

### getSessionFor()

```php
public function getSessionFor(
    Account $account = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](Account.md)|`null`|

Return Value: `mixed`

### sendConfirmationMail()

```php
public function sendConfirmationMail(
    Account $account
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](Account.md)||

Return Value: `mixed`

### sendPasswordResetMail()

```php
public function sendPasswordResetMail(
    string $email
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$email`|`string`||

Return Value: `mixed`

### confirmEmail()

```php
public function confirmEmail(
    string $confirmationToken,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$confirmationToken`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Account`](Account.md)

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
`$account`|[`Account`](Account.md)||
`$cart`|?[`Cart`](../../CartApiBundle/Domain/Cart.md)|`null`|
`$locale`|`string`|`null`|

Return Value: ?[`Account`](Account.md)

### refresh()

```php
public function refresh(
    Account $account,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](Account.md)||
`$locale`|`string`|`null`|

Return Value: [`Account`](Account.md)

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
`$account`|[`Account`](Account.md)||
`$cart`|?[`Cart`](../../CartApiBundle/Domain/Cart.md)|`null`|
`$locale`|`string`|`null`|

Return Value: [`Account`](Account.md)

### update()

```php
public function update(
    Account $account,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](Account.md)||
`$locale`|`string`|`null`|

Return Value: [`Account`](Account.md)

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
`$account`|[`Account`](Account.md)||
`$oldPassword`|`string`||
`$newPassword`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Account`](Account.md)

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

Return Value: [`Account`](Account.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
