#  Wishlist

**Fully Qualified**: [`\Frontastic\Common\WishlistApiBundle\Domain\Wishlist`](../../../../src/php/WishlistApiBundle/Domain/Wishlist.php)

**Extends**: [`ApiDataObject`](../../CoreBundle/Domain/ApiDataObject.md)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`wishlistId` | `string` |  | *Yes* | 
`wishlistVersion` | `string` |  | - | 
`anonymousId` | `string` |  | - | 
`accountId` | `string` |  | - | 
`name` | `string[]` | `[]` | *Yes* | 
`lineItems` | [`LineItem`](LineItem.md)[] | `[]` | *Yes* | 
`dangerousInnerWishlist` | `mixed` |  | - | Access original object from backend

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
