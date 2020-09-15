#  SprykerProductSearchApi

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\ProductSearch\SprykerProductSearchApi`](../../../../../src/php/SprykerBundle/Domain/ProductSearch/SprykerProductSearchApi.php)

**Extends**: [`ProductSearchApiBase`](../../../ProductSearchApiBundle/Domain/ProductSearchApiBase.md)

## Methods

* [__construct()](#__construct)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    SprykerClientInterface $client,
    MapperResolver $mapperResolver,
    LocaleCreator $localeCreator,
    array $projectLanguages,
    array $queryResources = SprykerProductApiExtendedConstants::SPRYKER_PRODUCT_QUERY_RESOURCES
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`SprykerClientInterface`](../SprykerClientInterface.md)||
`$mapperResolver`|[`MapperResolver`](../MapperResolver.md)||
`$localeCreator`|[`LocaleCreator`](../Locale/LocaleCreator.md)||
`$projectLanguages`|`array`||
`$queryResources`|`array`|`SprykerProductApiExtendedConstants::SPRYKER_PRODUCT_QUERY_RESOURCES`|

Return Value: `mixed`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
