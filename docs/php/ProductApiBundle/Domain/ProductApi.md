# `interface`  ProductApi

Fully Qualified: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi`](../../../../src/php/ProductApiBundle/Domain/ProductApi.php)




## Methods

* [getCategories()](#getCategories)
* [getProductTypes()](#getProductTypes)
* [getProduct()](#getProduct)
* [query()](#query)
* [getDangerousInnerClient()](#getDangerousInnerClient)


### getCategories()


```php
public function getCategories(CategoryQuery $query): array
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`CategoryQuery`](ProductApi/Query/CategoryQuery.md)|``|

Return Value: `array`

### getProductTypes()


```php
public function getProductTypes(ProductTypeQuery $query): array
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`ProductTypeQuery`](ProductApi/Query/ProductTypeQuery.md)|``|

Return Value: `array`

### getProduct()


```php
public function getProduct(ProductQuery $query, string $mode = self::QUERY_SYNC): ?object
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`ProductQuery`](ProductApi/Query/ProductQuery.md)|``|
`$mode`|`string`|`self::QUERY_SYNC`|One of the QUERY_* connstants. Execute the query synchronously or asynchronously?

Return Value: `?object`

### query()


```php
public function query(ProductQuery $query, string $mode = self::QUERY_SYNC): object
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|[`ProductQuery`](ProductApi/Query/ProductQuery.md)|``|
`$mode`|`string`|`self::QUERY_SYNC`|One of the QUERY_* connstants. Execute the query synchronously or asynchronously?

Return Value: `object`

### getDangerousInnerClient()


```php
public function getDangerousInnerClient(): mixed
```


*Get *dangerous* inner client*

This method exists to enable you to use features which are not yet part
of the abstraction layer.

Be aware that any usage of this method might seriously hurt backwards
compatibility and the future abstractions might differ a lot from the
vendor provided abstraction.

Use this with care for features necessary in your customer and talk with
Frontastic about provising an abstraction.


Return Value: `mixed`

