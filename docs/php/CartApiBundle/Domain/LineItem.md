#  LineItem

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\LineItem`](../../../../src/php/CartApiBundle/Domain/LineItem.php)

**Extends**: [`ApiDataObject`](../../CoreBundle/Domain/ApiDataObject.md)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`lineItemId` | `string` |  | *Yes* | 
`name` | `string` |  | - | 
`type` | `string` |  | *Yes* | 
`count` | `int` |  | *Yes* | 
`price` | `int` |  | *Yes* | Price of a single item
`discountedPrice` | `?int` |  | - | Discounted price per item
`discountTexts` | `array` | `[]` | - | Translatable discount texts, if any are applied
`discounts` | [`Discount`](Discount.md)[] | `[]` | - | 
`totalPrice` | `int` |  | *Yes* | Total price, basically $price * $count, also discounted
`currency` | `string` |  | *Yes* | 
`isGift` | `bool` | `false` | *Yes* | 
`dangerousInnerItem` | `mixed` |  | - | Access original object from backend

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
