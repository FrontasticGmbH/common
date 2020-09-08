#  DefaultLocaleCreator

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\Locale\DefaultLocaleCreator`](../../../../../src/php/SprykerBundle/Domain/Locale/DefaultLocaleCreator.php)

**Extends**: [`LocaleCreator`](LocaleCreator.md)

## Methods

* [__construct()](#__construct)
* [createLocaleFromString()](#createlocalefromstring)

### __construct()

```php
public function __construct(
    SprykerProjectConfigApi $projectConfigApi
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$projectConfigApi`|[`SprykerProjectConfigApi`](../ProjectConfig/SprykerProjectConfigApi.md)||

Return Value: `mixed`

### createLocaleFromString()

```php
public function createLocaleFromString(
    string $localeString
): SprykerLocale
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$localeString`|`string`||

Return Value: [`SprykerLocale`](SprykerLocale.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
