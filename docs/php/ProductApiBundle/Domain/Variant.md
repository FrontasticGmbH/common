#  Variant

Fully Qualified: [`\Frontastic\Common\ProductApiBundle\Domain\Variant`](../../../../src/php/ProductApiBundle/Domain/Variant.php)



Property|Type|Default|Description
--------|----|-------|-----------
`id`|`string`|``|
`sku`|`string`|``|
`groupId`|`string`|``|
`price`|`int`|``|The product price in cent
`discountedPrice`|`int|null`|``|If a discount is applied to the product, this contains the reduced value.
`discounts`|`mixed`|`[]`|Array of discount descriptions
`currency`|`string`|``|A three letter currency code in upper case.
`attributes`|`array`|`[]`|
`images`|`array`|`[]`|
`isOnStock`|`bool`|`true`|
`dangerousInnerVariant`|`mixed`|``|Access original object from backend

