#  SprykerUrlAppender

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\SprykerUrlAppender`](../../../../src/php/SprykerBundle/Domain/SprykerUrlAppender.php)

## Methods

* [getSeparator()](#getseparator)
* [appendCurrencyToUrl()](#appendcurrencytourl)
* [withIncludes()](#withincludes)

### getSeparator()

```php
public function getSeparator(
    string $url
): string
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$url`|`string`||

Return Value: `string`

### appendCurrencyToUrl()

```php
public function appendCurrencyToUrl(
    string $url,
    string $currency
): string
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$url`|`string`||
`$currency`|`string`||

Return Value: `string`

### withIncludes()

```php
public function withIncludes(
    string $url,
    array $includes = []
): string
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$url`|`string`||
`$includes`|`array`|`[]`|

Return Value: `string`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
