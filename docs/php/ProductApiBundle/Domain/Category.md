#  Category

Fully Qualified: [`\Frontastic\Common\ProductApiBundle\Domain\Category`](../../../../src/php/ProductApiBundle/Domain/Category.php)



Property|Type|Default|Description
--------|----|-------|-----------
`categoryId`|`string`|``|
`name`|`string`|``|
`depth`|`int`|``|
`path`|`string`|``|The materialized id path for this category.
`slug`|`string`|``|
`dangerousInnerCategory`|`mixed`|``|Access original object from backend

## Methods

### getPathAsArray

`function getPathAsArray(): array`




**


### getAncestorIds

`function getAncestorIds(): array`




**


### getParentCategoryId

`function getParentCategoryId(): ?string`







