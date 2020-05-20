#  ShopwareProjectApi

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\ProjectApi\ShopwareProjectApi`](../../../../../src/php/ShopwareBundle/Domain/ProjectApi/ShopwareProjectApi.php)

**Extends**: [`AbstractShopwareApi`](../AbstractShopwareApi.md)

**Implements**: [`ProjectApi`](../../../ProjectApiBundle/Domain/ProjectApi.md)

## Methods

* [__construct()](#__construct)
* [getSearchableAttributes()](#getsearchableattributes)

### __construct()

```php
public function __construct(
    ClientInterface $client,
    DataMapperResolver $mapperResolver,
    LocaleCreator $localeCreator,
    array $projectLanguages
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`ClientInterface`](../ClientInterface.md)||
`$mapperResolver`|[`DataMapperResolver`](../DataMapper/DataMapperResolver.md)||
`$localeCreator`|[`LocaleCreator`](../Locale/LocaleCreator.md)||
`$projectLanguages`|`array`||

Return Value: `mixed`

### getSearchableAttributes()

```php
public function getSearchableAttributes(): array
```

Return Value: `array`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
