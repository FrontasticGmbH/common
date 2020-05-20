#  StatsD

**Fully Qualified**: [`\Frontastic\Common\HttpClient\StatsD`](../../../src/php/HttpClient/StatsD.php)

**Extends**: [`HttpClient`](../HttpClient.md)

## Methods

* [__construct()](#__construct)
* [addDefaultHeaders()](#adddefaultheaders)
* [requestAsync()](#requestasync)

### __construct()

```php
public function __construct(
    string $name,
    \Domnikl\Statsd\Client $statsdClient,
    HttpClient $aggregate
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$name`|`string`||
`$statsdClient`|`\Domnikl\Statsd\Client`||
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

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
