#  Attribute

**Fully Qualified**: [`\Frontastic\Common\ProjectApiBundle\Domain\Attribute`](../../../../src/php/ProjectApiBundle/Domain/Attribute.php)

**Extends**: [`ApiDataObject`](../../CoreBundle/Domain/ApiDataObject.md)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`attributeId` | `string` |  | *Yes* | 
`type` | `string` |  | *Yes* | TYPE_*
`label` | `array<string, string>|null` |  | - | The labels with the locale as key and the actual label as value. `null` if the label is unknown
`values` | `?array` |  | - | 

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
