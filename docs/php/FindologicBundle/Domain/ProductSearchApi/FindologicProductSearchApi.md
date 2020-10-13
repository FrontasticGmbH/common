#  FindologicProductSearchApi

**Fully Qualified**: [`\Frontastic\Common\FindologicBundle\Domain\ProductSearchApi\FindologicProductSearchApi`](../../../../../src/php/FindologicBundle/Domain/ProductSearchApi/FindologicProductSearchApi.php)

**Extends**: [`ProductSearchApiBase`](../../../ProductSearchApiBundle/Domain/ProductSearchApiBase.md)

## Methods

* [__construct()](#__construct)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    FindologicClient $client,
    ProductSearchApi $originalDataSource,
    Mapper $mapper,
    QueryValidator $validator,
    \Psr\Log\LoggerInterface $logger,
    array $languages
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`FindologicClient`](../FindologicClient.md)||
`$originalDataSource`|[`ProductSearchApi`](../../../ProductSearchApiBundle/Domain/ProductSearchApi.md)||
`$mapper`|[`Mapper`](Mapper.md)||
`$validator`|[`QueryValidator`](QueryValidator.md)||
`$logger`|`\Psr\Log\LoggerInterface`||
`$languages`|`array`||

Return Value: `mixed`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
