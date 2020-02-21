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

### beforeGetWishlist

`function beforeGetWishlist(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, string wishlistId, string locale): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$wishlistId`|`string`|``|
`$locale`|`string`|``|

### afterGetWishlist

`function afterGetWishlist(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, \Frontastic\Common\WishlistApiBundle\Domain\Wishlist wishlist): ?\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$wishlist`|`\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`|``|

### beforeGetAnonymous

`function beforeGetAnonymous(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, string anonymousId, string locale): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$anonymousId`|`string`|``|
`$locale`|`string`|``|

### afterGetAnonymous

`function afterGetAnonymous(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, \Frontastic\Common\WishlistApiBundle\Domain\Wishlist wishlist): ?\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$wishlist`|`\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`|``|

### beforeGetWishlists

`function beforeGetWishlists(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, string accountId, string locale): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$accountId`|`string`|``|
`$locale`|`string`|``|

### afterGetWishlists

`function afterGetWishlists(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, array wishlists): ?array`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$wishlists`|`array`|``|

### beforeCreate

`function beforeCreate(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, \Frontastic\Common\WishlistApiBundle\Domain\Wishlist wishlist, string locale): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$wishlist`|`\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`|``|
`$locale`|`string`|``|

### afterCreate

`function afterCreate(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, \Frontastic\Common\WishlistApiBundle\Domain\Wishlist wishlist): ?\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$wishlist`|`\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`|``|

### beforeAddToWishlist

`function beforeAddToWishlist(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, \Frontastic\Common\WishlistApiBundle\Domain\Wishlist wishlist, \Frontastic\Common\WishlistApiBundle\Domain\LineItem lineItem, string locale): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$wishlist`|`\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`|``|
`$lineItem`|`\Frontastic\Common\WishlistApiBundle\Domain\LineItem`|``|
`$locale`|`string`|``|

### afterAddToWishlist

`function afterAddToWishlist(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, \Frontastic\Common\WishlistApiBundle\Domain\Wishlist wishlist): ?\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$wishlist`|`\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`|``|

### beforeAddMultipleToWishlist

`function beforeAddMultipleToWishlist(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, \Frontastic\Common\WishlistApiBundle\Domain\Wishlist wishlist, array lineItems, string locale): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$wishlist`|`\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`|``|
`$lineItems`|`array`|``|
`$locale`|`string`|``|

### afterAddMultipleToWishlist

`function afterAddMultipleToWishlist(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, \Frontastic\Common\WishlistApiBundle\Domain\Wishlist wishlist): ?\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$wishlist`|`\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`|``|

### beforeUpdateLineItem

`function beforeUpdateLineItem(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, \Frontastic\Common\WishlistApiBundle\Domain\Wishlist wishlist, \Frontastic\Common\WishlistApiBundle\Domain\LineItem lineItem, int count, string locale): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$wishlist`|`\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`|``|
`$lineItem`|`\Frontastic\Common\WishlistApiBundle\Domain\LineItem`|``|
`$count`|`int`|``|
`$locale`|`string`|``|

### afterUpdateLineItem

`function afterUpdateLineItem(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, \Frontastic\Common\WishlistApiBundle\Domain\Wishlist wishlist): ?\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$wishlist`|`\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`|``|

### beforeRemoveLineItem

`function beforeRemoveLineItem(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, \Frontastic\Common\WishlistApiBundle\Domain\Wishlist wishlist, \Frontastic\Common\WishlistApiBundle\Domain\LineItem lineItem, string locale): void`






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$wishlist`|`\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`|``|
`$lineItem`|`\Frontastic\Common\WishlistApiBundle\Domain\LineItem`|``|
`$locale`|`string`|``|

### afterRemoveLineItem

`function afterRemoveLineItem(\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi wishlistApi, \Frontastic\Common\WishlistApiBundle\Domain\Wishlist wishlist): ?\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`






Argument|Type|Default|Description
--------|----|-------|-----------
`$wishlistApi`|`\Frontastic\Common\WishlistApiBundle\Domain\WishlistApi`|``|
`$wishlist`|`\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`|``|

