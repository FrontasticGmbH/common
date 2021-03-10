#  Commercetools

**Fully Qualified**: [`\Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi\Commercetools`](../../../../../src/php/ProductSearchApiBundle/Domain/ProductSearchApi/Commercetools.php)

**Extends**: [`ProductSearchApiBase`](../ProductSearchApiBase.md)

## Methods

* [__construct()](#__construct)
* [getDangerousInnerClient()](#getdangerousinnerclient)

### __construct()

```php
public function __construct(
    Commercetools\Client $client,
    Commercetools\Mapper $mapper,
    Commercetools\Locale\CommercetoolsLocaleCreator $localeCreator,
    EnabledFacetService $enabledFacetService,
    array $languages,
    string $defaultLocale,
    ?int $maxQueryOffset = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`Commercetools`](../../../ProductApiBundle/Domain/ProductApi/Commercetools.md)\Client||
`$mapper`|[`Commercetools`](../../../ProductApiBundle/Domain/ProductApi/Commercetools.md)\Mapper||
`$localeCreator`|[`Commercetools`](../../../ProductApiBundle/Domain/ProductApi/Commercetools.md)\Locale\CommercetoolsLocaleCreator||
`$enabledFacetService`|[`EnabledFacetService`](../../../ProductApiBundle/Domain/ProductApi/EnabledFacetService.md)||
`$languages`|`array`||
`$defaultLocale`|`string`||
`$maxQueryOffset`|`?int`|`null`|

Return Value: `mixed`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): Commercetools\Client
```

Return Value: [`Commercetools`](../../../ProductApiBundle/Domain/ProductApi/Commercetools.md)\Client

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
