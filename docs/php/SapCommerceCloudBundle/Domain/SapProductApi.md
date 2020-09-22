#  SapProductApi

**Fully Qualified**: [`\Frontastic\Common\SapCommerceCloudBundle\Domain\SapProductApi`](../../../../src/php/SapCommerceCloudBundle/Domain/SapProductApi.php)

**Extends**: [`ProductApiBase`](../../ProductApiBundle/Domain/ProductApiBase.md)

## Methods

* [__construct()](#__construct)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    SapClient $client,
    SapLocaleCreator $localeCreator,
    SapDataMapper $dataMapper,
    ProductSearchApi $productSearchApi
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`SapClient`](SapClient.md)||
`$localeCreator`|[`SapLocaleCreator`](Locale/SapLocaleCreator.md)||
`$dataMapper`|[`SapDataMapper`](SapDataMapper.md)||
`$productSearchApi`|[`ProductSearchApi`](../../ProductSearchApiBundle/Domain/ProductSearchApi.md)||

Return Value: `mixed`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
