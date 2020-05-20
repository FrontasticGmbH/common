#  SapProjectApi

**Fully Qualified**: [`\Frontastic\Common\SapCommerceCloudBundle\Domain\SapProjectApi`](../../../../src/php/SapCommerceCloudBundle/Domain/SapProjectApi.php)

**Implements**: [`ProjectApi`](../../ProjectApiBundle/Domain/ProjectApi.md)

## Methods

* [__construct()](#__construct)
* [getSearchableAttributes()](#getsearchableattributes)

### __construct()

```php
public function __construct(
    SapClient $client,
    SapLocaleCreator $localeCreator,
    array $projectLanguages
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`SapClient`](SapClient.md)||
`$localeCreator`|[`SapLocaleCreator`](Locale/SapLocaleCreator.md)||
`$projectLanguages`|`array`||

Return Value: `mixed`

### getSearchableAttributes()

```php
public function getSearchableAttributes(): array
```

Return Value: `array`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
