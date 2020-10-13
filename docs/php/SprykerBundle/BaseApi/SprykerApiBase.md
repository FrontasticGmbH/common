#  SprykerApiBase

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\BaseApi\SprykerApiBase`](../../../../src/php/SprykerBundle/BaseApi/SprykerApiBase.php)

## Methods

* [__construct()](#__construct)

### __construct()

```php
public function __construct(
    SprykerClientInterface $client,
    MapperResolver $mapperResolver,
    ?LocaleCreator $localeCreator = null,
    ?string $defaultLanguage = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`SprykerClientInterface`](../Domain/SprykerClientInterface.md)||
`$mapperResolver`|[`MapperResolver`](../Domain/MapperResolver.md)||
`$localeCreator`|?[`LocaleCreator`](../Domain/Locale/LocaleCreator.md)|`null`|
`$defaultLanguage`|`?string`|`null`|

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
