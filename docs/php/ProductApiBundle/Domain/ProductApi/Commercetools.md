#  Commercetools

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools`](../../../../../src/php/ProductApiBundle/Domain/ProductApi/Commercetools.php)

**Extends**: [`ProductApiBase`](../ProductApiBase.md)

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
    string $defaultLocale
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`Commercetools`](Commercetools.md)\Client||
`$mapper`|[`Commercetools`](Commercetools.md)\Mapper||
`$localeCreator`|[`Commercetools`](Commercetools.md)\Locale\CommercetoolsLocaleCreator||
`$enabledFacetService`|[`EnabledFacetService`](EnabledFacetService.md)||
`$defaultLocale`|`string`||

Return Value: `mixed`

### getDangerousInnerClient()

```php
public function getDangerousInnerClient(): Commercetools\Client
```

Return Value: [`Commercetools`](Commercetools.md)\Client

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
