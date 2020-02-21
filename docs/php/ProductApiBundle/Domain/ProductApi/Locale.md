#  Locale

Fully Qualified: [`\Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale`](../../../../../src/php/ProductApiBundle/Domain/ProductApi/Locale.php)


language[_territory[.codeset]][@modifier]

- de_DE - en_GB@euro
Property|Type|Default|Description
--------|----|-------|-----------
`language`|`string`|``|A two or three letter identifier for the language, e.g. fr, de, en …
`territory`|`string`|``|A two letter identifier for the territory, e.g. CH, DE, FR …
`country`|`string`|``|A human readable country identifier.
`currency`|`string`|``|A three letter identifier for used currency.
`original`|`string`|``|

## Methods

### __toString

`function __toString(): string`




**


### toString

`function toString(): string`




**


### createFromPosix

`static function createFromPosix(string locale): \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale`






Argument|Type|Default|Description
--------|----|-------|-----------
`$locale`|`string`|``|

