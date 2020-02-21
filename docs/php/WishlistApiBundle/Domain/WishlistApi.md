# `interface`  WishlistApi

Fully Qualified: [`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`](../../../../src/php/WishlistApiBundle/Domain/WishlistApi.php)




## Methods

* [getWishlist()](#getWishlist)
* [getAnonymous()](#getAnonymous)
* [getWishlists()](#getWishlists)
* [create()](#create)
* [addToWishlist()](#addToWishlist)
* [addMultipleToWishlist()](#addMultipleToWishlist)
* [updateLineItem()](#updateLineItem)
* [removeLineItem()](#removeLineItem)
* [getDangerousInnerClient()](#getDangerousInnerClient)


### getWishlist()


```php
public function getWishlist(string $wishlistId, string $locale): [Wishlist](Wishlist.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistId`|`string`|``|
`$locale`|`string`|``|

### getAnonymous()


```php
public function getAnonymous(string $anonymousId, string $locale): [Wishlist](Wishlist.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$anonymousId`|`string`|``|
`$locale`|`string`|``|

### getWishlists()


```php
public function getWishlists(string $accountId, string $locale): array
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$accountId`|`string`|``|
`$locale`|`string`|``|

### create()


```php
public function create([Wishlist](Wishlist.md) $wishlist, string $locale): [Wishlist](Wishlist.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlist`|`[Wishlist](Wishlist.md)`|``|
`$locale`|`string`|``|

### addToWishlist()


```php
public function addToWishlist([Wishlist](Wishlist.md) $wishlist, [LineItem](LineItem.md) $lineItem, string $locale): [Wishlist](Wishlist.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlist`|`[Wishlist](Wishlist.md)`|``|
`$lineItem`|`[LineItem](LineItem.md)`|``|
`$locale`|`string`|``|

### addMultipleToWishlist()


```php
public function addMultipleToWishlist([Wishlist](Wishlist.md) $wishlist, array $lineItems, string $locale): [Wishlist](Wishlist.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlist`|`[Wishlist](Wishlist.md)`|``|
`$lineItems`|`array`|``|
`$locale`|`string`|``|

### updateLineItem()


```php
public function updateLineItem([Wishlist](Wishlist.md) $wishlist, [LineItem](LineItem.md) $lineItem, int $count, string $locale): [Wishlist](Wishlist.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlist`|`[Wishlist](Wishlist.md)`|``|
`$lineItem`|`[LineItem](LineItem.md)`|``|
`$count`|`int`|``|
`$locale`|`string`|``|

### removeLineItem()


```php
public function removeLineItem([Wishlist](Wishlist.md) $wishlist, [LineItem](LineItem.md) $lineItem, string $locale): [Wishlist](Wishlist.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlist`|`[Wishlist](Wishlist.md)`|``|
`$lineItem`|`[LineItem](LineItem.md)`|``|
`$locale`|`string`|``|

### getDangerousInnerClient()


```php
public function getDangerousInnerClient(): mixed
```


*Get *dangerous* inner client*

This method exists to enable you to use features which are not yet part
of the abstraction layer.

Be aware that any usage of this method might seriously hurt backwards
compatibility and the future abstractions might differ a lot from the
vendor provided abstraction.

Use this with care for features necessary in your customer and talk with
Frontastic about provising an abstraction.


