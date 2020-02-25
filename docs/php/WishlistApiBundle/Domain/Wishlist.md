#  Wishlist

**Fully Qualified**: [`\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`](../../../../src/php/WishlistApiBundle/Domain/Wishlist.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Description
--------|----|-------|-----------
`wishlistId`|`string`||
`wishlistVersion`|`string`||
`anonymousId`|`string`||
`accountId`|`string`||
`name`|`string[]`|`[]`|
`lineItems`|[`LineItem`](LineItem.md)[]|`[]`|
`dangerousInnerWishlist`|`mixed`||Access original object from backend
