#  DefaultSapLocaleCreator

**Fully Qualified**: [`\Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\DefaultSapLocaleCreator`](../../../../../src/php/SapCommerceCloudBundle/Domain/Locale/DefaultSapLocaleCreator.php)

**Extends**: [`SapLocaleCreator`](SapLocaleCreator.md)

## Methods

* [__construct()](#__construct)
* [createLocaleFromString()](#createlocalefromstring)

### __construct()

```php
public function __construct(
    SapProjectConfigApi $projectConfigApi
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$projectConfigApi`|[`SapProjectConfigApi`](../SapProjectConfigApi.md)||

Return Value: `mixed`

### createLocaleFromString()

```php
public function createLocaleFromString(
    string $localeString
): SapLocale
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$localeString`|`string`||

Return Value: [`SapLocale`](SapLocale.md)

