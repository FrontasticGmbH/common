#  Variant

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\Variant`](../../../../src/php/ProductApiBundle/Domain/Variant.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`id` | `string` |  | *Yes* | 
`sku` | `string` |  | *Yes* | 
`groupId` | `string` |  | - | 
`price` | `int` |  | *Yes* | The product price in cent
`discountedPrice` | `?int` |  | - | If a discount is applied to the product, this contains the reduced value.
`discounts` | `mixed` | `[]` | - | Array of discount descriptions
`currency` | `string` |  | - | A three letter currency code in upper case.
`attributes` | `array` | `[]` | - | 
`images` | `array` | `[]` | - | 
`isOnStock` | `bool` | `true` | - | 
`dangerousInnerVariant` | `mixed` |  | - | Access original object from backend

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
