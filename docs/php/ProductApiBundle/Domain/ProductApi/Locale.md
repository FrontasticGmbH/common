#  Locale

**Fully Qualified**: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale`](../../../../../src/php/ProductApiBundle/Domain/ProductApi/Locale.php)

**Extends**: [`ApiDataObject`](../../../CoreBundle/Domain/ApiDataObject.md)

language[_territory[.codeset]][@modifier]

- de_DE - en_GB@euro
Property|Type|Default|Required|Description
--------|----|-------|--------|-----------
`language` | `string` |  | *Yes* | A two or three letter identifier for the language, e.g. fr, de, en …
`territory` | `string` |  | *Yes* | A two letter identifier for the territory, e.g. CH, DE, FR …
`country` | `string` |  | *Yes* | A human readable country identifier.
`currency` | `string` |  | *Yes* | A three letter identifier for used currency.
`original` | `string` |  | *Yes* | 

## Methods

* [__toString()](#__tostring)
* [toString()](#tostring)
* [createFromPosix()](#createfromposix)

### __toString()

```php
public function __toString(): string
```

Return Value: `string`

### toString()

```php
public function toString(): string
```

Return Value: `string`

### createFromPosix()

```php
static public function createFromPosix(
    string $locale
): Locale
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$locale`|`string`||

Return Value: [`Locale`](Locale.md)

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
