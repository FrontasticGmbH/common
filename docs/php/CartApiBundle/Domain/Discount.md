#  Discount

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\Discount`](../../../../src/php/CartApiBundle/Domain/Discount.php)

**Extends**: [`ApiDataObject`](../../CoreBundle/Domain/ApiDataObject.md)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`discountId` | `string` |  | *Yes* | 
`code` | `string` |  | *Yes* | 
`state` | `string` |  | *Yes* | 
`name` | `array<string, string>` |  | *Yes* | 
`description` | `array<string, string>` |  | - | 
`discountedAmount` | `?int` |  | - | Amount discounted.
`dangerousInnerDiscount` | `mixed` |  | - | Access original object from backend

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
