#  DefaultLocaleCreator

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\Locale\DefaultLocaleCreator`](../../../../../src/php/ShopwareBundle/Domain/Locale/DefaultLocaleCreator.php)

**Extends**: [`LocaleCreator`](LocaleCreator.md)

## Methods

* [__construct()](#__construct)
* [createLocaleFromString()](#createlocalefromstring)

### __construct()

```php
public function __construct(
    ShopwareProjectConfigApiInterface $projectConfigApi
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$projectConfigApi`|[`ShopwareProjectConfigApiInterface`](../ProjectConfigApi/ShopwareProjectConfigApiInterface.md)||

Return Value: `mixed`

### createLocaleFromString()

```php
public function createLocaleFromString(
    string $localeString
): ShopwareLocale
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$localeString`|`string`||

Return Value: [`ShopwareLocale`](ShopwareLocale.md)

