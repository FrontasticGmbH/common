#  SprykerCartApi

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCartApi`](../../../../../src/php/SprykerBundle/Domain/Cart/SprykerCartApi.php)

**Extends**: [`CartApiBase`](../../../CartApiBundle/Domain/CartApiBase.md)

## Methods

* [__construct()](#__construct)
* [setAccount()](#setaccount)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    SprykerClientInterface $client,
    MapperResolver $mapperResolver,
    AccountHelper $accountHelper,
    \Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\SprykerCartInterface $guestCart,
    \Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\SprykerCartInterface $customerCart,
    LocaleCreator $localeCreator,
    SprykerUrlAppender $urlAppender,
    array $orderIncludes = [],
    ?string $defaultLanguage = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`SprykerClientInterface`](../SprykerClientInterface.md)||
`$mapperResolver`|[`MapperResolver`](../MapperResolver.md)||
`$accountHelper`|[`AccountHelper`](../Account/AccountHelper.md)||
`$guestCart`|`\Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\SprykerCartInterface`||
`$customerCart`|`\Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\SprykerCartInterface`||
`$localeCreator`|[`LocaleCreator`](../Locale/LocaleCreator.md)||
`$urlAppender`|[`SprykerUrlAppender`](../SprykerUrlAppender.md)||
`$orderIncludes`|`array`|`[]`|
`$defaultLanguage`|`?string`|`null`|

Return Value: `mixed`

### setAccount()

```php
public function setAccount(
    Cart $cart,
    Account $account
): Cart
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$cart`|[`Cart`](../../../CartApiBundle/Domain/Cart.md)||
`$account`|[`Account`](../../../AccountApiBundle/Domain/Account.md)||

Return Value: [`Cart`](../../../CartApiBundle/Domain/Cart.md)

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
