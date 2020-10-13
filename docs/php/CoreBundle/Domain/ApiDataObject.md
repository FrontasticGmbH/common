# `abstract`  ApiDataObject

**Fully Qualified**: [`\Frontastic\Common\CoreBundle\Domain\ApiDataObject`](../../../../src/php/CoreBundle/Domain/ApiDataObject.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`rawApiInput` | `object|array` | `[]` | - | Raw api data from client to backend.
`projectSpecificData` | `mixed` | `[]` | - | Access backend data from and to frontend.

## Methods

* [newWithProjectSpecificData()](#newwithprojectspecificdata)
* [updateWithProjectSpecificData()](#updatewithprojectspecificdata)

### newWithProjectSpecificData()

```php
static public function newWithProjectSpecificData(
    array $values
): self
```

*Creates a new instance of the class called on*

Argument|Type|Default|Description
--------|----|-------|-----------
`$values`|`array`||

Return Value: `self`

### updateWithProjectSpecificData()

```php
public function updateWithProjectSpecificData(
    array $values
): self
```

*Updates instance of the class called on*

Argument|Type|Default|Description
--------|----|-------|-----------
`$values`|`array`||

Return Value: `self`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
