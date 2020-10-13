#  SprykerWishlistApi

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\Wishlist\SprykerWishlistApi`](../../../../../src/php/SprykerBundle/Domain/Wishlist/SprykerWishlistApi.php)

**Extends**: [`SprykerApiBase`](../../BaseApi/SprykerApiBase.md)

**Implements**: [`WishlistApi`](../../../WishlistApiBundle/Domain/WishlistApi.md)

## Methods

* [__construct()](#__construct)
* [registerExpander()](#registerexpander)
* [getWishlist()](#getwishlist)
* [getAnonymous()](#getanonymous)
* [getWishlists()](#getwishlists)
* [create()](#create)
* [addToWishlist()](#addtowishlist)
* [updateLineItem()](#updatelineitem)
* [removeLineItem()](#removelineitem)
* [getDangerousInnerClient()](#getdangerousinnerclient)
* [addMultipleToWishlist()](#addmultipletowishlist)

### __construct()

```php
public function __construct(
    SprykerClientInterface $client,
    MapperResolver $mapperResolver,
    AccountHelper $accountHelper,
    LocaleCreator $localeCreator,
    array $includes = WishlistConstants::RESOURCES_MAIN
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`SprykerClientInterface`](../SprykerClientInterface.md)||
`$mapperResolver`|[`MapperResolver`](../MapperResolver.md)||
`$accountHelper`|[`AccountHelper`](../Account/AccountHelper.md)||
`$localeCreator`|[`LocaleCreator`](../Locale/LocaleCreator.md)||
`$includes`|`array`|`WishlistConstants::RESOURCES_MAIN`|

Return Value: `mixed`

### registerExpander()

```php
public function registerExpander(
    \Frontastic\Common\SprykerBundle\Domain\Wishlist\Expander\WishlistExpanderInterface $expander
): self
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$expander`|`\Frontastic\Common\SprykerBundle\Domain\Wishlist\Expander\WishlistExpanderInterface`||

Return Value: `self`

### getWishlist()

```php
public function getWishlist(
    string $wishlistId,
    string $locale
): Wishlist
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistId`|`string`||
`$locale`|`string`||

Return Value: [`Wishlist`](../../../WishlistApiBundle/Domain/Wishlist.md)

### getAnonymous()

```php
public function getAnonymous(
    string $anonymousId,
    string $locale
): Wishlist
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$anonymousId`|`string`||
`$locale`|`string`||

Return Value: [`Wishlist`](../../../WishlistApiBundle/Domain/Wishlist.md)

### getWishlists()

```php
public function getWishlists(
    string $accountId,
    string $locale
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`||
`$locale`|`string`||

Return Value: `array`

### create()

```php
public function create(
    Wishlist $wishlist,
    string $locale
): Wishlist
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlist`|[`Wishlist`](../../../WishlistApiBundle/Domain/Wishlist.md)||
`$locale`|`string`||

Return Value: [`Wishlist`](../../../WishlistApiBundle/Domain/Wishlist.md)

### addToWishlist()

```php
public function addToWishlist(
    Wishlist $wishlist,
    LineItem $lineItem,
    string $locale
): Wishlist
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlist`|[`Wishlist`](../../../WishlistApiBundle/Domain/Wishlist.md)||
`$lineItem`|[`LineItem`](../../../WishlistApiBundle/Domain/LineItem.md)||
`$locale`|`string`||

Return Value: [`Wishlist`](../../../WishlistApiBundle/Domain/Wishlist.md)

### updateLineItem()

```php
public function updateLineItem(
    Wishlist $wishlist,
    LineItem $lineItem,
    int $count,
    string $locale
): Wishlist
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlist`|[`Wishlist`](../../../WishlistApiBundle/Domain/Wishlist.md)||
`$lineItem`|[`LineItem`](../../../WishlistApiBundle/Domain/LineItem.md)||
`$count`|`int`||
`$locale`|`string`||

Return Value: [`Wishlist`](../../../WishlistApiBundle/Domain/Wishlist.md)

### removeLineItem()

```php
public function removeLineItem(
    Wishlist $wishlist,
    LineItem $lineItem,
    string $locale
): Wishlist
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlist`|[`Wishlist`](../../../WishlistApiBundle/Domain/Wishlist.md)||
`$lineItem`|[`LineItem`](../../../WishlistApiBundle/Domain/LineItem.md)||
`$locale`|`string`||

Return Value: [`Wishlist`](../../../WishlistApiBundle/Domain/Wishlist.md)

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

### addMultipleToWishlist()

```php
public function addMultipleToWishlist(
    Wishlist $wishlist,
    array $lineItems,
    string $locale
): Wishlist
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlist`|[`Wishlist`](../../../WishlistApiBundle/Domain/Wishlist.md)||
`$lineItems`|`array`||
`$locale`|`string`||

Return Value: [`Wishlist`](../../../WishlistApiBundle/Domain/Wishlist.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
