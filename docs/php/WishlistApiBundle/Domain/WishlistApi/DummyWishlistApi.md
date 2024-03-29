#  DummyWishlistApi

**Fully Qualified**: [`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi\DummyWishlistApi`](../../../../../src/php/WishlistApiBundle/Domain/WishlistApi/DummyWishlistApi.php)

**Implements**: [`WishlistApi`](../WishlistApi.md)

## Methods

* [getWishlist()](#getwishlist)
* [getAnonymous()](#getanonymous)
* [getWishlists()](#getwishlists)
* [create()](#create)
* [addToWishlist()](#addtowishlist)
* [addMultipleToWishlist()](#addmultipletowishlist)
* [updateLineItem()](#updatelineitem)
* [removeLineItem()](#removelineitem)
* [getDangerousInnerClient()](#getdangerousinnerclient)

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

Return Value: [`Wishlist`](../Wishlist.md)

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

Return Value: [`Wishlist`](../Wishlist.md)

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
`$wishlist`|[`Wishlist`](../Wishlist.md)||
`$locale`|`string`||

Return Value: [`Wishlist`](../Wishlist.md)

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
`$wishlist`|[`Wishlist`](../Wishlist.md)||
`$lineItem`|[`LineItem`](../LineItem.md)||
`$locale`|`string`||

Return Value: [`Wishlist`](../Wishlist.md)

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
`$wishlist`|[`Wishlist`](../Wishlist.md)||
`$lineItems`|`array`||
`$locale`|`string`||

Return Value: [`Wishlist`](../Wishlist.md)

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
`$wishlist`|[`Wishlist`](../Wishlist.md)||
`$lineItem`|[`LineItem`](../LineItem.md)||
`$count`|`int`||
`$locale`|`string`||

Return Value: [`Wishlist`](../Wishlist.md)

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
`$wishlist`|[`Wishlist`](../Wishlist.md)||
`$lineItem`|[`LineItem`](../LineItem.md)||
`$locale`|`string`||

Return Value: [`Wishlist`](../Wishlist.md)

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
