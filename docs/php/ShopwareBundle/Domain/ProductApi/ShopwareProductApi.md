#  ShopwareProductApi

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\ProductApi\ShopwareProductApi`](../../../../../src/php/ShopwareBundle/Domain/ProductApi/ShopwareProductApi.php)

**Extends**: [`ProductApiBase`](../../../ProductApiBundle/Domain/ProductApiBase.md)

## Methods

* [__construct()](#__construct)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    ClientInterface $client,
    LocaleCreator $localeCreator,
    DataMapperResolver $mapperResolver,
    EnabledFacetService $enabledFacetService,
    ShopwareProjectConfigApiFactory $projectConfigApiFactory,
    ProductSearchApi $productSearchApi,
    ?string $defaultLanguage
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`ClientInterface`](../ClientInterface.md)||
`$localeCreator`|[`LocaleCreator`](../Locale/LocaleCreator.md)||
`$mapperResolver`|[`DataMapperResolver`](../DataMapper/DataMapperResolver.md)||
`$enabledFacetService`|[`EnabledFacetService`](../../../ProductApiBundle/Domain/ProductApi/EnabledFacetService.md)||
`$projectConfigApiFactory`|[`ShopwareProjectConfigApiFactory`](../ProjectConfigApi/ShopwareProjectConfigApiFactory.md)||
`$productSearchApi`|[`ProductSearchApi`](../../../ProductSearchApiBundle/Domain/ProductSearchApi.md)||
`$defaultLanguage`|`?string`||

Return Value: `mixed`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): ClientInterface
```

Return Value: [`ClientInterface`](../ClientInterface.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
