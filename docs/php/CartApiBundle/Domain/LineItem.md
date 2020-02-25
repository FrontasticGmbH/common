#  LineItem

**Fully Qualified**: [`\Frontastic\Common\CartApiBundle\Domain\LineItem`](../../../../src/php/CartApiBundle/Domain/LineItem.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Description
--------|----|-------|-----------
`lineItemId`|`string`||
`name`|`string`||
`type`|`string`||
`custom`|`array`|`[]`|
`count`|`int`||
`price`|`int`||
`discountedPrice`|`int`||
`discountTexts`|`array`|`[]`|
`totalPrice`|`int`||
`currency`|`string`||
`isGift`|`bool`|`false`|
`dangerousInnerItem`|`mixed`||Access original object from backend

