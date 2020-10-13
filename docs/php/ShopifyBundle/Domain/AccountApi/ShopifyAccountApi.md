#  ShopifyAccountApi

**Fully Qualified**: [`\Frontastic\Common\ShopifyBundle\Domain\AccountApi\ShopifyAccountApi`](../../../../../src/php/ShopifyBundle/Domain/AccountApi/ShopifyAccountApi.php)

**Implements**: [`AccountApi`](../../../AccountApiBundle/Domain/AccountApi.md)

## Methods

* [__construct()](#__construct)
* [getSalutations()](#getsalutations)
* [confirmEmail()](#confirmemail)
* [create()](#create)
* [update()](#update)
* [updatePassword()](#updatepassword)
* [generatePasswordResetToken()](#generatepasswordresettoken)
* [resetPassword()](#resetpassword)
* [login()](#login)
* [refreshAccount()](#refreshaccount)
* [getAddresses()](#getaddresses)
* [addAddress()](#addaddress)
* [updateAddress()](#updateaddress)
* [removeAddress()](#removeaddress)
* [setDefaultShippingAddress()](#setdefaultshippingaddress)
* [setDefaultBillingAddress()](#setdefaultbillingaddress)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    ShopifyClient $client,
    ShopifyAccountMapper $accountMapper
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`ShopifyClient`](../ShopifyClient.md)||
`$accountMapper`|[`ShopifyAccountMapper`](../Mapper/ShopifyAccountMapper.md)||

Return Value: `mixed`

### getSalutations()

```php
public function getSalutations(
    string $locale
): ?array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$locale`|`string`||

Return Value: `?array`

### confirmEmail()

```php
public function confirmEmail(
    string $token,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Account`](../../../AccountApiBundle/Domain/Account.md)

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
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||
`$cart`|?[`Cart`](../../../CartApiBundle/Domain/Cart.md)|`null`|
`$locale`|`string`|`null`|

Return Value: [`Account`](../../../AccountApiBundle/Domain/Account.md)

### update()

```php
public function update(
    Account $account,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||
`$locale`|`string`|`null`|

Return Value: [`Account`](../../../AccountApiBundle/Domain/Account.md)

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
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||
`$oldPassword`|`string`||
`$newPassword`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Account`](../../../AccountApiBundle/Domain/Account.md)

### generatePasswordResetToken()

```php
public function generatePasswordResetToken(
    string $email
): PasswordResetToken
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$email`|`string`||

Return Value: [`PasswordResetToken`](../../../AccountApiBundle/Domain/PasswordResetToken.md)

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

Return Value: [`Account`](../../../AccountApiBundle/Domain/Account.md)

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
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||
`$cart`|?[`Cart`](../../../CartApiBundle/Domain/Cart.md)|`null`|
`$locale`|`string`|`null`|

Return Value: ?[`Account`](../../../AccountApiBundle/Domain/Account.md)

### refreshAccount()

```php
public function refreshAccount(
    Account $account,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||
`$locale`|`string`|`null`|

Return Value: [`Account`](../../../AccountApiBundle/Domain/Account.md)

### getAddresses()

```php
public function getAddresses(
    Account $account,
    string $locale = null
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||
`$locale`|`string`|`null`|

Return Value: `array`

### addAddress()

```php
public function addAddress(
    Account $account,
    Address $address,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||
`$address`|[`Address`](../../../AccountApiBundle/Domain/Address.md)||
`$locale`|`string`|`null`|

Return Value: [`Account`](../../../AccountApiBundle/Domain/Account.md)

### updateAddress()

```php
public function updateAddress(
    Account $account,
    Address $address,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||
`$address`|[`Address`](../../../AccountApiBundle/Domain/Address.md)||
`$locale`|`string`|`null`|

Return Value: [`Account`](../../../AccountApiBundle/Domain/Account.md)

### removeAddress()

```php
public function removeAddress(
    Account $account,
    string $addressId,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||
`$addressId`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Account`](../../../AccountApiBundle/Domain/Account.md)

### setDefaultShippingAddress()

```php
public function setDefaultShippingAddress(
    Account $account,
    string $addressId,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||
`$addressId`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Account`](../../../AccountApiBundle/Domain/Account.md)

### setDefaultBillingAddress()

```php
public function setDefaultBillingAddress(
    Account $account,
    string $addressId,
    string $locale = null
): Account
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||
`$addressId`|`string`||
`$locale`|`string`|`null`|

Return Value: [`Account`](../../../AccountApiBundle/Domain/Account.md)

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
