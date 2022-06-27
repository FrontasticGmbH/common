#  Mapper

**Fully Qualified**: [`\Frontastic\Common\AlgoliaBundle\Domain\ProductSearchApi\Mapper`](../../../../../src/php/AlgoliaBundle/Domain/ProductSearchApi/Mapper.php)

## Methods

* [__construct()](#__construct)
* [dataToProducts()](#datatoproducts)
* [dataToFacets()](#datatofacets)
* [dataToAttributes()](#datatoattributes)

### __construct()

```php
public function __construct(
    string $apiVersion = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$apiVersion`|`string`|`null`|

Return Value: `mixed`

### dataToProducts()

```php
public function dataToProducts(
    array $data,
    Query\ProductQuery $query
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$data`|`array`||
`$query`|[`Query`](../../../ProductApiBundle/Domain/ProductApi/Query.md)\ProductQuery||

Return Value: `array`

### dataToFacets()

```php
public function dataToFacets(
    array $data,
    Query\ProductQuery $query = null
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$data`|`array`||
`$query`|[`Query`](../../../ProductApiBundle/Domain/ProductApi/Query.md)\ProductQuery|`null`|

Return Value: `array`

### dataToAttributes()

```php
public function dataToAttributes(
    array $settingsResponse,
    array $searchResponse
): array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$settingsResponse`|`array`||
`$searchResponse`|`array`||

Return Value: `array`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
