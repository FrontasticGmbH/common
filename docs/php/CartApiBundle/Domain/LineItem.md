#  LineItem

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\LineItem`](../../../../src/php/CartApiBundle/Domain/LineItem.php)

**Extends**: [`ApiDataObject`](../../CoreBundle/Domain/ApiDataObject.md)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`lineItemId` | `string` |  | *Yes* | 
`name` | `string` |  | - | 
`type` | `string` |  | *Yes* | 
`count` | `int` |  | *Yes* | 
`price` | `int` |  | *Yes* | 
`discountedPrice` | `int` |  | - | 
`discountTexts` | `array` | `[]` | - | Translatable discount texts, if any are applied
`totalPrice` | `int` |  | *Yes* | 
`currency` | `string` |  | *Yes* | 
`isGift` | `bool` | `false` | *Yes* | 
`dangerousInnerItem` | `mixed` |  | - | Access original object from backend

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
