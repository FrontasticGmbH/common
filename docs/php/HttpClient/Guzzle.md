#  Guzzle

**Fully Qualified**: [`\Frontastic\Common\HttpClient\Guzzle`](../../../src/php/HttpClient/Guzzle.php)

**Extends**: [`HttpClient`](../HttpClient.md)

## Methods

* [__construct()](#__construct)
* [addDefaultHeaders()](#adddefaultheaders)
* [getDefaultHeaders()](#getdefaultheaders)
* [setDefaultHeaders()](#setdefaultheaders)
* [requestAsync()](#requestasync)

### __construct()

```php
public function __construct(
    Options $defaultOptions = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$defaultOptions`|[`Options`](Options.md)|`null`|

Return Value: `mixed`

### addDefaultHeaders()

```php
public function addDefaultHeaders(
    array $headers
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$headers`|`array`||

Return Value: `mixed`

### getDefaultHeaders()

```php
public function getDefaultHeaders(): array
```

Return Value: `array`

### setDefaultHeaders()

```php
public function setDefaultHeaders(
    array $headers
): void
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$headers`|`array`||

Return Value: `void`

### requestAsync()

```php
public function requestAsync(
    string $method,
    string $url,
    string $body = '',
    array $headers = array(),
    Options $options = null
): \GuzzleHttp\Promise\PromiseInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$method`|`string`||
`$url`|`string`||
`$body`|`string`|`''`|
`$headers`|`array`|`array()`|
`$options`|[`Options`](Options.md)|`null`|

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

