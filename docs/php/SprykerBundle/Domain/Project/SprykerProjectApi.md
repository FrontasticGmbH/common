#  SprykerProjectApi

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\Project\SprykerProjectApi`](../../../../../src/php/SprykerBundle/Domain/Project/SprykerProjectApi.php)

**Extends**: [`SprykerApiBase`](../../BaseApi/SprykerApiBase.md)

**Implements**: [`SprykerProjectApiInterface`](SprykerProjectApiInterface.md)

## Methods

* [__construct()](#__construct)
* [getSearchableAttributes()](#getsearchableattributes)

### __construct()

```php
public function __construct(
    SprykerClientInterface $client,
    MapperResolver $mapperResolver,
    LocaleCreator $localeCreator,
    array $projectLanguages
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`SprykerClientInterface`](../SprykerClientInterface.md)||
`$mapperResolver`|[`MapperResolver`](../MapperResolver.md)||
`$localeCreator`|[`LocaleCreator`](../Locale/LocaleCreator.md)||
`$projectLanguages`|`array`||

Return Value: `mixed`

### getSearchableAttributes()

```php
public function getSearchableAttributes(): array
```

Return Value: `array`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
