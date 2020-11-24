#  ShopifyProductMapper

**Fully Qualified**: [`\Frontastic\Common\ShopifyBundle\Domain\Mapper\ShopifyProductMapper`](../../../../../src/php/ShopifyBundle/Domain/Mapper/ShopifyProductMapper.php)

## Methods

* [mapDataToProduct()](#mapdatatoproduct)
* [parseDate()](#parsedate)
* [dataToDangerousInnerData()](#datatodangerousinnerdata)
* [mapDataToVariants()](#mapdatatovariants)
* [mapDataToVariant()](#mapdatatovariant)
* [mapDataToPriceValue()](#mapdatatopricevalue)
* [mapDataToVariantAttributes()](#mapdatatovariantattributes)
* [mapDataToFacets()](#mapdatatofacets)
* [mapDataToProductAttributes()](#mapdatatoproductattributes)
* [toFilterString()](#tofilterstring)

### mapDataToProduct()

```php
public function mapDataToProduct(
    array $productData,
    Query $query = null
): Product
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$productData`|`array`||
`$query`|[`Query`](../../../ProductApiBundle/Domain/ProductApi/Query.md)|`null`|

Return Value: [`Product`](../../../ProductApiBundle/Domain/Product.md)

### parseDate()

```php
public function parseDate(
    string $string
): \DateTimeImmutable
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$string`|`string`||

Return Value: `\DateTimeImmutable`

### dataToDangerousInnerData()

```php
public function dataToDangerousInnerData(
    array $rawData,
    Query $query = null
): ?array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$rawData`|`array`||
`$query`|[`Query`](../../../ProductApiBundle/Domain/ProductApi/Query.md)|`null`|

Return Value: `?array`

### mapDataToVariants()

```php
public function mapDataToVariants(
    array $variantsData,
    Query $query = null
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$variantsData`|`array`||
`$query`|[`Query`](../../../ProductApiBundle/Domain/ProductApi/Query.md)|`null`|

Return Value: `array`

### mapDataToVariant()

```php
public function mapDataToVariant(
    array $variantData,
    Query $query = null
): Variant
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$variantData`|`array`||
`$query`|[`Query`](../../../ProductApiBundle/Domain/ProductApi/Query.md)|`null`|

Return Value: [`Variant`](../../../ProductApiBundle/Domain/Variant.md)

### mapDataToPriceValue()

```php
public function mapDataToPriceValue(
    array $data
): int
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$data`|`array`||

Return Value: `int`

### mapDataToVariantAttributes()

```php
public function mapDataToVariantAttributes(
    array $variantData
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$variantData`|`array`||

Return Value: `array`

### mapDataToFacets()

```php
public function mapDataToFacets(
    array $productsData
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$productsData`|`array`||

Return Value: `array`

### mapDataToProductAttributes()

```php
public function mapDataToProductAttributes(
    array $productAttributesData
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$productAttributesData`|`array`||

Return Value: `array`

### toFilterString()

```php
public function toFilterString(
    Query\Filter $queryFilter
): string
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$queryFilter`|[`Query`](../../../ProductApiBundle/Domain/ProductApi/Query.md)\Filter||

Return Value: `string`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
