# `interface`  ProductApi

Fully Qualified: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi`](../../../../src/php/ProductApiBundle/Domain/ProductApi.php)




## Methods

### getCategories

`function getCategories(\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery query): array`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery`|``|

### getProductTypes

`function getProductTypes(\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery query): array`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery`|``|

### getProduct

`function getProduct(\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery query, string mode = self::QUERY_SYNC): ?object`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery`|``|
`$mode`|`string`|`self::QUERY_SYNC`|One of the QUERY_* connstants. Execute the query synchronously or asynchronously?

### query

`function query(\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery query, string mode = self::QUERY_SYNC): object`




**

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery`|``|
`$mode`|`string`|`self::QUERY_SYNC`|One of the QUERY_* connstants. Execute the query synchronously or asynchronously?

### getDangerousInnerClient

`function getDangerousInnerClient(): mixed`


*Get *dangerous* inner client*

*This method exists to enable you to use features which are not yet part
of the abstraction layer.

Be aware that any usage of this method might seriously hurt backwards
compatibility and the future abstractions might differ a lot from the
vendor provided abstraction.

Use this with care for features necessary in your customer and talk with
Frontastic about provising an abstraction.*


