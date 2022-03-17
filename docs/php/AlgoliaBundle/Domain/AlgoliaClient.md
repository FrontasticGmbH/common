#  AlgoliaClient

**Fully Qualified**: [`\Frontastic\Common\AlgoliaBundle\Domain\AlgoliaClient`](../../../../src/php/AlgoliaBundle/Domain/AlgoliaClient.php)

## Methods

* [__construct()](#__construct)
* [setLanguage()](#setlanguage)
* [setSortIndex()](#setsortindex)
* [search()](#search)
* [getSettings()](#getsettings)

### __construct()

```php
public function __construct(
    array $indexesConfig,
    string $defaultLanguage
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$indexesConfig`|`array`||
`$defaultLanguage`|`string`||

Return Value: `mixed`

### setLanguage()

```php
public function setLanguage(
    string $language
): self
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$language`|`string`||

Return Value: `self`

### setSortIndex()

```php
public function setSortIndex(
    array $sortAttributes
): self
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$sortAttributes`|`array`||

Return Value: `self`

### search()

```php
public function search(
    string $query,
    array $requestOptions
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$query`|`string`||
`$requestOptions`|`array`||

Return Value: `mixed`

### getSettings()

```php
public function getSettings(): mixed
```

Return Value: `mixed`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
