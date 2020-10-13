#  PaginatedQuery

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\PaginatedQuery`](../../../../../src/php/ProductApiBundle/Domain/ProductApi/PaginatedQuery.php)

**Extends**: [`Query`](Query.md)

In general terms, REST APIs use offset pagination whereas GraphQL APIs use
cursor-based pagination.

Regardless the pagination implemented by your backend of choice, we highly
recommend you to use in both cases the property $cursor to store the position
where the pagination should start.

NOTE: the property $offset will be deprecated in a further commit.
Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`limit` | `int` | `self::DEFAULT_LIMIT` | - | Optional limit, the default value is <b>24</b>, because it is divisble by 2, 3, 4 & 6 – which are common numbers or products per row in frontends.
`offset` | `int` |  | - | Optional start offset, default is <b>0</b>.
`cursor` | `string` |  | - | Optional item reference where the pagination should start.

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
