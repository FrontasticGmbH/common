#  ShopwareCartApi

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\CartApi\ShopwareCartApi`](../../../../../src/php/ShopwareBundle/Domain/CartApi/ShopwareCartApi.php)

**Extends**: [`CartApiBase`](../../../CartApiBundle/Domain/CartApiBase.md)

## Methods

* [__construct()](#__construct)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    ClientInterface $client,
    LocaleCreator $localeCreator,
    DataMapperResolver $mapperResolver,
    ShopwareProjectConfigApiFactory $projectConfigApiFactory,
    ?string $defaultLanguage
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`ClientInterface`](../ClientInterface.md)||
`$localeCreator`|[`LocaleCreator`](../Locale/LocaleCreator.md)||
`$mapperResolver`|[`DataMapperResolver`](../DataMapper/DataMapperResolver.md)||
`$projectConfigApiFactory`|[`ShopwareProjectConfigApiFactory`](../ProjectConfigApi/ShopwareProjectConfigApiFactory.md)||
`$defaultLanguage`|`?string`||

Return Value: `mixed`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): ClientInterface
```

Return Value: [`ClientInterface`](../ClientInterface.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
