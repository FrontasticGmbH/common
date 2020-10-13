#  SapProductSearchApi

**Fully Qualified**: [`\Frontastic\Common\SapCommerceCloudBundle\Domain\SapProductSearchApi`](../../../../src/php/SapCommerceCloudBundle/Domain/SapProductSearchApi.php)

**Extends**: [`ProductSearchApiBase`](../../ProductSearchApiBundle/Domain/ProductSearchApiBase.md)

## Methods

* [__construct()](#__construct)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    SapClient $client,
    SapLocaleCreator $localeCreator,
    SapDataMapper $dataMapper,
    array $projectLanguages
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`SapClient`](SapClient.md)||
`$localeCreator`|[`SapLocaleCreator`](Locale/SapLocaleCreator.md)||
`$dataMapper`|[`SapDataMapper`](SapDataMapper.md)||
`$projectLanguages`|`array`||

Return Value: `mixed`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
