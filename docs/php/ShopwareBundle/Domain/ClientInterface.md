# `interface`  ClientInterface

**Fully Qualified**: [`\Frontastic\Common\ShopwareBundle\Domain\ClientInterface`](../../../../src/php/ShopwareBundle/Domain/ClientInterface.php)

## Methods

* [forLanguage()](#forlanguage)
* [forCurrency()](#forcurrency)
* [withContextToken()](#withcontexttoken)
* [getAccessTokenHeader()](#getaccesstokenheader)
* [getBaseUri()](#getbaseuri)
* [get()](#get)
* [patch()](#patch)
* [post()](#post)
* [put()](#put)
* [delete()](#delete)

### forLanguage()

```php
public function forLanguage(
    ?string $languageId = null
): ClientInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$languageId`|`?string`|`null`|

Return Value: [`ClientInterface`](ClientInterface.md)

### forCurrency()

```php
public function forCurrency(
    ?string $currencyId = null
): ClientInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$currencyId`|`?string`|`null`|

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

### getAccessTokenHeader()

```php
public function getAccessTokenHeader(): string
```

Return Value: `string`

### getBaseUri()

```php
public function getBaseUri(): string
```

Return Value: `string`

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
