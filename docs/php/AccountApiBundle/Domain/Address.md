#  Address

**Fully Qualified**: [`\Frontastic\Common\AccountApiBundle\Domain\Address`](../../../../src/php/AccountApiBundle/Domain/Address.php)

**Extends**: [`ApiDataObject`](../../CoreBundle/Domain/ApiDataObject.md)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`addressId` | `string` |  | - | 
`salutation` | `string` |  | - | 
`firstName` | `string` |  | - | 
`lastName` | `string` |  | - | 
`streetName` | `string` |  | - | 
`streetNumber` | `string` |  | - | 
`additionalStreetInfo` | `string` |  | - | 
`additionalAddressInfo` | `string` |  | - | 
`postalCode` | `string` |  | - | 
`city` | `string` |  | - | 
`country` | `string` |  | - | 2 letter ISO code (https://en.wikipedia.org/wiki/ISO_3166-1_alpha-2)
`state` | `string` |  | - | 
`phone` | `string` |  | - | 
`isDefaultBillingAddress` | `bool` | `false` | - | 
`isDefaultShippingAddress` | `bool` | `false` | - | 
`dangerousInnerAddress` | `mixed` |  | - | Access original object from backend.

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
