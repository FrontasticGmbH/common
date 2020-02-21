# `abstract`  BaseImplementation

Fully Qualified: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\LifecycleEventDecorator\BaseImplementation`](../../../../../../src/php/ProductApiBundle/Domain/ProductApi/LifecycleEventDecorator/BaseImplementation.php)

The before* Methods will be obviously called *before* the original method is
executed and will get all the parameters handed over, which the original
method will get called with. Overwriting this method can be useful if you want
to manipulate the handed over parameters by simply manipulating it. These
methods doesn't return anything.

The after* Methods will be oviously called *after* the orignal method is
executed and will get the unwrapped result from the original method handed
over. So if the original methods returns a Promise, the resolved value will be
handed over to this function here. Overwriting this method could be useful if
you want to manipulate the result. These methods need to return null if
nothing should be manipulating, thus will lead to the original result being
returned or they need to return the same data-type as the original method
returns, otherwise you will get Type-Errors at some point.

In order to make this class available to the Lifecycle-Decorator, you will
need to tag your service based on this class with
"productApi.lifecycleEventListener": e.g. by adding the tag inside the
`services.xml` ``` <tag name="productApi.lifecycleEventListener" /> ```

## Methods

* [beforeGetCategories()](#beforegetcategories)
* [afterGetCategories()](#aftergetcategories)
* [beforeGetProductTypes()](#beforegetproducttypes)
* [afterGetProductTypes()](#aftergetproducttypes)
* [beforeGetProduct()](#beforegetproduct)
* [afterGetProduct()](#aftergetproduct)
* [beforeQuery()](#beforequery)
* [afterQuery()](#afterquery)

### beforeGetCategories()

```php
public function beforeGetCategories(
    ProductApi $productApi,
    CategoryQuery $query
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|[`ProductApi`](../../ProductApi.md)||
`$query`|[`CategoryQuery`](../Query/CategoryQuery.md)||

Return Value: `void`

### afterGetCategories()

```php
public function afterGetCategories(
    ProductApi $productApi,
    array $categories
): ?array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|[`ProductApi`](../../ProductApi.md)||
`$categories`|`array`||

Return Value: `?array`

### beforeGetProductTypes()

```php
public function beforeGetProductTypes(
    ProductApi $productApi,
    ProductTypeQuery $query
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|[`ProductApi`](../../ProductApi.md)||
`$query`|[`ProductTypeQuery`](../Query/ProductTypeQuery.md)||

Return Value: `void`

### afterGetProductTypes()

```php
public function afterGetProductTypes(
    ProductApi $productApi,
    array $productTypes
): ?array
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|[`ProductApi`](../../ProductApi.md)||
`$productTypes`|`array`||

Return Value: `?array`

### beforeGetProduct()

```php
public function beforeGetProduct(
    ProductApi $productApi,
    ProductQuery $query,
    string $mode = ProductApi::QUERY_SYNC
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|[`ProductApi`](../../ProductApi.md)||
`$query`|[`ProductQuery`](../Query/ProductQuery.md)||
`$mode`|`string`|`ProductApi::QUERY_SYNC`|

Return Value: `void`

### afterGetProduct()

```php
public function afterGetProduct(
    ProductApi $productApi,
    ?Product $product
): ?Product
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|[`ProductApi`](../../ProductApi.md)||
`$product`|?[`Product`](../../Product.md)||

Return Value: ?[`Product`](../../Product.md)

### beforeQuery()

```php
public function beforeQuery(
    ProductApi $productApi,
    ProductQuery $query,
    string $mode = ProductApi::QUERY_SYNC
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|[`ProductApi`](../../ProductApi.md)||
`$query`|[`ProductQuery`](../Query/ProductQuery.md)||
`$mode`|`string`|`ProductApi::QUERY_SYNC`|

Return Value: `void`

### afterQuery()

```php
public function afterQuery(
    ProductApi $productApi,
    ?Result $result
): ?Result
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|[`ProductApi`](../../ProductApi.md)||
`$result`|?[`Result`](../Result.md)||

Return Value: ?[`Result`](../Result.md)

