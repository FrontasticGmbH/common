# `abstract`  HttpClient

Fully Qualified: [`\Frontastic\Common\HttpClient`](../../src/php/HttpClient.php)

## Methods

* [addDefaultHeaders()](#adddefaultheaders)
* [request()](#request)
* [requestAsync()](#requestasync)
* [__call()](#__call)

### addDefaultHeaders()

```php
abstract public function addDefaultHeaders(
    array $headers
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$headers`|`array`||

Return Value: `mixed`

### request()

```php
public function request(
    string $method,
    string $url,
    string $body = '',
    array $headers = array(),
    Options $options = null
): Response
```

*Make any HTTP request*

Argument|Type|Default|Description
--------|----|-------|-----------
`$method`|`string`||
`$url`|`string`||
`$body`|`string`|`''`|
`$headers`|`array`|`array()`|
`$options`|[`Options`](HttpClient/Options.md)|`null`|

Return Value: [`Response`](HttpClient/Response.md)

### requestAsync()

```php
abstract public function requestAsync(
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
`$options`|[`Options`](HttpClient/Options.md)|`null`|

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

### __call()

```php
public function __call(
    string $functionName,
    array $arguments
): object
```

*Expose HTTP verbs as methods*

Magic wrapper for the request() method which allows you to use the HTTP
verbs as method names on this object. So ->get('http://example.com/')
will work. All parameters are passed on to the request() method.

Argument|Type|Default|Description
--------|----|-------|-----------
`$functionName`|`string`||HTTP verb as method name
`$arguments`|`array`||Arguments to pass to request method

Return Value: `object`

