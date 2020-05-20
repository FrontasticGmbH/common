#  Commercetools

**Fully Qualified**: [`\Frontastic\Common\ProjectApiBundle\Domain\ProjectApi\Commercetools`](../../../../../src/php/ProjectApiBundle/Domain/ProjectApi/Commercetools.php)

**Implements**: [`ProjectApi`](../ProjectApi.md)

## Methods

* [__construct()](#__construct)
* [getSearchableAttributes()](#getsearchableattributes)

### __construct()

```php
public function __construct(
    Commercetools\Client $client,
    Commercetools\Locale\CommercetoolsLocaleCreator $localeCreator,
    array $languages
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`Commercetools`](../../../ProductApiBundle/Domain/ProductApi/Commercetools.md)\Client||
`$localeCreator`|[`Commercetools`](../../../ProductApiBundle/Domain/ProductApi/Commercetools.md)\Locale\CommercetoolsLocaleCreator||
`$languages`|`array`||

Return Value: `mixed`

### getSearchableAttributes()

```php
public function getSearchableAttributes(): array
```

Return Value: `array`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
