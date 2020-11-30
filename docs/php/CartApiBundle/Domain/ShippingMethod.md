#  ShippingMethod

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\ShippingMethod`](../../../../src/php/CartApiBundle/Domain/ShippingMethod.php)

**Extends**: [`ApiDataObject`](../../CoreBundle/Domain/ApiDataObject.md)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`shippingMethodId` | `string` |  | - | 
`name` | `string` |  | - | 
`price` | `int` |  | - | 
`description` | `string` |  | - | Localized description of the shipping method.
`rates` | ?[`ShippingRate`](ShippingRate.md)[] |  | - | 
`dangerousInnerShippingMethod` | `?mixed` |  | - | 

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
