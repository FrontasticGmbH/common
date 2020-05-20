#  Client

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\Client`](../../../../src/php/ShopwareBundle/Domain/Client.php)

**Implements**: [`ClientInterface`](ClientInterface.md)

## Methods

* [__construct()](#__construct)
* [forLanguage()](#forlanguage)
* [forCurrency()](#forcurrency)
* [withContextToken()](#withcontexttoken)
* [get()](#get)
* [patch()](#patch)
* [post()](#post)
* [put()](#put)
* [delete()](#delete)

### __construct()

```php
public function __construct(
    HttpClient $httpClient,
    string $apiKey,
    string $baseUri
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$httpClient`|[`HttpClient`](../../HttpClient.md)||
`$apiKey`|`string`||
`$baseUri`|`string`||

Return Value: `mixed`

### forLanguage()

```php
public function forLanguage(
    string $languageId
): ClientInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$languageId`|`string`||

Return Value: [`ClientInterface`](ClientInterface.md)

### forCurrency()

```php
public function forCurrency(
    string $currencyId
): ClientInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$currencyId`|`string`||

Return Value: [`ClientInterface`](ClientInterface.md)

### withContextToken()

```php
public function withContextToken(
    string $token
): ClientInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$token`|`string`||

Return Value: [`ClientInterface`](ClientInterface.md)

### get()

```php
public function get(
    string $uri,
    array $parameters = [],
    array $headers = []
): \GuzzleHttp\Promise\PromiseInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$uri`|`string`||
`$parameters`|`array`|`[]`|
`$headers`|`array`|`[]`|

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

### patch()

```php
public function patch(
    string $uri,
    array $headers = [],
    mixed $body = null
): \GuzzleHttp\Promise\PromiseInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$uri`|`string`||
`$headers`|`array`|`[]`|
`$body`|`mixed`|`null`|

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

### post()

```php
public function post(
    string $uri,
    array $headers = [],
    mixed $body = null
): \GuzzleHttp\Promise\PromiseInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$uri`|`string`||
`$headers`|`array`|`[]`|
`$body`|`mixed`|`null`|

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

### put()

```php
public function put(
    string $uri,
    array $headers = [],
    mixed $body = null
): \GuzzleHttp\Promise\PromiseInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$uri`|`string`||
`$headers`|`array`|`[]`|
`$body`|`mixed`|`null`|

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

### delete()

```php
public function delete(
    string $uri,
    array $headers = []
): \GuzzleHttp\Promise\PromiseInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$uri`|`string`||
`$headers`|`array`|`[]`|

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

Generated with [Frontastic API Docs](https://github.com/FrontasticGmbH/apidocs).
