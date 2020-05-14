#  SapClient

**Fully Qualified**: [`\Frontastic\Common\SapCommerceCloudBundle\Domain\SapClient`](../../../../src/php/SapCommerceCloudBundle/Domain/SapClient.php)

## Methods

* [__construct()](#__construct)
* [getInstanceId()](#getinstanceid)
* [getHostUrl()](#gethosturl)
* [get()](#get)
* [delete()](#delete)
* [post()](#post)
* [put()](#put)
* [checkAccountCredentials()](#checkaccountcredentials)

### __construct()

```php
public function __construct(
    HttpClient $httpClient,
    \Psr\SimpleCache\CacheInterface $cache,
    string $hostUrl,
    string $siteId,
    string $clientId,
    string $clientSecret,
    string $catalogId,
    string $catalogVersionId
): mixed
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$httpClient`|[`HttpClient`](../../HttpClient.md)||
`$cache`|`\Psr\SimpleCache\CacheInterface`||
`$hostUrl`|`string`||
`$siteId`|`string`||
`$clientId`|`string`||
`$clientSecret`|`string`||
`$catalogId`|`string`||
`$catalogVersionId`|`string`||

Return Value: `mixed`

### getInstanceId()

```php
public function getInstanceId(): string
```

Return Value: `string`

### getHostUrl()

```php
public function getHostUrl(): string
```

Return Value: `string`

### get()

```php
public function get(
    string $urlTemplate,
    array $parameters = []
): \GuzzleHttp\Promise\PromiseInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$urlTemplate`|`string`||
`$parameters`|`array`|`[]`|

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

### delete()

```php
public function delete(
    string $urlTemplate,
    array $parameters = []
): \GuzzleHttp\Promise\PromiseInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$urlTemplate`|`string`||
`$parameters`|`array`|`[]`|

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

### post()

```php
public function post(
    string $urlTemplate,
    array $payload,
    array $parameters = []
): \GuzzleHttp\Promise\PromiseInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$urlTemplate`|`string`||
`$payload`|`array`||
`$parameters`|`array`|`[]`|

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

### put()

```php
public function put(
    string $urlTemplate,
    array $payload,
    array $parameters = []
): \GuzzleHttp\Promise\PromiseInterface
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$urlTemplate`|`string`||
`$payload`|`array`||
`$parameters`|`array`|`[]`|

Return Value: `\GuzzleHttp\Promise\PromiseInterface`

### checkAccountCredentials()

```php
public function checkAccountCredentials(
    string $username,
    string $password
): bool
```

Argument|Type|Default|Description
--------|----|-------|-----------
`$username`|`string`||
`$password`|`string`||

Return Value: `bool`

