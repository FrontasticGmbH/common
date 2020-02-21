#  ProductQueryFactory

Fully Qualified: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQueryFactory`](../../../../../../src/php/ProductApiBundle/Domain/ProductApi/Query/ProductQueryFactory.php)




## Methods

* [queryFromParameters()](#queryFromParameters)


### queryFromParameters()


```php
static public function queryFromParameters(array defaults, array parameters, array overrides = []): \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$defaults`|`array`|``|Can be overwritten by $parameters
`$parameters`|`array`|``|Query parameters (typically from HTTP request)
`$overrides`|`array`|`[]`|Overrides that eventually set fixed values, even if $parameters set these values

