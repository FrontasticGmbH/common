#  ShippingRate

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\ShippingRate`](../../../../src/php/CartApiBundle/Domain/ShippingRate.php)

**Extends**: [`ApiDataObject`](../../CoreBundle/Domain/ApiDataObject.md)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`zoneId` | `string` |  | - | Identifier of the shipping zone.
`name` | `string` |  | - | 
`locations` | ?[`ShippingLocation`](ShippingLocation.md)[] |  | - | Shipping locations this rate applies to.
`currency` | `string` |  | - | 3-letter currency code.
`price` | `int` |  | - | Price in minor currency (e.g. Cent).
`dangerousInnerShippingRate` | `?mixed` |  | - | 

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
