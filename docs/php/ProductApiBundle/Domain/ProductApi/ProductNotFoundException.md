#  ProductNotFoundException

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\ProductNotFoundException`](../../../../../src/php/ProductApiBundle/Domain/ProductApi/ProductNotFoundException.php)

**Extends**: [`Exception`](Exception.md)

## Methods

* [byProperty()](#byproperty)
* [byProductId()](#byproductid)
* [bySku()](#bysku)
* [fromQuery()](#fromquery)

### byProperty()

```php
static public function byProperty(
    string $propertyName,
    string $value
): ProductNotFoundException
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$propertyName`|`string`||
`$value`|`string`||

Return Value: [`ProductNotFoundException`](ProductNotFoundException.md)

### byProductId()

```php
static public function byProductId(
    string $productId
): ProductNotFoundException
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$productId`|`string`||

Return Value: [`ProductNotFoundException`](ProductNotFoundException.md)

### bySku()

```php
static public function bySku(
    string $sku
): ProductNotFoundException
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$sku`|`string`||

Return Value: [`ProductNotFoundException`](ProductNotFoundException.md)

### fromQuery()

```php
static public function fromQuery(
    Query\SingleProductQuery $query
): ProductNotFoundException
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`Query`](Query.md)\SingleProductQuery||

Return Value: [`ProductNotFoundException`](ProductNotFoundException.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
