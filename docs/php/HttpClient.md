# `abstract`  HttpClient

Fully Qualified: [`\Frontastic\Common\HttpClient`](../../src/php/HttpClient.php)




## Methods

* [addDefaultHeaders()](#addDefaultHeaders)
* [request()](#request)
* [requestAsync()](#requestAsync)
* [__call()](#call)


### addDefaultHeaders()


```php
abstract public function addDefaultHeaders(array $headers): mixed
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$headers`|`array`|``|

### request()


```php
public function request(string $method, string $url, string $body = '', array $headers = array(), [Options](HttpClient/Options.md) $options = null): [Response](HttpClient/Response.md)
```


*Make any HTTP request*



Argument|Type|Default|Description
--------|----|-------|-----------
`$method`|`string`|``|
`$url`|`string`|``|
`$body`|`string`|`''`|
`$headers`|`array`|`array()`|
`$options`|`[Options](HttpClient/Options.md)`|`null`|

### requestAsync()


```php
abstract public function requestAsync(string $method, string $url, string $body = '', array $headers = array(), [Options](HttpClient/Options.md) $options = null): \GuzzleHttp\Promise\PromiseInterface
```






Argument|Type|Default|Description
--------|----|-------|-----------
`$method`|`string`|``|
`$url`|`string`|``|
`$body`|`string`|`''`|
`$headers`|`array`|`array()`|
`$options`|`[Options](HttpClient/Options.md)`|`null`|

### __call()


```php
public function __call(string $functionName, array $arguments): object
```


*Expose HTTP verbs as methods*

Magic wrapper for the request() method which allows you to use the HTTP
verbs as method names on this object. So ->get('http://example.com/')
will work. All parameters are passed on to the request() method.

Argument|Type|Default|Description
--------|----|-------|-----------
`$functionName`|`string`|``|HTTP verb as method name
`$arguments`|`array`|``|Arguments to pass to request method

