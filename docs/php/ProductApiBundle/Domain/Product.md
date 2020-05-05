#  Product

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\Product`](../../../../src/php/ProductApiBundle/Domain/Product.php)

**Extends**: [`\Kore\DataObject\DataObject`](https://github.com/kore/DataObject)

Property|Type|Default|Description
--------|----|-------|-----------
`productId`|`string`||
`changed`|`?\DateTimeImmutable`||The date and time when this product was last changed or `null` if the date is unknown.
`version`|`?string`||
`name`|`string`||
`slug`|`string`||
`description`|`string`||
`categories`|`string[]`|`[]`|
`variants`|[`Variant`](Variant.md)[]|`[]`|
`dangerousInnerProduct`|`mixed`||Access original object from backend

## Methods

* [__get()](#__get)

### __get()

```php
public function __get(
    mixed $name
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$name`|`mixed`||

Return Value: `mixed`

