#  Tideways

**Fully Qualified**: [`\Frontastic\Common\HttpClient\Tideways`](../../../src/php/HttpClient/Tideways.php)

**Extends**: [`HttpClient`](../HttpClient.md)

## Methods

* [__construct()](#__construct)
* [addDefaultHeaders()](#adddefaultheaders)
* [requestAsync()](#requestasync)

### __construct()

```php
public function __construct(
    HttpClient $aggregate,
    \Psr\Log\LoggerInterface $logger,
    mixed $clientIdentifier = null
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$aggregate`|[`HttpClient`](../HttpClient.md)||
`$logger`|`\Psr\Log\LoggerInterface`||
`$clientIdentifier`|`mixed`|`null`|

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

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
