#  LineItem

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\LineItem`](../../../../src/php/CartApiBundle/Domain/LineItem.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`lineItemId` | `string` |  | - | 
`name` | `string` |  | - | 
`type` | `string` |  | - | 
`custom` | `array` | `[]` | - | 
`count` | `int` |  | - | 
`price` | `int` |  | - | 
`discountedPrice` | `int` |  | - | 
`discountTexts` | `array` | `[]` | - | Translatable discount texts, if any are applied
`totalPrice` | `int` |  | - | 
`currency` | `string` |  | - | 
`isGift` | `bool` | `false` | - | 
`dangerousInnerItem` | `mixed` |  | - | Access original object from backend

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
