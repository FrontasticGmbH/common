#  LogstashJson

**Fully Qualified**: [`\Frontastic\Common\HttpClient\LogstashJson`](../../../src/php/HttpClient/LogstashJson.php)

**Extends**: [`HttpClient`](../HttpClient.md)

The catwalk needs to be configured to read this JSON file.

## Methods

* [__construct()](#__construct)
* [addDefaultHeaders()](#adddefaultheaders)
* [requestAsync()](#requestasync)

### __construct()

```php
public function __construct(
    HttpClient $aggregate
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$aggregate`|[`HttpClient`](../HttpClient.md)||

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

