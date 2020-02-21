#  AccountService

Fully Qualified: [`\Frontastic\Common\AccountApiBundle\Domain\AccountService`](../../../../src/php/AccountApiBundle/Domain/AccountService.php)

## Methods

* [__construct()](#__construct)
* [getSessionFor()](#getsessionfor)
* [sendConfirmationMail()](#sendconfirmationmail)
* [sendPasswordResetMail()](#sendpasswordresetmail)
* [get()](#get)
* [exists()](#exists)
* [confirmEmail()](#confirmemail)
* [login()](#login)
* [create()](#create)
* [update()](#update)
* [updatePassword()](#updatepassword)
* [resetPassword()](#resetpassword)
* [remove()](#remove)

### __construct()

```php
public function __construct(
    AccountApi $accountApi,
    \Frontastic\Common\CoreBundle\Domain\Mailer $mailer
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountApi`|[`AccountApi`](AccountApi.md)||
`$mailer`|`\Frontastic\Common\CoreBundle\Domain\Mailer`||

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
    Account $account
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](Account.md)||

Return Value: `mixed`

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

### exists()

```php
public function exists(
    string $email
): bool
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$email`|`string`||

Return Value: `bool`

### confirmEmail()

```php
public function confirmEmail(
    string $confirmationToken
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$confirmationToken`|`string`||

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
    Account $account,
    string $oldPassword,
    string $newPassword
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](Account.md)||
`$oldPassword`|`string`||
`$newPassword`|`string`||

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

### remove()

```php
public function remove(
    Account $account
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](Account.md)||

Return Value: `mixed`

