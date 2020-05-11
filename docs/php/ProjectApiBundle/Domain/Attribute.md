#  Attribute

**Fully Qualified**: [`\Frontastic\Common\ProjectApiBundle\Domain\Attribute`](../../../../src/php/ProjectApiBundle/Domain/Attribute.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Description
--------|----|-------|-----------
`attributeId`|`string`||
`type`|`string`||TYPE_*
`label`|`array<string, string>|null`||The labels with the locale as key and the actual label as value. `null` if the label is unknown
`values`|`?array`||

