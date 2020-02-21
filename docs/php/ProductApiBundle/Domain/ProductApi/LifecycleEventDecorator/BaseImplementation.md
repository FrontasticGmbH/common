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

* [beforeGetCategories()](#beforeGetCategories)
* [afterGetCategories()](#afterGetCategories)
* [beforeGetProductTypes()](#beforeGetProductTypes)
* [afterGetProductTypes()](#afterGetProductTypes)
* [beforeGetProduct()](#beforeGetProduct)
* [afterGetProduct()](#afterGetProduct)
* [beforeQuery()](#beforeQuery)
* [afterQuery()](#afterQuery)


### beforeGetCategories()


```php
public function beforeGetCategories(\Frontastic\Common\ProductApiBundle\Domain\ProductApi productApi, \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery query): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi`|``|
`$query`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery`|``|

### afterGetCategories()


```php
public function afterGetCategories(\Frontastic\Common\ProductApiBundle\Domain\ProductApi productApi, array categories): ?array
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi`|``|
`$categories`|`array`|``|

### beforeGetProductTypes()


```php
public function beforeGetProductTypes(\Frontastic\Common\ProductApiBundle\Domain\ProductApi productApi, \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery query): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi`|``|
`$query`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery`|``|

### afterGetProductTypes()


```php
public function afterGetProductTypes(\Frontastic\Common\ProductApiBundle\Domain\ProductApi productApi, array productTypes): ?array
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi`|``|
`$productTypes`|`array`|``|

### beforeGetProduct()


```php
public function beforeGetProduct(\Frontastic\Common\ProductApiBundle\Domain\ProductApi productApi, \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery query, string mode = ProductApi::QUERY_SYNC): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi`|``|
`$query`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery`|``|
`$mode`|`string`|`ProductApi::QUERY_SYNC`|

### afterGetProduct()


```php
public function afterGetProduct(\Frontastic\Common\ProductApiBundle\Domain\ProductApi productApi, ?\Frontastic\Common\ProductApiBundle\Domain\Product product): ?\Frontastic\Common\ProductApiBundle\Domain\Product
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi`|``|
`$product`|`?\Frontastic\Common\ProductApiBundle\Domain\Product`|``|

### beforeQuery()


```php
public function beforeQuery(\Frontastic\Common\ProductApiBundle\Domain\ProductApi productApi, \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery query, string mode = ProductApi::QUERY_SYNC): void
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi`|``|
`$query`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery`|``|
`$mode`|`string`|`ProductApi::QUERY_SYNC`|

### afterQuery()


```php
public function afterQuery(\Frontastic\Common\ProductApiBundle\Domain\ProductApi productApi, ?\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result result): ?\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$productApi`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi`|``|
`$result`|`?\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result`|``|

