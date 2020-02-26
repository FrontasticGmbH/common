# `abstract`  BaseImplementation

**Fully Qualified**: [`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi\LifecycleEventDecorator\BaseImplementation`](../../../../../../src/php/WishlistApiBundle/Domain/WishlistApi/LifecycleEventDecorator/BaseImplementation.php)

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

* [beforeGetWishlist()](#beforegetwishlist)
* [afterGetWishlist()](#aftergetwishlist)
* [beforeGetAnonymous()](#beforegetanonymous)
* [afterGetAnonymous()](#aftergetanonymous)
* [beforeGetWishlists()](#beforegetwishlists)
* [afterGetWishlists()](#aftergetwishlists)
* [beforeCreate()](#beforecreate)
* [afterCreate()](#aftercreate)
* [beforeAddToWishlist()](#beforeaddtowishlist)
* [afterAddToWishlist()](#afteraddtowishlist)
* [beforeAddMultipleToWishlist()](#beforeaddmultipletowishlist)
* [afterAddMultipleToWishlist()](#afteraddmultipletowishlist)
* [beforeUpdateLineItem()](#beforeupdatelineitem)
* [afterUpdateLineItem()](#afterupdatelineitem)
* [beforeRemoveLineItem()](#beforeremovelineitem)
* [afterRemoveLineItem()](#afterremovelineitem)

### beforeGetWishlist()

```php
public function beforeGetWishlist(
    WishlistApi $wishlistApi,
    string $wishlistId,
    string $locale
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$wishlistId`|`string`||
`$locale`|`string`||

Return Value: `void`

### afterGetWishlist()

```php
public function afterGetWishlist(
    WishlistApi $wishlistApi,
    Wishlist $wishlist
): ?Wishlist
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$wishlist`|[`Wishlist`](../../Wishlist.md)||

Return Value: ?[`Wishlist`](../../Wishlist.md)

### beforeGetAnonymous()

```php
public function beforeGetAnonymous(
    WishlistApi $wishlistApi,
    string $anonymousId,
    string $locale
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$anonymousId`|`string`||
`$locale`|`string`||

Return Value: `void`

### afterGetAnonymous()

```php
public function afterGetAnonymous(
    WishlistApi $wishlistApi,
    Wishlist $wishlist
): ?Wishlist
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$wishlist`|[`Wishlist`](../../Wishlist.md)||

Return Value: ?[`Wishlist`](../../Wishlist.md)

### beforeGetWishlists()

```php
public function beforeGetWishlists(
    WishlistApi $wishlistApi,
    string $accountId,
    string $locale
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$accountId`|`string`||
`$locale`|`string`||

Return Value: `void`

### afterGetWishlists()

```php
public function afterGetWishlists(
    WishlistApi $wishlistApi,
    array $wishlists
): ?array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$wishlists`|`array`||

Return Value: `?array`

### beforeCreate()

```php
public function beforeCreate(
    WishlistApi $wishlistApi,
    Wishlist $wishlist,
    string $locale
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$wishlist`|[`Wishlist`](../../Wishlist.md)||
`$locale`|`string`||

Return Value: `void`

### afterCreate()

```php
public function afterCreate(
    WishlistApi $wishlistApi,
    Wishlist $wishlist
): ?Wishlist
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$wishlist`|[`Wishlist`](../../Wishlist.md)||

Return Value: ?[`Wishlist`](../../Wishlist.md)

### beforeAddToWishlist()

```php
public function beforeAddToWishlist(
    WishlistApi $wishlistApi,
    Wishlist $wishlist,
    LineItem $lineItem,
    string $locale
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$wishlist`|[`Wishlist`](../../Wishlist.md)||
`$lineItem`|[`LineItem`](../../LineItem.md)||
`$locale`|`string`||

Return Value: `void`

### afterAddToWishlist()

```php
public function afterAddToWishlist(
    WishlistApi $wishlistApi,
    Wishlist $wishlist
): ?Wishlist
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$wishlist`|[`Wishlist`](../../Wishlist.md)||

Return Value: ?[`Wishlist`](../../Wishlist.md)

### beforeAddMultipleToWishlist()

```php
public function beforeAddMultipleToWishlist(
    WishlistApi $wishlistApi,
    Wishlist $wishlist,
    array $lineItems,
    string $locale
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$wishlist`|[`Wishlist`](../../Wishlist.md)||
`$lineItems`|`array`||
`$locale`|`string`||

Return Value: `void`

### afterAddMultipleToWishlist()

```php
public function afterAddMultipleToWishlist(
    WishlistApi $wishlistApi,
    Wishlist $wishlist
): ?Wishlist
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$wishlist`|[`Wishlist`](../../Wishlist.md)||

Return Value: ?[`Wishlist`](../../Wishlist.md)

### beforeUpdateLineItem()

```php
public function beforeUpdateLineItem(
    WishlistApi $wishlistApi,
    Wishlist $wishlist,
    LineItem $lineItem,
    int $count,
    string $locale
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$wishlist`|[`Wishlist`](../../Wishlist.md)||
`$lineItem`|[`LineItem`](../../LineItem.md)||
`$count`|`int`||
`$locale`|`string`||

Return Value: `void`

### afterUpdateLineItem()

```php
public function afterUpdateLineItem(
    WishlistApi $wishlistApi,
    Wishlist $wishlist
): ?Wishlist
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$wishlist`|[`Wishlist`](../../Wishlist.md)||

Return Value: ?[`Wishlist`](../../Wishlist.md)

### beforeRemoveLineItem()

```php
public function beforeRemoveLineItem(
    WishlistApi $wishlistApi,
    Wishlist $wishlist,
    LineItem $lineItem,
    string $locale
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$wishlist`|[`Wishlist`](../../Wishlist.md)||
`$lineItem`|[`LineItem`](../../LineItem.md)||
`$locale`|`string`||

Return Value: `void`

### afterRemoveLineItem()

```php
public function afterRemoveLineItem(
    WishlistApi $wishlistApi,
    Wishlist $wishlist
): ?Wishlist
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|[`WishlistApi`](../../WishlistApi.md)||
`$wishlist`|[`Wishlist`](../../Wishlist.md)||

Return Value: ?[`Wishlist`](../../Wishlist.md)

