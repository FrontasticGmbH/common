# `abstract`  AbstractShopwareApi

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\AbstractShopwareApi`](../../../../src/php/ShopwareBundle/Domain/AbstractShopwareApi.php)

## Methods

* [__construct()](#__construct)

### __construct()

```php
public function __construct(
    ClientInterface $client,
    DataMapperResolver $mapperResolver,
    ?LocaleCreator $localeCreator = null,
    ?string $defaultLanguage = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`ClientInterface`](ClientInterface.md)||
`$mapperResolver`|[`DataMapperResolver`](DataMapper/DataMapperResolver.md)||
`$localeCreator`|?[`LocaleCreator`](Locale/LocaleCreator.md)|`null`|
`$defaultLanguage`|`?string`|`null`|

Return Value: `mixed`

