#  Category

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\Category`](../../../../src/php/ProductApiBundle/Domain/Category.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`categoryId` | `string` |  | - | 
`name` | `string` |  | - | 
`depth` | `int` |  | - | 
`path` | `string` |  | - | The materialized id path for this category.
`slug` | `string` |  | - | 
`dangerousInnerCategory` | `mixed` |  | - | Access original object from backend

## Methods

* [getPathAsArray()](#getpathasarray)
* [getAncestorIds()](#getancestorids)
* [getParentCategoryId()](#getparentcategoryid)

### getPathAsArray()

```php
public function getPathAsArray(): array
```

Return Value: `array`

### getAncestorIds()

```php
public function getAncestorIds(): array
```

Return Value: `array`

### getParentCategoryId()

```php
public function getParentCategoryId(): ?string
```

Return Value: `?string`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
