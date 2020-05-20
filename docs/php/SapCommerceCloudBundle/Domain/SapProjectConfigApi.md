#  SapProjectConfigApi

**Fully Qualified**: [`\Frontastic\Common\SapCommerceCloudBundle\Domain\SapProjectConfigApi`](../../../../src/php/SapCommerceCloudBundle/Domain/SapProjectConfigApi.php)

## Methods

* [__construct()](#__construct)
* [getLanguageCodes()](#getlanguagecodes)
* [getCurrencyCodes()](#getcurrencycodes)

### __construct()

```php
public function __construct(
    SapClient $client,
    \Psr\SimpleCache\CacheInterface $cache
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$client`|[`SapClient`](SapClient.md)||
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
