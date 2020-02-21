#  Locale

Fully Qualified: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale`](../../../../../src/php/ProductApiBundle/Domain/ProductApi/Locale.php)


language[_territory[.codeset]][@modifier]

- de_DE - en_GB@euro
Property|Type|Default|Description
--------|----|-------|-----------
`language`|`string`||A two or three letter identifier for the language, e.g. fr, de, en …
`territory`|`string`||A two letter identifier for the territory, e.g. CH, DE, FR …
`country`|`string`||A human readable country identifier.
`currency`|`string`||A three letter identifier for used currency.
`original`|`string`||

## Methods

* [__toString()](#__tostring)
* [toString()](#tostring)
* [createFromPosix()](#createfromposix)


### __toString()


```php
public function __toString(
    
): string
```







Return Value: `string`

### toString()


```php
public function toString(
    
): string
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

