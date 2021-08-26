#  AlgoliaProductSearchApi

**Fully Qualified**: [`\Frontastic\Common\AlgoliaBundle\Domain\ProductSearchApi\AlgoliaProductSearchApi`](../../../../../src/php/AlgoliaBundle/Domain/ProductSearchApi/AlgoliaProductSearchApi.php)

**Extends**: [`ProductSearchApiBase`](../../../ProductSearchApiBundle/Domain/ProductSearchApiBase.md)

## Methods

* [__construct()](#__construct)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    AlgoliaClient $client,
    EnabledFacetService $enabledFacetService,
    Mapper $mapper
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`AlgoliaClient`](../AlgoliaClient.md)||
`$enabledFacetService`|[`EnabledFacetService`](../../../ProductApiBundle/Domain/ProductApi/EnabledFacetService.md)||
`$mapper`|[`Mapper`](Mapper.md)||

Return Value: `mixed`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
