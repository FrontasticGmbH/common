#  SprykerProductApi

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\Product\SprykerProductApi`](../../../../../src/php/SprykerBundle/Domain/Product/SprykerProductApi.php)

**Extends**: [`ProductApiBase`](../../../ProductApiBundle/Domain/ProductApiBase.md)

## Methods

* [__construct()](#__construct)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    SprykerClientInterface $client,
    MapperResolver $mapperResolver,
    LocaleCreator $localeCreator,
    SprykerUrlAppender $urlAppender,
    ProductSearchApi $productSearchApi,
    ?string $defaultLanguage,
    array $productResources = SprykerProductApiExtendedConstants::SPRYKER_DEFAULT_PRODUCT_RESOURCES,
    array $queryResources = SprykerProductApiExtendedConstants::SPRYKER_PRODUCT_QUERY_RESOURCES,
    array $concreteProductResources = SprykerProductApiExtendedConstants::SPRYKER_DEFAULT_CONCRETE_PRODUCT_RESOURCES
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`SprykerClientInterface`](../SprykerClientInterface.md)||
`$mapperResolver`|[`MapperResolver`](../MapperResolver.md)||
`$localeCreator`|[`LocaleCreator`](../Locale/LocaleCreator.md)||
`$urlAppender`|[`SprykerUrlAppender`](../SprykerUrlAppender.md)||
`$productSearchApi`|[`ProductSearchApi`](../../../ProductSearchApiBundle/Domain/ProductSearchApi.md)||
`$defaultLanguage`|`?string`||
`$productResources`|`array`|`SprykerProductApiExtendedConstants::SPRYKER_DEFAULT_PRODUCT_RESOURCES`|
`$queryResources`|`array`|`SprykerProductApiExtendedConstants::SPRYKER_PRODUCT_QUERY_RESOURCES`|
`$concreteProductResources`|`array`|`SprykerProductApiExtendedConstants::SPRYKER_DEFAULT_CONCRETE_PRODUCT_RESOURCES`|

Return Value: `mixed`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): SprykerClientInterface
```

Return Value: [`SprykerClientInterface`](../SprykerClientInterface.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
