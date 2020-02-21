# `abstract`  BaseImplementation

Fully Qualified: [`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi\LifecycleEventDecorator\BaseImplementation`](../../../../../../src/php/WishlistApiBundle/Domain/WishlistApi/LifecycleEventDecorator/BaseImplementation.php)


The before* Methods will be obviously called *before* the original method is
executed and will get all the parameters handed over, which the original
method will get called with. Overwriting this method can be useful if you want
to manipulate the handed over parameters by simply manipulating it. These
methods doesn't return anything.

The after* Methods will be oviously called *after* the orignal method is
executed and will get the unwrapped result from the original method handed
over. So if the original methods returns a Promise, the resolved value will be
handed over to this function here. Overwriting this method could be useful if
you want to manipulate the result. These methods need to return null if
nothing should be manipulating, thus will lead to the original result being
returned or they need to return the same data-type as the original method
returns, otherwise you will get Type-Errors at some point.

In order to make this class available to the Lifecycle-Decorator, you will
need to tag your service based on this class with
"wishlistApi.lifecycleEventListener": e.g. by adding the tag inside the
`services.xml` ``` <tag name="wishlistApi.lifecycleEventListener" /> ```

## Methods

* [beforeGetWishlist()](#beforeGetWishlist)
* [afterGetWishlist()](#afterGetWishlist)
* [beforeGetAnonymous()](#beforeGetAnonymous)
* [afterGetAnonymous()](#afterGetAnonymous)
* [beforeGetWishlists()](#beforeGetWishlists)
* [afterGetWishlists()](#afterGetWishlists)
* [beforeCreate()](#beforeCreate)
* [afterCreate()](#afterCreate)
* [beforeAddToWishlist()](#beforeAddToWishlist)
* [afterAddToWishlist()](#afterAddToWishlist)
* [beforeAddMultipleToWishlist()](#beforeAddMultipleToWishlist)
* [afterAddMultipleToWishlist()](#afterAddMultipleToWishlist)
* [beforeUpdateLineItem()](#beforeUpdateLineItem)
* [afterUpdateLineItem()](#afterUpdateLineItem)
* [beforeRemoveLineItem()](#beforeRemoveLineItem)
* [afterRemoveLineItem()](#afterRemoveLineItem)


### beforeGetWishlist()


```php
public function beforeGetWishlist([WishlistApi](../../WishlistApi.md) $wishlistApi, string $wishlistId, string $locale): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$wishlistId`|`string`|``|
`$locale`|`string`|``|

### afterGetWishlist()


```php
public function afterGetWishlist([WishlistApi](../../WishlistApi.md) $wishlistApi, [Wishlist](../../Wishlist.md) $wishlist): ?[Wishlist](../../Wishlist.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$wishlist`|`[Wishlist](../../Wishlist.md)`|``|

### beforeGetAnonymous()


```php
public function beforeGetAnonymous([WishlistApi](../../WishlistApi.md) $wishlistApi, string $anonymousId, string $locale): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$anonymousId`|`string`|``|
`$locale`|`string`|``|

### afterGetAnonymous()


```php
public function afterGetAnonymous([WishlistApi](../../WishlistApi.md) $wishlistApi, [Wishlist](../../Wishlist.md) $wishlist): ?[Wishlist](../../Wishlist.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$wishlist`|`[Wishlist](../../Wishlist.md)`|``|

### beforeGetWishlists()


```php
public function beforeGetWishlists([WishlistApi](../../WishlistApi.md) $wishlistApi, string $accountId, string $locale): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$accountId`|`string`|``|
`$locale`|`string`|``|

### afterGetWishlists()


```php
public function afterGetWishlists([WishlistApi](../../WishlistApi.md) $wishlistApi, array $wishlists): ?array
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$wishlists`|`array`|``|

### beforeCreate()


```php
public function beforeCreate([WishlistApi](../../WishlistApi.md) $wishlistApi, [Wishlist](../../Wishlist.md) $wishlist, string $locale): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$wishlist`|`[Wishlist](../../Wishlist.md)`|``|
`$locale`|`string`|``|

### afterCreate()


```php
public function afterCreate([WishlistApi](../../WishlistApi.md) $wishlistApi, [Wishlist](../../Wishlist.md) $wishlist): ?[Wishlist](../../Wishlist.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$wishlist`|`[Wishlist](../../Wishlist.md)`|``|

### beforeAddToWishlist()


```php
public function beforeAddToWishlist([WishlistApi](../../WishlistApi.md) $wishlistApi, [Wishlist](../../Wishlist.md) $wishlist, [LineItem](../../LineItem.md) $lineItem, string $locale): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$wishlist`|`[Wishlist](../../Wishlist.md)`|``|
`$lineItem`|`[LineItem](../../LineItem.md)`|``|
`$locale`|`string`|``|

### afterAddToWishlist()


```php
public function afterAddToWishlist([WishlistApi](../../WishlistApi.md) $wishlistApi, [Wishlist](../../Wishlist.md) $wishlist): ?[Wishlist](../../Wishlist.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$wishlist`|`[Wishlist](../../Wishlist.md)`|``|

### beforeAddMultipleToWishlist()


```php
public function beforeAddMultipleToWishlist([WishlistApi](../../WishlistApi.md) $wishlistApi, [Wishlist](../../Wishlist.md) $wishlist, array $lineItems, string $locale): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$wishlist`|`[Wishlist](../../Wishlist.md)`|``|
`$lineItems`|`array`|``|
`$locale`|`string`|``|

### afterAddMultipleToWishlist()


```php
public function afterAddMultipleToWishlist([WishlistApi](../../WishlistApi.md) $wishlistApi, [Wishlist](../../Wishlist.md) $wishlist): ?[Wishlist](../../Wishlist.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$wishlist`|`[Wishlist](../../Wishlist.md)`|``|

### beforeUpdateLineItem()


```php
public function beforeUpdateLineItem([WishlistApi](../../WishlistApi.md) $wishlistApi, [Wishlist](../../Wishlist.md) $wishlist, [LineItem](../../LineItem.md) $lineItem, int $count, string $locale): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$wishlist`|`[Wishlist](../../Wishlist.md)`|``|
`$lineItem`|`[LineItem](../../LineItem.md)`|``|
`$count`|`int`|``|
`$locale`|`string`|``|

### afterUpdateLineItem()


```php
public function afterUpdateLineItem([WishlistApi](../../WishlistApi.md) $wishlistApi, [Wishlist](../../Wishlist.md) $wishlist): ?[Wishlist](../../Wishlist.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$wishlist`|`[Wishlist](../../Wishlist.md)`|``|

### beforeRemoveLineItem()


```php
public function beforeRemoveLineItem([WishlistApi](../../WishlistApi.md) $wishlistApi, [Wishlist](../../Wishlist.md) $wishlist, [LineItem](../../LineItem.md) $lineItem, string $locale): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$wishlist`|`[Wishlist](../../Wishlist.md)`|``|
`$lineItem`|`[LineItem](../../LineItem.md)`|``|
`$locale`|`string`|``|

### afterRemoveLineItem()


```php
public function afterRemoveLineItem([WishlistApi](../../WishlistApi.md) $wishlistApi, [Wishlist](../../Wishlist.md) $wishlist): ?[Wishlist](../../Wishlist.md)
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`[WishlistApi](../../WishlistApi.md)`|``|
`$wishlist`|`[Wishlist](../../Wishlist.md)`|``|

