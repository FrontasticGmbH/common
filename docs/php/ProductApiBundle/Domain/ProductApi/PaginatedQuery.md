#  PaginatedQuery

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\PaginatedQuery`](../../../../../src/php/ProductApiBundle/Domain/ProductApi/PaginatedQuery.php)

**Extends**: [`Query`](Query.md)

Property|Type|Default|Description
--------|----|-------|-----------
`limit`|`int`|`self::DEFAULT_LIMIT`|Optional limit, the default value is <b>24</b>, because it is divisble by 2, 3, 4 & 6 – which are common numbers or products per row in frontends.
`offset`|`int`||Optional start offset, default is <b>0</b>.

