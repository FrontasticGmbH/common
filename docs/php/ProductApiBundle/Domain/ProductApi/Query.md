#  Query

Fully Qualified: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query`](../../../../../src/php/ProductApiBundle/Domain/ProductApi/Query.php)

Property|Type|Default|Description
--------|----|-------|-----------
`locale`|`string`||
`loadDangerousInnerData`|`bool`|`false`|Access original object from backend
`limit`|`int`|`24`|Optional limit, the default value is <b>24</b>, because it is divisble
by 2, 3, 4 & 6 – which are common numbers or products per row in
frontends.
`offset`|`int`||Optional start offset, default is <b>0</b>.

