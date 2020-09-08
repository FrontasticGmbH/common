#  SprykerProjectConfigApi

**Fully Qualified**: [`\Frontastic\Common\SprykerBundle\Domain\ProjectConfig\SprykerProjectConfigApi`](../../../../../src/php/SprykerBundle/Domain/ProjectConfig/SprykerProjectConfigApi.php)

## Methods

* [__construct()](#__construct)
* [getLanguageCodes()](#getlanguagecodes)
* [getCurrencyCodes()](#getcurrencycodes)

### __construct()

```php
public function __construct(
    SprykerClient $client,
    \Psr\SimpleCache\CacheInterface $cache
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`SprykerClient`](../SprykerClient.md)||
`$cache`|`\Psr\SimpleCache\CacheInterface`||

Return Value: `mixed`

### getLanguageCodes()

```php
public function getLanguageCodes(): array
```

Return Value: `array`

### getCurrencyCodes()

```php
public function getCurrencyCodes(): array
```

Return Value: `array`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
